<?php 
/**
* @version      4.9.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<style type="text/css">
.shipping_descr{display: none;}

.jshop {
	margin-bottom: 10px;
}
.jshop input[type=radio] {
	display: none;
}
.jshop label {
	display: inline-block;
	cursor: pointer;
	position: relative;
	padding-left: 25px;
	margin-right: 0;
	line-height: 18px;
	user-select: none;
}
.jshop label:before {
	content: "";
	display: inline-block;
	width: 17px;
	height: 18px;
	position: absolute;
	left: 0;
	bottom: 1px;
	background: url(http://printervoronezh.ru/images/radio-1.png) 0 0 no-repeat;
}
 
/* Checked */
.jshop input[type=radio]:checked + label:before {
	background: url(http://printervoronezh.ru/images/radio-2.png) 0 0 no-repeat;
}
 
/* Hover */
.jshop label:hover:before {
	filter: brightness(120%);
}
 
/* Disabled */
.jshop input[type=radio]:disabled + label:before {
	filter: grayscale(100%);
}
</style>

<?php /* Hack for delivery */
$cart_data = JSFactory::getModel('cart', 'jshop');
$cart_data->load();
$payment_data_id = $cart_data->getPaymentId();
/* END of delivery hack*/ ?>


<div id="comjshop">
    <?php print $this->checkout_navigator?>
    <?php print $this->small_cart?>

<?php
//////////////////////////////////////////////////////
switch($this->cityy){
    case "Волгоград":$cit=400066;break;
    case "Уфа":$cit=450057;break;
    case "Ульяновск":$cit=432010;break;
}
$wg=0;
$arr = $this->product;
	foreach($arr as $value){
	  
		for($i=1;$i<=$value['quantity'];$i++){
		  $wg=$wg+$value["weight"];
			$goods[]=array(
					"weight"=>$value["weight"], 
					"length"=>"10", 
					"width"=>"7", 
					"height"=>"5"
				 );
		 }
	}
    
    $goods2 = serialize($goods);
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  ?>


<?php
echo '<pre>';

$session = JFactory::getSession();

$cart = JSFactory::getModel('cart', 'jshop');
$cart->load();

//$cart->setShippingId('50');
//$cart->setShippingPrId($sh_pr_method_id);
//$cart->setShippingsDatas('100', '50');

$city = $session->get('user_shop_guest');
$jshop_payment_price = $session->get('jshop_payment_price');

//print_r($cart->products); 

    $weight_sum = 0;
    foreach ($cart->products as $prod) {
        $weight_sum += $prod['weight'] * $prod['quantity'];
    }
    //echo '<br>';
    //echo $weight_sum;

    //echo '<br>';
    //print_r($session->set('jshop_payment_price', '111'));
    //echo '<br>';

    //print $this->delivery_info['zip']." ".$this->delivery_info['city']." ".$this->delivery_info['country'];
    $adv_user = JSFactory::getUser();
    //echo $adv_user->city;

    //echo '<br>';
    //print_r($session->get('jshop_payment_price'));
    //echo '<br>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://' . $_SERVER['HTTP_HOST'] . '/cdek/index.php?ct=' . $adv_user->city . '&weight1=' . $weight_sum . '&ti=136');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $array = json_decode($response,true); 
        curl_close($ch);

        //print_r($array['price']);

        $checkout = JSFactory::getModel('checkoutShipping', 'jshop');
        $checkout->setActiveShippingMethodPrice($array['price']);

echo '</pre>';
?>

<h1>Способ доставки</h1>
<hr>
<div class="jshop">
<form id = "shipping_form" name = "shipping_form" action = "<?php print $this->action ?>" method = "post" onsubmit = "return validateShippingMethods()" autocomplete="off" enctype="multipart/form-data">

<?php print $this->_tmp_ext_html_shipping_start?>
<br/>
<table id = "table_shippings" cellspacing="0" cellpadding="0">
<?php foreach($this->shipping_methods as $shipping){
    if($shipping->sh_pr_method_id!=15){?>
  <tr>
    <td style = "padding-top:5px; padding-bottom:5px;  <?php if($shipping->sh_pr_method_id==19){ echo " display: none;";}?>">
      <input type = "radio" name = "sh_pr_method_id" id = "shipping_method_<?php print $shipping->sh_pr_method_id?>" 
       <?php if($shipping->sh_pr_method_id==19){ echo " disabled"; } ?> value="<?php print $shipping->sh_pr_method_id ?>" 
      <?php //if ($shipping->sh_pr_method_id==$this->active_shipping){ echo 'checked = "checked"'; } ?> />
      <label for = "shipping_method_<?php print $shipping->sh_pr_method_id ?>"><?php if ($shipping->image){ ?><span class="shipping_image"><img src="<?php print $shipping->image?>" alt="<?php print htmlspecialchars($shipping->name)?>" /></span><?php } ?>
      <?php print $shipping->name?> (<?php print formatprice($shipping->calculeprice); ?>)</label>
      <?php if ((($this->config->show_list_price_shipping_weight) || ($shipping->sh_pr_method_id==25)) && count($shipping->shipping_price)){ ?>
          <br />
          <table class="shipping_weight_to_price">
          <?php foreach($shipping->shipping_price as $price){?>
              <tr>
                <td class="weight">
                    <?php if (($price->shipping_weight_to!=0) and ($price->shipping_weight_to<99998)){?>
                        <?php print formatweight($price->shipping_weight_from);?> - <?php print formatweight($price->shipping_weight_to);?>
                    <?php }else{ ?>
                        <?php print _JSHOP_FROM." ".formatweight($price->shipping_weight_from);?>
                    <?php } ?>
                </td>
                <td class="price">
                <?php print formatprice($price->shipping_price); ?>
                </td>
            </tr>
          <?php } ?>
          </table>
      <?php } ?>
      <div class="shipping_descr" <?php //if ($shipping->sh_pr_method_id==$this->active_shipping){  style="display: block;" }?>><?php print $shipping->description?>

<?php if($shipping->sh_pr_method_id==38) { ?> 

<?php if(strripos("!".$adv_user->city, "Сочи")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. Сочи, ул.Донская, д.10 офис 108</b><br>
<?php elseif(strripos("!".$adv_user->city, "сочи")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. Сочи, ул.Донская, д.10 офис 108</b><br>
<?php elseif(strripos("!".$adv_user->city, "Уфа")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. Уфа, ул Комсомольская, 26</b><br>
<?php elseif(strripos("!".$adv_user->city, "уфа")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. Уфа, ул Комсомольская, 26</b><br>
<?php elseif(strripos("!".$adv_user->city, "Воронеж")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. Воронеж Ленинский пр-кт д. 16</b><br>
<?php elseif(strripos("!".$adv_user->city, "воронеж")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. Воронеж Ленинский пр-кт д. 16</b><br>
<?php elseif(strripos("!".$adv_user->city, "Грозный")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. Грозный ул Н.Назарбаева 98</b><br>
<?php elseif(strripos("!".$adv_user->city, "грозный")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. Грозный ул Н.Назарбаева 98</b><br>
<?php elseif(strripos("!".$adv_user->city, "Санкт-Петербург")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. СПБ, Лиговский пр-кт 50 корп 13</b><br>
<?php elseif(strripos("!".$adv_user->city, "Питер")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. СПБ, Лиговский пр-кт 50 корп 13</b><br>
<?php elseif(strripos("!".$adv_user->city, "Петербург")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. СПБ, Лиговский пр-кт 50 корп 13</b><br>
<?php elseif(strripos("!".$adv_user->city, "санкт-петербург")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. СПБ, Лиговский пр-кт 50 корп 13</b><br>
<?php elseif(strripos("!".$adv_user->city, "питер")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. СПБ, Лиговский пр-кт 50 корп 13</b><br>
<?php elseif(strripos("!".$adv_user->city, "Алмата")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. Алматы, ул. Клочкова, 123</b><br>
<?php elseif(strripos("!".$adv_user->city, "алмата")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. Алматы, ул. Клочкова, 123</b><br>
<?php elseif(strripos("!".$adv_user->city, "Иваново")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. Иваново ул. Лежневская д.114</b><br>
<?php elseif(strripos("!".$adv_user->city, "иваново")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу г. Иваново ул. Лежневская д.114</b><br>
<?php elseif(strripos("!".$adv_user->city, "Москва")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу Москва, Зеленый проспект дом 24, офис 4</b><br>
<?php elseif(strripos("!".$adv_user->city, "москва")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу Москва, Зеленый проспект дом 24, офис 4</b><br>
<?php elseif(strripos("!".$adv_user->city, "Волгоград")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу Волгоград ул. Баррикадная, 10 Помещение №2</b><br>
<?php elseif(strripos("!".$adv_user->city, "волгоград")!=false): ?>
<br><b>Вы можете получить заказанный вами товар по адресу Волгоград ул. Баррикадная, 10 Помещение №2</b><br>
<?php else: ?>
<br>
<div style="clear: both;"> <b style="font-weight: bold; color: red;">Выберите город самовывоза</b>
<select name="cityy">
    <option value="Волгоград">Волгоград</option>
    <option value="Москва">Москва</option>
    <option value="Уфа">Уфа</option>
    <option value="Сочи">Сочи</option>
    <option value="Воронеж">Воронеж</option>
    <option value="Иваново">Иваново</option>
    <option value="Грозный">Грозный</option>
    <option value="Санкт-Петербург">Санкт-Петербург</option>
    <option value="Алмата">Алмата</option>
</select>
<a style="font-weight: bold;" target="_blank" href="https://vce-o-printere.ru/predstav.html">Стать представителем в своем регионе</a>
</div>
<?php endif; ?>
<?php }?> 


<?php 
if($shipping->sh_pr_method_id==6){?> 
<div style="clear: both;"> <b style="font-weight: bold; color: red;">Выберите ТК</b>
<select name="tkkk">
    <option value="Деловые линии">Деловые линии</option>
    <option value="ПЭК">ПЭК</option>
    <option value="Энергия">Энергия</option>
    <option value="РАТЭК">РАТЭК</option>
    <option value="КИТ">КИТ</option>
</select>
</div>
<?php }?>
            
      </div>
      <?php if ($shipping->delivery){?>
      <div class="shipping_delivery"><?php print _JSHOP_DELIVERY_TIME.": ".$shipping->delivery?></div>
      <?php }?>
      <?php if ($shipping->delivery_date_f){?>
      <div class="shipping_delivery_date"><?php print _JSHOP_DELIVERY_DATE.": ".$shipping->delivery_date_f?></div>
      <?php }?>      
      </td>
  </tr>
<?php }} ?>
</table>
<input type="hidden" name="sh5" value="">
<input type="hidden" name="sh7" value="">




<script>

jQuery(document).ready(function(){


    jQuery.ajax({
        type: 'GET',
        url: 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/dpd/Dpd2.php?cit=<?php echo $adv_user->city; ?>&wg=<?php echo $weight_sum; ?>',             // указываем URL и
        async: true,
        dataType : "json",
        beforeSend: function( xhr ) {
            jQuery("#shipping_method_40").parent("td").children("label").html("DPD ECONOMY до пункта выдачи в Вашем городе <img height='20px' src='http://<?=$_SERVER[HTTP_HOST]?>/images/wait.gif'/>");
        },
        success: function (data) {
           jQuery("#shipping_method_40").parent("td").children("label").html("DPD ECONOMY до пункта выдачи в Вашем городе (<b>"+data['price']+" руб.</b>) - срок от "+data['time']+" дней <input type='hidden' name='sh40' value='"+data['price']+"'>");
                var str=jQuery.trim(jQuery(".total .value").html());
                var str2=str.substr(0,str.indexOf(' '));
                
                if((parseFloat(str2)*0.035>=(parseFloat(data['price'])-150)) && (parseFloat(str2)>=5000)){
                    
                    jQuery("#table_shippings tbody tr td").each(function(){
                        jQuery(this).css("display", "none");
                    });
                    jQuery("#shipping_method_19").parent("td").css("display", "table-cell");
                    jQuery("#shipping_method_19").removeAttr('disabled');
                    jQuery("#shipping_method_19").prop('checked', true);
                    jQuery("#shipping_method_19").parent("td").children("label").html("DPD ECONOMY до пункта выдачи в Вашем городе (<span style='color: red;'>БЕСПЛАТНАЯ ДОСТАВКА!</span>) <input type='hidden' name='sh19' value='0'>");
                    
                    jQuery("#shipping_method_4").parent("td").css("display", "table-cell");
                    jQuery("#shipping_method_35").parent("td").css("display", "table-cell");
                    //jQuery("#shipping_method_17").parent("td").children(".shipping_descr").css("display", "block");
                    jQuery("#shipping_method_35").parent("td").children(".shipping_descr").css("display", "none");
                    jQuery("#shipping_method_6").parent("td").css("display", "table-cell");
                    jQuery("#shipping_method_7").parent("td").css("display", "table-cell");
                    jQuery("#shipping_method_18").parent("td").css("display", "table-cell");
                    jQuery("#shipping_method_21").parent("td").css("display", "table-cell");
                    jQuery("#shipping_method_23").parent("td").css("display", "table-cell");
                    jQuery("#shipping_method_24").parent("td").css("display", "table-cell");   
            }
        } 
    });

    // jQuery.ajax({
    //     type: 'GET',
    //     url: 'http://<?php //echo $_SERVER['HTTP_HOST']; ?>/boxberry/index.php?zip=<?php //echo $adv_user->zip; ?>&weight_sum=<?php //echo $weight_sum; ?>',             // указываем URL и
    //     async: true,
    //     dataType : "json",
    //     beforeSend: function( xhr ) {
    //         jQuery("#shipping_method_53").parent("td").children("label").html("Курьером BoxBerry <img height='20px' src='http://<?=$_SERVER[HTTP_HOST]?>/images/wait.gif'/>");
    //     },
    //     success: function (data) {
    //        jQuery("#shipping_method_53").parent("td").children("label").html("Курьером BoxBerry (<b>"+data['price']+" руб.</b>) - срок от "+data['delivery_period']+" дней <input type='hidden' name='sh45' value='"+data['price']+"'>");
    //     } 
    // });


    jQuery.ajax({
        type: 'GET',
        url: 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/dpd/Dpd.php?cit=<?php echo $adv_user->city; ?>&wg=<?php echo $weight_sum; ?>',             // указываем URL и
        async: true,
        dataType : "json",
        beforeSend: function( xhr ) {
            jQuery("#shipping_method_45").parent("td").children("label").html("DPD до пункта выдачи в Вашем городе <img height='20px' src='http://<?=$_SERVER[HTTP_HOST]?>/images/wait.gif'/>");
        },
        success: function (data) {
           jQuery("#shipping_method_45").parent("td").children("label").html("DPD до пункта выдачи в Вашем городе (<b>"+data['price']+" руб.</b>) - срок от "+data['time']+" дней <input type='hidden' name='sh45' value='"+data['price']+"'>");
        } 
    });

    jQuery.ajax({
        type: 'GET',
        url: 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/dpd/Dpd3.php?cit=<?php echo $adv_user->city; ?>&wg=<?php echo $weight_sum; ?>',             // указываем URL и
        async: true,
        dataType : "json",
        beforeSend: function( xhr ) {
            jQuery("#shipping_method_37").parent("td").children("label").html("DPD до Вашей двери <img height='20px' src='http://<?=$_SERVER[HTTP_HOST]?>/images/wait.gif'/>");
        },
        success: function (data) {
           jQuery("#shipping_method_37").parent("td").children("label").html("DPD до Вашей двери (<b>"+data['price']+" руб.</b>) - срок от "+data['time']+" дней <input type='hidden' name='sh37' value='"+data['price']+"'>");
        } 
    });

     jQuery.ajax({
        type: 'GET',
        url: 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/cdek/index.php?ct=<?php echo $adv_user->city; ?>&weight1=<?php echo $weight_sum; ?>&ti=136',             // указываем URL и
        async: true,
        dataType : "json",
        beforeSend: function(xhr) {
            jQuery("#shipping_method_50").parent("td").children("label").html("СДЭК склад-склад <img height='20px' src='http://<?=$_SERVER[HTTP_HOST]?>/images/wait.gif'/>");
        },
        success: function (data) {
           jQuery("#shipping_method_50").parent("td").children("label").html("СДЭК склад-склад (<b>" + data['price'] + " руб.</b>) - срок " + data['time'] + " <input type='hidden' name='sh50' value='" + data['price'] + "'>");
            //$checkout = JSFactory::getModel('checkoutShipping', 'jshop');
            //$checkout->setActiveShippingMethodPrice(data['price']);
        } 
    });

    jQuery.ajax({
        type: 'GET',
        url: 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/cdek/index.php?ct=<?php echo $adv_user->city; ?>&weight1=<?php echo $weight_sum; ?>&ti=137',             // указываем URL и
        async: true,
        dataType : "json",
        beforeSend: function( xhr ) {
            jQuery("#shipping_method_51").parent("td").children("label").html("СДЭК склад-дверь <img height='20px' src='http://<?=$_SERVER[HTTP_HOST]?>/images/wait.gif'/>");
        },
        success: function (data) {
           jQuery("#shipping_method_51").parent("td").children("label").html("СДЭК склад-дверь (<b>" + data['price'] + " руб.</b>) - срок " + data['time'] + " <input type='hidden' name='sh51' value='" + data['price'] + "'>");
        } 
    });

    jQuery.ajax({
        type: 'GET',
        url: 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/erg/index.php?zip=<?php echo $adv_user->city; ?>&wg=<?php echo $weight_sum; ?>',             // указываем URL и
        async: true,
        dataType : "json",
        beforeSend: function( xhr ) {
            jQuery("#shipping_method_49").parent("td").children("label").html("ТК Энергия <img height='20px' src='http://<?=$_SERVER[HTTP_HOST]?>/images/wait.gif'/>");
        },
        success: function (data) {
           jQuery("#shipping_method_49").parent("td").children("label").html("ТК Энергия (<b>"+data['price']+" руб.</b>) - срок от "+data['time']+" <input type='hidden' name='sh49' value='"+data['price']+"'>");
        } 
    });
    
    jQuery.ajax({
        type: 'GET',
        url: 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/ems/index.php?country=россия&wg=<?php echo $weight_sum; ?>',             // указываем URL и
        async: true,
        beforeSend: function( xhr ) {
            jQuery("#shipping_method_47").parent("td").children("label").html("EMS <img height='20px' src='http://<?=$_SERVER[HTTP_HOST]?>/images/wait.gif'/>");
        },
        success: function (data) {
           jQuery("#shipping_method_47").parent("td").children("label").html("EMS (<b>"+data+" руб.</b>) <input type='hidden' name='sh20' value='"+data+"'>");
        } 
    });
});


jQuery(document).ready(function(){
    
    
    var str=jQuery("label[for=shipping_method_35").html();
    var st=str.indexOf("(")+1;
    var end=str.indexOf(" RUR");
    var str2=str.substring(st, end);
    var was=str2;
    //alert(parseInt(str2)*1.04);
    jQuery("input[name=sh5]").val(parseInt(str2));
        //alert(jQuery("label[for=shipping_method_35").html());
     jQuery("#pochta1").bind("click", function(){
        if(jQuery("#pochta1").prop("checked")){
            jQuery("input[name=sh5]").val(parseInt(jQuery("input[name=sh5]").val())+(parseInt((jQuery("input[name=sh5]").val())*0.04)));
            jQuery("label[for=shipping_method_35").html("Почта России (" + jQuery("input[name=sh5]").val() + " руб)");
        }else{
            jQuery("input[name=sh5]").val(parseInt(was));
            jQuery("label[for=shipping_method_35").html("Почта России ("+was+" руб)");
        }
     });
     
     //jQuery("input[name=sh5]").val(parseInt(jQuery("input[name=sh5]").val())*1.04);

    
});


</script>



<script>
jQuery(document).ready(function(){
   jQuery("#table_shippings tbody tr td:first .shipping_descr").css("display", "block"); 
    
   jQuery("#table_shippings tbody tr td input[type=radio]").bind("click", function(){
        jQuery("#table_shippings tbody tr td .shipping_descr").each(function(){
            jQuery(this).css("display", "none");
        });
    jQuery(this).parent("td").children(".shipping_descr").css("display", "block");
   }); 
});
</script>


<?php
if ((strripos("!".$adv_user->city, "Москва")!=false) || 
(strripos("!".$adv_user->city, "москва")!=false) ||
(strripos("!".$adv_user->city, "Уфа")!=false) ||
(strripos("!".$adv_user->city, "уфа")!=false) ||
(strripos("!".$adv_user->city, "Санкт-Петербург")!=false) ||
(strripos("!".$adv_user->city, "санкт-петербург")!=false) ||
(strripos("!".$adv_user->city, "петербург")!=false) ||
(strripos("!".$adv_user->city, "Петербург")!=false) ||
(strripos("!".$adv_user->city, "Питер")!=false) ||
(strripos("!".$adv_user->city, "Иваново")!=false) ||
(strripos("!".$adv_user->city, "иваново")!=false) ||
(strripos("!".$adv_user->city, "Сочи")!=false) ||
(strripos("!".$adv_user->city, "сочи")!=false) ||
(strripos("!".$adv_user->city, "Грозный")!=false) ||
(strripos("!".$adv_user->city, "грозный")!=false) ||
(strripos("!".$adv_user->city, "Воронеж")!=false) ||
(strripos("!".$adv_user->city, "воронеж")!=false) ||
(strripos("!".$adv_user->city, "алмата")!=false) ||
(strripos("!".$adv_user->city, "алматы")!=false) ||
(strripos("!".$adv_user->city, "Алмата")!=false) ||
(strripos("!".$adv_user->city, "Алматы")!=false)) { ?>

<?php //if (strripos("!".$adv_user->city, "Москва")!=false) { ?>
<script>
jQuery(document).ready(function(){
    jQuery("#table_shippings tbody tr td").each(function(){
                        jQuery(this).css("display", "none");
                    });
    jQuery(".shipping_weight_to_price tbody tr td").each(function(){
                        jQuery(this).css("display", "table-cell");
                    });
    jQuery("#shipping_method_37").parent("td").css("display", "table-cell");
    //jQuery("#shipping_method_37").prop('checked', true);

    jQuery("#shipping_method_38").parent("td").css("display", "table-cell");
    jQuery("#shipping_method_53").parent("td").css("display", "table-cell");
    jQuery("#shipping_method_35").parent("td").css("display", "table-cell");
    jQuery("#shipping_method_50").parent("td").css("display", "table-cell");

    });

  
</script>    
<?php }else{ ?>
    <script>
        jQuery(document).ready(function(){
                jQuery("#shipping_method_53").parent("td").css("display", "none");
            });
    </script>
<?php } ?>






<br>
<br>
<input type = "submit" class = "button" value = "<?php print _JSHOP_NEXT ?>" />
</form>
</div>
<?php
//$session->set('jshop_payment_price', '111');

    $adv_user = JSFactory::getUser();
    echo '<pre>';
    //print_r($adv_user->city);
    echo '</pre>';

    echo '<br>';
    ///print_r($session->get('jshop_payment_price'));
?>
