<?php

$weight_sum = $_GET['weight_sum'];
$zip = $_GET['zip'];


$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://account.boxberry.ru/json.php?token=d69782d4a230915c96ca204369eb0f2f&method=DeliveryCosts&targetstart=14weight=$weight_sum&zip=$zip");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        // $array = json_decode($response,true); 
        // curl_close($ch);

        $array = $response; 
        curl_close($ch);

        print_r($array);

?>
