<?php
    /*
    * @version      1.1.9 26.01.2019
    * @author       Garry
    * @package      update.php
    * @copyright    Copyright (C) 2019 joom-shopping.com. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die;

    $show_categories           = $results_categories    && $categories;
    $show_search_category      = $result_search_in_category && $category_in;
    $show_suggestions          = $results_suggestions   && $suggestions;
    $show_manufacturers        = $results_manufacturers && $manufacturers;
    $show_search_manufacturers = $result_search_in_manufacturers && $manufacturers_in;
    $jshopConfig = JSFactory::getConfig();
?>
<div class="results">
    <?php if ($show_suggestions || $show_categories || $show_manufacturers) { ?>
        <div class="left">
            <?php if ($show_suggestions) { ?>
                <div class="suggestions">
                    <div class="title">
                        <?php echo JText::_('MOD_ADDON_SEARCH_PLUS_PLUS_SUGGESTIONS'); ?>
                    </div>
                    <ul>
                        <?php foreach ($suggestions as $suggestion) { ?>
                            <li>
                                <a href="">
                                    <?php echo $suggestion; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <?php if ($show_categories) { ?>
                <div class="categories">
                    <div class="title">
                        <?php echo _JSHOP_SEARCH_CATEGORIES; ?>
                    </div>
                    <ul>
                        <?php foreach ($categories as $category) { ?>
                            <li>
                                <a href="<?php echo $category->link; ?>">
                                    <?php echo $category->name; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <?php if($show_search_category) { ?>
                <div class="search_in_category">
                    <div class="title">
                        <?php echo _JSHOP_SEARCH_IN_CATEGORY; ?>
                    </div>
                    <ul>
                        <?php foreach ($category_in as $category) { ?>
                            <li>
                                <a href="<?php echo $category->link; ?>">
                                    <?php echo $category->name; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <?php if ($show_manufacturers) { ?>
                <div class="manufacturers">
                    <div class="title">
                        <?php echo _JSHOP_SEARCH_MANUFACTURERS; ?>
                    </div>
                    <ul>
                        <?php foreach ($manufacturers as $manufacturer) { ?>
                            <li>
                                <a href="<?php echo $manufacturer->link; ?>">
                                    <?php echo $manufacturer->name; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <?php if ($show_search_manufacturers) { ?>
                <div class="search_in_manufacturers">
                    <div class="title">
                        <?php echo _JSHOP_SEARCH_IN_MANUFACTURERS ; ?>
                    </div>
                    <ul>
                        <?php foreach ($manufacturers_in as $manufacturer) { ?>
                            <li>
                                <a href="<?php echo $manufacturer->link1; ?>">
                                    <?php echo $manufacturer->name; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <div class="right">
        <div class="products">
            <div class="title">
                <?php echo _JSHOP_PRODUCTS; ?>
            </div>
            <?php if ($products) { ?>
                <table>
                    <?php foreach($products as $product) { ?>
                        <tr class="product">
                            <td class="image">
                                <a class="itemlink" href="<?php echo $product->product_link; ?>">
                                    <span class="img_block">
                                        <img src="<?php echo $product->image; ?>">
                                    </span>
                                </a>
                            </td>
                            <td class="name">
                                <a class="itemlink" href="<?php echo $product->product_link; ?>">
                                    <span class="titlesearch">
                                        <?php echo $product->name; ?>
                                    </span>
                                </a>
                                <input name="product_id" value="<?php echo $product->product_id; ?>" type="hidden">
                            </td>

                            <?php if ($add_to_cart || $add_to_wishlist) { ?>
                                <td class="add_to_cart_wishlist">
                                    <?php if(!empty($product->buy_link) || !empty($product->wishlist_link) ) { ?>
                                        <div class="changable-qty-box">                                        
                                            &nbsp;<input
                                                class="btn qty-plus"
                                                type="button"
                                                value="+"
                                                onclick="AddonSearchPlusPlus.changeQty(this, jQuery(this).siblings('.inputbox').get(0));"
                                            ><!--
                                         -->&nbsp;<input
                                                class="inputbox"
                                                type="text"
                                                value="<?php echo $qty_min; ?>"
                                                name="quantity"
                                            ><!--
                                         -->&nbsp;<input
                                                class="btn qty-minus"
                                                type="button"
                                                value="-"
                                                onclick="AddonSearchPlusPlus.changeQty(this, jQuery(this).siblings('.inputbox').get(0));"
                                            >
                                        </div>
                                    <?php } ?>

                                    <?php if ( $add_to_wishlist && !empty($product->wishlist_link) ) { ?>
                                        <a
                                            href="<?php echo $product->wishlist_link; ?>"
                                            class="btn btn-success button_buy add_to_wishlist"
                                            onclick="return AddonSearchPlusPlus.addTo(this, jQuery(this).closest('td').find('.inputbox').get(0));"
                                            title="<?php echo _JSHOP_ADD_TO_WISHLIST; ?>"
                                        >
                                            <span class="icon-heart"></span>
                                        </a>
                                    <?php } ?>

                                    <?php if ($add_to_cart && !empty($product->buy_link) ) { ?>
                                        <a
                                            href="<?php echo $product->buy_link; ?>"
                                            class="btn btn-success button_buy add_to_cart"
                                            onclick="return AddonSearchPlusPlus.addTo(this, jQuery(this).closest('td').find('.inputbox').get(0));"
                                            title="<?php echo _JSHOP_ADD_TO_CART; ?>"
                                        >
                                            <span class="icon-cart">В корзину</span>
                                        </a>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                            <td class="price">
                                <?php if (getDisplayPriceForProduct($product->product_price)) { ?>
                                    <span class="pricesearch">
                                        <?php echo ($product->show_price_from ? (_JSHOP_FROM . ' ') : '') . formatprice($product->product_price); ?>
                                    </span>
                                <?php } else { ?>
                                    
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                <?php if ($show_all_results_link) { ?>
                    <div class="more_results">
                        <a href="<?php echo $all_results_link; ?>">
                            <?php echo JText::_('MOD_ADDON_SEARCH_PLUS_PLUS_ALL_RESULTS'); ?>
                        </a>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>