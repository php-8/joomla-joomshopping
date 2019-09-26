<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class JshoppingControllerShopDockBar extends JControllerLegacy{

	function addToCart(){
	}

	function removeFromCart(){
	}

	function addToWishlist(){
	}

	function removeFromWishlist(){
	}

	function displayCompare(){
        $jshopConfig = JSFactory::getConfig(); 
        $db = JFactory::getDBO();
        $session = JFactory::getSession();
		if (!$moduleParams = $this->_getModuleParams()) return;
		$moduleParams = $moduleParams->toObject();
        $shopdockbar_compare = $session->get('shopdockbar_compare',array());
        $lang = JSFactory::getLang();
        $name = $lang->get("name");
        $short_description = $lang->get("short_description");
        $query = "SELECT attr_id, `".$name."` as name FROM `#__jshopping_attr`";
        $db->setQuery($query);
        $all_attributes = $db->loadObjectList('attr_id');
        
        $shopurl = SEFLink('index.php?option=com_jshopping&controller=category',1);
        $endpagebuyproduct = $session->get('jshop_end_page_list_product');
        if ($endpagebuyproduct){
            $shopurl = $endpagebuyproduct;
        }
        
		$products = array();
		$table_compare = new stdClass();
		foreach ($shopdockbar_compare as $product_id=>$value) {
			$product = JTable::getInstance('product', 'jshop');
			$product->load($product_id);
			$product->getCategory();
			$product->extra_fields = $product->getExtraFields();
			$requireAttribute = $product->getRequireAttribute();
			foreach($requireAttribute as $attr_id){
				$product->attribute_values[$attr_id] = $product->getAttribValue($attr_id, array(), $jshopConfig->hide_product_not_avaible_stock);
			}
			$products[] = $product;
		}
		listProductUpdateData($products, 1);
		
		foreach ($products as $product) {
			$table_compare->name[$product->product_id] = $product->$name;
			$table_compare->product_link[$product->product_id] = $product->product_link;
			if ($moduleParams->show_buy)
				$table_compare->buy_link[$product->product_id] = $product->buy_link;
			if ($product->image && $moduleParams->show_image)
				$table_compare->image[$product->product_id] = $product->image;
			if ($moduleParams->show_reviews) {
				$table_compare->average_rating[$product->product_id] = $product->average_rating;
				$table_compare->reviews_count[$product->product_id] = $product->reviews_count;
			}
			if ($product->$short_description && $moduleParams->show_desc)
				$table_compare->short_description[$product->product_id] = $product->$short_description;
			if ($product->manufacturer->name && $moduleParams->show_manuf)
				$table_compare->manufacturer[$product->product_id] = $product->manufacturer->name;
			if ($product->delivery_time && $moduleParams->show_delivery)
				$table_compare->delivery_time[$product->product_id] = $product->delivery_time;
			if ($product->product_old_price > 0)
				$table_compare->product_old_price[$product->product_id] = $product->product_old_price;
			if ($product->product_price > 0)
				$table_compare->product_price[$product->product_id] = $product->product_price;
			if ($product->show_price_from)
				$table_compare->show_price_from[$product->product_id] = $product->show_price_from;
			if ($moduleParams->show_available)
				$table_compare->product_available[$product->product_id] = $product->product_quantity > 0 ? true : false;
			if ($product->product_weight > 0 && $moduleParams->show_weight)
				$table_compare->product_weight[$product->product_id] = $product->product_weight;
			if(!empty($product->extra_fields) && $moduleParams->show_extra_fields){
				foreach($product->extra_fields as $field){
					$table_compare->extra_field[$field['name']][$product->product_id] = $field['value'];
				}
			} 
			if(!empty($product->attribute_values) && $moduleParams->show_attributes){
				foreach($product->attribute_values as $attr_id=>$field){
					$_tmp = array();
					foreach($field as $value) {
						$_tmp[] = $value->value_name;
					}
					$table_compare->attribute_values[$all_attributes[$attr_id]->name][$product->product_id] = implode(', ', $_tmp);
				}
			} 
		}
        
        $view_name = 'shopdockbar';
        $view_config = array('template_path'=>JPATH_COMPONENT.'/templates/addons/'.$view_name.'/'.substr($moduleParams->layout, $moduleParams->layoutdigit));
        $view = $this->getView($view_name, 'html', '', $view_config);
        $view->setLayout("compare");
        $view->assign('config', $jshopConfig);
        $view->assign('products', $products);
        $view->assign('max_quantity', $moduleParams->max_quantity);
        $view->assign('table_compare', $table_compare);
        $view->assign('shopurl', $shopurl);              
        $view->display();
	}

	function addToCompare(){
		$app = JFactory::getApplication();
		$product_id = $app->input->getInt('product_id');
		$category_id = $app->input->getInt('category_id');
		$session = JFactory::getSession();
		if (!$moduleParams = $this->_getModuleParams()) return;
		$max_quantity = $moduleParams->get('max_quantity', 3, int);

		$shopdockbar_compare = $session->get('shopdockbar_compare', array());
		if (count($shopdockbar_compare) >= $max_quantity && $max_quantity > 0) {
			jexit(json_encode(array('msg'=>JText::sprintf('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_MAX_LIMIT', $max_quantity))));
		}

		$product = JTable::getInstance('product', 'jshop');
		$category = JTable::getInstance('category', 'jshop');
		$product->load($product_id);
		$category->load($category_id);
		if (!$product->product_id || !$category->category_id) {
			jexit(json_encode(array('msg'=>_JSHOP_ERROR_DATA)));
		}
		
		$jshopConfig = JSFactory::getConfig();
		$lang = JSFactory::getLang();
		$name = $lang->get('name');
		$noimage = $jshopConfig->noimage;
		if (!$noimage) $noimage = 'noimage.gif';
		$product_compare = new stdClass();
		$product_compare->name = $product->$name;
		if (isset($product->product_thumb_image))
			$product_compare->image = $product->product_thumb_image;
		else
			$product_compare->image = $product->image;
		$product_compare->href = SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$category->category_id.'&product_id='.$product->product_id, 1);
		$product_compare->href_delete = SEFLink('index.php?option=com_jshopping&controller=shopdockbar&task=removeFromCompare&category_id='.$category->category_id.'&product_id='.$product->product_id, 1);
		
		$shopdockbar_compare[$product->product_id] = $product_compare;
		$session->set('shopdockbar_compare', $shopdockbar_compare);

		ob_start();
		
		include str_replace('.php', '/compare.php', JModuleHelper::getLayoutPath('mod_jshopping_shopdockbar', 'default'));
		
		$compare_html = ob_get_contents();  
		ob_end_clean();
		
		jexit(json_encode(array('count'=>count($shopdockbar_compare),'html'=>$compare_html)));
	}

	function removeFromCompare(){
		$app = JFactory::getApplication();
		$product_id = $app->input->getInt('product_id');
		$category_id = $app->input->getInt('category_id');
        $session = JFactory::getSession();
		if (!$moduleParams = $this->_getModuleParams()) return;
		
        $shopdockbar_compare = $session->get('shopdockbar_compare');
		if (isset($shopdockbar_compare[$product_id])) {
			$product_name = $shopdockbar_compare[$product_id]->name;
			unset($shopdockbar_compare[$product_id]);
			$session->set('shopdockbar_compare', $shopdockbar_compare);
			$this->setRedirect($_SERVER['HTTP_REFERER'], JText::sprintf('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_REMOVED', $product_name));
		} else if (!$product_id) {
			$session->clear('shopdockbar_compare');
			$this->setRedirect($_SERVER['HTTP_REFERER'], JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_REMOVED_ALL'));
		} else {
			$this->setRedirect($_SERVER['HTTP_REFERER'], _JSHOP_ERROR_DATA);
		}
	}

	function sendCallBack() {
		JSession::checkToken() or jexit('invalid token');
		
		$app = JFactory::getApplication();
		if (!$moduleParams = $this->_getModuleParams()) return;
		$layout = substr($moduleParams->get('layout', 'default'), $moduleParams->get('layoutdigit'));

		if ($app->input->getString('callback_user_phone') != '' || $app->input->getString('callback_user_token') != '') {
			jexit(JText::_('MOD_JSHOPPING_SHOPDOCKBAR_CALLBACK_THANX'));
		}
		
		$name = $app->input->getString('name');
		$phone = $app->input->getString('phone');
		$url = base64_decode($app->input->getBase64('url'));

		$user = JFactory::getUser();
		if ($user->id){
			$adv_user = JTable::getInstance('userShop', 'jshop');
			$adv_user->load($user->id);
		}else{
			$adv_user = JSFactory::getUserShopGuest();
		}
		$newData = false;
		if ($name && $name != $adv_user->f_name) {
			$adv_user->f_name = $name;
			$newData = true;
		}
		if ($phone && $phone != $adv_user->mobil_phone) {
			$adv_user->mobil_phone = $phone;
			$newData = true;
		}
		if ($newData) {
			$adv_user->store();
		}

        $view_name = 'shopdockbar';
        $view_config = array('template_path'=>JPATH_COMPONENT.'/templates/addons/'.$view_name.'/'.$layout);
        $view = $this->getView($view_name, 'html', '', $view_config);
        $view->setLayout("callback_email");
        $view->assign('name', $name);
        $view->assign('phone', $phone);
        $view->assign('url', $url);
        $body = $view->loadTemplate();
		
		$mailer = JFactory::getMailer();
		$mailer->setSender(array($app->getCfg('mailfrom'), $app->getCfg('fromname')));
		$mailer->addRecipient(explode(';', $moduleParams->get('callback_receipt_email')));
		$mailer->setSubject($moduleParams->get('callback_mail_subject'));
		$mailer->setBody($body);
		$mailer->isHTML(true);

		if ($mailer->Send()) jexit(JText::_('MOD_JSHOPPING_SHOPDOCKBAR_CALLBACK_THANX'));
		jexit(_JSHOP_ERROR_SENDING_MAIL);
	}

	function sendFeedBack() {
		JSession::checkToken() or jexit('invalid token');
		
		$app = JFactory::getApplication();
		if (!$moduleParams = $this->_getModuleParams()) return;
		$layout = substr($moduleParams->get('layout', 'default'), $moduleParams->get('layoutdigit'));
		
		if ($app->input->getString('feedback_user_phone') != '' || $app->input->getString('feedback_user_token') != '') {
			jexit(JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FEEDBACK_THANX'));
		}
		
		$name = $app->input->getString('name');
		$email = $app->input->getString('email');
		$subject = $app->input->getString('subject');
		$message = $app->input->getString('message');
		$url = base64_decode($app->input->getBase64('url'));

		$user = JFactory::getUser();
		if ($user->id){
			$adv_user = JTable::getInstance('userShop', 'jshop');
			$adv_user->load($user->id);
		}else{
			$adv_user = JSFactory::getUserShopGuest();
		}
		$newData = false;
		if ($name && $name != $adv_user->f_name) {
			$adv_user->f_name = $name;
			$newData = true;
		}
		if ($email && $email != $adv_user->email) {
			$adv_user->email = $email;
			$newData = true;
		}
		if ($newData) {
			$adv_user->store();
		}

        $view_name = 'shopdockbar';
        $view_config = array('template_path'=>JPATH_COMPONENT.'/templates/addons/'.$view_name.'/'.$layout);
        $view = $this->getView($view_name, 'html', '', $view_config);
        $view->setLayout("feedback_email");
        $view->assign('name', $name);
        $view->assign('email', $email);
        $view->assign('message', $message);
        $view->assign('url', $url);
        $body = $view->loadTemplate();
		
		$mailer = JFactory::getMailer();
		$mailer->addReplyTo(array($email, $name));
		$mailer->setSender(array($app->getCfg('mailfrom'), $app->getCfg('fromname')));
		$mailer->addRecipient(explode(';', $moduleParams->get('feedback_receipt_email')));
		$mailer->setSubject($subject);
		$mailer->setBody($body);
		$mailer->isHTML(true);

		$error = 0;
		if (!$mailer->Send()) $error = 1;

		if ($app->input->getInt('email_copy')) {
			$mailer = JFactory::getMailer();
			$mailer->setSender(array($app->getCfg('mailfrom'), $app->getCfg('fromname')));
			$mailer->addRecipient(array($email));
			$mailer->setSubject($subject);
			$mailer->setBody($body);
			$mailer->isHTML(true);
			if (!$mailer->Send()) $error = 1;
		}
		if (!$error) jexit(JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FEEDBACK_THANX'));
		jexit(_JSHOP_ERROR_SENDING_MAIL);
	}

	function _getModuleParams(){
		JFactory::getLanguage()->load('mod_jshopping_shopdockbar', JPATH_BASE.'/modules/mod_jshopping_shopdockbar/');
		$module = JModuleHelper::getModule('mod_jshopping_shopdockbar');
		$moduleParams = new JRegistry();
		$moduleParams->loadString($module->params);
		
		return $moduleParams->get('layoutdigit') ? $moduleParams : $moduleParams->get('layoutdigit');
	}

}
?>