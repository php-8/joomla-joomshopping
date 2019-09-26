<?php
/**
* @version      4.10.5 09.12.2015
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/ 
defined('_JEXEC') or die('Restricted access');
$product = $this->product;
include(dirname(__FILE__)."/load.js.php");
$jshopConfig = JSFactory::getConfig();
?>



<div class="product_block">
<form name="product" method="post" action="<?php print $this->action?>" enctype="multipart/form-data" autocomplete="off">
	<div class="prod_rev_left col-xs-12 col-md-7">
		<div id='list_product_image_middle'>
			<?php print $this->_tmp_product_html_body_image?>
			
			<?php if(!count($this->images)){?>
				<img id = "main_image" src = "<?php print $this->image_product_path?>/<?php print $this->noimage?>" alt = "<?php print htmlspecialchars($this->product->name)?>" />
			<?php }?>
			
			
			
			<?php $i=0;?>
			
			<?php foreach($this->images as $k=>$image){ ?>
			
				<a class="lightbox" id="main_image_full_<?php print $i; ?>" href="<?php print $this->image_product_path?>/<?php print $image->image_full;?>" <?php if ($k!=0){?>style="display:none"<?php }?> title="<?php print htmlspecialchars($image->_title)?>">
					<img id = "main_image_<?php print $i?>" src = "<?php print $this->image_product_path?>/<?php print $image->image_name;?>" data-zoom-image="<?php print $this->image_product_path?>/<?php print $image->image_full;?>" alt="<?php print htmlspecialchars($image->_title)?>" title="<?php print htmlspecialchars($image->_title)?>" />
					
				</a>
				
			<?php $i++; }?>
		</div>
		

                
                <div id='mat_list_product_image_thumb' class="owl-carousel">
                    <?php if ( (count($this->images)>1) || (count($this->videos) && count($this->images)) ) {?>
					<?php $i=0;?>
                        <?php foreach($this->images as $k=>$image){?>
                           <div class="item"> <img class="jshop_img_thumb" src="<?php print $this->image_product_path?>/<?php print $image->image_thumb?>" alt="<?php print htmlspecialchars($image->_title)?>" title="<?php print htmlspecialchars($image->_title)?>" onclick="showImage(<?php print $i; ?>)" /></div>
                        <?php $i++; }?>
                    <?php }?>
                </div>
                

		

	</div>
	
	<div class="prod_rev_right col-xs-12 col-md-5">

	<div class="prod_right_info_block">
	
	<h1><?php print $this->product->name?><?php if ($this->config->show_product_code){?> <span class="jshop_code_prod">(<?php print _JSHOP_EAN?>: <span id="product_code"><?php print $this->product->getEan();?></span>)</span><?php }?></h1>
	
	
	<?php include(dirname(__FILE__)."/ratingandhits.php");?>
	
	
	
	<div class="prod_price_section">
	
	<?php // //////////////// Price //////////////////////// ?>
	
	    <?php if ($this->product->_display_price){?>
            <div class="prod_price">
				<div class="prod_currency">

				</div>
                <div id="block_price">
                    <?php print $this->product->getPriceCalculate()?>
                    <?php print $this->product->_tmp_var_price_ext;?>
					<?php print $jshopConfig->currency_code; ?>
                </div>
            </div>
        <?php }?>
	
		<?php if ($this->product->product_old_price > 0){?>
            <div class="old_price">
				
                <div class="" id="old_price">
                    <?php print $this->product->product_old_price?>
                    <?php print $this->product->_tmp_var_old_price_ext;?>

					<?php print $jshopConfig->currency_code; ?>

                </div>
            </div>
        <?php }?>

        <?php if ($this->product->product_price_default > 0 && $this->config->product_list_show_price_default){?>
            <div class="default_price"><?php print _JSHOP_DEFAULT_PRICE?>: <span id="pricedefault"><?php print formatprice($this->product->product_price_default)?></span></div>
        <?php }?>
        
        <?php print $this->_tmp_product_html_before_price;?>

	<?php // //////////////// END PRICE //////////////////////// ?>
	</div>
	
	
	
	<?php if (count($this->attributes)) : ?>
            <div class="jshop_prod_attributes jshop">
                <?php foreach($this->attributes as $attribut) : ?>
                    <?php if ($attribut->grshow){?>
                        <div>
                            <span class="attributgr_name"><?php print $attribut->groupname?></span>
                        </div>
                    <?php }?>               
                    <div class = "row-fluid">
                        <div class="attributes_title">
                            <span class="attributes_name"><?php print $attribut->attr_name?>:</span>
                            <span class="attributes_description"><?php print $attribut->attr_description;?></span>
                        </div>
                        <div class = "attr_select">
                            <span id='block_attr_sel_<?php print $attribut->attr_id?>'>
                                <?php print $attribut->selects?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
    <?php endif; ?>
	
	
	
	
	
	
	
	
		<?php if ($this->product->vendor_info){?>
            <div class="vendorinfo">
                <?php print _JSHOP_VENDOR?>: <?php print $this->product->vendor_info->shop_name?> (<?php print $this->product->vendor_info->l_name." ".$this->product->vendor_info->f_name;?>),
                ( 
                <?php if ($this->config->product_show_vendor_detail){?><a href="<?php print $this->product->vendor_info->urlinfo?>"><?php print _JSHOP_ABOUT_VENDOR?></a>,<?php }?> 
                <a href="<?php print $this->product->vendor_info->urllistproducts?>"><?php print _JSHOP_VIEW_OTHER_VENDOR_PRODUCTS?></a> )
            </div>
        <?php }?>

        <?php if (!$this->config->hide_text_product_not_available){ ?>
            <div class = "not_available" id="not_available"><?php print $this->available?></div>
        <?php }?>

        <?php if ($this->config->product_show_qty_stock){?>
            <div class="qty_in_stock">
                <?php print _JSHOP_QTY_IN_STOCK?>: 
                <span id="product_qty"><?php print sprintQtyInStock($this->product->qty_in_stock);?></span>
            </div>
        <?php }?>
	
		<?php if ($this->product->delivery_time != ''){?>
            <div class="deliverytime" <?php if ($product->hide_delivery_time){?>style="display:none"<?php }?>><?php print _JSHOP_DELIVERY_TIME?>: <?php print $this->product->delivery_time?></div>
        <?php }?>
	
</div>
	
	
<div class="prod_right_buttons_block">
	
	<?php // //////////////// Buy and Quantity Buttons //////////////////////// ?>
		<?php if (!$this->hide_buy){?>                         
            <div class="prod_buttons" style="<?php print $this->displaybuttons?>">
                
                
                <div class="prod_qty_input handle-counter" id="handleCounter">
					<button class="counter-minus">-</button>
						<input type="text" name="quantity" id="quantity" onkeyup="reloadPrices();" class="inputbox" value="<?php print $this->default_count_product?>" /><?php print $this->_tmp_qty_unit;?>
					<button class="counter-plus">+</button>
				</div>
				
				
				
				
				
				
				
				
				
				
                        
                <div class="buttons">            
					<button onclick="jQuery('#to').val('cart');" class="btn btn-transparent-dark button_prod_buy"><i class="icon-basket"></i></button>
					
                    <?php if ($this->enable_wishlist){?>
						<button onclick="jQuery('#to').val('wishlist');" class="btn btn-transparent-dark button_wish"><i class="icon-heart"></i></button>
                    <?php }?>
                    
                    <?php print $this->_tmp_product_html_buttons;?>
                </div>
                
                <div id="jshop_image_loading" style="display:none"></div>
            </div>
        <?php }?>
	
	
	</div>
	</div>
	
	<div class="clear"></div>
	
	
	<?php print $this->_tmp_product_html_after_buttons;?>
	
	<div class="button_back">
	    <?php if ($this->config->product_show_button_back){?>
            <a  onclick="<?php print $this->product->button_back_js_click;?>" href="#" class="btn btn-tr-shop"><?php print _JSHOP_BACK;?></a>
    <?php }?>
	</div>
	
		<input type="hidden" name="to" id='to' value="cart" />
        <input type="hidden" name="product_id" id="product_id" value="<?php print $this->product->product_id?>" />
        <input type="hidden" name="category_id" id="category_id" value="<?php print $this->category_id?>" />
</form>

<!-- Desctop Description !-->
<div class="prod_rev_description">

	
	
	
	
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#description" aria-controls="home" role="tab" data-toggle="tab"><i class="icon-note"> </i> <?php print JText::_('_JSHOP_PROD_DESCRIPTION');?></a></li>
		<li role="presentation"><a href="#additional_info" aria-controls="profile" role="tab" data-toggle="tab"><i class="icon-info"> </i> <?php print JText::_('_JSHOP_PROD_ADDITIONAL');?></a></li>
		<li role="presentation"><a href="#prod_comments" aria-controls="messages" role="tab" data-toggle="tab"><i class="icon-bubbles"> </i> <?php print _JSHOP_REVIEWS;?> <span class="comm_count"> <?php if($this->product->reviews_count){print "+ ". $this->product->reviews_count;}else{print 0;} ?></span> </a></li>
	</ul>
	
	
	
	
	
	<div class="tab-content">
	
		<div role="tabpanel" id="description" class="main_description tab-pane active">
			 <div class="jshop_prod_description">
				<?php print $this->product->description; ?>
			</div>    
		</div>

		<div role="tabpanel" id="additional_info" class="additional_info tab-pane">
		
		
		
		
		
		
		
		<?php // //////////////// Characteristics //////////////////////// ?>
			<?php if (is_array($this->product->extra_field)){?>
				<div class="extra_fields">
				<?php foreach($this->product->extra_field as $extra_field){?>
					<?php if ($extra_field['grshow']){?>
						<div class='block_efg'>
						<div class='extra_fields_group'><?php print $extra_field['groupname']?></div>
					<?php }?>
					
					<div class="extra_fields_el">
						<span class="extra_fields_name"><?php print $extra_field['name'];?></span><?php if ($extra_field['description']){?> 
							<span class="extra_fields_description">
								<?php print $extra_field['description'];?>
							</span><?php } ?>:
						<span class="extra_fields_value">
							<?php print $extra_field['value'];?>
						</span>
					</div>
									
					<?php if ($extra_field['grshowclose']){?>
						</div>
					<?php }?>
				<?php }?>
				</div>
			<?php }?>
		<?php // //////////////// END Characteristics //////////////////////// ?>
		
		<?php // //////////////// Shippings //////////////////////// ?>
		<?php if ($this->config->show_tax_in_product && $this->product->product_tax > 0){?>
            <div class="taxinfo"><?php print productTaxInfo($this->product->product_tax);?></div>
        <?php }?>
        
        <?php if ($this->config->show_plus_shipping_in_product){?>
            <div class="plusshippinginfo"><?php print sprintf(_JSHOP_PLUS_SHIPPING, $this->shippinginfo);?></div>
        <?php }?>
        
        <?php if ($this->config->product_show_weight && $this->product->product_weight > 0){?>
            <div class="productweight"><?php print _JSHOP_WEIGHT?>: <span id="block_weight"><?php print formatweight($this->product->getWeight())?></span></div>
        <?php }?>

        <?php if ($this->product->product_basic_price_show){?>
            <div class="prod_base_price"><?php print _JSHOP_BASIC_PRICE?>: <span id="block_basic_price"><?php print formatprice($this->product->product_basic_price_calculate)?></span> / <?php print $this->product->product_basic_price_unit_name;?></div>
        <?php }?>
		
		<?php // //////////////// Manufactures //////////////////////// ?>
		<?php if ($this->config->product_show_manufacturer && $this->product->manufacturer_info->name!=""){?>
            <div class="manufacturer_name">
                <?php print _JSHOP_MANUFACTURER?>: <span><?php print $this->product->manufacturer_info->name?></span>
            </div>
        <?php }?>
		
		</div>
		
		<div role="tabpanel" id="prod_comments" class="reviews tab-pane">
			<?php
			print $this->_tmp_product_html_before_review;
			include(dirname(__FILE__)."/review.php");
			?>
		</div>

	
	</div>
	
	<?php 
		print $this->_tmp_product_html_before_related;
		include(dirname(__FILE__)."/related.php");
	?>
	
</div>

	
	
<!-- Mobile Description !-->	
<div class="panel-group prod_rev_mob_description" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne">
      <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          <?php print _JSHOP_PROD_DESCRIPTION?>
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
		<div class="panel-body">
         <div class="jshop_prod_description">
				<?php print $this->product->description; ?>
			</div>   
		</div>
    </div>
  </div>
  
	<div class="panel panel-default">
		<div class="panel-heading" role="tab" id="headingTwo">
		  <h4 class="panel-title">
			<a role="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
			  <?php print _JSHOP_PROD_ADDITIONAL?>
			</a>
		  </h4>
		</div>
		<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
			<div class="panel-body">
			 <div class="jshop_prod_additional">
							<?php // //////////////// Characteristics //////////////////////// ?>
					<?php if (is_array($this->product->extra_field)){?>
						<div class="extra_fields">
						<?php foreach($this->product->extra_field as $extra_field){?>
							<?php if ($extra_field['grshow']){?>
								<div class='block_efg'>
								<div class='extra_fields_group'><?php print $extra_field['groupname']?></div>
							<?php }?>
							
							<div class="extra_fields_el">
								<span class="extra_fields_name"><?php print $extra_field['name'];?></span><?php if ($extra_field['description']){?> 
									<span class="extra_fields_description">
										<?php print $extra_field['description'];?>
									</span><?php } ?>:
								<span class="extra_fields_value">
									<?php print $extra_field['value'];?>
								</span>
							</div>
											
							<?php if ($extra_field['grshowclose']){?>
								</div>
							<?php }?>
						<?php }?>
						</div>
					<?php }?>
				<?php // //////////////// END Characteristics //////////////////////// ?>
				
				<?php // //////////////// Shippings //////////////////////// ?>
		<?php if ($this->config->show_tax_in_product && $this->product->product_tax > 0){?>
            <div class="taxinfo"><?php print productTaxInfo($this->product->product_tax);?></div>
        <?php }?>
        
        <?php if ($this->config->show_plus_shipping_in_product){?>
            <div class="plusshippinginfo"><?php print sprintf(_JSHOP_PLUS_SHIPPING, $this->shippinginfo);?></div>
        <?php }?>
        
        <?php if ($this->config->product_show_weight && $this->product->product_weight > 0){?>
            <div class="productweight"><?php print _JSHOP_WEIGHT?>: <span id="block_weight"><?php print formatweight($this->product->getWeight())?></span></div>
        <?php }?>

        <?php if ($this->product->product_basic_price_show){?>
            <div class="prod_base_price"><?php print _JSHOP_BASIC_PRICE?>: <span id="block_basic_price"><?php print formatprice($this->product->product_basic_price_calculate)?></span> / <?php print $this->product->product_basic_price_unit_name;?></div>
        <?php }?>
		
		<?php // //////////////// Manufactures //////////////////////// ?>
		<?php if ($this->config->product_show_manufacturer && $this->product->manufacturer_info->name!=""){?>
            <div class="manufacturer_name">
                <?php print _JSHOP_MANUFACTURER?>: <span><?php print $this->product->manufacturer_info->name?></span>
            </div>
        <?php }?>
				
				</div>   
			</div>
		</div>
	  </div>


<div class="panel panel-default">
		<div class="panel-heading" role="tab" id="headingThree">
		  <h4 class="panel-title">
			<a role="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
			  <?php print _JSHOP_REVIEWS?> <span class="comm_count"> +<?php if($this->product->reviews_count){print $this->product->reviews_count;} ?></span>
			</a>
		  </h4>
		</div>
		<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
			<div class="panel-body">
			 <div class="jshop_prod_review">
						
			<?php
			print $this->_tmp_product_html_before_review;
			include(dirname(__FILE__)."/review.php");
			
			print $this->_tmp_product_html_before_related;
			include(dirname(__FILE__)."/related.php");
			?>
						
				</div>   
			</div>
		</div>
	  </div>

	  

</div>
	
</div>


