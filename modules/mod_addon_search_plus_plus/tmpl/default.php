<?php
    /*
    * @version      1.1.9 26.01.2019
    * @author       Garry
    * @package      update.php
    * @copyright    Copyright (C) 2019 joom-shopping.com. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die;

    JHtml::_('jquery.framework');
    
    JFactory::getDocument()->addScriptOptions(
        'changable_qty',
        [
            'qty_min' => $qty_min,
            'qty_max' => $qty_max
        ]
    );
    JHtml::stylesheet('modules/' . $module->module . '/css/module.css');
    JHtml::script('modules/'     . $module->module . '/js/module.js');
?>

<div class="addon_search_plus_plus" >

<form name="searchForm" method="POST" action="<?php echo $action; ?>">
        <input type="hidden" name="search_type"   value="<?php echo $search_type; ?>">
        <input type="hidden" name="setsearchdata" value="1">
        <div class="search-result">
            <input
                type="text"
                name="search"
                class="inputbox"
                id="jshop_search"
                placeholder="<?php echo _JSHOP_SEARCH_RESULT; ?>"
                value="<?php echo $search; ?>"
                <?php if ($results_popup) { ?>
                    oninput="this.onfocus();"
                    onfocus="
                        var include_subcat = jQuery('.addon_search_plus_plus [name=include_subcat]');
                        include_subcat.val(+include_subcat.is(':checked'));
                        AddonSearchPlusPlus.search(
                            this,
                            <?php
                                foreach ([
                                    'search_type',
                                    'category_id',
                                    'include_subcat',
                                    'manufacturer_id',
                                    'price_from',
                                    'price_to',
                                    'date_from',
                                    'date_to'
                                ] as $var) {
                                    echo (
                                        'String(jQuery(\'.addon_search_plus_plus [name=' . $var . ']\').val() || \'' . $$var . '\')' .
                                        ($var == 'date_to' ? '' : ', ')
                                    );
                                }
                            ?>
                        );
                    "
                <?php } ?>
            >
            <?php if ($reset_search) { ?>
                <div class="reset_search"></div>
            <?php } ?>

            <input type="submit" value="<?php echo JText::_('MOD_ADDON_SEARCH_PLUS_PLUS_SEARCH_SEARCH'); ?>">
            <div class="popup"></div>
        </div>
        <div class="filters">
            <?php if ($filter_search_type) { ?>
                <div class="control-group search_type">
                    <div class="control-label">
                        <?php echo _JSHOP_SEARCH_FOR; ?>
                    </div>
                    <div class="controls">
                        <?php echo $search_type_list; ?>
                    </div>
                </div>
            <?php } ?>
            <?php if ($filter_categories) { ?>
                <div class="control-group categories">
                    <div class="control-label">
                        <?php echo _JSHOP_SEARCH_CATEGORIES; ?>
                    </div>
                    <div class="controls">
                        <?php echo $categories_list; ?>
                        <div class="include_subcat">
                            <label>
                                <input type="checkbox" name="include_subcat" value="1"<?php if ($include_subcat) echo ' checked'; ?>>
                                <span>
                                    <?php echo _JSHOP_SEARCH_INCLUDE_SUBCAT; ?>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if ($filter_manufacturers) { ?>
                <div class="control-group manufacturers">
                    <div class="control-label">
                        <?php echo _JSHOP_SEARCH_MANUFACTURERS; ?>
                    </div>
                    <div class="controls">
                        <?php echo $manufacturers_list; ?>
                    </div>
                </div>
            <?php } ?>
            <?php if ($filter_price_from) { ?>
                <div class="control-group price_from">
                    <div class="control-label">
                        <?php echo _JSHOP_SEARCH_PRICE_FROM . ' (' . $jshopConfig->currency_code . ')'; ?>
                    </div>
                    <div class="controls">
                        <input type="text" class="input" name="price_from" value="<?php echo $price_from ? $price_from : ''; ?>">
                    </div>
                </div>
            <?php } ?>
            <?php if ($filter_price_to) { ?>
                <div class="control-group price_to">
                    <div class="control-label">
                      <?php echo _JSHOP_SEARCH_PRICE_TO . ' (' . $jshopConfig->currency_code . ')'; ?>
                    </div>
                    <div class="controls">
                        <input type="text" class="input" name="price_to" value="<?php echo $price_to ? $price_to : ''; ?>">
                    </div>
                </div>
            <?php } ?>
            <?php if ($filter_date_from) { ?>
                <div class="control-group date_from">
                    <div class="control-label">
                        <?php echo _JSHOP_SEARCH_DATE_FROM; ?>
                    </div>
                    <div class="controls">
                        <?php
                            echo JHtml::_(
                                'calendar',
                                $date_from,
                                'date_from',
                                'date_from',
                                '%Y-%m-%d',
                                [
                                    'class'     => 'inputbox',
                                    'maxlength' => '19',
                                    'size'      => '25'
                                ]
                            );
                        ?>
                    </div>
                </div>
            <?php } ?>
            <?php if ($filter_date_to) { ?>
                <div class="control-group date_to">
                    <div class="control-label">
                        <?php echo _JSHOP_SEARCH_DATE_TO; ?>
                    </div>
                    <div class="controls">
                        <?php
                            echo JHtml::_(
                                'calendar',
                                $date_to,
                                'date_to',
                                'date_to',
                                '%Y-%m-%d',
                                [
                                    'class'     => 'inputbox',
                                    'maxlength' => '19',
                                    'size'      => '25'
                                ]
                            );
                        ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php if ($advanced_search_link) { ?>
            <div class="advanced_search_link">
                <a href="<?php echo $advanced_search; ?>">
                    <?php echo _JSHOP_ADVANCED_SEARCH; ?>
                </a>
            </div>
        <?php } ?>
    </form>
</div>
<?php if ($homepage && $sitelinks) { ?>

    <!--Sitelinks Search Box-->

    <script type="application/ld+json">
        {
          "@context"       : "http://schema.org",
          "@type"          : "WebSite",
          "url"            : "<?php echo JUri::base(); ?>",
          "potentialAction": {
            "@type"        : "SearchAction",
            "target"       : "<?php echo JUri::base(); ?>index.php?option=com_jshopping&controller=search&task=result&setsearchdata=1&search_type=<?php echo $search_type; ?>&category_id=<?php echo $category_id; ?>&include_subcat=<?php echo $include_subcat; ?>&search={search_term_string}",
            "query-input"  : "required name=search_term_string"
          }
        }
    </script>
<?php } ?>
<br>