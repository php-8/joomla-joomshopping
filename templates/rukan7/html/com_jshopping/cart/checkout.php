<?php 
/**
* @version      4.12.2 22.10.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>


<div class="prod_cart col-xs-12">

	<?php
    $i=1;
    foreach($this->products as $key_id=>$prod){
    ?> 
	
	<div class="cart_prod_count">
	
		<div class="prod_cart_left col-xs-12 col-md-2">
		
				<div class="data">
					<a href = "<?php print $prod['href']; ?>">
						<img src = "<?php print $this->image_product_path ?>/<?php if ($prod['thumb_image']) print $prod['thumb_image']; else print $this->no_image; ?>" alt = "<?php print htmlspecialchars($prod['product_name']);?>" class = "jshop_img" />
					</a>
				</div>
		
		</div>
		
		<div class="prod_cart_right col-xs-12 col-md-10"> 
	
		<div class="prod_cart_name col-md-5">
		
			 <div class="data">
                <a href="<?php print $prod['href']?>">
                    <?php print $prod['product_name']?>
                </a>
            </div>
		
		</div>
		<div class="prod_cart_price col-md-2">
		
			<div class="data">
                <?php print formatprice($prod['price'])?>
                <?php print $prod['_ext_price_html']?>
            </div>
		
		</div>
		<div class="prod_cart_quan col-md-3">
		
			<div class="prod_qty_input">
                <?php print $prod['quantity']?><?php print $prod['_qty_unit'];?>
            </div>
		
		</div>
		<div class="prod_cart_total col-md-2">
		
			<div class="data">
                <?php print formatprice($prod['price']*$prod['quantity']);?>
                <?php print $prod['_ext_price_total_html']?>
            </div>
		
		</div>
	
		<div class="cart_tax_info col-xs-12">
					
					<?php if ($this->config->show_tax_product_in_cart && $prod['tax']>0){?>
                    <span class="taxinfo"><?php print productTaxInfo($prod['tax']);?></span>
					<?php }?>
					
		</div>
	
	</div>
	</div>
		<?php } ?>
		</div>

		
		
<div class = "jshop jshop_subtotal col-xs-12">
	
    <?php if (!$this->hide_subtotal){?>
        <div class="subtotal">    
            <td class = "name">
                <?php print _JSHOP_SUBTOTAL ?>
            </td>
            <td class = "value">
                <?php print formatprice($this->summ);?><?php print $this->_tmp_ext_subtotal?>
            </td>
        </div>
    <?php } ?>

    <?php print $this->_tmp_html_after_subtotal?>

    <?php if ($this->discount > 0){ ?>
        <div class="discount">
            <td class = "name">
                <?php print _JSHOP_RABATT_VALUE ?><?php print $this->_tmp_ext_discount_text?>
            </td>
            <td class = "value">
                <?php print formatprice(-$this->discount);?><?php print $this->_tmp_ext_discount?>
            </td>
        </div>
    <?php } ?>

    <?php if (isset($this->summ_delivery)){?>
        <div class="shipping">
            <td class = "name">
                <?php print _JSHOP_SHIPPING_PRICE;?>
            </td>
            <td class = "value">
                <?php print formatprice($this->summ_delivery);?><?php print $this->_tmp_ext_shipping?>
            </td>
        </div>
    <?php } ?>

    <?php if (isset($this->summ_package)){?>
        <div class="package">
            <td class = "name">
                <?php print _JSHOP_PACKAGE_PRICE;?>
            </td>
            <td class = "value">
                <?php print formatprice($this->summ_package);?><?php print $this->_tmp_ext_shipping_package?>
            </td>
        </div>
    <?php } ?>

    <?php if ($this->summ_payment != 0){ ?>
        <div class="payment">
            <td class = "name">
                <?php print $this->payment_name;?>
            </td>
            <td class = "value">
                <?php print formatprice($this->summ_payment);?><?php print $this->_tmp_ext_payment?>
            </td>
        </div>
    <?php } ?>

    <?php if (!$this->config->hide_tax){ ?>
        <?php foreach($this->tax_list as $percent=>$value){?>
            <div class="tax">
                <td class = "name">
                    <?php print displayTotalCartTaxName();?>
                    <?php if ($this->show_percent_tax) print formattax($percent)."%"?>
                </td>
                <td class = "value">
                    <?php print formatprice($value);?><?php print $this->_tmp_ext_tax[$percent]?>
                </td>
            </div>
        <?php } ?>
    <?php } ?>

    <div class="total">
        <td class = "name">
            <?php print $this->text_total; ?>
        </td>
        <td class = "value">
            <?php print formatprice($this->fullsumm)?><?php print $this->_tmp_ext_total?>
        </td>
    </div>

    <?php print $this->_tmp_html_after_total?>

    <?php if ($this->free_discount > 0){?>  
        <div class="free_discount">
            <td colspan="2" align="right">    
                <span class="free_discount">
                    <?php print _JSHOP_FREE_DISCOUNT;?>:
                    <span><?php print formatprice($this->free_discount); ?></span>
                </span>
            </td>
        </div>
    <?php }?>  
</div>