<?php

/**
* @package Joomla
* @subpackage JoomShopping
* @author Garry
* @website https://joom-shopping.com
* @email info@joom-shopping.com
**/

class JshSCHelper{
	
	const ADDON = 'jshopping_save_cart';
	const AADDON = 'addon_jshopping_save_cart';
	private static 
		$_this,
		$_real_lang,
		$_cur,
		$_translate,
		$_translate_lang,
		$_addonParams;
	public $data;

	
	public static function init($data = null){
		if(!isset(self::$_this)){
			JSFactory::loadExtLanguageFile(self::AADDON);
			$this_class_name = get_class();
			self::$_this = new $this_class_name();
		}
		if($data)
			self::$_this->data = $data;
		return self::$_this;
	}
	public function param($name){
		if($this->data){
			$private_metod_name = '_'.$name;
			if(
				method_exists($this,$private_metod_name)
				&& ($refl = new ReflectionMethod($this, $private_metod_name))
				&& $refl->isPrivate()
			){
				return $this->$private_metod_name();
			}else if(property_exists($this->data,$name)){
				return $this->data->$name;
			}
		}
		return '';		
	}
	
	
			private function _title(){
				return JSFactory::getConfig()->user_field_title[$this->data->title];
			}
			private function _first_name(){
				return $this->data->f_name;
			}
			private function _last_name(){
				return $this->data->l_name;
			}
			private function _date_cart(){
				return $this->data->date_create;
			}
			private function _date_cart_short(){
				return date('d.m.Y', strtotime($this->data->date_create));
			}
			private function _products(){
				return self::getHtml('products',array(
					'products'=>$this->data->_cart->products,
					'lang'=>$this->data->cart_lang
				));
			}
			private function _sub_total(){
				return formatprice($this->data->_cart->getPriceProducts());
			}
			private function _total(){
				return formatprice($this->data->_cart->getSum());
			}
			private function _discount(){
				$discount = $this->data->_cart->getDiscountShow();
				return $discount ? formatprice(-$discount) : '';
			}
			private function _tax(){				
				return self::getHtml('taxes',array(
					'taxes'=>$this->data->_cart->getTaxExt()
				));
			}
			private function _cart_url(){
				return JURI::getInstance()
					->toString(array("scheme",'host', 'port'))
					.SEFLink('index.php?option=com_jshopping&controller=cart&task=view&lang='.$this->data->cart_lang, 1);
			}
			private function _user_login_url(){
				return JURI::getInstance()
					->toString(array("scheme",'host', 'port'))
					.SEFLink('index.php?option=com_jshopping&controller=user&task=login&lang='.$this->data->cart_lang, 1);
			}	
			private function _user_url(){
				return JURI::getInstance()
					->toString(array("scheme",'host', 'port'))
					.SEFLink('index.php?option=com_jshopping&controller=user&task=view&lang='.$this->data->cart_lang, 1);
			}			
			private function _sitename(){
				return JFactory::getApplication()->getCfg('sitename');
			}

	public static function getInitCart($products){
		$cart = JSFactory::getModel('cart', 'jshop');
		$products = ($products = unserialize($products)) ? $products : array();
		$cart->products = $products;
		$cart->setDisplayFreeAttributes();
		$cart->loadPriceAndCountProducts();
		$cart->reloadRabatValue();
		return $cart;
	}
	
	public static function mailParamsWithValues($data,$wrap=true){
		self::setCur($data->cur);
		$params = self::mailParams();
		$data->_cart = self::getInitCart($data->products);
		$_this = self::init($data);
		foreach($params as $k=>$v)
			$params[$k] = $_this->param($k);
		self::setCur();
		return $wrap ? self::arrayKeyWrap($params) : $params;
	}
	public static function mailParams(){
		return array(
			'title' => 'title',
			'first_name' => 'first_name',
			'last_name' => 'last_name',
			'date_cart' => 'date_cart',
			'date_cart_short' => 'date_cart_short',
			'products' => 'products',		
			'sub_total' => 'sub_total',
			'total' => 'total',
			'discount'=>'discount',
			'tax' => 'tax',
			'cart_url' => 'cart_url',
			'user_url' => 'user_url',
			'user_login_url' => 'user_login_url',
			'user_id' => 'user_id',
			'sitename'=>'sitename'
		);
	}	
	public static function mailParamsKeysWraped(){
		return array_keys(self::arrayKeyWrap(self::mailParams()));
	}	
	public static function mailParamsImplode(){
		return implode(', ', self::mailParamsKeysWraped());
	}
	
	
	
	public static function sendList($email = null, $test_limit = null, $user_id = null){
		$send_list = self::getSendList($test_limit, $user_id);
		$mailfrom = JFactory::getApplication()->getCfg('mailfrom');
		$fromname = JFactory::getApplication()->getCfg('fromname');
		
		foreach($send_list as $key=>$send){					
			if(
				($subject = self::AP('subject_'.$send->cart_lang))
				&& ($body = self::AP('text_'.$send->cart_lang))
			){
				$mailParams = self::mailParamsWithValues($send);
				$recipient = $email ? $email : $send->email;
				
				try {
					$mailer = JFactory::getMailer();
					$mailer->setSender(array($mailfrom, $fromname));
					$mailer->addRecipient($recipient);
					$mailer->setSubject(strtr($subject,$mailParams));
					$mailer->setBody(strtr($body,$mailParams));
					$mailer->isHTML(true);
					if ($mailer->Send() && !$email){
						self::checkSend($send->user_id, $send->type_cart);
					}
				} catch (Exception $e){
					saveToLog('error.log', 'Cart save. Send mail error. '.$e->getMessage());
					self::checkSend($send->user_id, $send->type_cart);
				}
			}
		}
	}
	
	
	public static function getSendList($test_limit = null, $user_id = null){
		$hoursToSeconds = self::AP('notification_after')*60*60;
		$date = date('Y-m-d H:i:s', strtotime(getJsDate())-$hoursToSeconds);
		$db = JFactory::getDbo();
		if ($user_id){
            $where = "cart.type_cart = 'cart' AND cart.user_id=".(int)$user_id;
        }else{
			$where = null === $test_limit
				? " cart.type_cart = 'cart' AND `email_sent` = 0 AND `date_create` <= '".$date."'" 
				: ' cart.type_cart = \'cart\' ORDER BY cart.user_id LIMIT '.$test_limit;
		}
		$query = "
			SELECT cart.*, user.*, cart.lang AS cart_lang
			FROM #__jshopping_cart_for_user AS cart
			LEFT JOIN #__jshopping_users AS user ON (cart.user_id = user.user_id)
			WHERE ".$where."
		";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public static function checkSend($user_id,$type_cart){
		$db = JFactory::getDbo();
		$query = 'UPDATE #__jshopping_cart_for_user
			SET email_sent = 1
			WHERE user_id = '.(int)$user_id.'
				AND type_cart = \''.$type_cart.'\'
		';
		$db->setQuery($query);
		return $db->query();
	}
		
//	public static function getLangNameShortTag($key){
//		$langs = self::langs();
//		foreach($langs as $lang)
//			if($lang->lang == $key)
//				return $lang->language;
//		return JFactory::getConfig()->get('language');
//	}
//	public static function setRealLang(){
//		if(isset(self::$_real_lang))
//			return self::setLang(self::$_real_lang);
//	}
//	public static function setLang($locale){
//		if(!isset(self::$_real_lang))
//			self::$_real_lang = JFactory::getLanguage();
//		if(is_object($locale))
//			return JFactory::$language = $locale;		
//		$locale = self::getLangNameShortTag($locale);
//		return JFactory::$language = JLanguage::getInstance($locale);
//	}
	
	
	public static function setCur($id=null){		
		$conf = JSFactory::getConfig();
		if(!isset(self::$_cur))
			self::$_cur = $conf->cur_currency;
		if(!$id)
			$id = self::$_cur;
		
		$all_currency = JSFactory::getAllCurrency();
		if($id != $conf->cur_currency && isset($all_currency[$id])){
			if (!$all_currency[$id]->currency_value)
				$all_currency[$id]->currency_value = 1;
			$conf->currency_value = $all_currency[$id]->currency_value;
			$conf->currency_code = $all_currency[$id]->currency_code;
			$conf->currency_code_iso = $all_currency[$id]->currency_code_iso;
		}
	}

	public static function getLangCurrentShortTag(){
		$lang = explode('-',JSFactory::getLang()->lang);
		return trim($lang[0]);
	}
	public static function langs(){
		return getAllLanguages();
	}
	public static function htmlLangFlag($lang){
		$lang = is_object($lang) ? $lang->lang : $lang;
		$title = is_object($lang) ? $lang->name.' ('.$lang->language.')' : $lang;
		$alt = '*'.$lang.'*';
		return '<img title="'.$title.'" alt="'.$alt.'" src="/media/mod_languages/images/'.$lang.'.gif">';
	}	
	public static function htmlCheckbox($name, $value = null){
		$checked = (strlen($value)>0) ? 'checked' : '';
		$html = "<input type=\"checkbox\" name=\"params[".$name."]\" ".$checked."  id=\"\" value=\"1\" />";
		return $html;
	}
	
	public static function getHtml($layout,$params=array()){	
		include_once(JPATH_JOOMSHOPPING.'/views/addons/view.html.php');
		$config = array('template_path'=>JPATH_JOOMSHOPPING.'/addons/'.self::AADDON);
		$view = new JshoppingViewAddons($config);
		$view->setLayout('html.'.$layout);
		foreach($params as $k=>$v)
			$view->assign($k, $v);
		return $view->loadTemplate();
	}	
	
	static function AP($name=''){
			if(!isset(self::$_addonParams)) {
				$addon = JTable::getInstance('addon', 'jshop');
				$addon->loadAlias(self::AADDON);
				self::$_addonParams = $addon->getParams();
			}
			if($name !== ''){
				if(array_key_exists($name,self::$_addonParams))
					return self::$_addonParams[$name];
				return false;
			}
			return self::$_addonParams;
	}
	
	
	
	public static function arrayKeyWrap($array,$left='{', $right='}'){
		foreach($array as $k=>$v){
			unset($array[$k]);
			$array[$left.$k.$right] = $v;
		}
		return $array;
	}
	
	
	public static function translateSet($lang){
		return self::$_translate_lang = $lang;
	}
	public static function translateLang($lang){
		if(!$lang)
			$lang = self::$_translate_lang;
		return $lang ? $lang : 'en';
	}
	public static function translateLoadLang($lang){
		if($lang){
			$file = JPATH_JOOMSHOPPING.'/addons/'.self::AADDON.'/langs/'.$lang.'.php';
			if(!isset(self::$_translate[$lang]))
				self::$_translate[$lang] = file_exists($file) ? include $file : array();
			return self::$_translate[$lang];
		}
		return false;
	}
	public static function _($key,$lang=null){
		$lang = self::translateLang($lang);
		$lang = self::translateLoadLang($lang);
		return isset($lang[$key]) ? $lang[$key] : $key;
	}

    public static function deleteItemCart($user_id, $cart_type, $prodnum){
        $db = JFactory::getDbo();
        $query = "SELECT * FROM  #__jshopping_cart_for_user WHERE user_id=".(int)$user_id." and `type_cart`='".$db->escape($cart_type)."'";
        $db->setQuery($query);
        $data = $db->loadObject();
        if ($data->products){
            $products = unserialize($data->products);			
            unset($products[$prodnum]);
			if (count($products)){
				$str = serialize($products);
				$query = "UPDATE #__jshopping_cart_for_user SET products='".$db->escape($str)."' "
                    . "WHERE user_id=".(int)$user_id." and `type_cart`='".$db->escape($cart_type)."'";
			}else{
				$query = "DELETE FROM `#__jshopping_cart_for_user` "
				. "WHERE user_id=".(int)$user_id." and `type_cart`='".$db->escape($cart_type)."'";
			}
            $db->setQuery($query);
            $db->query();
            return 1;
        }else{
            return 0;
        }
    }
	
}

JshSCHelper::init();
