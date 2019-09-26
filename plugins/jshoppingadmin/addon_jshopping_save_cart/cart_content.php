<?php
    $jshopConfig = JSFactory::getConfig();
    if($result->type_cart=='cart') $type_cart=_JSHOP_CART;
    if($result->type_cart=='wishlist') $type_cart=_JSHOP_WISHLIST;

?>
<script>
    jQuery(document).ready(function() 
    {
        var cart_content = jQuery("#cart-content").html();
        
        jQuery("#cart-content").remove();
        
        jQuery(".col100:first").append(cart_content);
    });
</script>

<div id="cart-content">
    <fieldset class="adminform">
        <legend><?php print $type_cart?> (<?php echo $date_create; ?>)</legend>
        <table class="jshop cart table table-striped">
            <tr>
                <th width="20%">
                  <?php print _JSHOP_IMAGE?>
                </th>
                <th>
                  <?php print _JSHOP_ITEM?>
                </th>    
                <th width="15%">
                  <?php print _JSHOP_SINGLEPRICE?>
                </th>
                <th width="10%">
                  <?php print _JSHOP_QUANTITY?>
                </th>
                <th width="15%">
                  <?php print _JSHOP_PRICE_TOTAL?>
                </th>
                <th width="10%">
                  <?php print _JSHOP_DELETE?>
                </th>
            </tr>
            <?php foreach ($products as $k=>$p) { ?>
            <tr>
                <td>
                    <img src="<?php echo $jshopConfig->image_product_live_path."/".($p["thumb_image"] == '' ? 'noimage.gif' : $p["thumb_image"]); ?>" />
                </td>
                <td>
                    <a href="index.php?option=com_jshopping&controller=products&task=edit&product_id=<?php print $p['product_id']?>">
                        <?php print $p['product_name']?>
                    </a>
                    <div>                        
                        <?php print sprintAtributeInCart($p['attributes_value']); ?>
                        <?php print sprintFreeAtributeInCart($p['free_attributes_value']); ?>
                    </div>
                </td>
                <td>
                    <?php print formatprice($p['price'])?>
                    <?php print $p['_ext_price_html']?>
                </td>
                <td>
                    <?php echo $p["quantity"]; ?>
                </td>
                <td>
                    <?php print formatprice($p['price']*$p['quantity']); ?>
                    <?php print $p['_ext_price_total_html']?>
                </td>
                <td>
                    <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=users&task=edit&user_id=<?php print $user_id?>&acs_act=delete&pn=<?php print $k?>&cart_type=<?php print $result->type_cart?>' onclick="return confirm('<?php print _JSHOP_DELETE?>?')">
                        <i class="icon-delete"></i>
                    </a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </fieldset>
</div>