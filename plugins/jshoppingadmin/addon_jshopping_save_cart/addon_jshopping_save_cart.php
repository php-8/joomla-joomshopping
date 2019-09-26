<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Garry
* @website https://joom-shopping.com
* @email info@joom-shopping.com
**/
defined('_JEXEC') or die('Restricted access');
require_once JPATH_SITE.'/components/com_jshopping/addons/addon_jshopping_save_cart/JshSCHelper.php'; 

class plgJshoppingAdminAddon_jshopping_save_cart extends JPlugin{

    const AADDON = 'addon_jshopping_save_cart';

    public function __construct(&$subject, $config){
        parent::__construct($subject, $config);
    }

    public function onAfterSaveAddons(&$params, &$post, &$row){
        if(isset($post['addon_alias'])
            && $post['addon_alias'] == JshSCHelper::AADDON
            && isset($post['params'])
            && isset($post['params']['test_send'])
            && isset($post['params']['test_email'])
            && isset($params['test_send'])
            && $post['params']['test_email']
        ) JshSCHelper::sendList($post['params']['test_email'],1);
    }

    public function onBeforeEditUsers(&$view){
        $user_id = JRequest::getInt("user_id");
        if (!$user_id || !$this->checkLicKey()){
            return NULL;
        }
        $act = JRequest::getVar('acs_act');
        $prodnum = JRequest::getInt('pn');
        $cart_type = JRequest::getVar('cart_type');
        if ($act=='delete'){
            JshSCHelper::deleteItemCart($user_id, $cart_type, $prodnum);
        }

        $alias_addon = self::AADDON;
        $tableAddon = JTable::getInstance('addon', 'jshop');
        $tableAddon->loadAlias($alias_addon);
        $params = $tableAddon->getParams();

        $db = JFactory::getDbo();
        $query = "SELECT products, date_create, type_cart, cur, lang FROM  #__jshopping_cart_for_user WHERE user_id = ".$user_id." ORDER BY type_cart";
        $db->setQuery($query);
        $results = $db->loadObjectList();
        if($results)
            foreach($results as $result){
                if(($params['save_for_cart']&&$result->type_cart=='cart')||($params['save_for_wishlist']&&$result->type_cart=='wishlist')){
                    JshSCHelper::setCur($result->cur);
                    $products_rez = $result->products;
                    $date_create = $result->date_create;
                    $date_create = date("d.m.Y", strtotime($date_create));

                    $products = strlen($products_rez) ? unserialize($products_rez) : array();
                    if (count($products)>0){
                        $this->initDisplayFreeAttributes($products);
                        JSFactory::loadLanguageFile();
                        include 'cart_content.php';
                    }
                }
            }
            JshSCHelper::setCur();
    }
	
	private function getFilterUserWithCart(){
		$app = JFactory::getApplication();
        $context = "jshopping.list.admin.users";
        return $app->getUserStateFromRequest($context.'user_with_cart', 'user_with_cart', 0, 'int' );
	}
	
	private function showFilterForUserList(&$view){
		$user_with_cart = $this->getFilterUserWithCart();
		
		$option = array();
		$option[] = JHTML::_('select.option', 0, ' - '._JSHOP_CART.' - ', 'id', 'name');
		$option[] = JHTML::_('select.option', 1, _USER_WITH_CART, 'id', 'name');
		$option[] = JHTML::_('select.option', 2, _USER_WITHOUT_CART, 'id', 'name');
		
		$view->tmp_html_filter .= '<div class="pull-left" style="padding-right:15px;">'.
		JHTML::_('select.genericlist', $option, 'user_with_cart', 'style="width: 100px;" class="chosen-select" onchange="document.adminForm.submit();"', 'id', 'name', $user_with_cart).
		'</div>';
	}

    public function onBeforeDisplayUsers(&$view){
        JSFactory::loadLanguageFile();
		
		$this->showFilterForUserList($view);
        
        if (!$this->checkLicKey()){
            JError::raiseWarning('', 'Please enter license key (Addon Save Cart)');
            return;
        }
        if (count($view->rows)===0){
            return;
		}
        $uids = array();
        foreach($view->rows as $v){
            $uids[] = $v->user_id;
        }
        $alias_addon = self::AADDON;
        $tableAddon = JTable::getInstance('addon', 'jshop');
        $tableAddon->loadAlias($alias_addon);
        $params = $tableAddon->getParams();

        $db = JFactory::getDBO();
        $query = "SELECT * FROM  #__jshopping_cart_for_user WHERE user_id IN (".implode(',', $uids).")";
        $db->setQuery($query);
        $list = $db->loadObjectList();
        if(count($list)===0)
            return;
        $ajson = array();
        foreach($list as $u){
            //var_dump($u);die();
            if(($params['save_for_cart']&&$u->type_cart=='cart')||($params['save_for_wishlist']&&$u->type_cart=='wishlist')){
                JshSCHelper::setCur($u->cur);
                $products = strlen($u->products) ? unserialize($u->products) : array();
                if(count($products)===0)
                    continue;
                $uid = $u->user_id;
                if($u->type_cart=='cart')
                    $type_cart = _JSHOP_CART;
                if($u->type_cart=='wishlist')
                    $type_cart = _JSHOP_WISHLIST;
                $ajson[$uid][$u->type_cart]['uid'] = $uid;
                $ajson[$uid][$u->type_cart]['type_cart'] = $u->type_cart;
                $ajson[$uid][$u->type_cart]['date'] = $type_cart.' ('.date("d.m.Y", strtotime($u->date_create)).') '.$u->lang;
                $ajson[$uid][$u->type_cart]['total'] = 0.00;
                $ajson[$uid][$u->type_cart]['list'] = '<table>';
                //print_r($products);die;
                foreach($products as $p){
                    $ajson[$uid][$u->type_cart]['total'] += $sum = $p['price']*$p['quantity'];
                    $ajson[$uid][$u->type_cart]['list'] .= '<tr><td><a href="index.php?option=com_jshopping&controller=products&task=edit&product_id='.$p['product_id'].'" target="_blank">'.$p['product_name'].'</a></td><td>'.formatprice($sum).$p['_ext_price_total_html'].'('.$p["quantity"].')</td></tr>';						
                }
                $ajson[$uid][$u->type_cart]['list'] .= '</table>';
                $ajson[$uid][$u->type_cart]['total'] = formatprice($ajson[$uid][$u->type_cart]['total']).$p['_ext_price_total_html'];
                $ajson[$uid][$u->type_cart]['email'] = '<a class="btn" href="index.php?option=com_jshopping&controller=cart_save&task=sendmail&user_id='.$u->user_id.'" style="margin-left:35px;">Send mail</a>';
            }
        }
        JshSCHelper::setCur();

        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root().'plugins/jshoppingadmin/addon_jshopping_save_cart/style.css');
        $document->addScript(JURI::root().'plugins/jshoppingadmin/addon_jshopping_save_cart/jshuserscart.js');
        $document->addScriptDeclaration('jQuery(document).ready(function(){jshuserscart.init('.json_encode($ajson).');});');
    }
    
    function initDisplayFreeAttributes(&$products){
        $jshopConfig = JSFactory::getConfig();
        if ($jshopConfig->admin_show_freeattributes){
            $_freeattributes = JSFactory::getTable('freeattribut', 'jshop');
            $namesfreeattributes = $_freeattributes->getAllNames();
        }
        foreach($products as $k=>$prod){
            if ($jshopConfig->admin_show_freeattributes){
                $freeattributes = unserialize($prod['freeattributes']);
                if (!is_array($freeattributes)) $freeattributes = array();
                $free_attributes_value = array();
                foreach($freeattributes as $id=>$text){
                    $obj = new stdClass();
                    $obj->attr = $namesfreeattributes[$id];
                    $obj->value = $text;
                    $free_attributes_value[] = $obj;
                }
                $products[$k]['free_attributes_value'] = $free_attributes_value;
            }else{
                $products[$k]['free_attributes_value'] = array();
            }
        }
    }
	
	function onBeforeSaveAddons(&$params, &$post, &$row){
		$input = JFactory::getApplication()->input;
		$paramsRaw = $input->get('params', '', 'RAW');		
		foreach(JshSCHelper::langs() as $l){
			$params['text_'.$l->lang] = $paramsRaw['text_'.$l->lang];
		}
	}
	
	function onJshoppingModelUsersGetCountAllUsersBefore(&$obj, &$var){
		$user_with_cart = $this->getFilterUserWithCart();
		if ($user_with_cart){
			if ($user_with_cart==1){
				$exWhere = " left join #__jshopping_cart_for_user as C on U.user_id=C.user_id
						 where C.products!='' and ";
			}else{
				$exWhere = " left join #__jshopping_cart_for_user as C on U.user_id=C.user_id
						 where (C.products='' or C.products is null) and ";
			}
			$var['query'] = str_replace('where', $exWhere, $var['query']);
		}
	}
	
	function onJshoppingModelUsersGetAllUsersBefore(&$obj, &$var){
		$user_with_cart = $this->getFilterUserWithCart();
		if ($user_with_cart){
			if ($user_with_cart==1){
				$exWhere = " left join #__jshopping_cart_for_user as C on U.user_id=C.user_id
						 where C.products!='' and ";
			}else{
				$exWhere = " left join #__jshopping_cart_for_user as C on U.user_id=C.user_id
						 where (C.products='' or C.products is null) and ";
			}
			$var['query'] = str_replace('where', $exWhere, $var['query']);
		}
	}
    
    function checkLicKey(){
        return true;
    }
	
}
