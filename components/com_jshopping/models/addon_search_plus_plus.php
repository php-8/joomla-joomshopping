<?php
     /*
    * @version      1.1.9 26.01.2019
    * @author       Garry
    * @package      update.php
    * @copyright    Copyright (C) 2019 joom-shopping.com. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die;

    class JshoppingModelAddon_search_plus_plus extends jshopBase {

        public function getCategories(array $products_all, $max_qty) {
            $res = [];
            foreach($products_all as $product) {
                $category = $this->getProductCategory($product);
                if ($category) {
                    $res[(int) $category->category_id] = $category;
                    if (count($res) >= $max_qty) {
                        break;
                    }
                }
            }
            return $res;
        }

        public function getGategory_in(array $products_all, $max_qty, $search) {
            $res = [];
            foreach($products_all as $product) {
                $category = $this->getProductCategory_in($product, $search);
                if ($category) {
                    $res[(int) $category->category_id] = $category;
                    if (count($res) >= $max_qty) {
                        break;
                    }
                }
            }
            return $res;   
        }

        public function getManufacturers(array $products_all, $max_qty) {
            $res = [];
            foreach($products_all as $product) {
                $manufacturer = $this->getProductManufacturer($product);
                if ($manufacturer) {
                    $res[(int) $manufacturer->id] = $manufacturer;
                    if (count($res) >= $max_qty) {
                        break;
                    }
                }
            }
            return $res;
        }

        public function getManufacturers_in(array $products_all, $max_qty, $search) {
            $res = [];
            foreach($products_all as $product) {
                $manufacturer_in = $this->getProductManufacturer_in($product, $search);
                if ($manufacturer_in) {
                    $res[(int) $manufacturer_in->id] = $manufacturer_in;
                    if (count($res) >= $max_qty) {
                        break;
                    }
                }
            }
            return $res;
        }

        public function getProducts($search, $products_per_query) {
            $app             = JFactory::getApplication();
            $addon           = AddonSearchPlusPlus::getInst();
            $params          = $addon->getModule()->params;
            $jshopConfig     = JSFactory::getConfig();
            $add_to_cart     = (bool) $params->get('add_to_cart');
            $add_to_wishlist = (bool) $params->get('add_to_wishlist');
            $model           = JSFactory::getModel('productssearch', 'jshop');
            $limit           = $model->getCountProductsPerPage();
            $limit_context   = $model->getContext() . 'limit';
            $productlist     = JSFactory::getModel('productList', 'jshop');
            $app->setUserState($limit_context, $products_per_query);
            $productlist->setModel($model);
            $productlist->load();
            $app->setUserState($limit_context, $limit);
            $products        = (array) $productlist->getProducts();
            foreach($products as $product) {

                if ( $add_to_cart && !($jshopConfig->hide_buy_not_avaible_stock && ($product->product_quantity <= 0)) ) {
                    $product->buy_link = $addon->SEFLink(
                        'index.php?option=com_jshopping&controller=cart&task=add&category_id=' .
                        $product->category_id . '&product_id=' . $product->product_id . '&quantity=1'
                    );
                }

                if ( $add_to_wishlist && !($jshopConfig->hide_buy_not_avaible_stock && ($product->product_quantity <= 0)) ) {
                    $product->wishlist_link = $addon->SEFLink(
                        'index.php?option=com_jshopping&controller=cart&to=wishlist&task=add&category_id=' .
                        $product->category_id . '&product_id=' . $product->product_id . '&quantity=1'
                    );
                }


            }
            return $products;
        }

        public function getProductCategory($product) {
            $category       = JSFactory::getTable('category', 'jshop');
            $category->load($product->category_id);
            if (empty($category->category_id)) {
                return [];
            }
            $category->name = $category->{JSFactory::getLang()->get('name')};
            $category->link = AddonSearchPlusPlus::getInst()->SEFLink(
                'index.php?option=com_jshopping&controller=category&category_id=' .
                $category->category_id .
                '&task=view'
            );
            return $category;
        }

        public function getProductCategory_in($product, $search) {
            $category       = JSFactory::getTable('category', 'jshop');
            $category->load($product->category_id);
            if (empty($category->category_id)) {
                return [];
            }
            $category->name = $category->{JSFactory::getLang()->get('name')};
            $category->link = AddonSearchPlusPlus::getInst()->SEFLink(
                'index.php?option=com_jshopping&controller=search&task=result&category_id=' .
                $category->category_id .
                '&search=' . urlencode($search)
            );
            return $category;   
        }

        public function getProductManufacturer($product) {
            $manufacturer = $product->manufacturer;
            if (empty($manufacturer->id)) {
                return [];
            }
            $manufacturer->link = AddonSearchPlusPlus::getInst()->SEFLink(
                'index.php?option=com_jshopping&controller=manufacturer&manufacturer_id=' .
                $manufacturer->id .
                '&task=view'
            );
            return $manufacturer;
        }

        public function getProductManufacturer_in($product, $search) {
            $manufacturer_in = $product->manufacturer;
            if (empty($manufacturer_in->id)) {
                return [];
            }
            $manufacturer_in->link1 = AddonSearchPlusPlus::getInst()->SEFLink(
                'index.php?option=com_jshopping&controller=search&task=result&manufacturer_id=' .
                $manufacturer_in->id .
                '&search=' . urlencode($search)
            );
            return $manufacturer_in;
        }        

        public function getSearchFields() {
            $res = [
                'prod' => [],
                'cat'  => [],
                'man'  => []
            ];
            foreach ((array) JSFactory::getConfig()->product_search_fields as $field) {
                if (strpos($field, ':') === false) {
                    $arr            = explode('.', $field);
                    $res[$arr[0]][] = $arr[1];
                }
                else {
                    $arr                          = explode(':', $field);
                    $res[rtrim($arr[0], '.ml')][] = $arr[1];
                }
            }
            return $res;
        }

        public function getSearchLink(array $args = []) {
            return AddonSearchPlusPlus::getInst()->SEFlink(
                'index.php?option=com_jshopping&controller=search&task=result&' .
                http_build_query(
                    array_filter(
                        $args,
                        function($v, $k) {
                            if (
                                (is_numeric($v) && $v > 0) ||
                                (is_string($v)  && strlen($v))
                            ) {
                                return $v;
                            }
                        },
                        ARRAY_FILTER_USE_BOTH
                    )
                )
            );
        }

        public function getSuggestions(
                  $search,
                  $max_qty,
            array $products,
            array $categories    = [],
            array $manufacturers = []
        ) {
            if (!$search || !$max_qty) {
                return [];
            }
            $res           = [];
            $search_fields = $this->getSearchFields();
            foreach ([
                'prod' => $products,
                'cat'  => $categories,
                'man'  => $manufacturers
            ] as $key => $items) {
                foreach ($search_fields[$key] as $field) {
                    foreach ($items as $item) {
                        if (
                            is_string($item->$field) &&
                            preg_match_all('/\b' . $search . '\w+\b/i', $item->$field, $matches) !== false
                        ) {
                            $res = array_unique(
                                array_merge(
                                    $res,
                                    array_map('strtolower', reset($matches))
                                )
                            );
                            if (count($res) >= $max_qty) {
                                break 3;
                            }
                        }
                    }
                }
            }
            natsort($res);
            return $res;
        }

        public function loadModuleLang(stdClass $module = null) {
            $lang   = JFactory::getLanguage();
            $module = $module ? $module : AddonSearchPlusPlus::getInst()->getModule();
            return $lang->load(
                $module->module,
                JPATH_SITE . '/modules/' . $module->module,
                $lang->getTag(),
                true
            );
        }

    }
