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
<div class="jshop" id="comjshop">
<?php print $this->checkout_navigator?>

<form action="<?php print SEFLink('index.php?option=com_jshopping&controller=cart&task=refresh') ?>" method="post" name="updateCart">

<?php print $this->_tmp_ext_html_cart_start ?>    

<?php if ($countprod > 0) : ?>
    <table class="jshop cart">
    <?php
    $i = 1;
    foreach ($this->products as $key_id => $prod){
    ?> 
    
        <td class="jshop_img_description_center">
            <div class="mobile-cart">
                <?php print _JSHOP_IMAGE; ?>
            </div>
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
        </td>
        <td class="product_name">
            <div class="mobile-cart">
                <?php print _JSHOP_ITEM; ?>
            </div>
            <div class="data">
                <a href="<?php print $prod['href'] ?>">
                    <?php print $prod['product_name'] ?>
                </a>
                <?php if ($this->config->show_product_code_in_cart) { ?>
                    <span class="jshop_code_prod">(<?php print $prod['ean'] ?>)</span>
                <?php } ?>
				<?php print $prod['_ext_product_name'] ?>
                <?php if ($prod['manufacturer'] != '') { ?>
                    <div class="manufacturer"><?php print _JSHOP_MANUFACTURER ?>: <span><?php print $prod['manufacturer'] ?></span></div>
                <?php } ?>
                <?php print sprintAtributeInCart($prod['attributes_value']); ?>
                <?php print sprintFreeAtributeInCart($prod['free_attributes_value']); ?>
                <?php print sprintFreeExtraFiledsInCart($prod['extra_fields']); ?>
                <?php print $prod['_ext_attribute_html'] ?>
            </div>
        </td> 
        <td class="single_price">
            <div class="mobile-cart">
                <?php print _JSHOP_SINGLEPRICE; ?>
            </div>
            <div class="data">
                <?php print formatprice($prod['price']) ?>
                <?php print $prod['_ext_price_html'] ?>
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
        </td>
        <td class="quantity">
            <div class="mobile-cart">
                <?php print _JSHOP_NUMBER; ?>
            </div>
            <div class="data">
				<?php if ($prod['not_qty_update']){?>
					<span class="qtyval"><?php print $prod['quantity'] ?></span>
				<?php }else{?>
					<input type = "text" name = "quantity[<?php print $key_id ?>]" value = "<?php print $prod['quantity'] ?>" class = "inputbox" />
				<?php }?>
                <?php print $prod['_qty_unit']; ?>
				<?php if (!$prod['not_qty_update']){?>
					<span class = "cart_reload">
						<img src="<?php print $this->image_path?>images/reload.png" title="<?php print _JSHOP_UPDATE_CART ?>" alt = "<?php print _JSHOP_UPDATE_CART ?>" onclick="document.updateCart.submit();" />
					</span>
				<?php }?>
            </div>
        </td>
        <td class="total_price">
            <div class="mobile-cart">
                <?php print _JSHOP_PRICE_TOTAL; ?>
            </div>
            <div class="data">
                <?php print formatprice($prod['price'] * $prod['quantity']); ?>
                <?php print $prod['_ext_price_total_html'] ?>
                <?php if ($this->config->show_tax_product_in_cart && $prod['tax'] > 0) { ?>
                    <span class="taxinfo"><?php print productTaxInfo($prod['tax']); ?></span>
                <?php } ?>
            </div>
        </td>
        <td class="remove">
            <div class="mobile-cart">
                <?php print _JSHOP_REMOVE; ?>
            </div>
            <div class="data">
                <a class="button-img" href="<?php print $prod['href_delete']?>" onclick="return confirm('<?php print _JSHOP_CONFIRM_REMOVE?>')">
                    <img src = "<?php print $this->image_path ?>images/remove.png" alt = "<?php print _JSHOP_DELETE?>" title = "<?php print _JSHOP_DELETE?>" />
                </a>
            </div>
        </td>
    </tr>
    <?php
    $i++;
    }
    ?>
    </table>

    <?php if ($this->config->show_weight_order) : ?>
        <div class = "weightorder">
            <?php print _JSHOP_WEIGHT_PRODUCTS?>: <span><?php print formatweight($this->weight);?></span>
        </div>
    <?php endif; ?>
      
    <?php if ($this->config->summ_null_shipping > 0) : ?>
        <div class = "shippingfree">
            <?php printf(_JSHOP_FROM_PRICE_SHIPPING_FREE, formatprice($this->config->summ_null_shipping, null, 1));?>
        </div>
    <?php endif; ?>
      
    <div class = "cartdescr"><?php print $this->cartdescr; ?></div>
<?php endif; ?>
   

</form>
<?php print_r($this->products)?>
<?php print $this->_tmp_ext_html_before_discount?>

            
</div>