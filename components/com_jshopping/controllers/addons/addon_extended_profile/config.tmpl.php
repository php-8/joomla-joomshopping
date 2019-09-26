<?php

defined('_JEXEC') or die;

$templateList = array();
$filelist = scandir(JPATH_SITE.'/components/com_jshopping/templates/addons/extended_profile');
foreach ($filelist as $file) {
	if (preg_match("/(.*)\.php/", $file, $matches)){
		$match = $matches[1];
		$templateList[$match] = $match;
	}
}

JFactory::getLanguage()->load('plg_jshopping_extended_profile', JPATH_SITE . '/plugins/jshopping/extended_profile', null, false, 'en-GB');
$accessgroups = getAccessGroups();
?>
<table border="0" cellpadding="0">
	<tr>
		<td valign="top" style="font-weight: 700; padding: 5px">
			<?php echo JText::_('EXAC_ENABLE') ?>
		</td>
		<td valign="top" style="padding: 5px">
			<?php echo JHTML::_('select.booleanlist', 'params[enable]', 'style="margin-left:5px;float:none" class = "radiobox"', in_array($params['enable'], array('0','1'), true) ? $params['enable'] : 0, JText::_('JYES'), JText::_('JNO')) ?>
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-weight: 700; padding: 5px">
			<?php echo JText::_('EXAC_LAYOUT') ?>
		</td>
		<td valign="top" style="padding: 5px">
			<?php echo JHTML::_('select.genericlist', $templateList, 'params[template]','class = "inputbox" size = "1"','key','value', $params['template']) ?>
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-weight: 700; padding: 5px">
			<?php echo JText::_('EXAC_DISABLE_TAB_INFO') ?>
		</td>
		<td valign="top" style="padding: 5px">
			<?php echo JHTML::_('select.booleanlist', 'params[disable_tab_info]', 'style="margin-left:5px;float:none" class = "radiobox"', in_array($params['disable_tab_info'], array('0','1'), true) ? $params['disable_tab_info'] : 0, JText::_('JYES'), JText::_('JNO')) ?>
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-weight: 700; padding: 5px">
			<?php echo JText::_('EXAC_DISABLE_TAB_ORDERS') ?>
		</td>
		<td valign="top" style="padding: 5px">
			<?php echo JHTML::_('select.booleanlist', 'params[disable_tab_orders]', 'style="margin-left:5px;float:none" class = "radiobox"', in_array($params['disable_tab_orders'], array('0','1'), true) ? $params['disable_tab_orders'] : 0, JText::_('JYES'), JText::_('JNO')) ?>
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-weight: 700; padding: 5px">
			<?php echo JText::_('EXAC_DISABLE_TAB_WISHLIST') ?>
		</td>
		<td valign="top" style="padding: 5px">
			<?php echo JHTML::_('select.booleanlist', 'params[disable_tab_wishlist]', 'style="margin-left:5px;float:none" class = "radiobox"', in_array($params['disable_tab_wishlist'], array('0','1'), true) ? $params['disable_tab_wishlist'] : 0, JText::_('JYES'), JText::_('JNO')) ?>
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-weight: 700; padding: 5px">
			<?php echo JText::_('EXAC_DISABLE_TAB_COUPONS') ?>
		</td>
		<td valign="top" style="padding: 5px">
			<?php echo JHTML::_('select.booleanlist', 'params[disable_tab_coupons]', 'style="margin-left:5px;float:none" class = "radiobox"', in_array($params['disable_tab_coupons'], array('0','1'), true) ? $params['disable_tab_coupons'] : 0, JText::_('JYES'), JText::_('JNO')) ?>
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-weight: 700; padding: 5px">
			<?php echo JText::_('EXAC_DISABLE_TAB_DISCOUNT') ?>
		</td>
		<td valign="top" style="padding: 5px">
			<?php echo JHTML::_('select.booleanlist', 'params[disable_tab_discount]', 'style="margin-left:5px;float:none" class = "radiobox"', in_array($params['disable_tab_discount'], array('0','1'), true) ? $params['disable_tab_discount'] : 0, JText::_('JYES'), JText::_('JNO')) ?>
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-weight: 700; padding: 5px">
			<?php echo JText::_('EXAC_ORDERS_FROM') ?> <?php echo JHTML::tooltip(JText::_('EXAC_ORDERS_FROM_DESC')) ?>
		</td>
		<td valign="top" style="padding: 5px">
			<input type="text" name="params[show_orders_from]" class = "inputbox" value = "<?php echo (int)$params['show_orders_from'] ?>" />
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-weight: 700; padding: 5px">
			<?php echo JText::_('EXAC_ADMIN_ACCESS') ?> <?php echo JHTML::tooltip(JText::_('EXAC_ADMIN_ACCESS_DESC')) ?>
		</td>
		<td valign="top" style="padding: 5px">
			<?php echo JHTML::_('select.genericlist', array_merge(array(JText::_('JNO')), $accessgroups), 'params[admin_access]','class = "inputbox" size = "1"','id','title', $params['admin_access']) ?>
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-weight: 700; padding: 5px">
			<?php echo JText::_('EXAC_NOT_DELETE_PRODUCTS_FROM_WISHLIST') ?> <?php echo JHTML::tooltip(JText::_('EXAC_NOT_DELETE_PRODUCTS_FROM_WISHLIST_DESC')) ?>
		</td>
		<td valign="top" style="padding: 5px">
			<?php echo JHTML::_('select.booleanlist', 'params[not_delete_wishlist]', 'style="margin-left:5px;float:none" class = "radiobox"', in_array($params['not_delete_wishlist'], array('0','1'), true) ? $params['not_delete_wishlist'] : 0, JText::_('JYES'), JText::_('JNO')) ?>
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-weight: 700; padding: 5px">
			<?php echo JText::_('EXAC_SHOW_ACYMAILING_LINK') ?> <?php echo JHTML::tooltip(JText::_('EXAC_SHOW_ACYMAILING_LINK_DESC')) ?>
		</td>
		<td valign="top" style="padding: 5px">
			<?php echo JHTML::_('select.booleanlist', 'params[show_acymailing_link]', 'style="margin-left:5px;float:none" class = "radiobox"', in_array($params['show_acymailing_link'], array('0','1'), true) ? $params['show_acymailing_link'] : 0, JText::_('JYES'), JText::_('JNO')) ?>
		</td>
	</tr>
	<tr>
		<td valign="top" style="font-weight: 700; padding: 5px">
			<?php echo JText::_('EXAC_SHOW_SLOGIN_LINK') ?> <?php echo JHTML::tooltip(JText::_('EXAC_SHOW_SLOGIN_LINK_DESC')) ?>
		</td>
		<td valign="top" style="padding: 5px">
			<?php echo JHTML::_('select.booleanlist', 'params[show_slogin_link]', 'style="margin-left:5px;float:none" class = "radiobox"', in_array($params['show_slogin_link'], array('0','1'), true) ? $params['show_slogin_link'] : 0, JText::_('JYES'), JText::_('JNO')) ?>
		</td>
	</tr>
</table>