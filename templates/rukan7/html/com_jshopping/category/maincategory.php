<?php 
/**
* @version      4.10.5 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

print $this->_tmp_maincategory_html_start;
?>
<?php if ($this->params->get('show_page_heading') && $this->params->get('page_heading')){?>
<div class="shophead<?php print $this->params->get('pageclass_sfx');?>">
    <h1><?php print $this->params->get('page_heading')?></h1>
</div>
<?php }?>

<div class="jshop" id="comjshop">
    <div class="category_description">
        <?php print $this->category->description?>
    </div>

    <div class="jshop_list_category">
    <?php if (count($this->categories)) : ?>
    
        <?php foreach ($this->categories as $k => $category) : ?>
            <?php if ($k % $this->count_category_to_row == 0) : ?>
                <div class = "col-xs-4">
            <?php endif; ?>
        
            <div class = "sblock<?php echo $this->count_category_to_row;?> jshop_categ category">
			<?php if (!$category->category_image){?>
			<a href = "<?php print $category->category_link;?>">
                <div class="sblock2 image prod_no_image">
                    
					
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
			</a>
			<?php } else{?>
			  <div class="sblock2 image">
                    <a href = "<?php print $category->category_link;?>">
                        <img class = "jshop_img  <?php if (!$category->category_image) print 'prod_no_image'; ?>" src = "<?php print $this->image_category_path;?>/<?php if ($category->category_image) print $category->category_image; ?>" alt="<?php print htmlspecialchars($category->name);?>" title="<?php print htmlspecialchars($category->name);?>" />
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
                <div class="sblock2">
                    <div class="category_name">
                        <a class = "product_link" href = "<?php print $category->category_link?>">
                            <?php print $category->name?>
                        </a>
                    </div>
                    <p class = "category_short_description">
                        <?php print $category->short_description?>
                    </p>
                </div>
            </div>
            
            <?php if ($k % $this->count_category_to_row == $this->count_category_to_row - 1) : ?>
                <div class = "clearfix"></div>
                </div>
            <?php endif; ?>
        <?php endforeach;?>
        
        <?php if ($k % $this->count_category_to_row != $this->count_category_to_row - 1) : ?>
            <div class = "clearfix"></div>
            </div>
        <?php endif; ?>
        
    <?php endif; ?>
    </div>
    
    <?php print $this->_tmp_maincategory_html_end;?>
</div>