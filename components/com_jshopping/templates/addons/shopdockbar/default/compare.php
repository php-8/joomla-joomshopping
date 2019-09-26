<?php

defined('_JEXEC') or die;

$table_compare = $this->table_compare;
$i = 0;
?>
<script type="text/javascript">
//<![CDATA[
function toggleCompareTable(show) {
	if (show) {
		jQuery('.jshop_product_compare tr.not_diff').hide();
		jQuery('#show_all').hide();
		jQuery('#show_differences').show();
	} else {
		jQuery('.jshop_product_compare tr.not_diff').show();
		jQuery('#show_all').show();
		jQuery('#show_differences').hide();
	}
	jQuery('table.jshop_product_compare tbody tr:visible:odd').removeClass('odd even').addClass('odd');
    jQuery('table.jshop_product_compare tbody tr:visible:even').removeClass('odd even').addClass('even');
}
//]]>
</script>
<div class="jshop jshop_list_product">
	<h1><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FIELDSETLABEL_COMPARE') ?></h1>
	<?php if (count($this->products)){?>

	<table class="jshop_product_compare">
		<thead>
			<tr>
				<td>
					<div class="selectoralldef">
						<div id="show_all">
							<div class="whalf"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_ALL') ?></div>
							<div class="whalf_a"><a href="#" onclick="toggleCompareTable(1); return false;"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_DIFF') ?></a></div>
						</div>
						<div id="show_differences" style="display: none;">
							<div class="whalf_a"><a href="#" onclick="toggleCompareTable(0); return false;"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_ALL') ?></a></div>
							<div class="whalf"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_DIFF') ?></div>
						</div>
					</div>
					<?php if (count($this->products)<$this->max_quantity || !$this->max_quantity){?>
					<div class="add_product">
						<a href="<?php print $this->shopurl?>">
							<?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_ADD_PRODUCT') ?>
						</a>
					</div>
					<?php } ?>
					<div class="remove_all">
						<a href="<?php print SEFLink('index.php?option=com_jshopping&controller=shopdockbar&task=removeFromCompare')?>" onclick="return confirm('<?php print _JSHOP_DELETE?>?')">
						<?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_REMOVE_ALL') ?>
						</a>
					</div>
				</td>
				<?php foreach($this->products as $product){ ?>
				<td>
					<div class="block_product">
					<a class="but_rem" href="<?php print SEFLink('index.php?option=com_jshopping&controller=shopdockbar&task=removeFromCompare&product_id='.$product->product_id)?>" onclick="return confirm('<?php print _JSHOP_DELETE?>?')">
						<img src="/modules/mod_jshopping_shopdockbar/images/default/trash.png" />
					</a>
					<div class="name">
						<a href="<?php print $table_compare->product_link[$product->product_id] ?>">
						<?php echo $table_compare->name[$product->product_id] ?><?php if ($this->config->show_product_code && $product->product_ean){?> <span class="jshop_code_prod">(<?php print _JSHOP_EAN?>: <span id="product_code"><?php print $product->product_ean;?></span>)</span><?php }?>
						</a>
					</div>
					<?php if (isset($table_compare->image[$product->product_id])){ ?>
					<div class="image">
						<a href="<?php print $table_compare->product_link[$product->product_id] ?>">
							<img class="jshop_img" src = "<?php print $table_compare->image[$product->product_id] ?>" />
						</a>
					</div>
					<?php } ?>

					<?php if ($product->_display_price){?>
						<?php if ($table_compare->product_old_price[$product->product_id] > 0){?>
							<div class="old_price">
								<?php if ($this->config->product_list_show_price_description) print _JSHOP_OLD_PRICE.": ";?>
								<span>
									<?php print formatprice($table_compare->product_old_price[$product->product_id])?>
								</span>
							</div>
						<?php }?>
						<div class = "jshop_price">
							<?php if ($this->config->product_list_show_price_description) print _JSHOP_PRICE.": ";?>
							<?php if ($table_compare->show_price_from[$product->product_id]) print _JSHOP_FROM." ";?>
							<span>
								<?php print formatprice($table_compare->product_price[$product->product_id]);?>
							</span>
						</div>
					<?php }?>
					<?php if (isset($table_compare->buy_link[$product->product_id])){ ?>
					<div class="buttons">
						<?php if ($table_compare->buy_link[$product->product_id]){?>
						<a class="button_buy btn uk-button" href="<?php print $table_compare->buy_link[$product->product_id]?>"><?php print _JSHOP_BUY?></a>
						<?php }?>
					</div>
					<?php }?>					
					</div>
				</td>
			<?php }?> 
			</tr>
			<tr>
				<td>
				</td>
				<?php foreach($this->products as $product){ ?>
				<td>
					<?php if (isset($table_compare->average_rating[$product->product_id])){ ?>
					<div class="review_mark">
						<?php print showMarkStar($product->average_rating);?>
					</div>
					<div class="count_commentar">
						<?php print sprintf(_JSHOP_X_COMENTAR, $product->reviews_count);?>
					</div>
					<?php } ?>				
				</td>
				<?php } ?>
			</tr>
		</thead>
		<?php
		if (isset($table_compare->short_description)) {
			$class = 'diff';
			if (count($table_compare->short_description) == count($this->products)) {
				$res = array_unique($table_compare->short_description);
				if (count($res) == 1) {
					$class = 'not_diff';
				}
			}
			$class .= $i%2==0 ? ' even' : ' odd';
			$i++;
		?>
		<tr class="<?php echo $class ?>">
			<td class="title">
				<!-- <?php echo _JSHOP_DESCRIPTION_PRODUCT ?>: -->
			</td>
			<?php foreach($this->products as $product){ ?>
			<td>
				<div class="short_descr no_display">
					<?php echo $table_compare->short_description[$product->product_id] ? $table_compare->short_description[$product->product_id] : '-' ?>
				</div>
			</td>
			<?php }?>
		</tr>
		<?php } ?>
		<?php
		if (isset($table_compare->manufacturer)) {
			$class = 'diff';
			if (count($table_compare->manufacturer) == count($this->products)) {
				$res = array_unique($table_compare->manufacturer);
				if (count($res) == 1) {
					$class = 'not_diff';
				}
			}
			$class .= $i%2==0 ? ' even' : ' odd';
			$i++;
		?>
		<tr class="<?php echo $class ?>">
			<td class="title">
				<?php echo _JSHOP_MANUFACTURER ?>:
			</td>
			<?php foreach($this->products as $product){ ?>
			<td align="center">
				<?php echo $table_compare->manufacturer[$product->product_id] ? $table_compare->manufacturer[$product->product_id] : '-' ?>
			</td>
			<?php }?>
		</tr>
		<?php } ?>
		<?php
		if (isset($table_compare->product_weight)) {
			$class = 'diff';
			if (count($table_compare->product_weight) == count($this->products)) {
				$res = array_unique($table_compare->product_weight);
				if (count($res) == 1) {
					$class = 'not_diff';
				}
			}
			$class .= $i%2==0 ? ' even' : ' odd';
			$i++;
		?>
		<tr class="<?php echo $class ?>">
			<td class="title">
				<?php echo _JSHOP_WEIGHT ?>:
			</td>
			<?php foreach($this->products as $product){ ?>
			<td align="center">
				<?php echo $table_compare->product_weight[$product->product_id] ? formatweight($table_compare->product_weight[$product->product_id]) : '-' ?>
			</td>
			<?php }?>
		</tr>
		<?php } ?>
		<?php
		if (isset($table_compare->product_available)) {
			$class = 'diff';
			if (count($table_compare->product_available) == count($this->products)) {
				$res = array_unique($table_compare->product_available);
				if (count($res) == 1) {
					$class = 'not_diff';
				}
			}
			$class .= $i%2==0 ? ' even' : ' odd';
			$i++;
		?>
		<tr class="<?php echo $class ?>">
			<td class="title">
				<?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_AVAILABILITY') ?>:
			</td>
			<?php foreach($this->products as $product){ ?>
			<td align="center">
				<?php echo $table_compare->product_available[$product->product_id] ? '<span class="available">'.JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_AVAILABLE').'</span>' : '<span class="not_available">'._JSHOP_PRODUCT_NOT_AVAILABLE.'</span>' ?>
			</td>
			<?php }?>
		</tr>
		<?php } ?>
		<?php
		if (isset($table_compare->delivery_time)) {
			$class = 'diff';
			if (count($table_compare->delivery_time) == count($this->products)) {
				$res = array_unique($table_compare->delivery_time);
				if (count($res) == 1) {
					$class = 'not_diff';
				}
			}
			$class .= $i%2==0 ? ' even' : ' odd';
			$i++;
		?>
		<tr class="<?php echo $class ?>">
			<td class="title">
				<?php echo _JSHOP_DELIVERY_TIME ?>:
			</td>
			<?php foreach($this->products as $product){ ?>
			<td align="center">
				<?php echo $table_compare->delivery_time[$product->product_id] ? $table_compare->delivery_time[$product->product_id] : '-' ?>
			</td>
			<?php }?>
		</tr>
		<?php } ?>
		<?php 
		if(!empty($table_compare->extra_field)) 
		foreach($table_compare->extra_field as $name=>$value){
			$class = 'diff';
			if (count($value) == count($this->products)) {
				$res = array_unique($value);
				if (count($res) == 1) {
					$class = 'not_diff';
				}
			}
			$class .= $i%2==0 ? ' even' : ' odd';
			$i++;
		?>
		<tr class="<?php echo $class ?>">
			<td class="title">
				<?php echo $name.':';?>
			</td>
			<?php foreach($this->products as $product){ ?>
			<td align="center">
			<?php
				if (isset($value[$product->product_id])) {
					echo $value[$product->product_id];
				} else {
					echo '-';
				}
			?>
			</td>
			<?php }?>
		</tr>
		<?php }	?>	
		<?php 
		if(!empty($table_compare->attribute_values)) 
		foreach($table_compare->attribute_values as $name=>$value){
			$class = 'diff';
			if (count($value) == count($this->products)) {
				$res = array_unique($value);
				if (count($res) == 1) {
					$class = 'not_diff';
				}
			}
			$class .= $i%2==0 ? ' even' : ' odd';
			$i++;
		?>
		<tr class="<?php echo $class ?>">
			<td class="title">
				<?php echo $name.':';?>
			</td>
			<?php foreach($this->products as $product){ ?>
			<td align="center">
			<?php
				if (isset($value[$product->product_id])) {
					echo $value[$product->product_id];
				} else {
					echo '-';
				}
			?>
			</td>
			<?php }?>
		</tr>
		<?php }	?>	
	</table>          
	<?php        
	}
	else{
		echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_COMPARE_NO_PRODUCTS');
	}
	?>

	<div class="back_to_shop">
		<a href="<?php print $this->shopurl?>">
			<?php print _JSHOP_BACK?>
		</a>
	</div> 
</div>