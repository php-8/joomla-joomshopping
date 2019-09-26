<?php 
/**
* @version      4.8.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="col-xs-12 col-md-8  col-sm-8 prod-filter">
<form action="<?php print $this->action;?>" method="post" name="sort_count" id="sort_count" class="form-horizontal">
<div class="form_sort_count">
<?php if ($this->config->show_sort_product || $this->config->show_count_select_products) : ?>
<div class="block_sorting_count_to_page row">
    <?php if ($this->config->show_sort_product) : ?>
        <div class="control-group box_products_sorting">
            <div class="control-label">
                <?php print _JSHOP_ORDER_BY.": "; ?>
            </div>
            <div class="controls">
                <?php echo $this->sorting?>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($this->config->show_count_select_products) : ?>
        <div class="control-group box_products_count_to_page">
            <div class="control-label">
                <?php print _JSHOP_DISPLAY_NUMBER.": "; ?>
            </div>
            <div class="controls">
                <?php echo $this->product_count?>
            </div>
        </div>
    <?php endif; ?>
	<?php if ($this->filter_show_manufacturer) : ?>
                <div class="control-group box_manufacrurer">
					<div class="man_mob_center">
						<div class="control-label">
							<?php print _JSHOP_MANUFACTURER.": "; ?>
						</div>
						<div class="controls">
							<?php echo $this->manufacuturers_sel; ?>
						</div>
					</div>
                </div>
    <?php endif; ?>
</div>
<?php endif; ?>


</div>
<input type="hidden" name="orderby" id="orderby" value="<?php print $this->orderby?>" />
<input type="hidden" name="limitstart" value="0" />
</form>

</div>

<div class="col-xs-12 col-md-4  col-sm-4 grid-list">
	<div class="grid-list-buttons">
			<a href="#" id="list" class="btn btn-default btn-sm"><i class="icon-list"></i></a> 
			<a href="#" id="grid" class="btn btn-default btn-sm"><i class="icon-grid"></i></a>
	</div>
</div>