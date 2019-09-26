<script type = "text/javascript">
function isEmptyValue(value){
    var pattern = /\S/;
    return ret = (pattern.test(value)) ? (true) : (false);
}
</script>
<form class="form-inline mod-shop-search" name = "searchForm" method = "post" action="<?php print SEFLink("index.php?option=com_jshopping&controller=search&task=result", 1);?>" onsubmit = "return isEmptyValue(jQuery('#jshop_search').val())">
<input type="hidden" name="setsearchdata" value="1">
<input type = "hidden" name = "category_id" value = "<?php print $category_id?>" />
<input type = "hidden" name = "search_type" value = "<?php print $search_type;?>" />
<input type = "text" class = "inputbox" placeholder="<?php print _JSHOP_SEARCH?>" name = "search" id = "jshop_search" value = "<?php print $search?>" />
<button type="submit" class="btn btn-primary"><i class="icon-magnifier"></i></button>
<?php if ($adv_search) {?>
<div class="adv_search_link">
    <a href = "<?php print $adv_search_link?>"><?php print _JSHOP_ADVANCED_SEARCH?></a>
</div>
<?php } ?>
</form>