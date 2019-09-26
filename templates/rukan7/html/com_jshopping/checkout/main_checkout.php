<?php
/**
* @version      4.13.0 25.03.2016
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
jimport('joomla.application.component.controller');
class MatCheckout extends JControllerLegacy{
    
    function __construct($config = array()){
        parent::__construct($config);
        JPluginHelper::importPlugin('jshoppingcheckout');
        JPluginHelper::importPlugin('jshoppingorder');
        JDispatcher::getInstance()->trigger('onConstructJshoppingControllerCheckout', array(&$this));
    }
    
    function display($cachable = false, $urlparams = false){
        $this->step2();
    }
    
   
    function step2save(){
		$jshopConfig = JSFactory::getConfig();
		$dispatcher = JDispatcher::getInstance();
		$model = JSFactory::getModel('useredit', 'jshop');
		$adv_user = JSFactory::getUser();
		$user = JFactory::getUser();
		$checkoutStep = JSFactory::getModel('checkoutStep', 'jshop');
		$checkout = JSFactory::getModel('checkout', 'jshop');
        $checkout->checkStep(2);
		
		$post = $this->input->post->getArray();
		$back_url = $checkoutStep->getCheckoutUrl('2');
        
        $dispatcher->trigger('onLoadCheckoutStep2save', array(&$post));

        $cart = JSFactory::getModel('cart', 'jshop');
        $cart->load();
		
		$model->setUser($adv_user);
		$model->setData($post);
		if (!$model->check("address")){
            JError::raiseWarning('', $model->getError());
            $this->setRedirect($back_url );
            return 0;
        }

        $dispatcher->trigger('onBeforeSaveCheckoutStep2', array(&$adv_user, &$user, &$cart, &$model));

		if (!$model->save()){
            JError::raiseWarning('500', _JSHOP_REGWARN_ERROR_DATABASE);
            $this->setRedirect($back_url );
            return 0;
        }
        
        setNextUpdatePrices();
		$checkout->setCart($cart);
		$checkout->setEmptyCheckoutPrices();
			
        $dispatcher->trigger('onAfterSaveCheckoutStep2', array(&$adv_user, &$user, &$cart));
        		
		$next_step = $checkoutStep->getNextStep(2);
		$checkout->setMaxStep($next_step);
		$this->setRedirect($checkoutStep->getCheckoutUrl($next_step));
    }
    
    function step3save(){
        $checkout = JSFactory::getModel('checkoutPayment', 'jshop');
        $checkout->checkStep(3);
        
		$dispatcher = JDispatcher::getInstance();        
		$checkoutStep = JSFactory::getModel('checkoutStep', 'jshop');
        $post = $this->input->post->getArray();
        
        $dispatcher->trigger('onBeforeSaveCheckoutStep3save', array(&$post) );
        
        $cart = JSFactory::getModel('cart', 'jshop');
        $cart->load();
		$checkout->setCart($cart);
        
        $adv_user = JSFactory::getUser();
        
        $payment_method = $this->input->getVar('payment_method'); //class payment method
        $params = $this->input->getVar('params');

		if (!$checkout->savePaymentData($payment_method, $params, $adv_user)){
			JError::raiseWarning('', $checkout->getError());
            $this->setRedirect($checkoutStep->getCheckoutUrl('3'));
            return 0;
		}
		$paym_method = $checkout->getActivePaymMethod();
        
        $dispatcher->trigger('onAfterSaveCheckoutStep3save', array(&$adv_user, &$paym_method, &$cart));
				
		$next_step = $checkoutStep->getNextStep(3);
		$checkout->setMaxStep($next_step);
		$this->setRedirect($checkoutStep->getCheckoutUrl($next_step));
    }
    
    
    function step4save(){
        $checkout = JSFactory::getModel('checkoutShipping', 'jshop');
    	$checkout->checkStep(4);        
		$checkoutStep = JSFactory::getModel('checkoutStep', 'jshop');
		
		$dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeSaveCheckoutStep4save', array());
		
		$sh_pr_method_id = $this->input->getInt('sh_pr_method_id');
		$allparams = $this->input->getVar('params');

        $cart = JSFactory::getModel('cart', 'jshop');
        $cart->load();
		$checkout->setCart($cart);        
        $adv_user = JSFactory::getUser();
		
		if (!$checkout->saveShippingData($sh_pr_method_id, $allparams, $adv_user)){
			JError::raiseWarning('', $checkout->getError());
            $this->setRedirect($checkoutStep->getCheckoutUrl('4'));
            return 0;
		}
		$sh_method = $checkout->getActiveShippingMethod();
		$shipping_method_price = $checkout->getActiveShippingMethodPrice();
        
        $dispatcher->trigger('onAfterSaveCheckoutStep4', array(&$adv_user, &$sh_method, &$shipping_method_price, &$cart));
				
		$next_step = $checkoutStep->getNextStep(4);
		if ($next_step==3){
			$checkout->setMaxStep(4);
		}else{
			$checkout->setMaxStep($next_step);
		}
		$this->setRedirect($checkoutStep->getCheckoutUrl($next_step));
    }
    
    
    function step5save(){
		$session = JFactory::getSession();
        $jshopConfig = JSFactory::getConfig();
		$checkoutStep = JSFactory::getModel('checkoutStep', 'jshop');
        $checkout = JSFactory::getModel('checkoutOrder', 'jshop');
        $checkout->checkStep(5);
		
		$checkagb = $this->input->getVar('agb');
		$post = $this->input->post->getArray();
		$back_url = $checkoutStep->getCheckoutUrl('5');
		$cart_url = SEFLink('index.php?option=com_jshopping&controller=cart&task=view', 1, 1);
		
        $dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onLoadStep5save', array(&$checkagb));

        $adv_user = JSFactory::getUser();
        $cart = JSFactory::getModel('cart', 'jshop')->init();
        $cart->setDisplayItem(1, 1);
		
		$checkout->setCart($cart);
		
		if (!$checkout->checkAgb($checkagb)){
			JError::raiseWarning("", $checkout->getError());
            $this->setRedirect($back_url);
            return 0;
		}
        if (!$cart->checkListProductsQtyInStore()){
            $this->setRedirect($cart_url);
            return 0;
        }
		if (!$checkout->checkCoupon()){
			JError::raiseWarning("", $checkout->getError());
            $this->setRedirect($cart_url);
            return 0;
		}
		
		$order = $checkout->orderDataSave($adv_user, $post);
        
        $dispatcher->trigger('onEndCheckoutStep5', array(&$order, &$cart));

		$checkout->setSendEndForm(0);
        
        if ($jshopConfig->without_payment || $order->order_total==0){
            $checkout->setMaxStep(10);
            $this->setRedirect($checkoutStep->getCheckoutUrl('finish'));
            return 0;
        }
        
        $pmconfigs = $checkout->getPaymentMethod()->getConfigs();
        
        $task = "step6";
        if (isset($pmconfigs['windowtype']) && $pmconfigs['windowtype']==2){
            $task = "step6iframe";
            $session->set("jsps_iframe_width", $pmconfigs['iframe_width']);
            $session->set("jsps_iframe_height", $pmconfigs['iframe_height']);
        }
        $checkout->setMaxStep(6);
        $this->setRedirect($checkoutStep->getCheckoutUrl($task));
    }

    function step6iframe(){
        $checkout = JSFactory::getModel('checkout', 'jshop');
        $checkout->checkStep(6);
        $session = JFactory::getSession();
		$checkoutStep = JSFactory::getModel('checkoutStep', 'jshop');
		
        $width = $session->get("jsps_iframe_width");
        $height = $session->get("jsps_iframe_height");
        if (!$width) $width = 600;
        if (!$height) $height = 600;
		$url = $checkoutStep->getCheckoutUrl('step6&wmiframe=1');
		
        JDispatcher::getInstance()->trigger('onBeforeStep6Iframe', array(&$width, &$height, &$url));
		
		$view = $this->getView("checkout");
        $view->setLayout("step6iframe");
		$view->assign('width', $width);
		$view->assign('height', $height);
		$view->assign('url', $url);
    	$view->display();
    }

    function step6(){
        $checkout = JSFactory::getModel('checkoutOrder', 'jshop');
        $checkout->checkStep(6);
        $jshopConfig = JSFactory::getConfig();
		$checkoutStep = JSFactory::getModel('checkoutStep', 'jshop');
		
        header("Cache-Control: no-cache, must-revalidate");
        $order_id = $checkout->getEndOrderId();
        $wmiframe = $this->input->getInt("wmiframe");

        if (!$order_id){
            JError::raiseWarning("", _JSHOP_SESSION_FINISH);
            if (!$wmiframe){
                $this->setRedirect($checkoutStep->getCheckoutUrl('5'));
            }else{
                $this->iframeRedirect($checkoutStep->getCheckoutUrl('5'));
            }
        }
		
		// user click back in payment system
        if ($checkout->getSendEndForm() == 1){
            $this->cancelPayOrder($order_id);
            return 0;
        }
		
		if (!$checkout->showEndFormPaymentSystem($order_id)){
			$checkout->setMaxStep(10);
            if (!$wmiframe){
                $this->setRedirect($checkoutStep->getCheckoutUrl('finish'));
            }else{
                $this->iframeRedirect($checkoutStep->getCheckoutUrl('finish'));
            }
            return 0;
		}
    }

    function step7(){
        $checkout = JSFactory::getModel('checkoutBuy', 'jshop');
        $wmiframe = $this->input->getInt("wmiframe");
		$checkoutStep = JSFactory::getModel('checkoutStep', 'jshop');

        JDispatcher::getInstance()->trigger('onLoadStep7', array());
		
		$act = $this->input->getVar("act");
        $payment_method = $this->input->getVar("js_paymentclass");
		$no_lang = $this->input->getInt('no_lang');
        
        $checkout->saveToLogPaymentData();
		$checkout->setSendEndForm(0);
		
		$checkout->setAct($act);
		$checkout->setPaymentMethodClass($payment_method);
		$checkout->setNoLang($no_lang);		
		if (!$checkout->loadUrlParams()){
			JError::raiseWarning('', $checkout->getError());
            return 0;
		}
        
        if ($act == "cancel"){
            $this->cancelPayOrder($checkout->getOrderId());
            return 0;
        }
        
        if ($act == "return" && !$checkout->getCheckReturnParams()){
            $checkout->setMaxStep(10);
            if (!$wmiframe){
                $this->setRedirect($checkoutStep->getCheckoutUrl('finish'));
            }else{
                $this->iframeRedirect($checkoutStep->getCheckoutUrl('finish'));
            }
            return 1;
        }
        
		$codebuy = $checkout->buy();

		if ($codebuy==0){
			JError::raiseWarning('', $checkout->getError());
            return 0;
		}
		if ($codebuy==2){
			die();
		}

        if ($checkout->checkTransactionNoBuyCode()){
            JError::raiseWarning(500, $checkout->getCheckTransactionResText());
            if (!$wmiframe){
                $this->setRedirect($checkoutStep->getCheckoutUrl('5'));
            }else{
                $this->iframeRedirect($checkoutStep->getCheckoutUrl('5'));
            }
            return 0;
        }else{
            $checkout->setMaxStep(10);
            if (!$wmiframe){
                $this->setRedirect($checkoutStep->getCheckoutUrl('finish'));
            }else{
                $this->iframeRedirect($checkoutStep->getCheckoutUrl('finish'));
            }
            return 1;
        }
    }

    function finish(){
        $checkout = JSFactory::getModel('checkoutFinish', 'jshop');
        $checkout->checkStep(10);
        $jshopConfig = JSFactory::getConfig();
        $order_id = $checkout->getEndOrderId();
		$text = $checkout->getFinishStaticText();

        JshopHelpersMetadata::checkoutFinish();

        JDispatcher::getInstance()->trigger('onBeforeDisplayCheckoutFinish', array(&$text, &$order_id));

        $view = $this->getView("checkout");
        $view->setLayout("finish");
        $view->assign('text', $text);
        $view->display();

        if ($order_id){
			$checkout->paymentComplete($order_id, $text);
        }

        $checkout->clearAllDataCheckout();
    }

    function cancelPayOrder($order_id=""){
        $jshopConfig = JSFactory::getConfig();
        $checkout = JSFactory::getModel('checkout', 'jshop');
		$checkoutStep = JSFactory::getModel('checkoutStep', 'jshop');
        $wmiframe = $this->input->getInt("wmiframe");

        if (!$order_id){
			$order_id = $checkout->getEndOrderId();
		}
        if (!$order_id){
            JError::raiseWarning("", _JSHOP_SESSION_FINISH);
            if (!$wmiframe){
                $this->setRedirect($checkoutStep->getCheckoutUrl('5'));
            }else{
                $this->iframeRedirect($checkoutStep->getCheckoutUrl('5'));
            }
            return 0;
        }

        $checkout->cancelPayOrder($order_id);
        
        JError::raiseWarning("", _JSHOP_PAYMENT_CANCELED);
        if (!$wmiframe){ 
            $this->setRedirect($checkoutStep->getCheckoutUrl('5'));
        }else{
            $this->iframeRedirect($checkoutStep->getCheckoutUrl('5'));
        }
        return 0;
    }
    
    function iframeRedirect($url){
        echo "<script>parent.location.href='$url';</script>\n";
        $mainframe = JFactory::getApplication();
        $mainframe->close();
    }
    
}