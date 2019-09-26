<?php
/**
 * @name Plus and Minus Count Product 2.0
 * @package Joomla
 * @package JoomShopping
 * @author Meling Vadim (Linfuby)
 * @website http://linfuby.com/
 * @email support@linfuby.com
 * @copyright Copyright by Linfuby. All rights reserved.
 * @license The MIT License (MIT); See \components\com_jshopping\addons\jshopping_plus_minus_count_product\license.txt
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class plgJshoppingProductsPlus_Minus_Count_Product extends JPlugin{

	function onBeforeDisplayProductListView(&$view){
		$jshopConfig = JSFactory::getConfig();
		$script = 'function format_price(price){
						var currency_code = \''.$jshopConfig->currency_code.'\'
						var format_currency = \''.$jshopConfig->format_currency[$jshopConfig->currency_format].'\'
						var decimal_count = \''.$jshopConfig->decimal_count.'\'
						var decimal_symbol = \''.$jshopConfig->decimal_symbol.'\'
						if (typeof(decimal_count)===\'undefined\') decimal_count = 2;
						if (typeof(decimal_symbol)===\'undefined\') decimal_symbol = ".";
						price = price.toFixed(decimal_count).toString();
						price = price.replace(\'.\', decimal_symbol);
						res = format_currency.replace("Symb", currency_code);
						res = res.replace("00", price);
						return res;
					}
					function reloadPriceInList(product_id, qty){

						var data = {};
						data["change_attr"] = 0;
						data["qty"] = qty;
						if (prevAjaxHandler){
							prevAjaxHandler.abort();
						}
						prevAjaxHandler = jQuery.getJSON(
							"index.php?option=com_jshopping&controller=product&task=ajax_attrib_select_and_price&product_id=" + product_id + "&ajax=1",
							data,
							function(json){
								var price = parseFloat(json.price);
								jQuery(".product.productitem_"+product_id+" .jshop_price span").html(format_price(price * qty));
							}
						);
					}';
		JFactory::getDocument()->addScriptDeclaration($script);
		foreach($view->rows as $key => $product){
			if($view->rows[$key]->buy_link){
				$view->rows[$key]->_tmp_var_buttons .= '<div class="quantity_buttons">
				<input class = "product_minus" type = "button" value="-" onclick = "
				var qty_el = document.getElementById(\'quantity'.$product->product_id.'\');
				var qty = qty_el.value;
				if( !isNaN( qty ) && qty > 1) qty_el.value--;
				var url_el = document.getElementById(\'productlink'.$product->product_id.'\');
				url_el.href=\''.$view->rows[$key]->buy_link.'&quantity=\'+qty_el.value;reloadPriceInList('.$product->product_id.',qty_el.value);return false;" />
				<input type = "text" name = "quantity'.$product->product_id.'" id = "quantity'.$product->product_id.'"
				style = "min-width:20px; max-width:100px;" class = "inputbox" value = "1" onkeyup="
				var qty_el = document.getElementById(\'quantity'.$product->product_id.'\');
				var url_el = document.getElementById(\'productlink'.$product->product_id.'\');
				url_el.href=\''.$view->rows[$key]->buy_link.'&quantity=\'+qty_el.value;reloadPriceInList('.$product->product_id.',qty_el.value);return false;" />
				<input class = "product_plus" type = "button" value="+" onclick = "
				var qty_el = document.getElementById(\'quantity'.$product->product_id.'\');
				var qty = qty_el.value;
				if( !isNaN( qty )) qty_el.value++;
				var url_el = document.getElementById(\'productlink'.$product->product_id.'\');
				url_el.href=\''.$view->rows[$key]->buy_link.'&quantity=\'+qty_el.value;reloadPriceInList('.$product->product_id.',qty_el.value);return false;" />
				</div>';
				$view->rows[$key]->buy_link .= "\" Id = \"productlink".$product->product_id;
			}
		}
	}
	function onBeforeDisplayProductView(&$view){
		$jshopConfig = JSFactory::getConfig();
		$script = 'function format_price(price){
						var currency_code = \''.$jshopConfig->currency_code.'\'
						var format_currency = \''.$jshopConfig->format_currency[$jshopConfig->currency_format].'\'
						var decimal_count = \''.$jshopConfig->decimal_count.'\'
						var decimal_symbol = \''.$jshopConfig->decimal_symbol.'\'
						if (typeof(decimal_count)===\'undefined\') decimal_count = 2;
						if (typeof(decimal_symbol)===\'undefined\') decimal_symbol = ".";
						price = price.toFixed(decimal_count).toString();
						price = price.replace(\'.\', decimal_symbol);
						res = format_currency.replace("Symb", currency_code);
						res = res.replace("00", price);
						return res;
					}
					function reloadPriceInList(product_id, qty){

						var data = {};
						data["change_attr"] = 0;
						data["qty"] = qty;
						if (prevAjaxHandler){
							prevAjaxHandler.abort();
						}
						prevAjaxHandler = jQuery.getJSON(
							"index.php?option=com_jshopping&controller=product&task=ajax_attrib_select_and_price&product_id=" + product_id + "&ajax=1",
							data,
							function(json){
								var price = parseFloat(json.price);
								jQuery("#block_price").html(format_price(price * qty));
							}
						);
					}
					jQuery(document).ready(function(){
						jQuery(\'#quantity\').live(\'change\', function(){
							reloadPriceInList('.$view->product->product_id.', jQuery(this).val());
						});
					});';
		JFactory::getDocument()->addScriptDeclaration($script);
		$view->_tmp_qty_unit =
			'<div class="quantity_buttons">
			<input class = "product_minus" type = "button" value="-" onclick = "
			var qty_el = document.getElementById(\'quantity\');
			var qty = qty_el.value;
			if( !isNaN(qty) && qty > 1){
				qty_el.value--;
			}reloadPriceInList('.$view->product->product_id.',qty_el.value);return false;">
			<input class = "product_plus" type = "button" value="+" onclick = "
			var qty_el = document.getElementById(\'quantity\');
			var qty = qty_el.value;
			if( !isNaN( qty )){
				qty_el.value++;
			}reloadPriceInList('.$view->product->product_id.',qty_el.value);return false;">
			</div>';
	}
}