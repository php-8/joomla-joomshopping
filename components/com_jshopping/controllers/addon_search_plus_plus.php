<?php
      /*
    * @version      1.1.9 26.01.2019
    * @author       Garry
    * @package      update.php
    * @copyright    Copyright (C) 2019 joom-shopping.com. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die;

    class JshoppingControllerAddon_search_plus_plus extends JshoppingControllerBase {

        public function __construct(array $config = []) {
            parent::__construct($config);
            if ($this->input->getBool('ajax', false)) {
                $this->ajax();
            }
        }

        protected function ajax() {
            header('Content-Type: application/json');
            die(
                json_encode(
                    call_user_func_array(
                        [
                            $this,
                            $this->input->getCmd('task', $this->taskMap['__default'])
                        ],
                        (array) json_decode($this->input->getString('args', '[]'))
                    )
                )
            );
        }

        public function search(
            $search          = '',
            $search_type     = '',
            $category_id     = 0,
            $include_subcat  = 0,
            $manufacturer_id = 0,
            $price_from      = 0,
            $price_to        = 0,
            $date_from       = '',
            $date_to         = ''
        ) {

            JFactory::getSession()->clear('jshop_end_form_data');
            $jshopConfig                  = JSFactory::getConfig();
            $addon                        = AddonSearchPlusPlus::getInst();
            $model                        = $addon->getModel();
            $module                       = $addon->getModule();
            $params                       = $module->params;
            $categories                   = [];
            $category_in                  = [];
            $suggestions                  = [];
            $manufacturers                = [];
            $manufacturers_in             = [];
            $products_per_query           = (int)  $params->get('products_per_query');
            $results_categories           = (bool) $params->get('results_categories');
            $categories_max_qty           = (int)  $params->get('categories_max_qty');
            $results_suggestions          = (bool) $params->get('results_suggestions');
            $suggestions_max_qty          = (int)  $params->get('suggestions_max_qty');
            $results_manufacturers        = (bool) $params->get('results_manufacturers');
            $manufacturers_max_qty        = (int)  $params->get('manufacturers_max_qty');
            $add_to_cart                  = (bool) $params->get('add_to_cart');
            $add_to_wishlist              = (bool) $params->get('add_to_wishlist');
            $qty_min                      = (int)  $jshopConfig->min_count_order_one_product ? $jshopConfig->min_count_order_one_product : 1;
            $results_max_qty              = (int)  $params->get('results_max_qty');
            $show_all_results_link        = (bool) $params->get('show_all_results_link');
            $all_results_link             = '';
            $result_search_in_category      = (bool) $params->get('result_search_in_category');
            $result_search_in_manufacturers = (bool) $params->get('result_search_in_manufacturers');
            /* get products */
            $products_all = $model->getProducts($search, $products_per_query);
            $products_qty = count($products_all);
            if (!$products_qty) {
                return '';
            }
            if ($products_qty > $results_max_qty) {
                $products = array_slice($products_all, 0, $results_max_qty);
            } else {
                $products = $products_all;
                $show_all_results_link = false;
            }
            /* get categories */
            if ($results_categories) {
                $categories = $model->getCategories($products_all, $categories_max_qty);
            }
            /*get search in category*/
            if($result_search_in_category) {
                $category_in = $model->getGategory_in($products_all, $categories_max_qty, $search);
            }
            /* get manufacturers */
            if ($results_manufacturers) {
                $manufacturers = $model->getManufacturers($products_all, $manufacturers_max_qty);
            }
            /*get search in manufacturers*/
            if ($result_search_in_manufacturers) {
                $manufacturers_in = $model->getManufacturers_in($products_all, $manufacturers_max_qty, $search);
            }
            /* get suggestions */
            if ($results_suggestions && $suggestions_max_qty) {
                $suggestions = $model->getSuggestions(
                    $search,
                    $suggestions_max_qty,
                    $products_all,
                    $results_categories    ? $categories    : [],
                    $results_manufacturers ? $manufacturers : []
                );
            }
            /* get all results link */
            if ($show_all_results_link) {
                $all_results_link = $model->getSearchLink([
                    'search'          => (string) $search,
                    'search_type'     => (string) $search_type,
                    'category_id'     => (int)    $category_id,
                    'include_subcat'  => (int)    $include_subcat,
                    'manufacturer_id' => (int)    $manufacturer_id,
                    'price_from'      => (float)  $price_from,
                    'price_to'        => (float)  $price_to,
                    'date_from'       => (string) $date_from,
                    'date_to'         => (string) $date_to,
                    'setsearchdata'   => 0
                ]);
            }
            /* return */
            $model->loadModuleLang();
            ob_start();
            require JModuleHelper::getLayoutPath($module->module, 'result');
            return ob_get_clean();
        }

		private function checkLicKey() {
            $addon = AddonSearchPlusPlus::getInst();
			return true;
		}

    }
