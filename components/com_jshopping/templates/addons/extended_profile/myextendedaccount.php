<?php

defined('_JEXEC') or die;

$config_fields = $this->config_fields;
include(dirname(__FILE__)."/myextendedaccount.js.php");
include(dirname(__FILE__)."/myextendedaccount.css.php");
$group = JTable::getInstance('userGroup', 'jshop');
$this->rows = $group->getList();
if (!is_array($this->extendedTabs)) $this->extendedTabs = array();
if (!is_array($this->coupons)) $this->coupons = array();
if (!is_array($this->products)) $this->products = array();
if (!is_array($this->orders)) $this->orders = array();
if (!$this->other_discount) $this->other_discount = '<div><span class="acc_nvg_name">'._EXAC_GENERAL_NOTSETUPED.'</span></div>';
$date_now = strtotime(date('Y-m-d'));
?>
<div class="tabs_section">
	<ul class="eac_tabs">
		<li id="exac_info_tab" class="current"><?php echo _EXAC_GENERAL_INFO ?></li>
		<li id="exac_orders_tab"><?php echo _EXAC_GENERAL_ORDERS ?></li>
		<?php if ($this->config->enable_wishlist) { ?>
		<li id="exac_wishlist_tab"><?php echo _EXAC_GENERAL_WISHLIST ?></li>
		<?php } ?>
		<li id="exac_coupons_tab"><?php echo _EXAC_GENERAL_COUPONS ?></li>
		<li id="exac_discount_tab"><?php echo _EXAC_GENERAL_DISCOUNT ?></li>
		<?php foreach ($this->extendedTabs as $tab) { ?>
		<li id="<?php echo $tab->id; ?>_tab">
		<?php echo $tab->name; ?>
		</li>
		<?php } ?>
	</ul>
	<div id="exac_info_data" class="eac_box visible">
		<div class="jshop_nvg">
			<?php if ($config_fields['f_name']['display'] || $config_fields['l_name']['display']){?>
				<div class="nvg_name"><?php print $this->user->f_name." ".$this->user->l_name;?></div>
			<?php }?>
			<table class = "jshop" style = "margin-top:20px; width:98%">
				<tr>
					<td style = "margin-top:20px; width:50%">
						<fieldset>
							<legend><?php echo _EXAC_GENERAL_LEGCONTACT?></legend>
							<?php if ($config_fields['country']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_COUNTRY?>: </span> <?php print $this->user->country?></div>
							<?php }?>
							<?php if ($config_fields['state']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_STATE?>: </span> <?php print $this->user->state?></div>
							<?php }?>
							<?php if ($config_fields['city']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_CITY?>: </span> <?php print $this->user->city?></div>
							<?php }?>
							<?php if ($config_fields['phone']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_TELEFON?>: </span> <?php print $this->user->phone?></div>
							<?php }?>
							<?php if ($config_fields['mobil_phone']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_MOBIL_PHONE?>: </span> <?php print $this->user->mobil_phone?></div>
							<?php }?>
							<?php if ($config_fields['fax']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_FAX?>: </span> <?php print $this->user->fax?></div>
							<?php }?>
							<?php if ($config_fields['ext_field_1']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_EXT_FIELD_1?>: </span> <?php print $this->user->ext_field_1?></div>
							<?php }?>
							<?php if ($config_fields['email']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_EMAIL?>: </span> <?php print $this->user->email?></div>
							<?php }?>
						</fieldset>
					</td>
					<td>
						<fieldset>
							<legend><?php echo _EXAC_GENERAL_LEGDOSTAV?></legend>
							<?php if ($config_fields['d_country']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_COUNTRY?>: </span> <?php print $this->user->delivery_adress ? $this->user->d_country : $this->user->country?></div>
							<?php }?>
							<?php if ($config_fields['d_state']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_STATE?>: </span> <?php print $this->user->delivery_adress ? $this->user->d_state : $this->user->state?></div>
							<?php }?>
							<?php if ($config_fields['d_city']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_CITY?>: </span> <?php print $this->user->delivery_adress ? $this->user->d_city : $this->user->city?></div>
							<?php }?>
							
							<?php if ($config_fields['d_zip']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_ZIP?>: </span> <?php print $this->user->delivery_adress ? $this->user->d_zip : $this->user->zip?></div>
							<?php }?>
							<?php if ($config_fields['d_street']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_STREET_NR?>: </span> <?php print $this->user->delivery_adress ? $this->user->d_street : $this->user->street?></div>
							<?php }?>
							<?php if ($config_fields['d_home']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_HOME?>: </span> <?php print $this->user->delivery_adress ? $this->user->d_home : $this->user->home?></div>
							<?php }?>
							<?php if ($config_fields['d_apartment']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_APARTMENT?>: </span> <?php print $this->user->delivery_adress ? $this->user->d_apartment : $this->user->apartment?></div>
							<?php }?>
							<?php if ($config_fields['d_phone']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_TELEFON?>: </span><?php print $this->user->delivery_adress ? $this->user->d_phone : $this->user->phone ?></div>
							<?php }?>
							<?php if ($config_fields['d_mobil_phone']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_MOBIL_PHONE?>: </span> <?php print $this->user->delivery_adress ? $this->user->d_mobil_phone : $this->user->mobil_phone?></div>
							<?php }?>
							<?php if ($config_fields['d_fax']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_FAX?>: </span> <?php print $this->user->delivery_adress ? $this->user->d_fax : $this->user->fax?></div>
							<?php }?>
							<?php if ($config_fields['d_ext_field_1']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_EXT_FIELD_1?>: </span> <?php print $this->user->delivery_adress ? $this->user->d_ext_field_1 : $this->user->ext_field_1?></div>
							<?php }?>
							<?php if ($config_fields['d_email']['display']){?>
							<div><span class="acc_nvg_name"><?php print _JSHOP_EMAIL?>: </span> <?php print $this->user->delivery_adress ? $this->user->d_email : $this->user->email?></div>
							<?php }?>
						</fieldset>
					</td>
				</tr>
			</table>
			<div class="jshop_nvg_edit"> 
				<a href =  "<?php print $this->href_edit_data?>"><?php print _JSHOP_EDIT_DATA ?></a>
			</div>
			<div style="clear:both;"></div>					
		</div>
	</div>
	<div id="exac_orders_data" class="eac_box">
		<div class="jshop_nvg">
			<div class="bon_orders">
				<fieldset>
					<legend><?php print _EXAC_GENERAL_ORDERSYS ?></legend>
					<div><span class="acc_nvg_name"><?php print _EXAC_GENERAL_QNTORDERS ?>: </span><span class="aec_value"> <?php print $this->order_total_qnt ?> </span></div>
					<div><span class="acc_nvg_name"><?php print _EXAC_GENERAL_SUMORDERS ?>: </span><span class="aec_value"> <?php print formatprice($this->order_total_ammount, $this->config->currency_code) ?> </span> </div>
				</fieldset>
			</div>
			<div class="bon_orders">
				<fieldset>
					<legend><?php print _EXAC_GENERAL_ORDERSTAT ?></legend>
					<?php foreach ($this->order_status as $status_name=>$status) { ?>
					<div><span class="acc_nvg_name"><?php print $status_name?>: </span> <span class="aec_value"><?php print $status->qty?></span>, <span class="acc_nvg_name"><?php print _EXAC_GENERAL_SUMORDERS ?>: </span><span class="aec_value"> <?php print formatprice($status->sum, $this->config->currency_code) ?> </span></div>
					<?php } ?>
				</fieldset>
			</div>
			<table class="eac_orders">
				<tr>
					<th class="eac_title" style="width:12%"><?php print _JSHOP_ORDER_NUMBER1 ?> / <?php print _JSHOP_ORDER_DATE1 ?></th> 
					<th class="eac_title" style="width:48%"><?php print _JSHOP_PRODUCTS ?></th> 
					<th class="eac_title" style="width:15%"><?php print _JSHOP_PRICE_TOTAL ?></th>
					<?php if (!$this->config->without_shipping || !$this->config->without_payment) { ?>
					<th class="eac_title" style="width:13%">
						<?php
						if (!$this->config->without_shipping) { 
							print _JSHOP_SHIPPING_INFORMATION1;
							if (!$this->config->without_payment) {
								print ' / '._JSHOP_PAYMENT_INFORMATION1;
							}
						} else if (!$this->config->without_payment) {
							print _JSHOP_PAYMENT_INFORMATION1;
						}
						?>
					</th> 
					<?php } ?>
					<th class="eac_title" style="width:12%"><?php print _JSHOP_ORDER_STATUS ?></th> 
				</tr>
				<?php foreach ($this->orders as $order) { ?>
				<tr>
					<td style="padding-right: 3px;"><a class="eac_norder" href = "<?php print $order->order_href ?>" title="<?php print _EAC_NORDERHINT?>"><?php print $order->order_number ?></a>
					<div class="order_date"><?php print formatdate($order->order_date, 0) ?></div></td> 
					<td style="border-left: 1px dotted #999;">
					<?php foreach ($order->order_items as $order_item) {
						print '<div class="eac_order_product_name">';
						print $order_item->product_name;
						print '</div>';
					}?>
					</td> 
					<td style="text-align:right; font-weight:bold;"><?php print formatprice($order->order_total, $order->currency_code); ?></td> 
					<?php if (!$this->config->without_shipping || !$this->config->without_payment) { ?>
					<td>
						<?php
						if (!$this->config->without_shipping && $order->shipping_method) { 
							print $order->shipping_method.' / ';
							if (!$this->config->without_payment && $order->payment_method) {
								print '<br />'.$order->payment_method;
							}
						} else if (!$this->config->without_payment && $order->payment_method) {
							print $order->payment_method;
						}
						?>
					</td> 
					<?php }?>
					<td>
					<span class="eac_preview">
					<?php foreach ($order->order_history as $order_history) {
						print formatdate($order_history->status_date_added, 1).' - '.$order_history->name.'<br />';
					}?>
					</span>
					<?php print $order->status_name ?>
					</td> 
				</tr>
				<?php } ?>
			</table>
		</div>
	</div>
	<?php if ($this->config->enable_wishlist) { ?>
	<div id="exac_wishlist_data" class="eac_box">
		<div class="jshop_nvg">
			<table class="eac_bloknot">
				<tr>
					<th class="eac_title" style="width:15%;"><?php print _JSHOP_IMAGE?></th> 
					<th class="eac_title" style="width:35%;"><?php print _JSHOP_ITEM?></th> 
					<th class="eac_title" style="width:15%;"><?php print _JSHOP_SINGLEPRICE1 ?></th> 
					<th class="eac_title" style="width:5%;"><?php print _JSHOP_NUMBER1 ?></th> 
					<th class="eac_title" style="width:15%;"><?php print _JSHOP_PRICE_TOTAL ?></th> 
					<th class="eac_title"style="width:15%"></th> 
				</tr>
				<?php foreach ($this->products as $key_id=>$prod) { 
					$prod['href'] = SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$prod['category_id'].'&product_id='.$prod['product_id'], 1);
					$prod['href_delete'] = SEFLink('index.php?option=com_jshopping&controller=wishlist&task=delete&number_id='.$key_id,1);
					$prod['remove_to_cart'] = SEFLink('index.php?option=com_jshopping&controller=wishlist&task=remove_to_cart&number_id='.$key_id,1);
				?>
				<tr>
					<td class = "jshop_img_description_center">
						<a href = "<?php print $prod['href']; ?>">
							<img src = "<?php print $this->config->image_product_live_path ?>/<?php if ($prod['thumb_image']) print $prod['thumb_image']; else print 'noimage.gif'; ?>" alt = "<?php print htmlspecialchars($prod['product_name']);?>" class = "jshop_img" />
						</a>
					</td>
					<td style="text-align:left"> 
						<a href="<?php print $prod['href']?>"><?php print $prod['product_name']?></a>
						<?php if ($this->config->show_product_code_in_cart) print "<span class='jshop_code_prod'>(".$prod['ean'].")</span>";?>
						
						<?php print sprintAtributeInCart($prod['attributes_value']);?>
						<?php print sprintFreeAtributeInCart($prod['free_attributes_value']);?>
						<?php print sprintFreeExtraFiledsInCart($prod['extra_fields']);?>
						<?php print $prod['_ext_attribute_html']?>        
					</td>    
					<td style="text-align:right;">
						<?php print formatprice($prod['price'])?>
						<?php print $prod['_ext_price_html']?>
						<?php if ($this->config->show_tax_product_in_cart && $prod['tax']>0){?>
						<span class="taxinfo"><?php print productTaxInfo($prod['tax']);?></span>
						<?php }?>
					</td>
					<td style="text-align:center;">
					  <?php print $prod['quantity']?><?php print $prod['_qty_unit'];?>
					</td>
					<td style="text-align:right;">
						<?php print formatprice($prod['price']*$prod['quantity']);?>
						<?php print $prod['_ext_price_total_html']?>
						<?php if ($this->config->show_tax_product_in_cart && $prod['tax']>0){?>
						<span class="taxinfo"><?php print productTaxInfo($prod['tax']);?></span>
						<?php }?>
					</td>
					<td>
						<a href="<?php print $prod['remove_to_cart']?>">
							<img title="<?php print _JSHOP_REMOVE_TO_CART?>" alt="<?php print _JSHOP_REMOVE_TO_CART?>" src="/components/com_jshopping/images/extended_profile/tocart.png " />
						</a>
						&nbsp;&nbsp;
						<a onclick="return confirm('<?php print _JSHOP_REMOVE?>')" href="<?php print $prod['href_delete']?>">
							<img title="<?php print _JSHOP_DELETE?>" alt="<?php print _JSHOP_DELETE?>" src="/components/com_jshopping/images/extended_profile/delnote.png" />
						</a>
					</td>
				</tr>
				<?php } ?>
			</table>
		</div>
	</div>
	<?php } ?>
	<div id="exac_coupons_data" class="eac_box">
		<div class="jshop_nvg">
			<table class="eac_coupons">
				<tr>
					<th class="eac_title"><?php print _EXAC_COUPONCODE?></th> 
					<th class="eac_title"><?php print _EXAC_COUPONNOMINAL?></th> 
					<th class="eac_title"><?php print _EXAC_COUPONTIME?></th> 
					<th class="eac_title"><?php print _EXAC_COUPONSTATUS?></th> 
				</tr>
				<?php
				foreach($this->coupons as $coupon){
					$coupontime = '';
					if ($coupon->coupon_start_date != '0000-00-00' || $coupon->coupon_expire_date != '0000-00-00') {
						if ($coupon->coupon_start_date != '0000-00-00') {
							$coupontime .=  ' <span class="coupon_date">'._EXAC_FROM.'</span> '.formatdate($coupon->coupon_start_date, 0);
						}
						if ($coupon->coupon_expire_date != '0000-00-00') {
							$coupontime .= ' <span class="coupon_date">'._EXAC_TO.'</span> '.formatdate($coupon->coupon_expire_date, 0);
						}
					} else {
						$coupontime .= _EXAC_COUPONUNLIM;
					}
					if ($coupon->used) {
						$coupon_status = _EXAC_GENERAL_USED;
						$class = 'eac_coupon_used';
					} else if ($coupon->coupon_expire_date != '0000-00-00' && strtotime($coupon->coupon_expire_date) < $date_now) {
						$coupon_status = _EXAC_GENERAL_TIMEOUT;
						$class = 'eac_coupon_downtime';
					} else {
						$coupon_status = _EXAC_GENERAL_ACTIVED;
						$class = 'eac_coupon_acticve';
					}
					if ($coupon->coupon_start_date != '0000-00-00' && strtotime($coupon->coupon_start_date) > $date_now) {
						$coupon_status = _EXAC_GENERAL_ACTIVFUTURE;
						$class = 'eac_coupon_future';
					}
				?>
				<tr class="<?php print $class?>">
					<td style="text-align:left!important;"><?php print $coupon->coupon_code?></td> 
					<td>
					<?php
					if ($coupon->coupon_type) {
						print formatprice($coupon->coupon_value * $this->config->currency_value, $this->config->currency_code);
					} else {
						print $coupon->coupon_value.' %';
					}
					?>
					</td> 
					<td style="text-align:left!important;"><?php print $coupontime ?></td>
					<td><?php print $coupon_status ?></td> 
				</tr>
				<?php }?>
			</table>
		</div>
	</div>
	<div id="exac_discount_data" class="eac_box" id="eac_bonusndiv">
		<div class="jshop_nvg">
			<div class="bon_dissys">
				<fieldset>
					<legend><?php print _EXAC_GENERAL_DICOUNTSYS ?></legend>
					<div>
						<span class="acc_nvg_name"><?php print _JSHOP_DISCOUNT?>: </span><span class="aec_value"><?php print $this->user->discountpercent?>%</span>
					</div>
					<div>
						<span class="acc_nvg_name"><?php print _JSHOP_GROUP?>: </span><span class="aec_value"><?php print $this->user->groupname?></span>
					</div>
					<?php print $this->general_discount ?>
				</fieldset>
			</div>
			<div class="bon_dissys">
				<fieldset>
					<legend><?php print _EXAC_GENERAL_OTHERDISCOUNT ?></legend>
					<?php print $this->other_discount ?>
				</fieldset>
			</div>
			<table class="eac_groups_list" style = "margin-top:20px; width:98%">
				<tr>
					<th class="eac_title"><?php print _JSHOP_TITLE?></th> 
					<th class="eac_title"><?php print _JSHOP_DISCOUNT?></th> 
					<th class="eac_title"><?php print _JSHOP_DESCRIPTION?></th> 
				</tr>
				<?php foreach($this->rows as $row){?>
				<tr>
					<td class="eac_title"><?php print $row->usergroup_name?></td> 
					<td class="eac_discount"><?php print floatval($row->usergroup_discount)?>%</td>
					<td class="eac_desription"><?php print $row->usergroup_description?></td>
				</tr>
				<?php }?>
			</table>
		</div>
		<div style="clear;both;"></div>
	</div>
	<?php foreach ($this->extendedTabs as $tab) { ?>
	<div id="<?php echo $tab->id; ?>_data" class="eac_box">
		<div class="jshop_nvg">
		<?php echo $tab->content; ?>
		</div>
	</div>
	<?php } ?>
</div>