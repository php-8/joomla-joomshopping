<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Garry
* @website https://joom-shopping.com
* @email info@joom-shopping.com
* @copyright Copyright © All rights reserved.
* @license GNU GPL v3
* @license agreement https://joom-shopping.com/polzovatelskoe-soglashenie
**/

defined('_JEXEC') or die;

$form = JForm::getInstance('onestepcheckouttemplate', __DIR__ . '/config.xml');
$params = array();
foreach ($this->params as $key=>$value) {
	$params['params['.$key.']'] = $value;
}
$form->bind($params);

?>
<table border="0" cellpadding="0">
	<tr>
		<td valign="top" style="padding: 5px 10px">
			<div class="onestepcheckout-title"><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_TEMPLATE_VIEW') ?></div>
			<table>
				<tr>
					<td>
						<?php echo $form->getLabel('params[message_adress]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[message_adress]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[message_payment]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[message_payment]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[message_shipping]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[message_shipping]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[overlay]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[overlay]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[login_form]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[login_form]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[step_number]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[step_number]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[step_name]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[step_name]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[product_image]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[product_image]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[placeholder]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[placeholder]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[payment_params]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[payment_params]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[shipping_params]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[shipping_params]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[columns_number]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[columns_number]') ?>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top" style="padding: 5px 10px">
			<div class="onestepcheckout-title"><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_PACKAGE') ?></div>
			<table>
				<tr>
					<td>
						<?php echo $form->getLabel('params[package_image]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[package_image]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[package_text]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[package_text]') ?>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top" style="padding: 5px 10px">
			<div class="onestepcheckout-title"><?php echo JText::_('JSHOP_ONESTEPCHECKOUT_FINISH_EXTENDED') ?></div>
			<table>
				<tr>
					<td>
						<?php echo $form->getLabel('params[order_number]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[order_number]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[order_subtotal]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[order_subtotal]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[order_discount]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[order_discount]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[order_payment]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[order_payment]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[order_shipping]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[order_shipping]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[order_package]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[order_package]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[order_total]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[order_total]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[order_products]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[order_products]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[order_shipping_desc]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[order_shipping_desc]') ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo $form->getLabel('params[order_payment_desc]') ?>
					</td>
					<td style="padding: 5px 10px">
						<?php echo $form->getInput('params[order_payment_desc]') ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
