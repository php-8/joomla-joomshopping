<?php
/**
* @version      1.1 31.03.2012
* @author       Arkadiy Sedelnikov
* @copyright    Copyright (C) 2012 Arkadiy Sedelnikov. All rights reserved.
* @license      GNU/GPL
*/

class sm_ems extends shippingextRoot{
    
    function showShippingPriceForm($params, &$shipping_ext_row, &$template){        
        include(dirname(__FILE__)."/shippingpriceform.php");
    }
    
    function showConfigForm($config, &$shipping_ext, &$template){
        $checkedNo = $checkedYes = '';
        if($config['debug']){
            $checkedYes = 'checked="checked"';
        }
        else{
            $checkedNo = 'checked="checked"';
        }
        include(dirname(__FILE__)."/configform.php");
    }
    
    function getPrice($cart, $params, $price, &$shipping_ext_row, &$shipping_method_price){
        //параметры способа доставки
        $sm_params = unserialize($shipping_ext_row->params);

        //загружаем пользователя
        $user = &JFactory::getUser();
        if ($user->id){
            $adv_user = &JSFactory::getUserShop();
        }else{
            $adv_user = &JSFactory::getUserShopGuest();
        }

        //вычисляем стоимость доставки
        $price_shipping = $this->calculatePrice($adv_user, $cart, $sm_params);
        if($sm_params['debug']) echo '<br/>Стоимость доставки = '.$price_shipping;

        //если стоимость доставки не определена
        if(!$price_shipping){
            $price_shipping = '10000';
        }

        return $price_shipping;
    }


     private function calculatePrice($adv_user, $cart, $sm_params)
    {
        $debug = (!empty($sm_params['debug'])) ? (int)$sm_params['debug'] : 0;
        $price_tax = (!empty($sm_params['price_tax'])) ? (int)$sm_params['price_tax'] : 0;
        $weight_factor = (float)str_replace(',', '.', $sm_params['weight_factor']);
        $weight_factor = ($weight_factor ==0) ? $weight_factor = 1 : $weight_factor;
        $general_factor = (float)str_replace(',', '.', $sm_params['general_factor']);
        $general_factor = ($general_factor ==0) ? $general_factor = 1 : $general_factor;

        $vendor = JSFactory::getMainVendor();

        //сумма счета
        $summ = $cart->price_product_brutto;
        //вес
        $weight = $cart->getWeightProducts();
        $weight = $weight*$weight_factor;
        if($debug) echo '<br/>Суммарный вес товара в корзине = '.$weight;

        $weight_count = 1;
        //максимальный вес посылки
        $url = 'http://emspost.ru/api/rest/?method=ems.get.max.weight';
        $json = $this->getJson($url, $debug);
        $max_weight = (!empty($json['max_weight'])) ? $json['max_weight'] : 31.5;

        //если посылка превышает допустимый вес, то бьем ее на несколько посылок (равномерно без точной разбивки по фактическому весу товара)
        if($weight > $max_weight){
            while($weight/$weight_count > $max_weight){
                $weight_count++;
            }
            $weight = $weight/$weight_count;
        }

        //города доставки
        $url = 'http://emspost.ru/api/rest/?method=ems.get.locations&type=russia&plain=true';
        $json = $this->getJson($url, $debug);
        if (!$json) return false;

        //куда доставлять
        $to = (!empty($adv_user->d_city)) ? mb_strtoupper($adv_user->d_city) : mb_strtoupper($adv_user->city);
        $from = mb_strtoupper($vendor->city);
        $to_state = (!empty($adv_user->d_state)) ? mb_strtoupper($adv_user->d_state) : mb_strtoupper($adv_user->state);
        $from_state = mb_strtoupper($vendor->state);
        $to_ems = null;
        $from_ems = null;

        if($debug) {
            echo '<br/>Город отгрузки = '.$from;
            echo '<br/>Область доставки = '.$to_state;
            echo '<br/>Город доставки = '.$to;
            echo '<br/>Область отгрузки = '.$from_state;

        }
        //находим коды городов доставки и отгрузки по городам.
        foreach ($json['locations'] as $city) {
            if (mb_strtoupper($city['name']) == $to) {
                $to_ems = $city['value'];
            }
            if (mb_strtoupper($city['name']) == $from) {
                $from_ems = $city['value'];
            }
            if ($to_ems && $from_ems) {
                break;
            }
        }

        //если не нашелся город отгрузки или доставки, пытаемся вычислить регионы
        if (is_null($from_ems)) {
            //находим код региона отгрузки
            foreach ($json['locations'] as $city) {
                if (mb_strtoupper($city['name']) == $from_state) {
                    $from_ems = $city['value'];
                    break;
                }
            }
        }

        if (is_null($to_ems)) {
            //находим код региона доставки
            foreach ($json['locations'] as $city) {
                if (mb_strtoupper($city['name']) == $to_state) {
                    $to_ems = $city['value'];
                    break;
                }
            }
        }

        if (is_null($to_ems)) {
            //если покупатель забыл написать в регионе слово область
            if(strpos($to_state, 'ОБЛАСТЬ') === false){
                $to_state = $to_state . ' ОБЛАСТЬ';
                //находим код региона доставки
                foreach ($json['locations'] as $city) {
                    if (mb_strtoupper($city['name']) == $to_state) {
                        $to_ems = $city['value'];
                        break;
                    }
                }
            }
        }

        if($debug) {
            echo '<br/>Код города отгрузки = '.$from_ems;
            echo '<br/>Код города доставки = '.$to_ems;
        }
        //если не нашелся один из городов завершаем функцию.
        if (is_null($to_ems) || is_null($from_ems)) {
            if($debug) echo '<br/><span style="color: #ff0000;">Ошибка! Город/регион отгрузки или доставки не определен.</span>';
            return false;
        }
        //запрашиваем стоимость перевозки
        $url = 'http://emspost.ru/api/rest?callback=&method=ems.calculate&from=' . $from_ems . '&to=' . $to_ems . '&weight=' . $weight;
        $json = $this->getJson($url, $debug);
        if (!$json){
            return false;
        }
        $price = $json['price'];
        $price = ($price + ($summ/100)*$price_tax)*$general_factor*$weight_count;

        return $price;
    }

    private function getJson($url, $debug)
    {
        //запрашиваем файл с сервера ЕМС, для работы директива allow_url_fopen должна быть разрешена
        $file = file($url);
        if ($file) {
            $json = json_decode($file[0], true);
            $json = $json['rsp'];
            //Если json вернул ошибку, завершаем
            if ($json['stat'] != 'ok') {
                if($debug) echo '<br/><span style="color: #ff0000;">Ошибка! Сервер EMS вернул ошибку.</span>';
                return false;
            }
        }
        else {
            if($debug) echo '<br/><span style="color: #ff0000;">Ошибка! Невозможно подключиться к серверу EMS, проверьте директиву allow_url_fopen.</span>';
            return false;
        }
        return $json;
    }
}

?>