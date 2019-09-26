<?php 
/**
* @version      4.9.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="jshop list_product" id="comjshop_list_product">

<div id="products" class="list_product">
<?php print $this->_tmp_list_products_html_start?>
<?php foreach ($this->rows as $k=>$product) : ?>
    <?php if ($k % $this->count_product_to_row == 0) : ?>
			<div class = "mat_prod_row">
				<?php endif; ?>
		
				<div class = "">
					<div class = "block_product">
						<?php include(dirname(__FILE__)."/".$product->template_block_product);?>
					</div>
				</div>
				
				<?php if ($k % $this->count_product_to_row == $this->count_product_to_row - 1) : ?>
	
			</div>
    <?php endif; ?>
<?php endforeach; ?>

<?php if ($k % $this->count_product_to_row != $this->count_product_to_row - 1) : ?>
    <div class = "clearfix"></div>
	</div>
    </div>
<?php endif; ?>
<?php print $this->_tmp_list_products_html_end;?>
</div>