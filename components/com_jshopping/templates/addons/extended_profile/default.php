<?php

defined('_JEXEC') or die;
// print_r($this->pageNav);
?>
<script type="text/javascript">
var register_field_require = {};
<?php
foreach($this->config_fields as $key=>$val){
    if ($val['require']){
        echo "register_field_require['".$key."']=1;";
    }
}
?>
</script>
<div class="nvg-eac-tabs-section">
	<ul class="eac_tabs">
		<?php foreach ($this->extendedTabs as $key=>$tab) { ?>
		<li id="<?php echo $tab->id ?>_tab" class="litab<?php echo $this->active_tab == $key ? ' current' : ''?>" onclick="extendedProfile.changeTab(this)">
			<?php if ($tab->image) { ?>
			<span class="litab_icon"><img src="<?php echo $tab->image ?>" /></span>
			<?php } ?>
			<span class="tabli"><?php echo $tab->name ?></span>
		</li>
		<?php } ?>
	</ul>
	<?php foreach ($this->extendedTabs as $key=>$tab) { ?>
	<div id="<?php echo $tab->id ?>_data" class="eac_box<?php echo $this->active_tab == $key ? ' visible' : '' ?>">
		<div class="eac_jshop_nvg">
		<?php
		switch ($key) {
			case 'info' :
			// INFO
			echo $this->_tmpl_editaccount_html_1;
		?>
			<form action="<?php print $this->action ?>" method="post" id="eacAdressForm" name="eacAdressForm" onsubmit="return validateEditAccountForm('<?php print $this->live_path ?>', this.name)">
				<div class="nvg-grid">
					<div class="nvg-width-1-<?php if ($this->d_adress) { ?>2<?php } else { ?>1<?php } ?> nvg-box-solid-border nvg_boxshadow">
						<div class="nvg-block_header">
							<?php echo JText::_('EXAC_GENERAL_LEGCONTACT') ?> 
							<div class="eac_btnblock fadeIn">
								<span><img title="<?php echo JText::_('JSAVE') ?>" src="/components/com_jshopping/templates/addons/extended_profile/images/ico_save.png" onclick="extendedProfile.saveAdressFields()" /></span>
								<span><img title="<?php echo JText::_('JCANCEL') ?>" src="/components/com_jshopping/templates/addons/extended_profile/images/ico_cancel.png" onclick="extendedProfile.resetAdressFields()" /></span>
							</div>
						</div>
						<div id="adress_fields">
						<?php
						foreach ($this->config_fields as $fieldName=>$fieldData) {
							if (substr($fieldName, 0, 2) == 'd_') {
								continue;
							}
							if ($fieldData['display']) {
						?>
							<div class="nvg-grid nvg-border-bottom-dotted">
								<div class="acc_nvg_name nvg-width-30"><?php echo JText::_('EXAC_USER_FIELD_'.$fieldName) ?>: </div>
								<?php
								switch ($fieldName) {
									case 'country' :
										echo $this->select_countries;
										break;
									case 'title' :
										echo $this->select_titles;
										break;
									case 'client_type' :
										echo $this->select_client_types;
										break;
									case 'birthday' :
										echo JHTML::_('calendar', $this->user->birthday, 'birthday', 'birthday', $this->config->field_birthday_format);
										break;
									default:
								?>
								<input type="text" name="<?php echo $fieldName ?>" id="<?php echo $fieldName ?>" value="<?php echo $this->user->$fieldName ?>" />
								<?php
								}
								if ($fieldData['require']){
								?>
								<span class="eac_required bounceInRight"></span>
								<?php } ?>
							</div>
						<?php
							}
							switch ($fieldName) {
								case 'birthday' :
									echo $this->_tmpl_editaccount_html_2;
									break;
								case 'country' :
									echo $this->_tmpl_editaccount_html_3;
									break;
								case 'ext_field_3' :
									echo $this->_tmpl_editaccount_html_4;
									break;
							}
						}
						echo $this->_tmpl_editaccount_html_4_1;
						?>
					</div>
					</div>
					<?php if ($this->d_adress) { ?>
					<div class="nvg-width-1-2 nvg-box-solid-border nvg_boxshadow">
						<div class="nvg-block_header">
							<?php echo JText::_('EXAC_GENERAL_LEGDOSTAV') ?>
							<input type="radio" name="delivery_adress" id="delivery_adress_1" value="0" <?php if (!$this->user->delivery_adress) {?> checked="checked" <?php } ?> onchange="extendedProfile.toggleDeliveryAdress()" />
							<label for="delivery_adress_1">
								<div class="delivery_adress_1"></div>
							</label>
							<input type="radio" name="delivery_adress" id="delivery_adress_2" value="1" <?php if ($this->user->delivery_adress) {?> checked="checked" <?php } ?> onchange="extendedProfile.toggleDeliveryAdress()" />
							<label for="delivery_adress_2">
								<div class="delivery_adress_2"></div>
							</label>
						</div>
						<div id="d_adress_fields" style="<?php if (!$this->user->delivery_adress) { ?>display:none;<?php } ?>">
						<?php
						foreach ($this->config_fields as $fieldName=>$fieldData) {
							if (substr($fieldName, 0, 2) != 'd_') {
								continue;
							}
							if ($fieldData['display']) {
						?>
							<div class="nvg-grid nvg-border-bottom-dotted">
								<div class="acc_nvg_name nvg-width-30"><?php echo JText::_('EXAC_USER_FIELD_'.substr($fieldName, 2)) ?>: </div>
								<?php
								switch ($fieldName) {
									case 'd_country' :
										echo $this->select_d_countries;
										break;
									case 'd_title' :
										echo $this->select_d_titles;
										break;
									case 'd_client_type' :
										echo $this->select_d_client_types;
										break;
									case 'd_birthday' :
										echo JHTML::_('calendar', $this->user->d_birthday, 'd_birthday', 'd_birthday', $this->config->field_birthday_format);
										break;
									default:
								?>
								<input type="text" name="<?php echo $fieldName ?>" id="<?php echo $fieldName ?>" value="<?php echo $this->user->$fieldName ?>" />
								<?php } ?>
								<?php if ($fieldData['require']){ ?>
								<span class="eac_required bounceInRight"></span>
								<?php } ?>
							</div>
						<?php
							}
							switch ($fieldName) {
								case 'd_birthday' :
									echo $this->_tmpl_editaccount_html_5;
									break;
								case 'd_country' :
									echo $this->_tmpl_editaccount_html_6;
									break;
								case 'd_ext_field_3' :
									echo $this->_tmpl_editaccount_html_4;
									break;
							}
						}
						?>
						</div>
					</div>
					<?php } ?>
				</div>
			</form>
			<?php if ($this->acymailing_link) { ?>
			<div class="nvg_clearfix"></div>
			<br/>
			<div class="nvg-width-1-1 nvg-box-solid-border nvg_boxshadow">
				<div class="nvg-block_header">
					<a href="<?php echo $this->acymailing_link ?>" target="_blank"><?php echo JText::_('EXAC_GENERAL_SUBSCRIBECONTROL') ?></a>
				</div>
			</div> 
			<?php } ?>
			<?php if ($this->slogin_link) { ?>
			<div class="nvg_clearfix"></div>
			<br/>
			<div class="nvg-width-1-1 nvg-box-solid-border nvg_boxshadow">
				<div class="nvg-block_header">
					<a href="<?php echo $this->slogin_link ?>" target="_blank"><?php echo JText::_('EXAC_GENERAL_SOCIALCONTROL') ?></a>
				</div>
			</div>
			<?php } ?>
			
			<?php
				break;
			case 'orders' :
			// ORDERS
			?>
			<div class="nvg-grid">
				<div class="nvg-width-1-2 nvg-box-solid-border nvg_boxshadow">
					<div class="nvg-block_header"><?php echo JText::_('EXAC_GENERAL_ORDERSYS') ?></div>
					<div><span class="acc_nvg_name"><?php echo JText::_('EXAC_GENERAL_QNTORDERS') ?>: </span><span class="aec_value"> <?php echo $this->order_total_qnt ?> </span></div>
					<div><span class="acc_nvg_name"><?php echo JText::_('EXAC_GENERAL_SUMORDERS') ?>: </span><span class="aec_value"> <?php echo formatprice($this->order_total_ammount, $this->config->currency_code) ?> </span> </div>
				</div>
				<div class="nvg-width-1-2 nvg-box-solid-border nvg_boxshadow">
					<div class="nvg-block_header"><?php echo JText::_('EXAC_GENERAL_ORDERSTAT') ?></div>
					<?php foreach ($this->order_status as $status_id=>$status) { ?>
					<div><span class="acc_nvg_name"><?php echo $this->order_statuses[$status_id]->name ?>: </span> <span class="aec_value"><?php echo $status->qty?></span>, <span class="acc_nvg_name"><?php echo JText::_('EXAC_GENERAL_SUMORDERS') ?>: </span><span class="aec_value"> <?php echo formatprice($status->sum, $this->config->currency_code) ?> </span></div>
					<?php } ?>
				</div>
			</div>
			<?php /*
			<!--  таблица ------------------------------------------->
			*/ ?>
			<div class="eac_orders">
				<?php foreach ($this->orders as $order) { ?>
					 <div class="eac-listorder nvg-box-solid-border nvg_boxshadow">
						<div class="order-n nvg-inline-block">
							<div class="nvg-text-align-center">
							<a class="eac_norder" href = "<?php echo $order->order_href ?>" title="<?php echo JText::_('EAC_NORDERHINT')?>"><?php echo $order->order_number ?></a>
							<div class="order_date nvg-text-align-center"><?php echo formatdate($order->order_date, 0) ?></div>
							<div><?php echo $order->_tmp_after_order_date ?></div>
							</div>
						</div>
						
						<div class="order-body nvg-inline-block">
							<div class="order-top-service-info">
								<div class="order-status-now nvg-font-weight-bold nvg-text-truncate">
									<?php echo $order->status_name ?>
								</div>
								<?php if (!$this->config->without_shipping || !$this->config->without_payment) { ?>
								<?php
								if (!$this->config->without_shipping && $order->shipping_method) { 
									echo '<div class="listorder_delivery nvg-text-truncate "><img src="/components/com_jshopping/templates/addons/extended_profile/images/eac-delivery.png" /> '.$order->shipping_method.'</div>';
									if (!$this->config->without_payment && $order->payment_method) {
										echo '<div class="listorder_payment nvg-text-truncate "><img src="/components/com_jshopping/templates/addons/extended_profile/images/eac-payment.png" /> '.$order->payment_method.'</div>';
									}
								} else if (!$this->config->without_payment && $order->payment_method) {
									echo '<div class="listorder_payment">'.$order->payment_method.'</div>';
								}
								?>
								<?php }?>

							</div>
							<div class="order-content">
								<div class="nvgheadslide"  id="eac_accordion_<?php echo $order->order_number ?>">
									  <div onclick="jQuery('#listprodinorder_<?php echo $order->order_number ?>').slideToggle()">  <img src="/components/com_jshopping/templates/addons/extended_profile/images/orderlist.png" />  <?php echo _JSHOP_PRODUCTS?></div>
									  <div id="listprodinorder_<?php echo $order->order_number ?>" class="nvg_accordion" <?php if ($order->show_order_items) { ?>style="display:block"<?php } ?>>
										<?php foreach ($order->order_items as $order_item) { ?>
												<div class="eac_order_product_block nvg-grid">
													<div class="eac_order_product_img nvg-width-1-3">
														<a class="nvg_orderprod" href="<?php echo $order_item->product_link ?>"><img src="<?php echo $this->config->image_product_live_path.'/'.$order_item->thumb_image?>" /></a>
													</div>
													<div class="nvg-width-1-3"> 
														<div class="name"><?php echo $order_item->product_name?></div>
														<?php echo sprintAtributeInOrder($order_item->product_attributes).sprintFreeAtributeInOrder($order_item->product_freeattributes) ?>
													</div>
													<div class="nvg-width-1-3 nvg-text-align-right"> 
													 <?php echo formatprice($order_item->product_item_price, $order->currency_code) ?> x <?php echo formatqty($order_item->product_quantity);?><?php echo $order->_qty_unit;?>
													 <div class="nvg-font-weight-bold"><?php echo formatprice($order_item->product_item_price * $order_item->product_quantity, $order->currency_code); ?></div>
													</div>
												</div>

										<?php } ?>
									  </div>
								</div>
							</div>
						</div>
						
						<div class="order-price nvg-inline-block">
							<div class="listorder_price nvg-font-weight-bold"><?php echo formatprice($order->order_total, $order->currency_code); ?></div>
							<?php if ($order->cancel_link){?>
							<div>
								<a href="<?php echo $order->cancel_link ?>"><?php print _JSHOP_CANCEL_ORDER?></a>
							</div>
							<?php }?>
							<div><?php echo $order->_tmp_after_order_summ ?></div>
						</div>
					</div>
				<?php } ?>
			</div>
			<div class="jshop_pagination">
			   <div class="pagination"><?php echo $this->pageNav->getPagesLinks() ?></div>
			</div>
			<?php /*
			<!-- конец таблица ------------------------------------------->
			*/ ?>
			<?php
				break;
			case 'wishlist' :
			// WISHLIST
			?>
			<table class="eac_bloknot table-hover">
				<tr>
					<th class="eac_title" style="width:35%;"><?php echo _JSHOP_ITEM?></th> 
					<th class="eac_title" style="width:15%;"><?php echo JText::_('EXAC_JSHOP_SINGLEPRICE1') ?></th> 
					<th class="eac_title" style="width:15%"></th> 
				</tr>
				<?php foreach ($this->products as $key_id=>$prod) { 
					$prod['href'] = SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$prod['category_id'].'&product_id='.$prod['product_id'], 1);
					$prod['href_delete'] = SEFLink('index.php?option=com_jshopping&controller=wishlist&task=delete&number_id='.$key_id,1);
					$prod['remove_to_cart'] = SEFLink('index.php?option=com_jshopping&controller=wishlist&task=remove_to_cart&number_id='.$key_id,1);
				?>
				<tr>
					<td class = "jshop_img_description_center">
						<a class="nvg_float_left" href = "<?php echo $prod['href']; ?>">
							<img src = "<?php echo $this->config->image_product_live_path ?>/<?php if ($prod['thumb_image']) echo $prod['thumb_image']; else echo 'noimage.gif'; ?>" alt = "<?php echo htmlspecialchars($prod['product_name']);?>" class = "jshop_img" />
						</a>
						<a href="<?php echo $prod['href']?>"><?php echo $prod['product_name']?></a>
						<?php if ($this->config->show_product_code_in_cart) echo "<span class='jshop_code_prod'>(".$prod['ean'].")</span>";?>
						
						<?php echo sprintAtributeInCart($prod['attributes_value']);?>
						<?php echo sprintFreeAtributeInCart($prod['free_attributes_value']);?>
						<?php echo sprintFreeExtraFiledsInCart($prod['extra_fields']);?>
						<?php echo $prod['_ext_attribute_html']?>        
					</td>    
					<td style="text-align:center;">
						<?php echo formatprice($prod['price'])?>
						<?php echo $prod['_ext_price_html']?>
						<?php if ($this->config->show_tax_product_in_cart && $prod['tax']>0){?>
						<span class="taxinfo"><?php echo productTaxInfo($prod['tax']);?></span>
						<?php }?>
					</td>
					<td>
						<a href="<?php echo $prod['remove_to_cart']?>">
							<img title="<?php echo _JSHOP_REMOVE_TO_CART?>" alt="<?php echo _JSHOP_REMOVE_TO_CART?>" src="/components/com_jshopping/templates/addons/extended_profile/images/buy_cart.png" />
						</a>
						&nbsp;&nbsp;
						<a onclick="return confirm('<?php echo _JSHOP_REMOVE?>')" href="<?php echo $prod['href_delete']?>">
							<img title="<?php echo _JSHOP_DELETE?>" alt="<?php echo _JSHOP_DELETE?>" src="/components/com_jshopping/templates/addons/extended_profile/images/eac_delete.png" />
						</a>
					</td>
				</tr>
				<?php } ?>
			</table>
			<?php
				break;
			case 'coupons':
			// COUPONS
			?>
			<?php
				foreach($this->coupons as $coupon){
					$coupontime = '';
					if ($coupon->coupon_start_date != '0000-00-00' || $coupon->coupon_expire_date != '0000-00-00') {
						if ($coupon->coupon_start_date != '0000-00-00') {
							$coupontime .=  ' <span class="coupon_date">'.JText::_('EXAC_FROM').'</span> '.formatdate($coupon->coupon_start_date, 0);
						}
						if ($coupon->coupon_expire_date != '0000-00-00') {
							$coupontime .= ' <span class="coupon_date">'.JText::_('EXAC_TO').'</span> '.formatdate($coupon->coupon_expire_date, 0);
						}
					} else {
						$coupontime .= JText::_('EXAC_COUPONUNLIM');
					}
					if ($coupon->used) {
						$coupon_status = JText::_('EXAC_GENERAL_USED');
						$class = 'eac_coupon_used';
						$classb = 'nvg-badge-used';
					} else if ($coupon->coupon_expire_date != '0000-00-00' && strtotime($coupon->coupon_expire_date) < $this->date_now) {
						$coupon_status = JText::_('EXAC_GENERAL_TIMEOUT');
						$class = 'eac_coupon_downtime';
						$classb = 'nvg-badge-downtime';
					} else {
						$coupon_status = JText::_('EXAC_GENERAL_ACTIVED');
						$class = 'eac_coupon_acticve';
						$classb = 'nvg-badge-success';
					}
					if ($coupon->coupon_start_date != '0000-00-00' && strtotime($coupon->coupon_start_date) > $this->date_now) {
						$coupon_status = JText::_('EXAC_GENERAL_ACTIVFUTURE');
						$class = 'eac_coupon_future';
						$classb = 'nvg-badge-future';
					}
				?>
				<div class="nvg-width-1-2 <?php echo $class?> coupon_box nvg_boxshadow ">
					<div class="nvg-panel-badge nvg-badge <?php echo $classb?>">
						<?php echo $coupon_status ?>
					</div>
						<div class="coupon-nominal">
						<?php
							if ($coupon->coupon_type) {
								echo '<span>'.formatprice($coupon->coupon_value * $this->config->currency_value, $this->config->currency_code).'</span>';
							} else {
								echo '<span>'.round($coupon->coupon_value).'%</span>';
							}
						?>
					</div>
					<div class="coupon-expare">
						<span class="coupon_title"><?php echo JText::_('EXAC_COUPONTIME') ?></span> : <?php echo $coupontime ?>
					</div>
					<div class="coupon-code">
						<?php echo $coupon->coupon_code?>
					</div>


				</div>
				<?php }?>
			<?php
				break;
			case 'discount':
			// DISCOUNT
			?>
				<div class="nvg-grid">
					<div class="nvg-width-1-4 nvg-inline-block nvg-box-solid-border nvg_boxshadow nvg-text-align-center">
						<img src="/components/com_jshopping/templates/addons/extended_profile/images/group.png" />
					</div>
					<div class="nvg-width-3-4 nvg-inline-block nvg-box-solid-border nvg_boxshadow ">
						<div class="nvg-block_header"><?php echo JText::_('EXAC_GENERAL_DICOUNTSYS') ?> </div>
						<div><span class="acc_nvg_name"><?php echo _JSHOP_DISCOUNT?>: </span><span class="aec_value"><?php echo $this->user->discountpercent?>%</span></div>
						<div><span class="acc_nvg_name"><?php echo _JSHOP_GROUP?>: </span><span class="aec_value"><?php echo $this->user->groupname?></span></div>
						<?php echo $this->general_discount ?>
					</div>
				</div>
				<div class="nvg_clearfix"></div>
				<table class="eac_groups_list table-hover">
					<thead>
						<tr>
							<th class="eac_title"><?php echo _JSHOP_TITLE?></th> 
							<th class="eac_title"><?php echo _JSHOP_DISCOUNT?></th> 
							<th class="eac_title"><?php echo _JSHOP_DESCRIPTION?></th> 
						</tr>
					</thead>
					<tbody>
					<?php foreach($this->groupList as $row){?>
						<tr>
							<td class="eac_title"><?php echo $row->name?></td> 
							<td class="eac_discount"><?php echo floatval($row->usergroup_discount)?>%</td>
							<td class="eac_desription"><?php echo $row->description?></td>
						</tr>
					<?php }?>
					</tbody>
				</table>
				<div class="nvg_clearfix"></div>
			<?php
			default:
			// EXTENDED
				echo $tab->content;
			?>
		<?php } ?>
		</div>
	</div>
	<?php } ?>
</div>