<?php

$url='http://api.boxberry.ru/json.php?token=d69782d4a230915c96ca204369eb0f2f&method=ListCities';
            $handle = fopen($url, "rb");
            $contents = stream_get_contents($handle);
            fclose($handle);
            echo '<pre>';
            print_r($data=json_decode($contents,true));
            echo '</pre>';
?>


<?php
ini_set("soap.wsdl_cache_enabled", "0");
 $clientS = new SoapClient("http://api.boxberry.ru/__soap/1c_public.php?wsdl",
 array('features' => SOAP_SINGLE_ELEMENT_ARRAYS));
 $s=array();
 $s['token']='d69782d4a230915c96ca204369eb0f2f';
 $s['weight']=500;
 $s['target']='010';
 try {$data=$clientS->DeliveryCosts($s);}
 catch (SoapFault $exception){$errS=$exception;}
 if(@$errS){
 echo $errS;
 } // если произошла ошибка и ответ не был получен else
 {
 print_r($data);
 }
 ?>