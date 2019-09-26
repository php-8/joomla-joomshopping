<?php
    /*
    * @version      1.1.9 26.01.2019
    * @author       Garry
    * @package      update.php
    * @copyright    Copyright (C) 2019 joom-shopping.com. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die;

    require_once JPATH_SITE . '/components/com_jshopping/lib/factory.php';
    require_once JPATH_SITE . '/components/com_jshopping/lib/functions.php';

    JSFactory::loadCssFiles();
    JSFactory::loadJsFiles();
    JSFactory::loadLanguageFile();

    $doc                  = JFactory::getDocument();
    $jshopConfig          = JSFactory::getConfig();
    $app                  = JFactory::getApplication();
    $jinp                 = $app->input->post;
    $menu                 = $app->getMenu();
    $addon                = AddonSearchPlusPlus::getInst();
    $homepage             = $menu->getActive() == $menu->getDefault(JFactory::getLanguage()->getTag());
    $action               = $addon->SEFlink('index.php?option=com_jshopping&controller=search&task=result');
    $search               = (string) htmlspecialchars($jinp->getRaw('search'), ENT_QUOTES);
    $search_type          = (string) $jinp->getCmd('search_type', $params->get('default_search_type'));
    $category_id          = (int)    $jinp->getInt('category_id');
    $include_subcat       = (bool)   $jinp->getBool('include_subcat');
    $manufacturer_id      = (int)    $jinp->getInt('manufacturer_id');
    $price_from           = (float)  $jinp->getInt('price_from');
    $price_to             = (float)  $jinp->getInt('price_to');
    $date_from            = (string) $jinp->getCmd('date_from');
    $date_to              = (string) $jinp->getCmd('date_to');
    $results_popup        = (bool)   $params->get('results_popup');
    $filter_search_type   = (bool)   $params->get('filter_search_type');
    $filter_categories    = (bool)   $params->get('filter_categories');
    $filter_manufacturers = (bool)   $params->get('filter_manufacturers');
    $display_price_shop   = (bool)   getDisplayPriceShop();
    $filter_price_from    = (bool)   $display_price_shop && $params->get('filter_price_from');
    $filter_price_to      = (bool)   $display_price_shop && $params->get('filter_price_to');
    $filter_date_from     = (bool)   $params->get('filter_date_from');
    $filter_date_to       = (bool)   $params->get('filter_date_to');
    $advanced_search_link = (bool)   $params->get('advanced_search_link');
    $sitelinks            = (bool)   $params->get('sitelinks');
    $reset_search         = (bool)   $params->get('reset_search');
    $qty_min              = (int)    $jshopConfig->min_count_order_one_product ? $jshopConfig->min_count_order_one_product : 1;
    $qty_max              = (int)    $jshopConfig->max_count_order_one_product;
    $search_type_list     = $filter_search_type ? JHtml::_(
        'select.genericlist',
        [
            'any'   => _JSHOP_ANY_WORDS,
            'all'   => _JSHOP_ALL_WORDS,
            'exact' => _JSHOP_EXACT_WORDS
        ],
        'search_type',
        'class="inputbox"',
        '',
        '',
        $search_type
    ) : '';
    $categories_list      = $filter_categories    ? JshopHelpersSelects::getSearchCategory($category_id)   : '';
    $manufacturers_list   = $filter_manufacturers ? JshopHelpersSelects::getManufacturer($manufacturer_id) : '';
    $advanced_search      = $advanced_search_link ? $addon->SEFlink('index.php?option=com_jshopping&controller=search') : '';
    require JModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default'));
