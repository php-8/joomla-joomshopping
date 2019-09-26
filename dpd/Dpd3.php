<?php 
//error_reporting(0);
include 'dpd_service.class2.php';

$DPD = new DPD_service;
$cit=$DPD->getCityList();

$ck=-1;
$fll=false;


    
$ccttii=str_replace("-", " ", $_GET["cit"]);
//echo iconv ('utf-8', 'windows-1251',$ccttii)."!!!!!";
if(!function_exists('mb_ucfirst')) {
    function mb_ucfirst($str) { 
            $name = $str;
            $last = mb_substr($name,0);//все кроме первой буквы
            
            $last = mb_strtolower($last, 'UTF-8');
            return $last;
            
    }
}


    
$mas=explode(" ", htmlspecialchars_decode($ccttii));

    
foreach($cit as $val=>$key){
            if(mb_strtoupper($key["abbreviation"], 'windows-1251')==mb_strtoupper("г", 'windows-1251')){
                if(mb_strtoupper($key["cityName"], 'windows-1251')==mb_strtoupper(iconv('utf-8', 'windows-1251',$ccttii), 'windows-1251')){
                    $ck=$val;
                    //echo "Find!!!<br/>";
                    //echo mb_strtoupper($key["cityName"], 'windows-1251')."==".mb_strtoupper(iconv('utf-8', 'windows-1251',$ccttii), 'windows-1251');
                    $fll=true;
                      /* echo "<pre>";
                       print_r($ck);
                       echo "</pre>";
                       echo "<pre>";
                       print_r($key);
                       echo "</pre>";*/
                    //echo "Go!";
                    break;
                    }
                }
            }
       
if($ck==-1){
        
        foreach($mas as $ky=>$vl){
            if(strlen($vl)>=3){
                foreach($cit as $val=>$key){
                    if(strpos(mb_strtoupper(" ".$key["cityName"], 'windows-1251'), mb_strtoupper(iconv('utf-8', 'windows-1251',$vl)), 'windows-1251')!=0){
                        if(strpos(mb_strtoupper(" ".$key["regionName"], 'windows-1251'), mb_strtoupper(iconv('utf-8', 'windows-1251',$vl), 'windows-1251'))!=0){
                        $ck=$val;
                        break;
                        }
                    }
                }
            }
        }
        if($ck==-1){
        echo iconv('windows-1251', 'utf-8', "Доставка не возможна");
        }else{
               $fll=true;
        }
 }
 $mas=array();   
if($fll==true){
   
    $arData = array(
	'delivery' => array(			// город доставки
			'cityId' => $cit[$ck]["cityId"],
			'cityName' => $cit[$ck]["cityName"],
		),
	'weight' => $_GET["wg"],					// вес отправки
    );
    
    
    $arCost = $DPD->getServiceCost($arData);
    
    
    $cost=-1;
    foreach($arCost as $val=>$key){
        if($key["serviceCode"]=="PCL"){
            $cost=$val;
            break;
        }
    }
    if($cost==-1){
    foreach($arCost as $val=>$key){
        if($key["serviceCode"]=="TEN"){
            $cost=$val;
            break;
        }
    }
    }
    if($cost==-1){
        foreach($arCost as $val=>$key){
        if($key["serviceCode"]=="DPT"){
            $cost=$val;
            break;
            }
        }
    }
   
   if($arCost[$cost]["cost"]==0){
        $mas['price']=$arCost[$cost]["cost"];
        $mas['time']=0;
        echo json_encode($mas);
    }    
    else{
        $mas['price']=$arCost[$cost]["cost"]+150;
        $mas['time']=$arCost[$cost]["days"]; 
        echo json_encode($mas);   
    }
    
    
}




   
?>


