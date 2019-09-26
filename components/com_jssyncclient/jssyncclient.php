<?php

defined('_JEXEC') or die('Restricted access');
$start_timestamp = time();

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);
ini_set('max_execution_time', 180);

define('RUNTIME_LIMIT', 120);
$cfg_server_base_url = 'http://vce-o-printere.ru/index.php?option=com_jssync&format=raw&action=';
$cfg_log_file = __DIR__ . '/tmp/sync.log';
$cfg_last_from_file = __DIR__ . '/tmp/last_from.txt';
$cfg_sync_product_fields = [
    'product_publish',
    'product_quantity',
    'unlimited',
    'product_old_price',
    'product_buy_price',
    'product_price',
    'min_price',
	'currency_id',
];

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-type: text/plain; charset=utf-8");

require_once(JPATH_BASE . '/components/com_jshopping/lib/factory.php');
require_once(JPATH_BASE . '/components/com_jshopping/lib/functions.php');

$app = JFactory::getApplication('site');
$app->initialise();
$db = JFactory::getDBO();
$jshopConfig = JSFactory::getConfig();

/* // не используется в оставленной части решения
//get list tax
$query = "SELECT tax_id, tax_value FROM `#__jshopping_taxes`";
$db->setQuery($query);
$rows = $db->loadObjectList();
$listTax = array();
foreach ($rows as $row) {
    $listTax[intval($row->tax_value)] = $row->tax_id;
}

//get list category
$query = "SELECT category_id as id, `" . $lang->get("name") . "` as name FROM `#__jshopping_categories`";
$db->setQuery($query);
$rows = $db->loadObjectList();
$listCat = array();
foreach ($rows as $row) {
    $listCat[$row->name] = $row->id;
}

$_products = JSFactory::getModel('products', 'JshoppingModel');
*/

// file_put_contents($cfg_log_file, date('Y-m-d H:i:s') . " > Start\n\n", FILE_APPEND);

$last_from = (file_exists($cfg_last_from_file)) ? file_get_contents($cfg_last_from_file) : '0000-00-00 00:00:00';
if (!preg_match('/\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}\:\d{2}/', $last_from)) $last_from = '0000-00-00 00:00:00';

$jUpdated = file_get_contents($cfg_server_base_url . 'get_products_updated_from&from=' . str_replace(' ', 'T', $last_from));
usleep(100);

$updated = json_decode($jUpdated);

if (is_array($updated)) {
    foreach ($updated as $row) {

        ob_start();
        echo date('Y-m-d H:i:s') . " > Update product id={$row->product_id}\n";

        $product = JSFactory::getTable('product', 'jshop');
        $product->load(['product_id' => $row->product_id]);

        if ($product instanceof jshopProduct && $product->product_id>0) {

            echo "Product found:\n";

            $updated = false;

            foreach ($cfg_sync_product_fields as $field) {
                if ($product->{$field} != $row->{$field}) {
                    echo " - {$field}: {$product->{$field}} => {$row->{$field}}\n";
                    $product->set($field, $row->{$field});
                    $updated = true;
                }
            }

            if ($updated) {
                $product->store();
                echo " - stored\n";
            } else {
                echo " - not modified\n";
            }
        } else {

            echo "PRODUCT NOT FOUND - skipping\n";
            /*

            // Товары имеют очень большое количество связей и зависимостей как внутри БД так и с файлами, и функционалом.
            // Корректно импортировать их таким способом нереально - нужно искать другой подход.

            $jNewProduct = file_get_contents($cfg_server_base_url . 'get_product&id=' . $row->product_id);
            usleep(100);

            $new_product = json_decode($jNewProduct);

            if ($new_product) {

                $tax_value = $new_product->tax;
                if (!isset($listTax[$tax_value])) {
                    $tax = JSFactory::getTable('tax', 'jshop');
                    $tax->set('tax_name', $tax_value);
                    $tax->set('tax_value', $tax_value);
                    $tax->store();
                    $listTax[$tax_value] = $tax->get("tax_id");
                }

                $category_name = $new_product->category_name;
                if (!isset($listCat[$category_name]) && $category_name != "") {

                    if ($new_product->category_parent_id) {
                        try {
                            $query = "SELECT `category_id` FROM `#__jshopping_categories` WHERE `sync_id`={$new_product->category_parent_id}";
                            $db->setQuery($query);
                            $category_parent_id = $db->loadResult();
                        } catch (Exception $e) {
                            echo "Parent category not found with exception: " . $e->getMessage();
                            $category_parent_id = 0;
                        }
                    } else {
                        $category_parent_id = 0;
                    }

                    $cat = JSFactory::getTable("category", "jshop");
                    $query = "SELECT max(ordering) FROM `#__jshopping_categories`";
                    $db->setQuery($query);
                    $ordering = $db->loadResult() + 1;
                    $cat->set($lang->get("name"), $category_name);
                    $cat->set("products_page", $jshopConfig->count_products_to_page);
                    $cat->set("products_row", $jshopConfig->count_products_to_row);
                    $cat->set("category_publish", $new_product->category_publish);
                    $cat->set("ordering", $ordering);
                    $cat->set("category_parent_id", $category_parent_id);
                    $cat->store();
                    $listCat[$category_name] = $cat->get("category_id");
                }

                if ($new_product->parent_id) {
                    try {
                        $query = "SELECT `prodyct_id` FROM `#__jshopping_products` WHERE `sync_id`={$new_product->parent_id}";
                        $db->setQuery($query);
                        $parent_id = $db->loadResult();
                    } catch (Exception $e) {
                        echo "Parent product not found with exception: " . $e->getMessage();
                        $parent_id = 0;
                    }
                } else {
                    $parent_id = 0;
                }

                $product = JSFactory::getTable('product', 'jshop');
                $product->set("parent_id", $parent_id);
                $product->set("product_ean", $new_product->product_ean);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("unlimited", $new_product->unlimited);
                $product->set("product_publish", $new_product->product_publish);
                $product->set("product_old_price", $new_product->product_old_price);
                $product->set("product_buy_price", $new_product->product_buy_price);
                $product->set("product_price", $new_product->product_price);
                $product->set("min_price", $new_product->min_price);
                $product->set("different_prices", $new_product->different_prices);
                $product->set("product_weight", $new_product->product_weight);

                $product->set("product_thumb_image", $new_product->product_quantity);
                $product->set("product_name_image", $new_product->product_quantity);
                $product->set("product_full_image", $new_product->product_quantity);

                $product->set("product_manufacturer_id", $new_product->product_quantity);



                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);
                $product->set("product_quantity", $new_product->product_quantity);


                $product->set("product_tax_id", $listTax[$tax_value]);
                $product->set("currency_id", $jshopConfig->mainCurrency);
                $product->set($lang->get("name"), utf8_encode($row[7]));
                $product->set($lang->get("short_description"), utf8_encode($row[8]));
                $product->set($lang->get("description"), utf8_encode($row[9]));
                $product->store();
                $product_id = $product->get("product_id");
                $category_id = $listCat[$category_name];
                if ($category_name != "" && $category_id) {
                    $_products->setCategoryToProduct($product_id, array($category_id));
                }

                unset($product);
            } else {
                echo "--FAIL-- new product JSON not recognized^\n{$jNewProduct}\n\n";
                break;
            }
            */
        }

        $last_from = $row->date_modify;
        file_put_contents($cfg_last_from_file, $last_from);
        echo "\n\n";

        $run_time = (time() - $start_timestamp);
        if ($run_time > RUNTIME_LIMIT) {
            echo "\n--BREAK-- by RUNTIME_LIMIT ({$run_time} > " . RUNTIME_LIMIT . ")\n";
        }

        $ob = ob_get_flush();
        file_put_contents($cfg_log_file, $ob, FILE_APPEND);

        if ($run_time > RUNTIME_LIMIT) {
            break;
        }
    }
}


echo "Last from: {$last_from}\n\n";
