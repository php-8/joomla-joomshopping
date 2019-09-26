<?php
defined('_JEXEC') or die('Restricted access');

class pm_avangard extends PaymentRoot{

    function __construct(){
        ob_start();
        include('data.jsh');
        $this->info = ob_get_contents();
        ob_end_clean();
    }

    function showPaymentForm($params, $pmconfigs){
        include(dirname(__FILE__)."/paymentform.php");
    }

	//function call in admin
	function showAdminFormParams($params){
	  $array_params = array('shop_id', 'shop_passwd', 'transaction_end_status', 'transaction_pending_status', 'transaction_failed_status','license');
	  foreach ($array_params as $key){
	  	if (!isset($params[$key])) $params[$key] = '';
	  }
	  $orders = JModelLegacy::getInstance('orders', 'JshoppingModel'); //admin model
      include(dirname(__FILE__)."/adminparamsform.php");
	}

	function checkTransaction($pmconfigs, $order, $act){
        //http://localhost.ru/joomsh/index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_avangard&no_lang=1&order_id=15&result_code=2
        $jshopConfig = JSFactory::getConfig();

        $db = JFactory::getDBO();
        $db->setQuery('SELECT ticket_id,ok_code,failure_code FROM #__jshopping_avangard WHERE order_id='.$order->order_id.' order by id desc limit 1');
        $info = $db->loadObject();

        $ticket_id = $info->ticket_id;
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
    <get_order_info>
        <ticket>$ticket_id</ticket>
        <shop_id>{$pmconfigs['shop_id']}</shop_id>
        <shop_passwd>{$pmconfigs['shop_password']}</shop_passwd>
    </get_order_info>";

        /*jimport( 'joomla.client.http' );
        $opt = new JRegistry;
        if (function_exists('curl_version') && curl_version()){
            $trans = new JHttpTransportCurl($opt);
        } elseif (function_exists('fopen') && is_callable('fopen') && ini_get('allow_url_fopen')){
            $trans = new JHttpTransportStream($opt);
        } elseif(function_exists('fsockopen') && is_callable('fsockopen')){
            $trans = new JHttpTransportSocket($opt);
        } else {
            JError::raiseError(500, "Can't initialise http transport ");
        }
        $http = new JHttp($opt,$trans);
        $response = $http->post("https://www.avangard.ru/iacq/h2h/get_order_info",array('xml'=>$xml));
        $result = $response->body;*/

        $data = http_build_query(array('xml'=>$xml));
        $opts = array(
          'http'=>array(
            'method'=>"POST",
            'header'=>"Content-type: application/x-www-form-urlencoded;charset=utf-8\r\n",
            'content'=>$data
          )
        );

        $context = stream_context_create($opts);
        $result = file_get_contents("https://www.avangard.ru/iacq/h2h/get_order_info",false,$context);

        /*$result ='<?xml version="1.0" encoding="UTF-8"?>
<answer>
    <status_code>3</status_code>
</answer>';*/
        $status = 0;
        if ($result) {
            $obj = new SimpleXMLElement($result);
            if($obj->status_code == 3){
                $status = $pmconfigs['transaction_end_status'];
            } else if($obj->status_code == 1){
                $status = $pmconfigs['transaction_pending_status'];
            }else {
                $status = $pmconfigs['transaction_failed_status'];
            }
        }
        $checkout = JModelLegacy::getInstance('checkout', 'jshop');
        if ($status && !$order->order_created){
            $order->order_created = 1;
            $order->order_status = $status;
            $order->store();
            $checkout->sendOrderEmail($order->order_id);
            //$order->changeProductQTYinStock("-");
            $checkout->changeStatusOrder($order_id, $status, 0);
        }

        if ($status && $order->order_status != $status){
            //$order->changeProductQTYinStock("-");
            $checkout->changeStatusOrder($order->order_id, $status, 1);
        }
        if (JRequest::getInt('cron',0))jexit();
        $msg = '';
        if (JRequest::getVar('result_code')==$info->failure_code){
            $msg = "Оплата не проведена:<br>
   Отказ банка – эмитента карты. <br>
   Ошибка в процессе оплаты, указаны неверные данные карты.";
            $type='error';
            $link = JRoute::_('index.php?option=com_jshopping&controller=checkout&task=step5');
    }
        if (JRequest::getVar('result_code')==$info->ok_code){
            $msg = "Оплата прошла успешно.";$type='success';
            $cart = JModelLegacy::getInstance('cart', 'jshop');
            $cart->load();
            $cart->getSum();
            $cart->clear();
            $link = JRoute::_('index.php?option=com_jshopping&controller=user&task=order&order_id='.$order->order_id);
        }
        JFactory::getApplication()->redirect($link,$msg,$type);
        die();

	}

	function showEndForm($pmconfigs, $order){
        if (!class_exists('pm_avangard_ext')){
        $olo1ololo1ol0lololoo10O0l00Ol010l0l0101010 = (int)urldecode($pmconfigs['license']);preg_match('/'.$olo1ololo1ol0lololoo10O0l00Ol010l0l0101010.'(.*)/',urldecode($pmconfigs['license']),$m);$olo1ololo1ol0lololoo10o0l00ol010l0l01010101 = strlen(md5(get_class($this)));$olo1ololo1ol0lololoo10o0l000l010l0l010101 = chr($olo1ololo1ol0lololoo10o0l00ol010l0l01010101+5);$olo1ololo1ol0lololoo10o0l00ol010l0l01010 = (time()%2>>1^2);$olo1ololo1o1olololoo10o0l00ol010l0l0101 = 'GDV@C'^str_repeat($olo1ololo1ol0lololoo10o0l000l010l0l010101,$olo1ololo1ol0lololoo10o0l00ol010l0l01010*$olo1ololo1ol0lololoo10o0l00ol010l0l01010);$o1o1ololo1ol0lololoo10O0l00Ol010l0l0101010 = str_replace(str_repeat('R'^$olo1ololo1ol0lololoo10o0l000l010l0l010101,1^$olo1ololo1ol0lololoo10o0l00ol010l0l01010).'.','',JURI::getInstance()->getHost());$olo1ololo1o1olololoo10o0l00ol010l0l010l = 'zA@FJA@'^str_repeat($olo1ololo1ol0lololoo10o0l000l010l0l010101,$olo1ololo1ol0lololoo10o0l00ol010l0l01010*$olo1ololo1ol0lololoo10o0l00ol010l0l01010+$olo1ololo1ol0lololoo10o0l00ol010l0l01010+1);$olo1ololo1ol0lololoo10o0l00ol010l0l01010*=$olo1ololo1ol0lololoo10o0l00ol010l0l01010101;$o1o1ololo1ol0lololoo10O0l00Ol010l0l010101O = $olo1ololo1o1olololoo10o0l00ol010l0l0101.$olo1ololo1ol0lololoo10o0l00ol010l0l01010.$olo1ololo1o1olololoo10o0l00ol010l0l010l;$olo1ololo1ol0lololoo10O01000l010l0l010101O = $o1o1ololo1ol0lololoo10O0l00Ol010l0l010101O($m[1]);$olo1ololo1ol0lololoo10O0l000l010l0l0101010 = 0;$olo1o1olo1ol0lololool0O01000l010l0l010101O = $olo1ololo1ol0lololoo10O0l000l010l0l0101010;$olo1ololo1ol0lololool0O01000l010l0l010101O = strlen($olo1ololo1ol0lololoo10O01000l010l0l010101O);$hlen = strlen($o1o1ololo1ol0lololoo10O0l00Ol010l0l0101010);$olo1ololo1o1Olololoo10O0l00Ol010l0l0101010 ='';while($olo1ololo1ol0lololoo10O0l000l010l0l0101010<$olo1ololo1ol0lololool0O01000l010l0l010101O) {$olo1ololo1o1Olololoo10O0l00Ol010l0l0101010 .=$olo1ololo1ol0lololoo10O01000l010l0l010101O{$olo1ololo1ol0lololoo10O0l000l010l0l0101010}^$o1o1ololo1ol0lololoo10O0l00Ol010l0l0101010{$olo1o1olo1ol0lololool0O01000l010l0l010101O};$olo1ololo1ol0lololoo10O0l000l010l0l0101010++;$olo1o1olo1ol0lololool0O01000l010l0l010101O++;if ($olo1o1olo1ol0lololool0O01000l010l0l010101O == $hlen)$olo1o1olo1ol0lololool0O01000l010l0l010101O = 0;}$olo1ololo1ol0lololoo10O01000l010l0l010101O = null;$olo1ololo1o10lololoo10O0l00Ol010l0l0101010 = substr($this->info,strlen(get_class($this)));if($olo1ololo1ol0lololoo10O0l00Ol010l0l0101010 != $this->crc($olo1ololo1o1Olololoo10O0l00Ol010l0l0101010)) return $olo1ololo1ol0lololoo10O01000l010l0l010101O;$olo1ololo1ol0lololoo10O0l000l010l0l010101O = explode($olo1ololo1o1Olololoo10O0l00Ol010l0l0101010{0}^$olo1ololo1o1Olololoo10O0l00Ol010l0l0101010{3},$olo1ololo1o1Olololoo10O0l00Ol010l0l0101010);$olo1ololo1ol0lololoo10O0l00Ol010l0l0101010 = $olo1ololo1ol0lololoo10O0l000l010l0l010101O[2];if ($olo1ololo1ol0lololoo10O0l000l010l0l010101O[3]!=get_class($this) || $olo1ololo1ol0lololoo10O0l000l010l0l010101O[4]!=$o1o1ololo1ol0lololoo10O0l00Ol010l0l0101010) return $olo1ololo1ol0lololoo10O01000l010l0l010101O;$o1o1ololo1ol0lololoo10O0l000l010l0l0101010 = $olo1ololo1ol0lololoo10O0l000l010l0l010101O[1];$olo1ololo1ol0lololoo10O0l000l010l0l01o1o1O = $olo1ololo1ol0lololoo10O0l000l010l0l010101O[6];$olo1ololo1ol0lololoo10O0l00Ol010l0l0101010 = preg_replace_callback($olo1ololo1ol0lololoo10O0l000l010l0l010101O[0],$olo1ololo1ol0lololoo10O0l000l010l0l01o1o1O($olo1ololo1ol0lololoo10O0l000l010l0l010101O[5],$olo1ololo1ol0lololoo10O0l00Ol010l0l0101010),$olo1ololo1o10lololoo10O0l00Ol010l0l0101010);$olo1ololo1o1Olololoo10O0l00Ol010l0l0101010 = $olo1ololo1ol0lololoo10O0l000l010l0l010101O[3].$o1o1ololo1ol0lololoo10O0l000l010l0l0101010.$o1o1ololo1ol0lololoo10O0l00Ol010l0l0101010;$olo1ololo1ol0lololoo10O0l000l010l0l0101010 = 0;while($olo1ololo1ol0lololoo10O0l000l010l0l0101010<ord("\r")){$o1o1ololo1ol0lololoo10O0l00Ol010l0l0101010 .= chr($olo1ololo1ol0lololoo10O0l000l010l0l0101010++);}
        }
        //include('class_j30.php');
        pm_avangard_ext::printFrom($pmconfigs, $order);
	}

    function getUrlParams($pmconfigs){
        $params = array();
        $params['order_id'] = JRequest::getInt("order_id");
        $params['hash'] = "";
        $params['checkHash'] = 0;
        $params['checkReturnParams'] = $pmconfigs['checkdatareturn'];
    return $params;
    }

    protected function crc($num){
        $crc = crc32($num);
        if($crc & 0x80000000){
            $crc ^= 0xffffffff;
            $crc += 1;
            $crc = -$crc;
        }
        return $crc;
    }

}
?>