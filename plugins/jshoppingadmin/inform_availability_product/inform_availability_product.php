<?php
defined('_JEXEC') or die('Restricted access');

class plgJshoppingAdminInform_availability_product extends JPlugin {

    function __construct(&$subject, $config){
        parent::__construct($subject, $config);
        JSFactory::loadExtLanguageFile('addon_inform_availability_product');
        include_once(JPATH_ROOT.'/components/com_jshopping/addons/inform_availability/inform_availability_helper.php');
    }
	
    public function onAfterSaveProductEnd($product_id){
        $product = JTable::getInstance('product', 'jshop');
        $product->load($product_id);
        if ($product->product_quantity > 0 && $product->product_publish == 1){
            $db = JFactory::getDBO();
            $query = "SELECT * FROM `#__jshopping_requests_availability_product` WHERE `product_id` = ".$product->product_id." AND `email_send` = 0";
            $db->setQuery($query);
            $users = $db->loadObjectList();
            
            $this->informUsers($product, $users);
        }
    }
    
    private function changeDependentAttributesKeys($dependentAttributes){
        $result = array();
        foreach ($dependentAttributes as $dependentAttribute){
            $result[$dependentAttribute->product_attr_id] = $dependentAttribute;
        }
        
        return $result;
    }
    
    private function informUsers($product, $users){
        $db = JFactory::getDBO();
        $liveurlhost = JURI::getInstance()->toString(array("scheme",'host', 'port'));
        $jshopConfig = JSFactory::getConfig();

        $mailfrom = JFactory::getApplication()->getCfg('mailfrom');
        $fromname = JFactory::getApplication()->getCfg('fromname');
        $lang = JSFactory::getLang();
        $dependentAttributes = $this->changeDependentAttributesKeys($product->getAttributes());
        
        foreach ($users as $user) {
            if (!empty($user->email) && (!$user->product_attr_id || (count($dependentAttributes) && $dependentAttributes[$user->product_attr_id]->count > 0))){
                if (empty($user->language)){
                    $user->language = JSFactory::getLang()->lang;
                }
                
                $adv_link = '';
                if (isset($user->product_attr_id) && $user->product_attr_id > 0){
                    $adv_where = " AND `product_attr_id` = ". $user->product_attr_id;
                    $adv_link = "&product_attr_id=".$user->product_attr_id;
                } else {
                    $adv_where = " AND `product_attr_id` = 0";
                }

                JFactory::getLanguage()->load('addon_inform_availability_product', $jshopConfig->admin_path, $user->language, true);

                $lang->setLang($user->language);
                $_name = $lang->get('name');
                $product_name = $product->$_name;

                $url = $liveurlhost.'/index.php?option=com_jshopping&controller=inform_availability_product&task=getlink&product_id='.$product->product_id.$adv_link.'&lang='.substr($user->language, 0, 2);
                
                $slang = substr($user->language, 0, 2);
                $data = array(
                    'product' => $product_name,
                    'url' => $url,
                    'email' => $user->email,
                    'name' => $user->name
                );
                $subject = InformAvailabilityHelper::getSubject($slang, $data);
                $msg = InformAvailabilityHelper::getBody($slang, $data);
                
                $mailer = JFactory::getMailer();                    
                $mailer->setSender(array($mailfrom, $fromname));
                $mailer->addRecipient($user->email);
                $mailer->setSubject($subject);
                $mailer->setBody($msg);
                $mailer->isHtml(1);

                if ($mailer->Send()){
                    $query = "UPDATE `#__jshopping_requests_availability_product` SET `email_send` = 1 WHERE `email` = '".$user->email."' AND `product_id` = ".$product->product_id." ".$adv_where;
                    $db->setQuery($query);
                    $db->query();
                }
            }
        }
    }
	
	function onBeforeSaveAddons(&$params, &$post, &$row){
		$input = JFactory::getApplication()->input;
		$paramsRaw = $input->get('params', '', 'RAW');		
		foreach(InformAvailabilityHelper::langs() as $l){
			$params['text_'.$l->lang] = $paramsRaw['text_'.$l->lang];
		}
	}
}