<?php
header("Content-Type:application/json");
include_once("connection.php");

switch($_REQUEST['method']) {

  case 'insertitemandfile':

  if(!empty($_POST['name'])) {

    $timestamp = $_POST['timestamp'];
    $product_template = $_POST['product_template'];
    $product_price = $_POST['price'];
    //$image = $_POST['img'];
    $name = $_POST['name'];
    $alias = $_POST['translit'];
    $short_description = $_POST['short_description'];
    $description = $_POST['description'];
    $meta_title_ru = $_POST['meta_title'];
    $meta_description_ru = $_POST['meta_description'];
    $meta_keyword_ru = $_POST['meta_keywords'];

     if (isset($_FILES['myimage']['tmp_name'])) {
         $filename = $_FILES['myimage']['name'];
         $tmp           = explode('.', $filename);
         $end = end($tmp);
         $filename = fotoKey();
         $image = "c://xampp/htdocs/development/o-printere/components/com_jshopping/files/img_products/thumb_" . $filename . "." . $end;
         move_uploaded_file($_FILES['myimage']['tmp_name'], $image);
         }
         $fotokey = $filename . "." . $end;
     $updateuser = insertItems($timestamp, $product_template, $product_price, $fotokey, $name, $alias, $short_description, $description, $meta_title_ru, $meta_description_ru, $meta_keyword_ru);

     if(empty($updateuser)) {
         jsonResponse(200,"Items Not Found",NULL);
     } else {
         jsonResponse(200,"Item Found",$updateuser);
     }
  }
  break;
}









if($_GET['method']) {




switch($_GET['method']) {


case 'get_imgname':

if(!empty($_GET['method'])) {

   $meta = get_imgname();
   if($meta) {
       jsonResponse(200, "", $meta);
       } else {
           jsonResponse(400, "Invalid Request", NULL);
       }
}
break;



case 'get_meta':

if(!empty($_GET['method'])) {

   $meta = get_meta();
   if($meta) {
       jsonResponse(200, "", $meta);
       } else {
           jsonResponse(400, "Invalid Request", NULL);
       }
}
break;


  
  case 'set':

  if(!empty($_GET['name'])) {

     $product_template = $_GET['product_template'];
     $product_price = $_GET['price'];
     $image = $_GET['img'];
     $name = $_GET['name'];
     $alias = $_GET['translit'];
     $short_description = $_GET['short_description'];
     $description = $_GET['description'];
     $meta_title_ru = $_GET['meta_title'];
     $meta_description_ru = $_GET['meta_description'];
     $meta_keyword_ru = $_GET['meta_keywords'];

     $send = set($product_template, $product_price, $image, $name, $alias, $short_description, $description, $meta_title_ru, $meta_description_ru, $meta_keyword_ru);
     if($send) {
         jsonResponse(200,"", $send);
         } else {
             jsonResponse(400,"Invalid Request",NULL);
         }
  }
  break;


  case 'insertitem':

  if(!empty($_GET['name'])) {

     $product_template = $_GET['product_template'];
     $product_price = $_GET['price'];
     $image = $_GET['img'];
     $name = $_GET['name'];
     $alias = $_GET['translit'];
     $short_description = $_GET['short_description'];
     $description = $_GET['description'];
     $meta_title_ru = $_GET['meta_title'];
     $meta_description_ru = $_GET['meta_description'];
     $meta_keyword_ru = $_GET['meta_keywords'];

     $send = insertItems($product_template, $product_price, $image, $name, $alias, $short_description, $description, $meta_title_ru, $meta_description_ru, $meta_keyword_ru);
     if($send) {
         jsonResponse(200,"", $send);
         } else {
             jsonResponse(400,"Invalid Request",NULL);
         }
  }
  break;

  
	
 case 'test':

 if(!empty($_GET['method'])) {
    $items = Items();
    if(empty($items)) {
    jsonResponse(200, "Items Not Found", $items);
    } else {
    jsonResponse(200, "Item Found", $items);
    }
    } else {
    jsonResponse(400, "Invalid Request", NULL);
    }
 break;	

 case 'update':

 if(!empty($_GET['name'])) {
    $name=$_GET['name'];
    $description = $_GET['description'];
    $price = $_GET['price'];
    $id = $_GET['id'];
    $update = updateItem($name, $description, $price, $id);
    if($update) {
        jsonResponse(200,"", $update);
        } else {
            jsonResponse(400,"Invalid Request",NULL);
        }
 }
 break;

 case 'post':

 if(!empty($_GET['name'])) {
    $name = $_GET['name'];
    $price_start = $_GET['price_start'];
    $price_end = $_GET['price_end'];
    $items = getItems($name, $price_start, $price_end);
    if(empty($items)) {
    jsonResponse(200, "Items Not Found", $items);
    } else {
    jsonResponse(200, "Item Found", $items);
    }
    } else {
    jsonResponse(400, "Invalid Request", NULL);
    }
 break;

 case 'put':

 if(!empty($_GET['name'])) {
    $name=$_GET['name'];
    $description = $_GET['description'];
    $price = $_GET['price'];
    $send = putItems($name, $description, $price);
    if($send) {
        jsonResponse(200,"", $send);
        } else {
            jsonResponse(400,"Invalid Request",NULL);
        }
 }
 break;

 case 'delete':

 if(!empty($_GET['id'])) {
    $id=$_GET['id'];
    $delete = deleteItem($id);
    if($delete) {
        jsonResponse(200,"", $delete);
        } else {
            jsonResponse(400,"Invalid Request",NULL);
        }
 }
 break;

 default:
 //http_response_code(405);
 echo 'ERROR';
}



}


function jsonResponse($status,$status_message,$data) {
header("HTTP/1.1 ".$status_message);
$response['status']=$status;
$response['status_message']=$status_message;
$response['data']=$data;
$json_response = json_encode($response);
echo $json_response;
}





function insertItems($timestamp, $product_template, $product_price, $image, $name, $alias, $short_description, $description, $meta_title_ru, $meta_description_ru, $meta_keyword_ru) {
  $sql = "INSERT into s1y5k_jshopping_products (`product_ean`, `product_template`, `product_price`, `image`, `name_ru-RU`, `alias_ru-RU`, `short_description_ru-RU`, `description_ru-RU`, `meta_title_ru-RU`, `meta_description_ru-RU`, `meta_keyword_ru-RU`) VALUES(:product_ean, :product_template, :product_price, :image, :name, :alias, :short_description, :description, :meta_title_ru, :meta_description_ru, :meta_keyword_ru)";
  $db = Db::getInstance();
  $query = $db->prepare($sql);
  $query->bindParam(':product_ean', $timestamp);
  $query->bindParam(':product_template', $product_template);
  $query->bindParam(':product_price', $product_price);
  $query->bindParam(':image', $image);
  $query->bindParam(':name', $name);
  $query->bindParam(':alias', $alias);
  $query->bindParam(':short_description', $short_description);
  $query->bindParam(':description', $description);
  $query->bindParam(':meta_title_ru', $meta_title_ru);
  $query->bindParam(':meta_description_ru', $meta_description_ru);
  $query->bindParam(':meta_keyword_ru', $meta_keyword_ru);
 
  if($query->execute())
  {
  $id = $db->lastInsertId();
  $category = '52';
  $newimagename = 'thumb_' . $image;
  $st = "INSERT into s1y5k_jshopping_products_to_categories (`product_id`,`category_id`) VALUES(:product, :category); ";
  $q = $db->prepare($st);
  $q->bindParam(':product', $id);
  $q->bindParam(':category', $category);
  $q->execute();

  $stmt = "INSERT into s1y5k_jshopping_products_images (`product_id`, `image_name`, `name`) VALUES(:product_id, :image_name, :product_name); ";
  $qu = $db->prepare($stmt);
  $qu->bindParam(':product_id', $id);
  $qu->bindParam(':image_name', $newimagename);
  $qu->bindParam(':product_name', $name);
  $qu->execute();

  return 'SUCCESS' . ' ' . $id;
  }
  else
  {
  return 'ERROR';
  } 
}



function get_meta() {         
  $query = "SELECT `product_id`, `meta_title_ru-RU`, `meta_description_ru-RU`, `meta_keyword_ru-RU` FROM `s1y5k_jshopping_products`";
  $db = Db::getInstance();
  $meta = $db->prepare($query);
  $meta->execute();
  $rows = $meta->fetchAll();

  return $rows;
} 


function get_imgname() {         
  $query = "SELECT `image_id`, `name` FROM `s1y5k_jshopping_products_images`";
  $db = Db::getInstance();
  $meta = $db->prepare($query);
  $meta->execute();
  $rows = $meta->fetchAll();

  return $rows;
} 




function Items() {
       $query = "SELECT `image`, `name_ru-RU`, `short_description_ru-RU` FROM `s1y5k_jshopping_products` LIMIT 10";
       $db = Db::getInstance();
       $stmt = $db->prepare($query);
       $stmt->execute();
       if($stmt->rowCount()>0)
       {
            while($rows=$stmt->fetch(PDO::FETCH_ASSOC))
            {
              $data[] = $rows;
            }
       } elseif($stmt->rowCount() == 0) {
        $data = 'Not found';
       } else {

       }
       return $data;
  } 






function getItems($name, $price_start, $price_end) {
    function addWhere($where, $add, $and = true) {
        if ($where) {
          if ($and) $where .= " AND $add";
          else $where .= " OR $add";
        }
        else $where = $add;
        return $where;
      }
      if (!empty($_GET['name'])) {
        $where = "";
        if ($_GET["price_start"]) $where = addWhere($where, "`price` >= '".htmlspecialchars($price_start))."'";
        if ($_GET["price_end"]) $where = addWhere($where, "`price` <= '".htmlspecialchars($price_end))."'";
        if ($_GET['name']) $where = addWhere($where, "`name` LIKE '%".$name)."%'";
       $query = "SELECT * FROM `items`";
       if ($where) $query .= " WHERE $where ORDER BY `id` DESC";
       $db = Db::getInstance();
       $stmt = $db->prepare($query);
       $stmt->execute();
       if($stmt->rowCount()>0)
       {
              while($rows=$stmt->fetch(PDO::FETCH_ASSOC))
              {

                $data[] = $rows;
              }
       } elseif($stmt->rowCount() == 0) {
        $data = 'Not found';
       } else {

       }
      } 
    return $data;
}

function putItems($name, $description, $price) {
    $sql = "INSERT into items (`name`,`description`,`price`) VALUES(:name, :description, :price); ";
    $db = Db::getInstance();
    $query = $db->prepare($sql);
    $query->bindParam(':name', $name);
    $query->bindParam(':description', $description);
    $query->bindParam(':price', $price);
    if($query->execute())
    {
    return 'SUCCESS';
    }
    else
    {
    return 'ERROR';
    } 
}

function deleteItem($id) {
    $sql = "DELETE FROM items WHERE id=:id";
    $db = Db::getInstance();
    $query = $db->prepare($sql);
    $query->bindParam(':id', $id);
    if($query->execute())
    {
    return 'SUCCESS';
    }
    else
    {
    return 'ERROR';
    } 
}

function updateItem($name, $description, $price, $id) {
    $sql = "UPDATE items SET name=:name, description=:description, price=:price WHERE id=:id";
    $db = Db::getInstance();
    $query = $db->prepare($sql);
    $query->bindParam(':name', $name);
    $query->bindParam(':description', $description);
    $query->bindParam(':price', $price);
    $query->bindParam(':id', $id);
    if($query->execute())
    {
    return 'SUCCESS';
    }
    else
    {
    return 'ERROR';
    } 
}


function fotoKey($length = 32) {
$chars = "abcdefghijklmnopqrstuvwxyz1234567890";
$key = "";
for ($i = 0; $i < $length; $i++) {
    $key .= $chars{rand(0, strlen($chars) - 1)};
}
return $key;
}
?>
