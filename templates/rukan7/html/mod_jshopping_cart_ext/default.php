
<div>


	<div class="icon-cart-top-wrapper">
		<div class="cart_button cart_white">
		<span class="cart_counter"><?= count($cart->products);?></span>
		</div>
	</div>
	
	<div class="cart_top_list">
	
	<div class = "cart_info_box">
	
	
	
	<div class="cart_close">
		<i class="cart-cloase-button icon-close"></i>
	</div>
	
		<?php 
		$jshopConfig = JSFactory::getConfig(); 
		$image_live_path = $jshopConfig->image_product_live_path;

			
			$countprod = 0;
			$array_products = array();
			if(count($cart->products)!= 0){
			foreach($cart->products as $value){
				$array_products [$countprod] = $value;
		?>
	<div class = "cart_prod col-xs-12">
	<div class="cart_image_block col-md-4">
	<img src="<?php print $image_live_path ?>/
				<?php
					if ($array_products [$countprod]['thumb_image']){
						print $array_products [$countprod]['thumb_image'];}
					else{
						print  "noimage.gif";}
				
				?>"/>
	</div>
	
		<div class="prod-cart-info col-md-8">
		
			<div class="prod-cat-name">
				<?php print $array_products [$countprod]["product_name"]; ?>
			</div>
			
			<?php if ($show_count =='1') {?>
			
			<div class="prod-cat-quan">
				<?php 
				print $array_products [$countprod]["quantity"] . " x"; 
				
				?>
				
			</div>
			
			<div class="prod-cat-sum">
				<?php print formatprice($array_products [$countprod]["price"]); ?>
			</div>
		
			<?php } else{ ?>
			
				<div class="prod-cat-quan">
				
				</div>
			
				<div class="prod-cat-sum">
				<?php print formatprice($array_products [$countprod]["price"] * $array_products [$countprod]["quantity"]); ?>
				</div>
			<?php } ?>
		</div>
	
	</div>
			<?php $countprod++; } } else{?> <div class="prod-cat-empty"><?php print JText::_('CART_EMPTY');?></div><?php } ?>
	
	
	
	</div>
	
	<div class="control_cart_button col-xs-12">
	
	<div class="go_to_cart col-xs-6">
	<a class="btn btn-transparent-light" href = "<?php print SEFLink('index.php?option=com_jshopping&controller=cart&task=view', 1)?>"><i class="icon-basket"></i> <?php print JText::_('GO_TO_CART')?></a>
	</div>
	
	<div class="total-price col-xs-6">
		<span id = "jshop_quantity_products"><?php print JText::_('SUM_TOTAL')?>:&nbsp;</span>
		<span id = "jshop_summ_product"><?php print formatprice($cart->getSum(0,1))?></span>
	</div>
	
	</div>
	
	</div>



</div>
