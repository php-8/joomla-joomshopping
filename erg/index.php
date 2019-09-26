<?php
/*
{
  "idCityFrom": 383,
  "idCityTo": 495,
  "cover": 0, - 1 конверт, 0 - посылка
  "idCurrency": 0, - валюта
  "items": [
    {
      "weight": 0,
      "width": 0,
      "height": 0,
      "length": 0
    }
  ]
}

Адрес для запроса https://api2.nrg-tk.ru/v2/price
*/
class City{
    public $zipcode;
    public $id;
    function __construct($zip) {
        $this->zipcode=$zip;
        $this->id=-1;
        
        $ch = curl_init('https://api2.nrg-tk.ru/v2/cities');
        curl_setopt_array($ch, array(
            CURLOPT_POST => FALSE,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,     // Disabled SSL Cert checks
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            )
        ));
        
        // Send the request
        
        $response = curl_exec($ch);
        
        // Check for errors
        if($response === FALSE){
            die(curl_error($ch));
        }
        // Decode the response
        $responseData = json_decode($response);
        
       /* echo "<pre>";
        print_r($responseData->cityList);
        echo "</pre>";*/
        
        foreach($responseData->cityList as $key=>$val){
            /*echo "<pre>";
                print_r($val);
            echo "</pre>";
            exit(0);*/
            //echo "Сравниваю: ".mb_strtoupper(" ".$val->name, 'UTF-8')." и ".mb_strtoupper($zip, 'UTF-8')."<br/>";
            if(strpos(mb_strtoupper(" ".$val->name, 'UTF-8'), mb_strtoupper($zip, 'UTF-8'))){
                $this->id=$val->id; 
            }
        }
        
        
        curl_close($ch);
        
        if($this->id==-1){
            echo "Доставка не возможна";exit(0);
        }
        
        
    
        /*$data=json_encode($this);
        //echo $data;
        // Setup cURL
        $ch = curl_init('https://api2.nrg-tk.ru/v2/search/city?zipCode='.$zip);
        curl_setopt_array($ch, array(
            CURLOPT_POST => FALSE,
            CURLOPT_RETURNTRANSFER => true,
            
            CURLOPT_SSL_VERIFYPEER => false,     // Disabled SSL Cert checks
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            )
        ));
        
        // Send the request
        
        $response = curl_exec($ch);
        
        // Check for errors
        if($response === FALSE){
            die(curl_error($ch));
        }
        // Decode the response
        $responseData = json_decode($response);
        
        if(!isset($responseData->city->id)){echo "Доставка не возможна";exit(0);}
        
        $this->id=$responseData->city->id;
        
        curl_close($ch);*/
   }
}




class Item{
    public $weight;
    public $width;
    public $height;
    public $length;
    function __construct($wg=0.1, $wd=0.01, $hg=0.01, $ln=0.01) {
        $this->weight=$wg;
        $this->width=$wd;
        $this->height=$hg;
        $this->length=$ln;
    }
}
class Base {
    public $idCityFrom;
    public $idCityTo;
    public $cover;
    public $idCurrency;
    public $items=array();
        function __construct($idf, $idt, $wg=0.1, $wd=0.01, $hg=0.01, $ln=0.01) {
            $this->cover=0;
            $this->idCityFrom=$idf;
            $this->idCityTo=$idt;
            $this->idCurrency=1;
            
            
        }
        
    public function GetCost(){
        
        $data=json_encode($this);
        //echo $data;
        // Setup cURL
        $ch = curl_init('https://api2.nrg-tk.ru/v2/price');
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => true,
            
            CURLOPT_POSTFIELDS=>$data,
            CURLOPT_SSL_VERIFYPEER => false,     // Disabled SSL Cert checks
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            )
        ));
        
        // Send the request
        
        $response = curl_exec($ch);
        //curl_error($ch);
        // Check for errors
        
        
        if($response === FALSE){
            die(curl_error($ch));
        }
        // Decode the response
        $responseData = json_decode($response);
       
        
        return $responseData;
        curl_close($ch);
    }
}


$cit=new City("Волгоград");

//echo $cit->id."<br/>";

$cit2=new City($_GET["zip"]);
//echo $cit2->id."<br/>";

$pos=new Base($cit->id, $cit2->id);

$pos->items[]=new Item((float)$_GET["wg"]);
/*echo "<pre>";
print_r($pos);
echo "</pre>";*/
$res=$pos->GetCost();

//echo "<pre>";
//print_r($res);
//echo "</pre>";

$itog=100+$res->transfer[0]->price;//+$res->request->price+$res->delivery->price;
$mas=array();
$mas['price']=$itog;
$mas['time']=$res->transfer[0]->interval;
        
echo json_encode($mas); 
        
    
?>