<?php 
$document = JFactory::getDocument();
$document->addStyleSheet(Juri::base() . "modules/mod_jshopping_ajax_filters/css/simple-line-icons.css",'text/css',"screen");
$document->addStyleSheet(Juri::base() . "modules/mod_jshopping_ajax_filters/css/jquery-ui.theme.min.css",'text/css',"screen");
$document->addStyleSheet(Juri::base() . "modules/mod_jshopping_ajax_filters/css/jquery-ui.structure.min.css",'text/css',"screen");
$document->addStyleSheet(Juri::base() . "modules/mod_jshopping_ajax_filters/css/polaris/polaris.css",'text/css',"screen");
$document->addStyleSheet(Juri::base() . "modules/mod_jshopping_ajax_filters/css/mat_jshopping_ajax_filters.css",'text/css',"screen");

if($load_jquery == 1){JHtml::script(Juri::base() . 'modules/mod_jshopping_ajax_filters/js/jquery-ui.min.js');}
if($load_javascript == 1){JHtml::script(Juri::base() . 'modules/mod_jshopping_ajax_filters/js/jshopping_ajax_filters.js');}
JHtml::script(Juri::base() . 'modules/mod_jshopping_ajax_filters/js/jquery-ui.min.js');
JHtml::script(Juri::base() . 'modules/mod_jshopping_ajax_filters/js/icheck.js');

?>

<div class="jshop_filters">
<form action="<?php print $_SERVER['REQUEST_URI'];?>" method="get" id="mat_js_filter" name="jshop_filters">

    <?php if (is_array($filter_manufactures) && count($filter_manufactures)) {?>
    <input type="hidden" name="manufacturers[]" value="0" />
    <span class="box_manufacrurer">
        <div class="box_manufacrurer_title"><?php print JText::_('MANUFACTURER').":"?></div>
        <?php foreach($filter_manufactures as $v){ ?>
        <input type="checkbox" name="manufacturers[]" value="<?php print $v->id;?>" <?php if (in_array($v->id, $manufacturers)) print "checked";?> > <?php print $v->name;?><br/>
        
		<?php }?>
    </span>
    <?php }?>
    
    <?php if (is_array($filter_categorys) && count($filter_categorys)) {?>
    <input type="hidden" name="categorys[]" value="0" />
    <span class="box_manufacrurer">
        <?php print JText::_('CATEGORY').":"?><br/>
        <?php foreach($filter_categorys as $v){ ?>
        <input type="checkbox" name="categorys[]" value="<?php print $v->id;?>" <?php if (in_array($v->id, $categorys)) print "checked";?>> <?php print $v->name;?><br/>
        <?php }?>
    </span>
    <?php }?>
    
    <?php if ($show_prices){?>
    <div class="filter_price"><span class="filter_price_title"><?php print JText::_('PRICE')?>:</span> <span class="mat_spinner_block"></span>

		<div class="range_slider_output">
			<div class="fprice_left_box"><input type = "text" class = "inputbox fprice_from" placeholder="<?php if ($fprice_from>0){print $fprice_from;} else{print 0;}?>" name = "fprice_from" id="fprice_from" size="7" value="<?php if ($fprice_from>0) {print $fprice_from;} else {print 0;}?>" /></div>
			<div class="fprice_right_box"><input type = "text" class = "inputbox fprice_to" placeholder="<?php  print $max_price ?>" name = "fprice_to"  id="fprice_to" size="7" value="<?php if ($fprice_to>0) print $fprice_to?>" /></div>
		</div>
		<div class="range_slider">
			<a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
			<a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
		</div>
    </div>    
    <span class="clear_filter"><a href="#"><i class="icon-refresh"></i> <?php print JText::_('RESET FILTER')?></a></span>
    <?php }?>
    
    <?php if (is_array($characteristic_displayfields) && count($characteristic_displayfields)){?>
    <br/>
        <div class="filter_characteristic">
        <?php foreach($characteristic_displayfields as $ch_id){?>   
            <?php if (is_array($characteristic_fieldvalues[$ch_id])){?>
                <div class="characteristic_name"><?php print $characteristic_fields[$ch_id]->name;?></div>
                <input type="hidden" name="extra_fields[<?php print $ch_id?>][]" value="0" />            
                <?php foreach($characteristic_fieldvalues[$ch_id] as $val_id=>$val_name){?>
                    <input type="checkbox" name="extra_fields[<?php print $ch_id?>][]" value="<?php print $val_id;?>" <?php if (is_array($extra_fields_active[$ch_id]) && in_array($val_id, $extra_fields_active[$ch_id])) print "checked";?> onclick="document.jshop_filters.submit();" /> <?php print $val_name;?><br/>
                <?php }?>
            <br/>
            <?php }?>
        <?php }?>
        </div>
    <?php } ?>
</form>
<input type="hidden" name="max_price" id="max_price" value="<?php print $max_price;?>" />

</div>