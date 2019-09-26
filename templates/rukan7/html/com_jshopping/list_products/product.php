<?php 
/**
* @version      4.8.0 05.11.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
$app    = JFactory::getApplication();
$path   = JURI::base(true).'/templates/'.$app->getTemplate().'/';
include(dirname(__FILE__)."/cart.php");
$model = JSFactory::getTable('AttributValue', 'jshop');
$back_value_attr = (array)$back_value['attr'];
$all_attr = $model->getAllAttributeValues();
$count_attr = count($all_attr);
?>
<?php print $product->_tmp_var_start?>

<div class="prod-item col-xs-12 col-sm-6 col-md-6 col-lg-4">
      <div class="thumbnail">
<div class="prod-left">
	<div class = "image">
        <?php if ($product->image){?>
            <div class="image_block">
			    <?php print $product->_tmp_var_image_block;?>
                <?php if ($product->label_id){?>
                    <div class="product_label">
                            <div class="label_name label_name_<?php print $product->_label_name; ?>"><?php print $product->_label_name;?></div>
                    </div>
                <?php }?>
                <a href="<?php print $product->product_link?>">
                    <img class="jshop_img" src="<?php print $product->image?>" alt="<?php print htmlspecialchars($product->name);?>" title="<?php print htmlspecialchars($product->name);?>"  />
                </a>
				<div class="maz_overlay_wrap"><div class="maz_overlay_wrap_box"><div><a class="btn-view" href="<?php print $product->product_link?>" itemprop="url"><i class="icon-eye"></i></a></div></div></div>
		
            </div>
        <?php }?>
        
        <?php print $product->_tmp_var_bottom_foto;?>
		
		
		
		
		
		
		
    </div>
 </div>




<div class="prod-center">


	
	<div class="name">
		<a href="<?php print $product->product_link?>">
			<?php print $product->name?>
		</a>
		<?php if ($this->config->product_list_show_product_code){?>
			<span class="jshop_code_prod">(<?php print _JSHOP_EAN?>: <span><?php print $product->product_ean;?></span>)</span>
		<?php }?>
	</div>
	
	<div class="rating-block">
		<div class="prod-rating-comment">
	
		<?php if ($this->allow_review){?>
			<div class="review_mark">
				<?php print showMarkStar($product->average_rating);?>
			</div>
			<div class="count_commentar">
				<?php print _JSHOP_COMMENT . ": ". $product->reviews_count;?>
			</div>
		<?php }?>
	
	</div>
	</div>
	
	<div class="description">
            <?php print $product->short_description?>
    </div>
	
	
</div>

<div class="prod-right">
	
	<div class = "oiproduct">
        
        <?php if ($product->product_quantity <=0 && !$this->config->hide_text_product_not_available){?>
            <div class="not_available"><?php print _JSHOP_PRODUCT_NOT_AVAILABLE;?></div>
        <?php }?>
        
        
		<?php print $product->_tmp_var_bottom_old_price;?>
        
        <?php if ($product->product_price_default > 0 && $this->config->product_list_show_price_default){?>
            <div class="default_price">
                <?php print _JSHOP_DEFAULT_PRICE.": ";?>
                <span><?php print formatprice($product->product_price_default)?></span>
            </div>
        <?php }?>
        
        <?php if ($product->_display_price){?>
            <div class = "jshop_price">
                <?php if ($this->config->product_list_show_price_description) print _JSHOP_PRICE.": ";?>
                <?php if ($product->show_price_from) print _JSHOP_FROM." ";?>
                <span class="prod-cur-price"><?php print formatprice($product->product_price);?><?php print $product->_tmp_var_price_ext;?></span>
				
				<?php if ($product->product_old_price > 0){?>
                <?php if ($this->config->product_list_show_price_description) print _JSHOP_OLD_PRICE.": ";?>
                
				<span class="old-price"><?php print formatprice($product->product_old_price)?></span>
        
		<?php }?>
            </div>
        <?php }?>
        
        <?php print $product->_tmp_var_bottom_price;?>
        
        
        
        <?php if ($product->basic_price_info['price_show']){?>
            <div class="base_price">
                <?php print _JSHOP_BASIC_PRICE?>: 
                <?php if ($product->show_price_from && !$this->config->hide_from_basic_price) print _JSHOP_FROM;?> 
                <span><?php print formatprice($product->basic_price_info['basic_price'])?> / <?php print $product->basic_price_info['name'];?></span>
            </div>
        <?php }?>
        
        
        <?php if ($this->config->product_list_show_weight && $product->product_weight > 0){?>
            <div class="productweight">
                <?php print _JSHOP_WEIGHT?>: 
                <span><?php print formatweight($product->product_weight)?></span>
            </div>
        <?php }?>
        
        
        <?php if (is_array($product->extra_field)){?>
            <div class="extra_fields">
                <?php foreach($product->extra_field as $extra_field){?>
                    <div>
                        <span class="label-name"><?php print $extra_field['name'];?>:</span>
                        <span class="data"><?php print $extra_field['value'];?></span>
                    </div>
                <?php }?>
            </div>            
        <?php }?>
        
        <?php if ($product->vendor){?>
            <div class="vendorinfo">
                <?php print _JSHOP_VENDOR?>: 
                <a href="<?php print $product->vendor->products?>"><?php print $product->vendor->shop_name?></a>
            </div>
        <?php }?>
        
		<?php 
			$product->product_id;
			$product_in = false;
			$cart = JSFactory::getModel('cart', 'jshop')->init('cart', 1);

					
			foreach($cart->products as $key){
			//echo $key[product_id] . "<br/>";
			if($product->product_id == $key[product_id]){
				$product_in = "prod-in";
			}
			}
		?>
		
        <?php print $product->_tmp_var_top_buttons;?>
        
        <div class="buttons">
            <?php if ($product->buy_link){?>
                <a class="btn btn-transparent-dark <?php if(!$product_in) {print 'button_buy';} else{print 'no_click';} ?> <?= $product_in ?>" <?php if(!$product_in) {?> href="<?php print $product->buy_link; ?>" <?php }?>>
                    <i class="<?php if(!$product_in) { echo 'icon-basket';} else{ echo 'icon-check';} ?>"></i>
                </a>
            <?php }?>
            
			<?php if ($product->product_quantity <=0){?>
				<a class="btn btn-transparent-dark no_click <?= $product_in ?>" <?php if(!$product_in) {?>href="" <?php }?>>
                    <i class="icon-minus"></i>
                </a>
			<?php }?>
			
           
			
            
            <?php print $product->_tmp_var_buttons;?>
        </div>
        
        <?php print $product->_tmp_var_bottom_buttons;?>
        
    </div>
    

</div>

</div>
</div>


<?php print $product->_tmp_var_end?>