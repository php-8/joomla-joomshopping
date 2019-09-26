<?php 
/**
* @version      4.11.0 17.09.2015
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

print $this->_tmp_category_html_start;
?>
<div class="jshop" id="comjshop">
    <h1 class="mat_category_title"><?php print $this->category->name?></h1>
    <div class="category_description">
        <?php print $this->category->description?>
    </div>

    <?php if (count($this->categories)) : ?>
	 <div class="jshop_list_category">
        <div class = "jshop list_category">
            <?php foreach($this->categories as $k=>$category) : ?>
            
                <?php if ($k % $this->count_category_to_row == 0) : ?>
                    <div class = "row-fluid col-md-4">
                <?php endif; ?>
                
                <div class = "sblock<?php echo $this->count_category_to_row; ?> jshop_categ category">
				
				<?php if ($category->category_image){?>
                    <div class = " image">
                        <a href = "<?php print $category->category_link;?>">
                            <img class = "jshop_img " src = "<?php print $this->image_category_path;?>/<?php print $category->category_image; ?>"  />
						</a>
                    
					
					<div class="maz_overlay_wrap">
						<div class="maz_overlay_wrap_box">
							<div>
								<a class="btn-view" href="<?php print $category->category_link;?>" itemprop="url">
									<i class="icon-link"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
				
				<?php } else { ?>
				
				<div class = "image">
				 
					<a href = "<?php print $category->category_link;?>">
					 <div class = "prod_no_image"></div>
					</a>
                
					<div class="maz_overlay_wrap">
						<div class="maz_overlay_wrap_box">
							<div>
								<a class="btn-view" href="<?php print $category->category_link;?>" itemprop="url">
									<i class="icon-link"></i>
								</a>
							</div>
						</div>
					</div>
				
				</div>
			
				
				<?php } ?>
				</div>
                    <div class = "">
                        <div class="category_name">
                            <a class = "product_link" href = "<?php print $category->category_link?>">
                                <?php print $category->name?>
                            </a>
                        </div>
                                            
                    </div>
                
                
                <?php if ($k % $this->count_category_to_row == $this->count_category_to_row - 1) : ?>
                    <div class = "clearfix"></div>
                    </div>
                <?php endif; ?>
                
            <?php endforeach; ?>
            
            <?php if ($k % $this->count_category_to_row != $this->count_category_to_row - 1) : ?>
                <div class = "clearfix"></div>
                </div>
            <?php endif; ?>
            
        </div>
    <?php endif; ?>
    </div>
	
	<?php print $this->_tmp_category_html_before_products;?>
        
    <?php include(dirname(__FILE__)."/products.php");?>
	
	<?php print $this->_tmp_category_html_end;?>
</div>