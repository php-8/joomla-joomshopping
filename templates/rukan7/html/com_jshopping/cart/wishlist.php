<?php
/**
 * @version      4.12.2 22.10.2014
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */
defined('_JEXEC') or die();

$countprod = count($this->products);
?>
<form action="<?php print SEFLink('index.php?option=com_jshopping&controller=cart&task=refresh') ?>" method="post" name="updateCart">

<div class="prod_cart">
	<?php
    $i = 1;
    foreach ($this->products as $key_id => $prod){
    ?> 
	
	<div class="cart_prod_count">
	
		<div class="prod_cart_left col-xs-12 col-md-2">
		
				<div class="data">
					<a href="<?php print $prod['href'] ?>">
						<img src="<?php print $this->image_product_path ?>/<?php
						if ($prod['thumb_image'])
							print $prod['thumb_image'];
						else
							print $this->no_image;
						?>" alt="<?php print htmlspecialchars($prod['product_name']); ?>" class="jshop_img" />
					</a>
				</div>
		
		</div>
		
		<div class="prod_cart_right col-xs-12 col-md-10"> 
	
		<div class="prod_cart_name col-xs-12 col-md-5">
		
			<div class="data">
                <a href="<?php print $prod['href'] ?>">
                    <?php print $prod['product_name'] ?>
                </a>
                
            </div>
		
		</div>
		<div class="prod_cart_price col-md-2">
		
			<div class="data">
                <?php print formatprice($prod['price']) ?>
                <?php print $prod['_ext_price_html'] ?>
            </div>
		
		</div>
		<div class="add_to_cart_icon col-md-3">
		
			<a class="button-img" href = "<?php print $prod['remove_to_cart'] ?>" >
                    <i class="icon-basket"></i>
            </a>
		
		</div>
		
		<div class="prod_cart_total col-md-2">
		
			<div class="data">
                <?php print formatprice($prod['price'] * $prod['quantity']); ?>
                <?php print $prod['_ext_price_total_html'] ?>
            </div>
		
		</div>
		<div class="cart_tax_info col-xs-12">
					<div class="tax_block col-xs-12">
					<?php if ($this->config->show_tax_product_in_cart && $prod['tax'] > 0) { ?>
						<span class="taxinfo"><?php print productTaxInfo($prod['tax']); ?></span>
					<?php } ?>
					<?php if ($this->config->cart_basic_price_show && $prod['basicprice'] > 0) { ?>
						<div class="basic_price">
							<?php print _JSHOP_BASIC_PRICE ?>: 
							<span><?php print sprintBasicPrice($prod); ?></span>
						</div>
					<?php } ?>
					</div>
					<div class="remove_block col-xs-12">
					
					<div class="mobile-cart">
						<?php print _JSHOP_REMOVE; ?>
					</div>
					<div class="data">
						<a class="button-img" href="<?php print $prod['href_delete']?>" onclick="return confirm('<?php print _JSHOP_CONFIRM_REMOVE?>')">
						
							<i class="icon-minus"></i>
						
						</a>
					</div>
					
					</div>
		</div>

	</div>

	
	</div>
	<?php } ?>
	<div class="cart_buttons col-xs-12">
	<?php print $this->_tmp_html_before_buttons?>
		<div class = "jshop cart_buttons">
			<div id = "checkout">
				<div class = "back_to_shop">
					<a href = "<?php print $this->href_shop ?>" class="">
						 <?php print _JSHOP_BACK_TO_SHOP ?>
					</a>
				</div>
				<div class = "go_to_check_out">
				<?php if ($countprod>0) : ?>
					<a href = "<?php print $this->href_checkout ?>" class="btn btn-primary">
						<i class="icon-basket"></i>   <?php print _JSHOP_GO_TO_CART ?>
					</a>
				<?php endif; ?>
				</div>
				<div class = "clearfix"></div>
			</div>
		</div>
	<?php print $this->_tmp_html_after_buttons?>
	</div>
</div>

</form>

<?php print $this->_tmp_ext_html_before_discount?>

<?php if ($this->use_rabatt && $countprod>0) : ?>
    <div class="cart_block_discount">
        <form name="rabatt" method="post" action="<?php print SEFLink('index.php?option=com_jshopping&controller=cart&task=discountsave'); ?>">
            <div class = "row-fluid jshop">
                <div class = "span12">
                    <input type = "text" class = "inputbox" name = "rabatt" value = "" placeholder="<?php print _JSHOP_RABATT ?>"/>
					<button type = "submit" class="btn btn-transparent-dark"><i class="icon-key"></i> <?php print _JSHOP_RABATT_ACTIVE ?></button>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>