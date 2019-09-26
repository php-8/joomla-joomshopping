<?php


$url = "http://emspost.ru/api/rest/?method=ems.get.max.weight";
$response = file_get_contents($url);
$mas=json_decode($response);
if(0+$mas->rsp->max_weight>=0+$_GET["wg"]){

/*$url = "http://emspost.ru/api/rest/?method=ems.get.locations&type=countries";
$response = file_get_contents($url);
$mas=json_decode($response);
echo "<pre>";
print_r($mas);
echo "</pre>";*/

$country="0";
switch($_GET["country"]){
    case 240: $country="MD"; break;
    case 11: $country="AM"; break;
    case 20: $country="BY"; break;
    case 109: $country="KZ"; break;
    case 176: $country="RU"; break;
    case 220: $country="UA"; break;
    case 242: $country="UZ"; break;
    case 241: $country="GE"; break;
    default: $country="0"; break;
}
if($country=="0"){
    echo iconv('windows-1251', 'utf-8', "Данным способом Достава невозможна");
}else{
    $wg=$_GET["wg"];
   if(($wg==0) || ($wg=="0")){
    $wg=0.1;
   }
   
    $url = "http://emspost.ru/api/rest?method=ems.calculate&to=".$country."&weight=".$wg."&type=att";
    $response = file_get_contents($url);
    $mas=json_decode($response);
    echo iconv('windows-1251', 'utf-8', $mas->rsp->price);
    }
}
else{
    echo iconv('windows-1251', 'utf-8', "Превышен Вес!");
}



?>