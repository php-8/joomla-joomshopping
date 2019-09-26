<?php
header("Content-Type:application/json");

$method = new Uploader;

switch($_REQUEST['method']) {

    case 'update_price':

    if(!empty($_POST['method'])) {
      $name = $_POST['name'];
      $price = $_POST['price'];
      $update_price = $method->update_price($price, $name);
       if(empty($update_price)) {
       jsonResponse(200, "Error", NULL);
       } else {
       jsonResponse(400, "Success", $update_price);
     }
 }
 
 break;

 case 'update':

 if(!empty($_POST['method'])) {
    $productid = $_POST['productid'];
    $productdescription = $_POST['productdescription'];
    $update = $method->update($productid, $productdescription);
    if(empty($update)) {
    jsonResponse(200, "Error", NULL);
    } else {
    jsonResponse(400, "Success", $update);
  }
}

break;


    case 'select_description':

    if(!empty($_GET['method'])) {
      $prefix = $_GET['prefix'];
      $select_description = $method->select_description($prefix);
       if(empty($select_description)) {
       jsonResponse(200, "Error", NULL);
       } else {
       jsonResponse(400, "Success", $select_description);
     }
 }
 
 break;


   case 'set_products':

   if(!empty($_POST['method'])) {
     $date_added = $_POST['product_date_added'];
     $modify = $_POST['date_modify'];
     $template = $_POST['product_template'];
     $weight = $_POST['product_weight'];
     $thumb_image = $_POST['product_thumb_image']; 
     $name_image = $_POST['product_name_image']; 
     $full_image  = $_POST['product_full_image']; 
     $name_ru = $_POST['name_ru_RU'];
     $alias_ru = $_POST['alias_ru_RU'];
     $short_description_ru = $_POST['short_description_ru_RU']; 
     $description_ru = $_POST['description_ru_RU'];
     $meta_title_ru = $_POST['meta_title_ru_RU'];
     $meta_keyword_ru = $_POST['meta_keyword_ru_RU'];
     $meta_description_ru = $_POST['meta_description_ru_RU'];

     $set_item = $method->set_product($date_added, $modify, $template, $weight, $thumb_image, $name_image, $full_image, $name_ru, $alias_ru, $short_description_ru, $description_ru, $meta_title_ru, $meta_keyword_ru, $meta_description_ru);

      if(empty($set_item)) {
      jsonResponse(200, "Error", $set_item);
      } else {
      jsonResponse(400, "Success", NULL);
    }
}

break;


case 'Set_new_images':

if(!empty($_POST['method'])) {

    $img_product_id = $_POST['img_product_id'];
    $thumb_img_image_name = $_POST['thumb_img_image_name'];
    $img_image_name = $_POST['img_image_name'];
    $full_img_image_name = $_POST['full_img_image_name'];
    $img_name = $_POST['img_name'];
    $set_images = $method->set_images($img_product_id, $thumb_img_image_name, $img_image_name, $full_img_image_name, $img_name);
    if(empty($set_images)) {
    jsonResponse(200, "Error", $set_images);
    } else {
    jsonResponse(400, "Success", NULL);
    }
}

break;


case 'Set_new_product_to_category':

if(!empty($_POST['method'])) {
    $category_product_id = $_POST['category_product_id'];
    $category_category_id = $_POST['category_category_id'];
    $products_to_categiry = $method->products_to_categiry($category_product_id, $category_category_id);
   if(empty($products_to_categiry)) {
   jsonResponse(200, "Error", $products_to_categiry);
   } else {
   jsonResponse(400, "Success", NULL);
 }
}

break;

case 'update_ean':

if(!empty($_POST['method'])) {
    $pro_id = $_POST['product_id'];
    $update_ean = $method->update_ean($pro_id);
   if(empty($update_ean)) {
   jsonResponse(200, "Error", $update_ean);
   } else {
   jsonResponse(400, "Success", NULL);
 }
}

break;


default:

echo 'ERROR';

}
function jsonResponse($status,$status_message,$data) {
    header("HTTP/1.1 " . $status_message);
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
            $dbname= 'admin_printervoronezh';
        
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

class Uploader extends Connect {

    public function __construct() {
        parent::__construct();
  }

    public function set_product($product_date_added, $date_modify, $product_template, $product_weight, $product_thumb_image, $product_name_image, $product_full_image, $name_ru_RU, $alias_ru_RU, $short_description_ru_RU, $description_ru_RU, $meta_title_ru_RU, $meta_description_ru_RU, $meta_keyword_ru_RU) {
        $sql = "INSERT into qtsb9_jshopping_products (`product_date_added`, `date_modify`, `product_template`, `product_weight`, `product_thumb_image`, `product_name_image`, `product_full_image`, `name_ru-RU`, `alias_ru-RU`, `short_description_ru-RU`, `description_ru-RU`, `meta_title_ru-RU`, `meta_description_ru-RU`, `meta_keyword_ru-RU`) 
        VALUES(:product_date_added, :date_modify, :product_template, :product_weight, :product_thumb_image, :product_name_image, :product_full_image, :name_ru_RU, :alias_ru_RU, :short_description_ru_RU, :description_ru_RU, :meta_title_ru_RU, :meta_description_ru_RU, :meta_keyword_ru_RU); ";

        $query = $this->dbconnect->prepare($sql);
        $query->bindParam(':product_date_added', $product_date_added);
        $query->bindParam(':date_modify', $date_modify);
        $query->bindParam(':product_template', $product_template);
        $query->bindParam(':product_weight', $product_weight);
        $query->bindParam(':product_thumb_image', $product_thumb_image);
        $query->bindParam(':product_name_image', $product_name_image);
        $query->bindParam(':product_full_image',  $product_full_image);
        $query->bindParam(':name_ru_RU', $name_ru_RU);
        $query->bindParam(':alias_ru_RU', $alias_ru_RU);
        $query->bindParam(':short_description_ru_RU', $short_description_ru_RU);
        $query->bindParam(':description_ru_RU', $description_ru_RU);
        $query->bindParam(':meta_title_ru_RU', $meta_title_ru_RU);
        $query->bindParam(':meta_description_ru_RU', $meta_description_ru_RU);
        $query->bindParam(':meta_keyword_ru_RU', $meta_keyword_ru_RU);
        if($query->execute()) {
        return 'uploaded';
        } 
     } 

     public function set_images($product_id, $thumb_image_name, $image_name, $full_image_name, $name) {
        $sql = "INSERT into qtsb9_jshopping_products_images (`product_id`, `image_thumb`, `image_name`, `image_full`, `name`) VALUES(:product_id, :image_thumb, :image_name, :image_full, :name); ";
        $qry = $this->dbconnect->prepare($sql);
        $qry->bindParam(':product_id', $product_id);
        $qry->bindParam(':image_thumb', $thumb_image_name);
        $qry->bindParam(':image_name', $image_name);
        $qry->bindParam(':image_full', $full_image_name);
        $qry->bindParam(':name', $name);
        if($qry->execute()) {
        return 'uploaded';
        } 
     } 

     public function products_to_categiry($product_id, $category_id) {
        $sql = "INSERT into qtsb9_jshopping_products_to_categories (`product_id`, `category_id`) VALUES(:product_id, :category_id); ";
        $q = $this->dbconnect->prepare($sql);
        $q->bindParam(':product_id', $product_id);
        $q->bindParam(':category_id', $category_id);
        if($q->execute()) {
        return 'uploaded';
        } 
     } 

     public function update_ean($p_id) {
        $sql = "UPDATE qtsb9_jshopping_products SET `product_ean` = :ean WHERE `product_id` = :id";
        $qr = $this->dbconnect->prepare($sql);
        $qr->bindParam(':ean', $p_id);
        $qr->bindParam(':id', $p_id);
        if($qr->execute()) {
        return 'updated';
        } 
     } 

     public function update_price($price, $name) {
        $sql = "UPDATE s1y5k_jshopping_products SET `product_old_price` = :price WHERE `name_ru-RU` = :name AND `product_id` BETWEEN '17129' AND '17455' ORDER BY `product_id`";
        $qr = $this->dbconnect->prepare($sql);
        $qr->bindParam(':price', $price);
        $qr->bindParam(':name', $name);
        if($qr->execute()) {
        return 'updated';
        } 
     } 

     public function update($id, $description) {
        $sql = "UPDATE s1y5k_jshopping_products SET `description_ru-RU` = :description WHERE `product_id` = :productid ORDER BY `product_id`";
        $qr = $this->dbconnect->prepare($sql);
        $qr->bindParam(':productid', $id);
        $qr->bindParam(':description', $description);
        if($qr->execute()) {
        return 'updated';
        } 
     } 

     public function select_description($pref) {
        $sql = "SELECT `product_id`, `name_ru-RU`, `description_ru-RU` FROM $pref WHERE `product_id` BETWEEN '17129' AND '17455' ORDER BY `product_id`";
        $qr = $this->dbconnect->prepare($sql);
        if($qr->execute()) {
            $response = $qr->fetchAll(PDO::FETCH_ASSOC);
            return $response;
            }
            else {
            return 'ERROR';
     } 
}

}

?>
