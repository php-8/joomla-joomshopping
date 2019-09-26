<div class="latest_products jshop jshop_list_product owl-carousel-last-product">

<?php if(count($rows)) foreach($rows as $product){ ?>
	 <div class="block_item block_product product">
<?php 	if ($show_image && $product->image){// option modul  show_image ?>
		<div class="image">
			<div class="image_block">
<?php 		print $product->_tmp_var_image_block;?>
<?php 		if($product->label_id && $show_image_label){?>		
				<div class="product_label">
<?php 			if($product->_label_image){?>
					<img src="<?php print $product->_label_image?>" alt="<?php print htmlspecialchars($product->_label_name)?>" />
<?php			}else{?>
					<span class="label_name"><?php print $product->_label_name;?></span>
<?php			}?>						 
				</div>
<?php 		}?>
				<a href="<?php print $product->product_link?>">
					<img class="jshop_img" src="<?php print $product->image ? $product->image : $noimage;?>" alt="<?php print htmlspecialchars($product->name);?>" />
				</a>
				<div class="maz_overlay_wrap"><div class="maz_overlay_wrap_box"><div><a class="btn-view" href="<?php print $product->product_link?>" itemprop="url"><i class="icon-eye"></i></a></div></div></div>
			</div>
		</div>
<?php 	} ?>

		<div class="name">
            <a href="<?php print $product->product_link?>"><?php print $product->name?></a>
            <?php if ($jshopConfig->product_list_show_product_code){?><span class="jshop_code_prod">(<?php print _JSHOP_EAN?>: <span><?php print $product->product_ean;?></span>)</span><?php }?>
        </div>

<?php	if($allow_review){	// option modul allow_review ?>
		<table class="review_mark"><tr><td><?php print showMarkStar($product->average_rating);?></td></tr></table>
		<div class="count_commentar">
<?php 		print "<i class='icon-bubbles'></i> " . $product->reviews_count;?>
		</div>
<?php 	} ?>

<?php 	print $product->_tmp_var_bottom_foto;?>
<?php	if($display_price){?>
<?php 		if ($product->_display_price){// option modul display_price?>
		<span class = "jshop_price">
<?php 		if ($jshopConfig->product_list_show_price_description) print _JSHOP_PRICE.": ";?>
<?php 		if ($product->show_price_from) print _JSHOP_FROM." ";?>
			<span><?php print formatprice($product->product_price);?><?php print $product->_tmp_var_price_ext;?></span>
		</span>
<?php 		}?>
<?php 	print $product->_tmp_var_bottom_price;?>
<?php 	}?>
<?php	if( $product_old_price){?>
<?php 		if ($product->product_old_price > 0){// option modul product_old_price?>
		<span class="old_price"><?php if ($jshopConfig->product_list_show_price_description) print _JSHOP_OLD_PRICE.": ";?><span><?php print formatprice($product->product_old_price)?></span></span>
<?php 		}?>
<?php 	print $product->_tmp_var_bottom_old_price;?>
<?php 	}?>
	</div>	
	
<?php print $product->_tmp_var_end?>

<?php } ?>

</div>