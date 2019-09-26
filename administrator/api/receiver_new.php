<?php
header("Content-Type:application/json");

$method = new Receiver;

switch($_REQUEST['method']) {

   case 'check_connect':

   if(!empty($_GET['method'])) {
      $check = $method->check_connect();
      if(empty($check)) {
         jsonResponse(200,"ERROR", NULL);
      } else {
         jsonResponse(200,"SUCCESS", $check);
      }
}
break;	


case 'get_new_products':

if(!empty($_GET['method'])) {
   $new_products = $method->get_new_products();
   if(empty($new_products)) {
      jsonResponse(200,"ERROR", NULL);
   } else {
      jsonResponse(200,"SUCCESS", $new_products);
   }
}
break;	


case 'get_new_images':

if(!empty($_GET['method'])) {
   $new_images = $method->get_new_images();
   if(empty($new_images)) {
      jsonResponse(200,"ERROR", NULL);
   } else {
      jsonResponse(200,"SUCCESS", $new_images);
   }
}
break;


case 'get_new_products_to_categiry':

if(!empty($_GET['method'])) {
   $new_products_to_categiry = $method->get_new_products_to_categiry();
   if(empty($new_products_to_categiry)) {
      jsonResponse(200,"ERROR", NULL);
   } else {
      jsonResponse(200,"SUCCESS", $new_products_to_categiry);
   }
}
break;






   case 'update_meta':
   if(!empty($_POST['metaid'])) {

      $img_image_id = $_POST['img_image_id'];
      $metaid = $_POST['metaid']; 
      $metatitle = $_POST['metatitle'];
      $metadescription = $_POST['metadescription']; 
      $metakeywords = $_POST['metakeywords'];

      $updater = $method->updater($metaid, $metatitle, $metadescription, $metakeywords);

      if(empty($updater)) {
         jsonResponse(200,"ERROR", NULL);
      } else {
         jsonResponse(200,"SUCCESS", $updater);
      }
}
break;	

   case 'update_imgname':
   if(!empty($_POST['imgid'])) {

      $imgid = $_POST['imgid']; 
      $imgname = $_POST['imgname'];

      $updateimg = $method->updateimg($imgid, $imgname);

      if(empty($updateimg)) {
         jsonResponse(200,"ERROR", NULL);
      } else {
         jsonResponse(200,"Image name updated", $updateimg);
      }
}
break;	

   case 'set_category':
   if(!empty($_POST['category_id'])) {

      $category_category_id  = $_POST['category_id'];
      $category_category_image = $_POST['category_image'];
      $category_category_parent_id = $_POST['category_parent_id'];
      $category_category_publish = $_POST['category_publish'];
      $category_category_ordertype = $_POST['category_ordertype'];
      $category_category_template = $_POST['category_template'];
      $category_ordering = $_POST['ordering'];
      $category_category_add_date = $_POST['category_add_date'];
      $category_products_page = $_POST['products_page'];
      $category_products_row = $_POST['products_row'];
      $category_access = $_POST['access'];
      $category_name_en_GB = $_POST['name_en-GB'];
      $category_alias_en_GB = $_POST['alias_en-GB'];
      $category_short_description_en_GB = $_POST['short_description_en-GB'];
      $category_description_en_GB = $_POST['description_en-GB'];
      $category_meta_title_en_GB =$_POST['meta_title_en-GB'];
      $category_meta_description_en_GB = $_POST['meta_description_en-GB'];
      $category_meta_keyword_en_GB = $_POST['meta_keyword_en-GB'];
      $category_name_ru_RU = $_POST['name_ru-RU'];
      $category_alias_ru_RU = $_POST['alias_ru-RU'];
      $category_short_description_ru_RU = $_POST['short_description_ru-RU'];
      $category_description_ru_RU = $_POST['description_ru-RU'];
      $category_meta_title_ru_RU = $_POST['meta_title_ru-RU'];
      $category_meta_description_ru_RU = $_POST['meta_description_ru-RU'];
      $category_meta_keyword_ru_RU = $_POST['meta_keyword_ru-RU'];
      $category_sync_id = $_POST['category_id'];

      $set_category = $method->set_category($category_category_id, $category_category_image, $category_category_parent_id,
      $category_category_publish,
      $category_category_ordertype,
      $category_category_template,
      $category_ordering,
      $category_category_add_date,
      $category_products_page,
      $category_products_row,
      $category_access,
      $category_name_en_GB,
      $category_alias_en_GB,
      $category_short_description_en_GB,
      $category_description_en_GB,
      $category_meta_title_en_GB,
      $category_meta_description_en_GB,
      $category_meta_keyword_en_GB,
      $category_name_ru_RU,
      $category_alias_ru_RU,
      $category_short_description_ru_RU,
      $category_description_ru_RU,
      $category_meta_title_ru_RU,
      $category_meta_description_ru_RU,
      $category_meta_keyword_ru_RU,
      $category_sync_id
      );

      if(empty($set_category)) {
         jsonResponse(200,"ERROR", NULL);
      } else {
         jsonResponse(200,"SUCCESS", $set_category);
      }
}
break;	

   case 'set_products_img':
   if(!empty($_POST['img_image_id'])) {

      $img_image_id = $_POST['img_image_id'];
      $img_product_id = $_POST['img_product_id'];
      $img_image_name = $_POST['img_image_name'];
      $img_name = $_POST['img_name'];
      $img_ordering = $_POST['img_ordering'];

      $set_product_img = $method->set_product_img($img_image_id, $img_product_id, $img_image_name, $img_name, $img_ordering);

      if(empty($set_product_img)) {
         jsonResponse(200,"ERROR", NULL);
      } else {
         jsonResponse(200,"SUCCESS", $set_product_img);
      }
}
break;	

   case 'set_product_to_category':
   if(!empty($_POST['category_product_id'])) {

      $category_product_id = $_POST['category_product_id'];
      $category_category_id = $_POST['category_category_id'];
      $category_product_ordering = $_POST['category_product_ordering'];

      $set_product = $method->set_product_to_category($category_product_id, $category_category_id, $category_product_ordering);

      if(empty($set_product)) {
         jsonResponse(200,"ERROR", NULL);
      } else {
         jsonResponse(200,"SUCCESS",$set_product);
      }
}
break;	

   case 'set_products':
   if(!empty($_POST['product_id'])) {
     $product_id11 = $_POST['product_id'];
     $parent_id11 = $_POST['parent_id'];
     $product_ean11 = $_POST['product_ean'];
     $product_quantity11 = $_POST['product_quantity'];
     $unlimited11= $_POST['unlimited'];
     $product_availability11 = $_POST['product_availability'];
     $product_date_added11 = $_POST['product_date_added'];
     $date_modify11 = $_POST['date_modify'];
     $product_publish11 = $_POST['product_publish'];
     $product_tax_id11 = $_POST['product_tax_id'];
     $currency_id11 = $_POST['currency_id'];
     $product_template11 = $_POST['product_template'];
     $product_url11 = $_POST['product_url'];
     $product_old_price11 = $_POST['product_old_price'];
     $product_buy_price11 = $_POST['product_buy_price'];
     $product_price11 = $_POST['product_price'];
     $min_price11 = $_POST['min_price'];
     $different_prices11 = $_POST['different_prices'];
     $product_weight11 = $_POST['product_weight'];
     $product_name_image11 = $_POST['product_name_image']; 
     $product_manufacturer_id11 = $_POST['product_manufacturer_id']; 
     $product_is_add_price11 = $_POST['product_is_add_price'];
     $add_price_unit_id11 = $_POST['add_price_unit_id'];
     $average_rating11 = $_POST['average_rating']; 
     $reviews_count11 = $_POST['reviews_count']; 
     $delivery_times_id11 = $_POST['delivery_times_id']; 
     $hits11 = $_POST['hits']; 
     $weight_volume_units11 = $_POST['weight_volume_units'];
     $basic_price_unit_id11 = $_POST['basic_price_unit_id'];
     $label_id11 = $_POST['label_id']; 
     $vendor_id11 = $_POST['vendor_id'];
     $access11 = $_POST['access']; 
     $name_en_GB11 = $_POST['name_en_GB']; 
     $alias_en_GB11 =  $_POST['alias_en_GB']; 
     $short_description_en_GB11 = $_POST['short_description_en_GB'];
     $description_en_GB11 = $_POST['description_en_GB']; 
     $meta_title_en_GB11 = $_POST['meta_title_en_GB'];
     $meta_description_en_GB11 = $_POST['meta_description_en_GB'];
     $meta_keyword_en_GB11 = $_POST['meta_keyword_en_GB'];
     $name_ru_RU11 = $_POST['name_ru_RU'];
     $alias_ru_RU11 = $_POST['alias_ru_RU'];
     $short_description_ru_RU11 = $_POST['short_description_ru_RU']; 
     $description_ru_RU11 = $_POST['description_ru_RU'];
     $meta_title_ru_RU11 = $_POST['meta_title_ru_RU'];
     $meta_keyword_ru_RU11 = $_POST['meta_keyword_ru_RU'];
     $meta_description_ru_RU11 = $_POST['meta_description_ru_RU'];
     $product_bonus_add11 = $_POST['product_bonus_add'];
     $product_bonus_sub11 = $_POST['product_bonus_sub'];
     $ballance11 = $_POST['ballance'];
     $ext_id11 = $_POST['ext_id'];
  
     $items = $method->set_product($product_id11, $parent_id11, $product_ean11, $product_quantity11, $unlimited11, $product_availability11, $product_date_added11, $date_modify11, $product_publish11, $product_tax_id11, $currency_id11, 
     $product_template11,  $product_url11, $product_old_price11, $product_buy_price11, $product_price11, $min_price11, $different_prices11, $product_weight11, $product_name_image11, $product_manufacturer_id11, 
     $product_is_add_price11,  $add_price_unit_id11, $average_rating11, $reviews_count11, $delivery_times_id11, $hits11, $weight_volume_units11, $basic_price_unit_id11,  $label_id11, $vendor_id11, $access11, $name_en_GB11, 
     $alias_en_GB11, $short_description_en_GB11, $description_en_GB11, $meta_title_en_GB11, $meta_description_en_GB11, $meta_keyword_en_GB11, $name_ru_RU11, $alias_ru_RU11, $short_description_ru_RU11, $description_ru_RU11, 
     $meta_title_ru_RU11, $meta_keyword_ru_RU11, $meta_description_ru_RU11, $product_bonus_add11, $product_bonus_sub11, $ballance11);

      if(empty($items)) {
      jsonResponse(200, "ERROR", $items);
      } else {
      jsonResponse(200, "SUCCESS", $items);
      }
      } else {
      jsonResponse(400, "Invalid Request", NULL);
      }

break;

default:
echo 'ERROR';

}
   function jsonResponse($status,$status_message,$data) {
   header("HTTP/1.1 ".$status_message);
   $response['status']=$status;
   $response['status_message']=$status_message;
   $response['data']=$data;
   $json_response = json_encode($response);
   echo $json_response;
   }


   class Connect {
      protected $dbconnect;
      public function __construct() {
  
          function getDB() {
              $dbhost= 'localhost';
              $dbuser= 'admin';
              $dbpass= 'password';
              $dbname= 'printervoronezh';
          
              try {
              $dbConnection = new \PDO("mysql:host=$dbhost; dbname=$dbname", $dbuser, $dbpass); 
              $dbConnection->exec("set names utf8");
              $dbConnection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
              return $dbConnection;
              }
              catch (PDOException $e) {
              echo 'Connection failed: ' . $e->getMessage();
              }
          }
      
          $this->dbconnect = getDB();
      
          return $this->dbconnect;
      }
  }


Class receiver extends Connect {

public function __construct() {
      parent::__construct();
}

public function check_connect() {
      $sqlquerry = "SELECT * FROM s1y5k_jshopping_products ORDER BY `product_id` DESC LIMIT 10";
      $check_connect = $this->dbconnect->prepare($sqlquerry);
      if($check_connect->execute()) {
      $response = $check_connect->fetchAll(PDO::FETCH_ASSOC);
      return $response;
      }
      else {
      return 'ERROR';
   } 
}

public function get_new_products() {
   $sqlq = "SELECT `product_date_added`, `date_modify`, `product_template`, `product_weight`, `image`, `name_ru-RU`, `alias_ru-RU`, `short_description_ru-RU`, `description_ru-RU`, `meta_title_ru-RU`, `meta_description_ru-RU`, `meta_keyword_ru-RU` FROM s1y5k_jshopping_products WHERE `product_id` > 16147 ORDER BY `product_id`";
   $get_new_products = $this->dbconnect->prepare($sqlq);
   if($get_new_products->execute()) {
   $response = $get_new_products->fetchAll(PDO::FETCH_ASSOC);
   return $response;
   }
   else {
   return 'ERROR';
} 
}


public function get_new_images() {
   $qlq = "SELECT `product_id`, `name_ru-RU`, `image` FROM s1y5k_jshopping_products WHERE `product_id` > 16147 ORDER BY `product_id`";
   $get_new_images = $this->dbconnect->prepare($qlq);
   if($get_new_images->execute()) {
   $response = $get_new_images->fetchAll(PDO::FETCH_ASSOC);
   return $response;
   }
   else {
   return 'ERROR';
} 
}


public function get_new_products_to_categiry() {
   $lq = "SELECT `product_id` FROM s1y5k_jshopping_products WHERE `product_id` > 16147 ORDER BY `product_id`";
   $get_new_products_to_categiry = $this->dbconnect->prepare($lq);
   if($get_new_products_to_categiry->execute()) {
   $response = $get_new_products_to_categiry->fetchAll(PDO::FETCH_ASSOC);
   return $response;
   }
   else {
   return 'ERROR';
} 
}
















public function updater($metaid1, $metatitle1, $metadescription1, $metakeywords1) {

$updatesql = "UPDATE s1y5k_jshopping_products SET `meta_title_ru-RU`=:metatitle, `meta_description_ru-RU`=:metadescription, `meta_keyword_ru-RU`=:metakeywords WHERE product_id=:productid";

    $updatequery = $this->dbconnect->prepare($updatesql);
    $updatequery->bindParam(':metatitle', $metatitle1);
    $updatequery->bindParam(':metadescription', $metadescription1);
    $updatequery->bindParam(':metakeywords', $metakeywords1);
    $updatequery->bindParam(':productid', $metaid1);
    if($updatequery->execute())
    {
    return 'SUCCESS';
    }
    else
    {
    return 'ERROR';
    } 
}

public function updateimg($imageid, $imagename) {

$updateimgsql = "UPDATE s1y5k_jshopping_products_images SET `name`=:imgname WHERE image_id=:imgid";

    $updateq = $this->dbconnect->prepare($updateimgsql);
    $updateq->bindParam(':imgid', $imageid);
    $updateq->bindParam(':imgname', $imagename);
    if($updateq->execute())
    {
    return 'SUCCESS';
    }
    else
    {
    return 'ERROR';
    } 

}

public function set_product_img($img_image_id1, $img_product_id1, $img_image_name1, $img_name1, $img_ordering1) {

      $quer = "INSERT into s1y5k_jshopping_products_images (`image_id`, `product_id`, `image_name`, `name`, `ordering`) VALUES(:image_id, :product_id, :image_name, :name, :ordering); ";

      $img = $this->dbconnect->prepare($quer);
      $img->bindParam(':image_id', $img_image_id1);
      $img->bindParam(':product_id', $img_product_id1);
      $img->bindParam(':image_name', $img_image_name1);
      $img->bindParam(':name', $img_name1);
      $img->bindParam(':ordering', $img_ordering1);

      if($img->execute())
      {
         return 'upload';
      } 
   }

public function set_product_to_category($category_product_id1, $category_category_id1, $category_product_ordering1) {

      $query = "INSERT into s1y5k_jshopping_products_to_categories (`product_id`, `category_id`, `product_ordering`) VALUES(:product_id, :category_id, :product_ordering); ";

      $sq = $this->dbconnect->prepare($query);
      $sq->bindParam(':product_id', $category_product_id1);
      $sq->bindParam(':category_id', $category_category_id1);
      $sq->bindParam(':product_ordering', $category_product_ordering1);

      if($sq->execute())
      {
         return 'upload';
      } 
   }

public function set_product($product_id1, $parent_id1, $product_ean1, $product_quantity1, $unlimited1, $product_availability1, $product_date_added1, $date_modify1, $product_publish1, $product_tax_id1, $currency_id1, 
   $product_template1,  $product_url1, $product_old_price1, $product_buy_price1, $product_price1, $min_price1, $different_prices1, $product_weight1, $product_name_image1, $product_manufacturer_id1, 
   $product_is_add_price1,  $add_price_unit_id1, $average_rating1, $reviews_count1, $delivery_times_id1, $hits1, $weight_volume_units1, $basic_price_unit_id1,  $label_id1, $vendor_id1, $access1, $name_en_GB1, 
   $alias_en_GB1, $short_description_en_GB1, $description_en_GB1, $meta_title_en_GB1, $meta_description_en_GB1, $meta_keyword_en_GB1, $name_ru_RU1, $alias_ru_RU1, $short_description_ru_RU1, $description_ru_RU1, 
   $meta_title_ru_RU1, $meta_keyword_ru_RU1, $meta_description_ru_RU1, $product_bonus_add1, $product_bonus_sub1) {

      $sql = "INSERT into s1y5k_jshopping_products (`product_id`, `parent_id`, `product_ean`, `product_quantity`, `unlimited`, `product_availability`, `product_date_added`, `date_modify`, 
      `product_publish`, `product_tax_id`, `currency_id`, `product_template`,  `product_url`, `product_old_price`, `product_buy_price`, `product_price`, `min_price`, `different_prices`, `product_weight`, `image`, 
      `product_manufacturer_id`, `product_is_add_price`, `add_price_unit_id`, `average_rating`, `reviews_count`, `delivery_times_id`, `hits`, `weight_volume_units`, `basic_price_unit_id`, `label_id`, `vendor_id`, 
      `access`, `name_en-GB`, `alias_en-GB`, `short_description_en-GB`, `description_en-GB`, `meta_title_en-GB`, `meta_description_en-GB`, `meta_keyword_en-GB`, 
      `name_ru-RU`, `alias_ru-RU`, `short_description_ru-RU`, `description_ru-RU`, `meta_title_ru-RU`, `meta_description_ru-RU`, `meta_keyword_ru-RU`, `product_bonus_add`, `product_bonus_sub`) 
      VALUES(
      :product_id, 
      :parent_id, 
      :product_ean, 
      :product_quantity, 
      :unlimited, 
      :product_availability, 
      :product_date_added, 
      :date_modify, 
      :product_publish, 
      :product_tax_id, 
      :currency_id, 
      :product_template, 
      :product_url, 
      :product_old_price, 
      :product_buy_price, 
      :product_price, 
      :min_price, 
      :different_prices, 
      :product_weight, 
      :image1, 
      :product_manufacturer_id, 
      :product_is_add_price, 
      :add_price_unit_id, 
      :average_rating, 
      :reviews_count, 
      :delivery_times_id, 
      :hits, 
      :weight_volume_units, 
      :basic_price_unit_id, 
      :label_id, 
      :vendor_id, 
      :access, 
      :name_en_GB, 
      :alias_en_GB, 
      :short_description_en_GB, 
      :description_en_GB, 
      :meta_title_en_GB, 
      :meta_description_en_GB, 
      :meta_keyword_en_GB, 
      :name_ru_RU, 
      :alias_ru_RU, 
      :short_description_ru_RU, 
      :description_ru_RU, 
      :meta_title_ru_RU, 
      :meta_description_ru_RU, 
      :meta_keyword_ru_RU, 
      :product_bonus_add, 
      :product_bonus_sub); ";
      
          $q = $this->dbconnect->prepare($sql);
          $q->bindParam(':product_id', $product_id1);
          $q->bindParam(':parent_id', $parent_id1);
          $q->bindParam(':product_ean', $product_ean1);
          //$q->bindParam(':manufacturer_code', $nullvalue);
          $q->bindParam(':product_quantity', $product_quantity1);
          $q->bindParam(':unlimited', $unlimited1);
          $q->bindParam(':product_availability', $product_availability1);
          $q->bindParam(':product_date_added', $product_date_added1);
          $q->bindParam(':date_modify', $date_modify1);
          $q->bindParam(':product_publish', $product_publish1);
          $q->bindParam(':product_tax_id', $product_tax_id1);
          $q->bindParam(':currency_id', $currency_id1);
          $q->bindParam(':product_template', $product_template1);
          $q->bindParam(':product_url', $product_url1);
          $q->bindParam(':product_old_price', $product_old_price1);
          $q->bindParam(':product_buy_price', $product_buy_price1);
          $q->bindParam(':product_price', $product_price1);
          $q->bindParam(':min_price', $min_price1);
          $q->bindParam(':different_prices', $different_prices1);
          $q->bindParam(':product_weight', $product_weight1);
          $q->bindParam(':image1', $product_name_image1);
          $q->bindParam(':product_manufacturer_id', $product_manufacturer_id1);
          $q->bindParam(':product_is_add_price', $product_is_add_price1);
          $q->bindParam(':add_price_unit_id', $add_price_unit_id1);
          $q->bindParam(':average_rating', $average_rating1);
          $q->bindParam(':reviews_count', $reviews_count1);
          $q->bindParam(':delivery_times_id', $delivery_times_id1);
          $q->bindParam(':hits', $hits1);
          $q->bindParam(':weight_volume_units', $weight_volume_units1);
          $q->bindParam(':basic_price_unit_id', $basic_price_unit_id1);
          $q->bindParam(':label_id', $label_id1);
          $q->bindParam(':vendor_id', $vendor_id1);
          $q->bindParam(':access', $access1);
          $q->bindParam(':name_en_GB', $name_en_GB1);
          $q->bindParam(':alias_en_GB', $alias_en_GB1);
          $q->bindParam(':short_description_en_GB', $short_description_en_GB1);
          $q->bindParam(':description_en_GB', $description_en_GB1);
          $q->bindParam(':meta_title_en_GB', $meta_title_en_GB1);
          $q->bindParam(':meta_description_en_GB', $meta_description_en_GB1);
          $q->bindParam(':meta_keyword_en_GB', $meta_keyword_en_GB1);
          $q->bindParam(':name_ru_RU', $name_ru_RU1);
          $q->bindParam(':alias_ru_RU', $alias_ru_RU1);
          $q->bindParam(':short_description_ru_RU', $short_description_ru_RU1);
          $q->bindParam(':description_ru_RU', $description_ru_RU1);
          $q->bindParam(':meta_title_ru_RU', $meta_title_ru_RU1);
          $q->bindParam(':meta_description_ru_RU', $meta_description_ru_RU1);
          $q->bindParam(':meta_keyword_ru_RU', $meta_keyword_ru_RU1);
          $q->bindParam(':product_bonus_add', $product_bonus_add1);
          $q->bindParam(':product_bonus_sub', $product_bonus_sub1);
          if($q->execute())
          {
             return 'upload';
         } 
      } 

public function set_category($category_category_id1, $category_category_image1, $category_category_parent_id1,
   $category_category_publish1,
   $category_category_ordertype,
   $category_category_template1,
   $category_ordering1,
   $category_category_add_date1,
   $category_products_page1,
   $category_products_row1,
   $category_access1,
   $category_name_en_GB1,
   $category_alias_en_GB1,
   $category_short_description_en_GB1,
   $category_description_en_GB1,
   $category_meta_title_en_GB1,
   $category_meta_description_en_GB1,
   $category_meta_keyword_en_GB1,
   $category_name_ru_RU1,
   $category_alias_ru_RU1,
   $category_short_description_ru_RU1,
   $category_description_ru_RU1,
   $category_meta_title_ru_RU1,
   $category_meta_description_ru_RU1,
   $category_meta_keyword_ru_RU1,
   $category_sync_id1) {
      $qe = "INSERT into s1y5k_jshopping_categories(`category_id`,
      `category_image`,
      `category_parent_id`,
      `category_publish`,
      `category_template`,
      `ordering`,
      `category_add_date`,
      `products_page`,
      `products_row`,
      `access`,
      `name_en-GB`,
      `alias_en-GB`,
      `short_description_en-GB`,
      `description_en-GB`,
      `meta_title_en-GB`,
      `meta_description_en-GB`,
      `meta_keyword_en-GB`,
      `name_ru-RU`,
      `alias_ru-RU`,
      `short_description_ru-RU`,
      `description_ru-RU`,
      `meta_title_ru-RU`,
      `meta_description_ru-RU`,
      `meta_keyword_ru-RU`,
      `sync_id`) VALUES(:category_id,
      :category_image,
      :category_parent_id,
      :category_publish,
      :category_template,
      :ordering,
      :category_add_date,
      :products_page,
      :products_row,
      :access,
      :name_en_GB,
      :alias_en_GB,
      :short_description_en_GB,
      :description_en_GB,
      :meta_title_en_GB,
      :meta_description_en_GB,
      :meta_keyword_en_GB,
      :name_ru_RU,
      :alias_ru_RU,
      :short_description_ru_RU,
      :description_ru_RU,
      :meta_title_ru_RU,
      :meta_description_ru_RU,
      :meta_keyword_ru_RU,
      :sync_id
      ); ";

      $category = $this->dbconnect->prepare($qe);
      $category->bindParam(':category_id', $category_category_id1);
      $category->bindParam(':category_image', $category_category_image1);
      $category->bindParam(':category_parent_id', $category_category_parent_id1);
      $category->bindParam(':category_publish', $category_category_publish1);
      $category->bindParam(':category_template', $category_category_template1);
      $category->bindParam(':ordering', $category_ordering1);
      $category->bindParam(':category_add_date', $category_category_add_date1);
      $category->bindParam(':products_page', $category_products_page1);
      $category->bindParam(':products_row', $category_products_row1);
      $category->bindParam(':access', $category_access1);
      $category->bindParam(':name_en_GB', $category_name_en_GB1);
      $category->bindParam(':alias_en_GB', $category_alias_en_GB1);
      $category->bindParam(':short_description_en_GB', $category_short_description_en_GB1);
      $category->bindParam(':description_en_GB', $category_description_en_GB1);
      $category->bindParam(':meta_title_en_GB', $category_meta_title_en_GB1);
      $category->bindParam(':meta_description_en_GB', $category_meta_description_en_GB1);
      $category->bindParam(':meta_keyword_en_GB', $category_meta_keyword_en_GB1);
      $category->bindParam(':name_ru_RU', $category_name_ru_RU1);
      $category->bindParam(':alias_ru_RU', $category_alias_ru_RU1);
      $category->bindParam(':short_description_ru_RU', $category_short_description_ru_RU1);
      $category->bindParam(':description_ru_RU', $category_description_ru_RU1);
      $category->bindParam(':meta_title_ru_RU', $category_meta_title_ru_RU1);
      $category->bindParam(':meta_description_ru_RU', $category_meta_description_ru_RU1);
      $category->bindParam(':meta_keyword_ru_RU', $category_meta_keyword_ru_RU1);
      $category->bindParam(':sync_id', $category_category_id1);
      if($category->execute())
      {
         return 'upload';
      } 
   }
}
?>
