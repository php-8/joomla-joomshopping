<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Nevigen.com
* @website https://nevigen.com/
* @email support@nevigen.com
* @copyright Copyright © Nevigen.com. All rights reserved.
* @license Proprietary. Copyrighted Commercial Software
* @license agreement https://nevigen.com/license-agreement.html
**/

defined( '_JEXEC' ) or die;

class plgJshoppingBoxberry extends JPlugin {

 	protected $autoloadLanguage = true;
	protected $listCities = array();
	protected $courierListCities = array();
	protected $calcule_pvz_price = null;
	protected $calcule_courier_price = null;
	
    protected function apiSend($method, $data = array()){
		$url = 'http://api.boxberry.ru/json.php?token='.$this->params->get('api_token').'&method='.$method;
		if (count($data)) {
			$url .= '&' . http_build_query($data);
		}
		$handle = fopen($url, 'rb');
		$contents = stream_get_contents($handle);
		fclose($handle);
		$data = json_decode($contents,true);
		if(count($data)<=0 || $data[0]['err']) {
			JFactory::getApplication()->enqueueMessage($data[0]['err'], 'error');
			return array();
		} else {
			return $data;
		}
	}
	
    protected function parcelSend($data){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://api.boxberry.ru/json.php');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array(
			'token' => $this->params->get('api_token'),
			'method' => 'ParselCreate',
			'sdata' => json_encode($data)
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$data = json_decode(curl_exec($ch),1);
		curl_close($ch);
		if(count($data)<=0 || $data['err']) {
			JFactory::getApplication()->enqueueMessage($data['err'], 'error');
			return array();
		} else {
			return $data;
		}
	}
   
    protected function parcel($order) {
		if ($order->shipping_method_id != $this->params->get('courier_id') && $order->shipping_method_id != $this->params->get('pvz_id')) {
			return;
		}
		
		$params = (array)unserialize($order->shipping_params_data);
		if (in_array($order->order_status, $this->params->get('status_id'))) {
			$shopData = array();
			if ($params['id']) {
				$shopData['name'] = $params['id'];
			}
			$parcel = array(
				'order_id' => $this->params->get('order_type', 'id') == 'id' ? $order->order_id : $order->order_number,
				'price' => $order->order_total - $order->order_shipping,
				'payment_sum' => in_array($order->payment_method_id, $this->params->get('payment_id', array())) ? $order->order_total : 0,
				'delivery_sum' => $order->order_shipping,
				'vid' => $order->shipping_method_id == $this->params->get('pvz_id') ? 1 : 2,
				'shop' => $shopData,
				'weights' => array('weight' => $order->getWeightItems() * 1000),
				'customer' => array(
					'fio' => $order->l_name . ' ' . $order->f_name,
					'email' => $order->email,
					'address' => $order->street,
					'phone' => $order->mobil_phone,
				),
				'kurdost' => array(
					'index' => $order->zip,
					'citi' => $order->city,
					'addressp' => $order->street,
					'comentk' => $order->order_add_info,
				),
				'items' => array(),
			);
			$order->items = $order->getAllItems();
			foreach ($order->items as $item) {
				$parcel['items'][] = array(
					'id' => $this->params->get('product_type', 'id') == 'id' ? $item->product_id : $item->product_ean,
					'name' => $item->product_name,
					'nds' => 0,
					'price' => $item->product_item_price,
					'quantity' => $item->product_quantity,
				);
			}
			if ($params['ImId'] != '') {
				$parcel['updateByTrack'] = $params['ImId'];
			}
			$data = $this->parcelSend($parcel);
			if (count($data)) {
				$params['ImId'] = $data['track'];
				$order->shipping_params_data = serialize($params);
				$order->store();
				if ($parcel['updateByTrack']) {
					JFactory::getApplication()->enqueueMessage(JText::sprintf('PLG_JSHOPPING_BOXBERRY_CHANGE_TRACKING_OK', $params['ImId']));
				} else {
					JFactory::getApplication()->enqueueMessage(JText::sprintf('PLG_JSHOPPING_BOXBERRY_SENDING_TRACKING_OK', $params['ImId']));
				}
			}
		} else if (in_array($order->order_status, $this->params->get('cancel_status_id')) && $params['ImId'] != '') {
			$data = $this->apiSend('ParselDel', array('ImId'=>$params['ImId']));
			if ($data['text'] == 'ok') {
				JFactory::getApplication()->enqueueMessage(JText::sprintf('PLG_JSHOPPING_BOXBERRY_DELETE_TRACKING_OK', $params['ImId']));
				unset($params['ImId']);
				$order->shipping_params_data = serialize($params);
				$order->store();
			}
		}
	}
   
    protected function init(){
		static $load;
		if ($load) {
			return;
		}
		
		$data = $this->apiSend('ListCities');
		foreach ($data as $row) {
			$this->listCities[$row['Code']] = JString::strtolower($row['Name']);
		}
		
		$data = $this->apiSend('CourierListCities');
		foreach ($data as $row) {
			$this->courierListCities[] = JString::strtolower($row['City']);
		}
		
		$load = 1;
	}
	
    function onBeforeDisplayCheckoutStep4View($view){
		$courier_id = $this->params->get('courier_id');
		$pvz_id = $this->params->get('pvz_id');
		foreach ($view->shipping_methods as $key=>$shipping_method) {
			if ($shipping_method->shipping_id != $courier_id && $shipping_method->shipping_id != $pvz_id) {
				continue;
			}
			$this->init();
			
			$cart = JSFactory::getModel('cart', 'jshop');
			$cart->load();
			$adv_user = JSFactory::getUser();
			$summ = $cart->getSum(0,1,0);
			$weight = $cart->getWeightProducts();
			if ($this->params->get('weight_type', 'kg') == 'kg') {
				$weight *= 1000;
			}
			$depth_id = $this->params->get('depth_id', 0);
			$width_id = $this->params->get('width_id', 0);
			$height_id = $this->params->get('height_id', 0);
			$depth = $width = $height = 0;
			if ($depth_id && $width_id && $height_id) {
				$volumeTotal = array();
				foreach ($cart->products as $key=>$product) {
					$tProduct = JTable::getInstance('Product', 'jshop');
					$tProduct->load($product['product_id']);
					$extraFields = $tProduct->getExtraFields(0);
					$volume = array(
						'depth' => 1,
						'width' => 1,
						'height' => 1
					);
					foreach ($extraFields as $extra_field) {
						switch ($extra_field['id']) {
						case $depth_id:
							$volume['depth'] = (float)str_replace(',', '.', $extra_field['value']);
							break;
						case $width_id:
							$volume['width'] = (float)str_replace(',', '.', $extra_field['value']);
							break;
						case $height_id:
							$volume['height'] = (float)str_replace(',', '.', $extra_field['value']);
							break;
						}
					}
					rsort($volume);
					if ($product['quantity'] != 1) {
						$volume[2] = $volume[2] * $product['quantity'];
					}
					$volumeTotal[] = $volume;
				}
				if (count($volumeTotal)) {
					$sizeA = $sizeB = $sizeC = array();
					foreach ($volumeTotal as $volume) {
						$sizeA[] = $volume[0];
						$sizeB[] = $volume[1];
						$sizeC[] = $volume[2];
					}
					$size_type = $this->params->get('size_type', 'sm');
					if ($size_type == 'sm') {
						$koef = 1;
					} else if ($size_type == 'mm') {
						$koef = 0.1;
					} else if ($size_type == 'm') {
						$koef = 100;
					}
					$depth = max(5, max($sizeA) * $koef); 
					$width = max(5, max($sizeB) * $koef);
					$height = max(5, array_sum($sizeC) * $koef);
				}
			}
			$params = array(
				'please_select_pvz' => JText::_('PLG_JSHOPPING_BOXBERRY_PLEASE_SELECT_PVZ'),
				'select_pvz' => JText::_('PLG_JSHOPPING_BOXBERRY_SELECT_PVZ'),
				'select_another_pvz' => JText::_('PLG_JSHOPPING_BOXBERRY_SELECT_ANOTHER_PVZ'),
				'summ' => $summ,
				'paysumm' => in_array($cart->getPaymentId(), $this->params->get('payment_id', array())) ? $summ : '',
				'weight' => max(100, $weight),
				'city' => $adv_user->delivery_adress ? $adv_user->d_city : $adv_user->city,
				'height' => $height,
				'width' => $width,
				'depth' => $depth,
			);
			if ($shipping_method->shipping_id == $pvz_id) {
				if (!in_array(JString::strtolower($params['city']), $this->listCities)) {
					unset($view->shipping_methods[$key]);
					continue;
				}
				$shipping_method->name .= ' <span id="boxberry_pvz_summ_label" style="display:none;font-weight:400"></span>';
				$shipping_method->description .= <<<DESCRIPTION
<script type="text/javascript" src="//points.boxberry.de/js/boxberry.js"> </script/>
<input type="hidden" name="params[{$pvz_id}][selfpickup]" id="boxberry_selfpick" />
<input type="hidden" name="params[{$pvz_id}][id]" id="boxberry_id" />
<input type="hidden" name="params[{$pvz_id}][summ]" id="boxberry_summ" />
<div id="boxberry_delivery"><span style="color:red">{$params['please_select_pvz']}</span></div>
<a id="boxberry_select" href="#" onclick="boxberry.open(boxberrySelect,'{$this->params->get('api_key')}','{$params['city']}','{$this->params->get('city_id')}','{$params['summ']}','{$params['weight']}','{$params['paysumm']}','{$params['height']}','{$params['width']}','{$params['depth']}'); return false;">{$params['select_pvz']}</a>
<script type="text/javascript">
function boxberrySelect(result) {
	jQuery('#boxberry_selfpick').val(result.address);
	jQuery('#boxberry_id').val(result.id);
	jQuery('#boxberry_summ').val(result.price);
	jQuery('#boxberry_delivery').html(result.address);
	jQuery('#boxberry_select').html('{$params['select_another_pvz']}');
	jQuery('#boxberry_pvz_summ_label').html('('+result.price+'.00 Руб.)').show();
	jQuery('#shipping_method_{$shipping_method->sh_pr_method_id}').click();
}
jQuery('#boxberry_id').closest('form').submit(function(e){
	if (jQuery('#shipping_method_{$shipping_method->sh_pr_method_id}').is(':checked') && jQuery('#boxberry_id').val() == '') {
		alert('{$params['please_select_pvz']}');
		return false;
	} else {
		return true;
	}
});
</script>
DESCRIPTION;
				$this->calcule_pvz_price = 0;
			} else {
				if (!in_array(JString::strtolower($params['city']), $this->courierListCities) || !$adv_user->zip) {
					unset($view->shipping_methods[$key]);
					continue;
				}
				$data = $this->apiSend('ZipCheck', array('Zip'=>$adv_user->zip));
				if (!count($data) || $data[0]['ExpressDelivery'] != 1) {
					unset($view->shipping_methods[$key]);
					continue;
				}
				$data = $this->apiSend('DeliveryCosts', array('zip'=>$adv_user->zip, 'weight'=>$params['weight'], 'ordersum'=>$params['summ'], 'paysum'=>$params['paysumm']));
				if (count($data)) {
					$shipping_method->calculeprice = $data['price'];
					$margin_value = $this->params->get('margin_value', 0);
					if ($margin_value) {
						$shipping_method->calculeprice += $this->params->get('margin_type', 1) == 1 ? $shipping_method->calculeprice * $margin_value / 100 : $margin_value;
					}
					$shipping_method->calculeprice = ceil($shipping_method->calculeprice);
					$shipping_method->description .= '<input type="hidden" name="params['.$courier_id.'][summ]" value="'.$shipping_method->calculeprice.'" />';
					$this->calcule_courier_price = $shipping_method->calculeprice;
				}
			}
		}
    }
    
    function onAfterSaveCheckoutStep4(&$adv_user, &$sh_method, &$shipping_method_price, &$cart){
		if ($sh_method->shipping_id != $this->params->get('courier_id') && $sh_method->shipping_id != $this->params->get('pvz_id')) {
			return;
		}
		$params = $cart->getShippingParams();
		if ($this->calcule_courier_price !== null && $sh_method->shipping_id == $this->params->get('courier_id')) {
			$params['summ'] = $this->calcule_courier_price;
		} else if ($this->calcule_pvz_price !== null && $sh_method->shipping_id == $this->params->get('pvz_id')) {
			$params['summ'] = $this->calcule_pvz_price;
		}
		$cart->setShippingPrice($params['summ']);
    }

	function onBeforeCreateOrder($order, $cart, $model) {
		if ($order->shipping_method_id != $this->params->get('pvz_id')) {
			return;
		}
		$params = unserialize($order->shipping_params_data);
		if ($params['selfpickup']) {
			$order->shipping_params = JText::_('PLG_JSHOPPING_BOXBERRY_PVZ').": ".$params['selfpickup']."\n";
		}
	}

	function onAfterCreateOrderFull($order, $cart) {
		$this->parcel($order);
	}
     
    function onAfterChangeOrderStatus(&$order_id, &$status, &$sendmessage, &$prev_order_status){
		$order = JTable::getInstance('order', 'jshop');
		$order->load($order_id);
		$this->parcel($order);
    }
    
    function onAfterChangeOrderStatusAdmin(&$order_id, &$order_status, &$status_id, &$notify, &$comments, &$include, &$view_order, &$prev_order_status){
		$order = JTable::getInstance('order', 'jshop');
		$order->load($order_id);
		$this->parcel($order);
    }

	function onAfterUserCancelOrder(&$order_id, &$status, &$model){
		$order = JTable::getInstance('order', 'jshop');
		$order->load($order_id);
		$this->parcel($order);
	}

}