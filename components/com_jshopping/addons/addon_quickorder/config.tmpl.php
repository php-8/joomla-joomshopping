<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author joom-shopping.com
* @website https://joom-shopping.com/
* @email info@joom-shopping.com
* @copyright Copyright © All rights reserved.
* @license GNU GPL v3
**/

defined('_JEXEC') or die;

JFactory::getLanguage()->load('plg_jshoppingproducts_quickorder', JPATH_SITE.'/plugins/jshoppingproducts/quickorder', null, false, 'en-GB');

$form = JForm::getInstance('quickorder', __DIR__ . '/config.xml');
$params = array();
foreach ($this->params as $key=>$value) {
	$params['params['.$key.']'] = $value;
}
$form->bind($params);

?>
<table border="0" cellpadding="0">
	<tr>
		<td>
			<?php echo $form->getLabel('params[enable]') ?>
		</td>
		<td style="padding: 10px">
			<?php echo $form->getInput('params[enable]') ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo $form->getLabel('params[show_f_name]') ?>
		</td>
		<td style="padding: 10px">
			<?php echo $form->getInput('params[show_f_name]') ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo $form->getLabel('params[show_l_name]') ?>
		</td>
		<td style="padding: 10px">
			<?php echo $form->getInput('params[show_l_name]') ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo $form->getLabel('params[show_email]') ?>
		</td>
		<td style="padding: 10px">
			<?php echo $form->getInput('params[show_email]') ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo $form->getLabel('params[show_comment]') ?>
		</td>
		<td style="padding: 10px">
			<?php echo $form->getInput('params[show_comment]') ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo $form->getLabel('params[show_phone]') ?>
		</td>
		<td style="padding: 10px">
			<?php echo $form->getInput('params[show_phone]') ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo $form->getLabel('params[which_phone]') ?>
		</td>
		<td style="padding: 10px">
			<?php echo $form->getInput('params[which_phone]') ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo $form->getLabel('params[attr_require]') ?>
		</td>
		<td style="padding: 10px">
			<?php echo $form->getInput('params[attr_require]') ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo $form->getLabel('params[check_summ]') ?>
		</td>
		<td style="padding: 10px">
			<?php echo $form->getInput('params[check_summ]') ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo $form->getLabel('params[insert_var]') ?>
		</td>
		<td style="padding: 10px">
			<?php echo $form->getInput('params[insert_var]') ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo $form->getLabel('params[show_in_list]') ?>
		</td>
		<td style="padding: 10px">
			<?php echo $form->getInput('params[show_in_list]') ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo $form->getLabel('params[insert_var_list]') ?>
		</td>
		<td style="padding: 10px">
			<?php echo $form->getInput('params[insert_var_list]') ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo $form->getLabel('params[redirect_finish]') ?>
		</td>
		<td style="padding: 10px">
			<?php echo $form->getInput('params[redirect_finish]') ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo $form->getLabel('params[load_assets]') ?>
		</td>
		<td style="padding: 10px">
			<?php echo $form->getInput('params[load_assets]') ?>
		</td>
	</tr>
</table>