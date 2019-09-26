<?php

defined('_JEXEC') or die;

JSFactory::loadExtLanguageFile('product_ask_question');

class JshoppingControllerProduct_Ask_Question extends JControllerLegacy{
    
    function display($cachable = false, $urlparams = false){
        $jshopConfig = JSFactory::getConfig();
        $user = JFactory::getUser();
		$db = JFactory::getDBO();
		$category_id = JRequest::getInt('category_id');
		$product_id = JRequest::getInt('product_id');
		$send = JRequest::getInt('send');
		if (!$category_id || !$product_id) {
            JError::raiseError( 404, _JSHOP_PAGE_NOT_FOUND);
            return;
		}
		
		$product = JTable::getInstance('product', 'jshop');
		$product->load($product_id);
        $listcategory = $product->getCategories(1);

        $category = JTable::getInstance('category', 'jshop');
        $category->load($category_id);
        
        if ($category->category_publish==0 || $product->product_publish==0 || !in_array($product->access, $user->getAuthorisedViewLevels()) || !in_array($category_id, $listcategory)){
            JError::raiseError( 404, _JSHOP_PAGE_NOT_FOUND);
            return;
        }
		
        $product->getExtendsData();
        $product_images = $product->getImages();
        if (trim($product->description)=="") $product->description = $product->short_description;
		
        $view_name = "product";
        $view_config = array("template_path"=>JPATH_COMPONENT."/templates/".$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("ask_question");
        $view->assign('config', $jshopConfig);
        $view->assign('image_product_path', $jshopConfig->image_product_live_path);
        $view->assign('product', $product);
        $view->assign('noimage', 'noimage.gif');
        $view->assign('images', $product_images);
        $view->assign('category_id', $category_id);
        $view->assign('send', $send);
        $view->assign('action', SEFLink('index.php?option=com_jshopping&controller=product_ask_question&task=send',1));
        $view->assign('user', $user);
		
        JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayProductAskQuestionView', array(&$view));
		
        $view->display();
    }
    
    function send(){
        $app = JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $user = JFactory::getUser();
		$db = JFactory::getDBO();
		$category_id = JRequest::getInt('category_id');
		$product_id = JRequest::getInt('product_id');
		$send = JRequest::getInt('send');
		if (!$category_id || !$product_id) {
            JError::raiseError( 404, _JSHOP_PAGE_NOT_FOUND);
            return;
		}
		
		$user_name = JRequest::getString('user_name');
		$user_email = JRequest::getString('user_email');
		$user_question = JRequest::getString('user_question');
		if (!$user_name || !$user_email || !$user_question) {
			$this->setRedirect(SEFLink('index.php?option=com_jshopping&controller=product_ask_question&category_id='.$category_id.'&product_id='.$product_id.'&tmpl=component',1));
            return;
		}
		
		$product = JTable::getInstance('product', 'jshop');
		$product->load($product_id);
        $listcategory = $product->getCategories(1);

        $category = JTable::getInstance('category', 'jshop');
        $category->load($category_id);
        
        if ($category->category_publish==0 || $product->product_publish==0 || !in_array($product->access, $user->getAuthorisedViewLevels()) || !in_array($category_id, $listcategory)){
            JError::raiseError( 404, _JSHOP_PAGE_NOT_FOUND);
            return;
        }
		
        $product->getExtendsData();
        $product_images = $product->getImages();
        if (trim($product->description)=="") $product->description = $product->short_description;
		$product->href = SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$category_id.'&product_id='.$product_id, 0 ,1, -1);
		
        $view_name = "product";
        $view_config = array("template_path"=>JPATH_COMPONENT."/templates/".$jshopConfig->template."/".$view_name);
        $view = $this->getView($view_name, getDocumentType(), '', $view_config);
        $view->setLayout("ask_question");
        $view->assign('image_product_path', $jshopConfig->image_product_live_path);
        $view->assign('admin_mail', true);
        $view->assign('product', $product);
        $view->assign('noimage', 'noimage.gif');
        $view->assign('images', $product_images);
        $view->assign('category_id', $category_id);
        $view->assign('user_name', $user_name);
        $view->assign('user_email', $user_email);
        $view->assign('user_question', $user_question);
		
        JPluginHelper::importPlugin('jshoppingproducts');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeSendAsk', array(&$view));
		
        $message_admin = $view->loadTemplate();
		
        $mailfrom = $app->getCfg( 'mailfrom' );
        $fromname = $app->getCfg( 'fromname' );
		$mailer = JFactory::getMailer();
		$mailer->addReplyTo($user_email, $user_name);
		$mailer->setSender(array($mailfrom, $fromname));
		$mailer->addRecipient(explode(',', $jshopConfig->contact_email));
		$mailer->setSubject( sprintf(_JSHOP_PRODUCT_ASK_QUESTION_SUBJECT, $product->name));
		$mailer->setBody($message_admin);
		$mailer->isHTML(true);
		$send = $mailer->Send();
		
		$this->setRedirect(SEFLink('index.php?option=com_jshopping&controller=product_ask_question&category_id='.$category_id.'&product_id='.$product_id.'&tmpl=component&send=1',1));
    }
    
}
?>