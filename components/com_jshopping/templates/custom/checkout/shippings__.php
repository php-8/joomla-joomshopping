<?php defined('_JEXEC') or die(); ?>
<?php print $this->checkout_navigator?>
<div style="display:none;">
<?php print $this->small_cart?>
</div>
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
	
<div class="jshop">
<form id = "shipping_form" name = "shipping_form" action = "<?php print $this->action ?>" method = "post" onsubmit = "return validateShippingMethods()">
<?php print $this->_tmp_ext_html_shipping_start?>
<br/>

<table id = "table_shippings" cellspacing="0" cellpadding="0">
<?php foreach($this->shipping_methods as $shipping){
    if($shipping->sh_pr_method_id!=15){?>
  <tr 
 
  >
    <td style = "padding-top:5px; padding-bottom:5px;  <?php 
  if($shipping->sh_pr_method_id==19){ 
    echo " display: none;";
    }
    ?>">
      <input type = "radio" name = "sh_pr_method_id" id = "shipping_method_<?php print $shipping->sh_pr_method_id?>" 
       <?php 
          if($shipping->sh_pr_method_id==19){ 
            echo " disabled";
            }
            ?>
    
      value="<?php print $shipping->sh_pr_method_id ?>" <?php if ($shipping->sh_pr_method_id==$this->active_shipping){ ?>checked = "checked"<?php } ?> />
      <label for = "shipping_method_<?php print $shipping->sh_pr_method_id ?>"><?php
      if ($shipping->image){
        ?><span class="shipping_image"><img src="<?php print $shipping->image?>" alt="<?php print htmlspecialchars($shipping->name)?>" /></span><?php
      }
      ?><?php print $shipping->name?> (<?php print formatprice($shipping->calculeprice); ?>)</label>
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
      <div class="shipping_descr" <?php if ($shipping->sh_pr_method_id==$this->active_shipping){ ?> style="display: block;" <?php }?>><?php print $shipping->description?>
       <?php 
          if($shipping->sh_pr_method_id==3){?> 
<div style="clear: both;"> <b style="font-weight: bold; color: red;">Выберите город самовывоза</b>
<select name="cityy">
    <option value="Волгоград">Волгоград</option>
    <option value="Москва">Москва</option>
    <option value="Нижний Новгород">Нижний Новгород</option>
    <option value="Новосибирск">Новосибирск</option>
    <option value="Ульяновск">Ульяновск</option>
    <option value="Уфа">Уфа</option>
</select>
<a style="font-weight: bold;" target="_blank" href="https://vce-o-printere.ru/predstav.html">Стать представителем в своем регионе</a>
</div>

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
        jQuery("#shipping_method_18").parent("td").css("display", "none");
        
       if(<?php echo $wg;?>>2.5){
         jQuery("#shipping_method_7").parent("td").parent("tr").css("display", "none");
         jQuery("#shipping_method_7").prop('checked', false);
       }     
                
    
    jQuery.ajax({
        type: 'GET',
        url: 'https://<?=$_SERVER[HTTP_HOST]?>/dpd/Dpd2.php?cit=<?=$this->zipp?>&wg=<?=$wg?>',             // указываем URL и
        async: true,
        dataType : "json",
        beforeSend: function( xhr ) {
            jQuery("#shipping_method_17").parent("td").children("label").html("DPD ECONOMY до пункта выдачи в Вашем городе (<img height='20px' src='https://<?=$_SERVER[HTTP_HOST]?>/724.GIF'/>)");
        },
        success: function (data) {
            
           jQuery("#shipping_method_17").parent("td").children("label").html("DPD ECONOMY до пункта выдачи в Вашем городе ("+data['price']+" руб.) - срок от "+data['time']+" дней <input type='hidden' name='sh17' value='"+data['price']+"'>");
           
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
                    jQuery("#shipping_method_5").parent("td").css("display", "table-cell");
                    //jQuery("#shipping_method_17").parent("td").children(".shipping_descr").css("display", "block");
                    jQuery("#shipping_method_5").parent("td").children(".shipping_descr").css("display", "none");
                    jQuery("#shipping_method_6").parent("td").css("display", "table-cell");
                    jQuery("#shipping_method_7").parent("td").css("display", "table-cell");
                    jQuery("#shipping_method_18").parent("td").css("display", "table-cell");
                    jQuery("#shipping_method_21").parent("td").css("display", "table-cell");
                    jQuery("#shipping_method_23").parent("td").css("display", "table-cell");
                    jQuery("#shipping_method_24").parent("td").css("display", "table-cell");
                    
                    
                }
            

        } 
    });
    
     jQuery.ajax({
        type: 'GET',
        url: 'https://<?=$_SERVER[HTTP_HOST]?>/cdek/index.php?ct=<?=$this->zipp?>&weight1=<?=$wg?>&ti=136',             // указываем URL и
        async: true,
        dataType : "json",
        beforeSend: function( xhr ) {
            jQuery("#shipping_method_23").parent("td").children("label").html("СДЭК склад-склад (<img height='20px' src='https://<?=$_SERVER[HTTP_HOST]?>/724.GIF'/>)");
        },
        success: function (data) {
           
           jQuery("#shipping_method_23").parent("td").children("label").html("СДЭК склад-склад ("+data['price']+" руб.) - срок "+data['time']+" <input type='hidden' name='sh23' value='"+data['price']+"'>");
        } 
    });
    jQuery.ajax({
        type: 'GET',
        url: 'https://<?=$_SERVER[HTTP_HOST]?>/cdek/index.php?ct=<?=$this->zipp?>&weight1=<?=$wg?>&ti=137',             // указываем URL и
        async: true,
        dataType : "json",
        beforeSend: function( xhr ) {
            jQuery("#shipping_method_24").parent("td").children("label").html("СДЭК склад-дверь (<img height='20px' src='https://<?=$_SERVER[HTTP_HOST]?>/724.GIF'/>)");
        },
        success: function (data) {
           jQuery("#shipping_method_24").parent("td").children("label").html("СДЭК склад-дверь ("+data['price']+" руб.) - срок "+data['time']+" <input type='hidden' name='sh24' value='"+data['price']+"'>");
        } 
    });
    
    jQuery.ajax({
        type: 'GET',
        url: 'https://<?=$_SERVER[HTTP_HOST]?>/erg/index.php?zip=<?=$this->zipp?>&wg=<?=$wg?>',             // указываем URL и
        async: true,
        dataType : "json",
        beforeSend: function( xhr ) {
            jQuery("#shipping_method_22").parent("td").children("label").html("ТК Энергия (<img height='20px' src='https://<?=$_SERVER[HTTP_HOST]?>/724.GIF'/>)");
        },
        success: function (data) {
           jQuery("#shipping_method_22").parent("td").children("label").html("ТК Энергия ("+data['price']+" руб.) - срок от "+data['time']+" <input type='hidden' name='sh22' value='"+data['price']+"'>");
        } 
    });
    
    
    jQuery.ajax({
        type: 'GET',
        url: 'https://<?=$_SERVER[HTTP_HOST]?>/dpd/Dpd3.php?cit=<?=$this->zipp?>&wg=<?=$wg?>',             // указываем URL и
        async: true,
        dataType : "json",
        beforeSend: function( xhr ) {
            jQuery("#shipping_method_21").parent("td").children("label").html("DPD до Вашей двери (<img height='20px' src='https://<?=$_SERVER[HTTP_HOST]?>/724.GIF'/>)");
        },
        success: function (data) {
           jQuery("#shipping_method_21").parent("td").children("label").html("DPD до Вашей двери ("+data['price']+" руб.) - срок от "+data['time']+" дней <input type='hidden' name='sh21' value='"+data['price']+"'>");
        } 
    });
    
    
    jQuery.ajax({
        type: 'GET',
        url: 'https://<?=$_SERVER[HTTP_HOST]?>/dpd/Dpd.php?cit=<?=$this->zipp?>&wg=<?=$wg?>',             // указываем URL и
        async: true,
        dataType : "json",
        beforeSend: function( xhr ) {
            jQuery("#shipping_method_16").parent("td").children("label").html("DPD до пункта выдачи в Вашем городе (<img height='20px' src='https://<?=$_SERVER[HTTP_HOST]?>/724.GIF'/>)");
        },
        success: function (data) {
           
           jQuery("#shipping_method_16").parent("td").children("label").html("DPD до пункта выдачи в Вашем городе ("+data['price']+" руб.) - срок от "+data['time']+" дней <input type='hidden' name='sh16' value='"+data['price']+"'>");
        } 
    });
    
    jQuery.ajax({
        type: 'GET',
        url: 'https://<?=$_SERVER[HTTP_HOST]?>/calc.php?cityy=<?=$this->cityy?>&goods=<?=$goods2?>&pstcd=<?=$this->zipp?>',             // указываем URL и
        async: true,
        beforeSend: function( xhr ) {
        jQuery("#shipping_method_13").parent("td").children("label").html("СДЕК доставка до пункта выдачи в вашем городе (<img height='20px' src='https://<?=$_SERVER[HTTP_HOST]?>/724.GIF'/>)");
        },
        success: function (data) {
           jQuery("#shipping_method_13").parent("td").children("label").html("СДЕК доставка до пункта выдачи в вашем городе ("+data+" руб.) <input type='hidden' name='sh13' value='"+data+"'>");
        } 
    });
    jQuery.ajax({
        type: 'GET',
        url: 'https://<?=$_SERVER[HTTP_HOST]?>/calc2.php?cityy=<?=$this->cityy?>&goods=<?=$goods2?>&pstcd=<?=$this->zipp?>',             // указываем URL и
        async: true,
        beforeSend: function( xhr ) {
        jQuery("#shipping_method_11").parent("td").children("label").html("СДЕК курьером до двери (<img height='20px' src='https://<?=$_SERVER[HTTP_HOST]?>/724.GIF'/>)");
        },
        success: function (data) {
           jQuery("#shipping_method_11").parent("td").children("label").html("СДЕК курьером до двери ("+data+" руб.) <input type='hidden' name='sh11' value='"+data+"'>");
        } 
    });
    
    
    

});

jQuery(document).ready(function(){
  
  var fl=true;
            jQuery(".notdost").each(function(){
                
                if(jQuery(this).val()=="1"){
                    fl=false;
                }
            });    
            if(fl){
                     
                    
                    
                    jQuery("#table_shippings tbody tr td").each(function(){
                        jQuery(this).css("display", "none");
                    });
                    jQuery("#shipping_method_19").parent("td").css("display", "table-cell");
                    
                    jQuery("#shipping_method_19").removeAttr('disabled');
                    jQuery("#shipping_method_19").prop('checked', true);
                    jQuery("#shipping_method_19").parent("td").children("label").html("Доставка не требуется<input type='hidden' name='sh19' value='0'>");
                    
                    
                    
                    
                }
                
    
    jQuery.ajax({
        type: 'GET',
        url: 'https://<?=$_SERVER[HTTP_HOST]?>/ems/index.php?country=<?=$this->country?>&wg=<?=$wg?>',             // указываем URL и
        async: true,
        beforeSend: function( xhr ) {
            jQuery("#shipping_method_20").parent("td").children("label").html("EMS (<img height='20px' src='https://<?=$_SERVER[HTTP_HOST]?>/724.GIF'/>)");
        },
        success: function (data) {
           jQuery("#shipping_method_20").parent("td").children("label").html("EMS ("+data+" руб.) <input type='hidden' name='sh20' value='"+data+"'>");
        } 
    });
    
    

});
jQuery(document).ready(function(){
    
    
    var str=jQuery("label[for=shipping_method_5").html();
    var st=str.indexOf("(")+1;
    var end=str.indexOf(" руб");
    var str2=str.substring(st, end);
    var was=str2;
    //alert(parseInt(str2)*1.04);
    jQuery("input[name=sh5]").val(parseInt(str2));
        //alert(jQuery("label[for=shipping_method_5").html());
     jQuery("#pochta1").bind("click", function(){
        if(jQuery("#pochta1").prop("checked")){
            jQuery("input[name=sh5]").val(parseInt(jQuery("input[name=sh5]").val())+(parseInt((jQuery("#summabluat").val())*0.04)));
            jQuery("label[for=shipping_method_5").html("Почта России ("+jQuery("input[name=sh5]").val()+" руб)");
        }else{
            jQuery("input[name=sh5]").val(parseInt(was));
            jQuery("label[for=shipping_method_5").html("Почта России ("+was+" руб)");
        }
     });
     
     
     
    var str34=jQuery("label[for=shipping_method_7").html();
    var st34=str34.indexOf("(")+1;
    var end34=str34.indexOf(" руб");
    var str234=str34.substring(st34, end34);
    var was34=str234;
    //alert(parseInt(str2)*1.04);
    jQuery("input[name=sh7]").val(parseInt(str234));
        //alert(jQuery("label[for=shipping_method_5").html());
     jQuery("#pochta134").bind("click", function(){
        if(jQuery("#pochta134").prop("checked")){
            jQuery("input[name=sh7]").val(parseInt(jQuery("input[name=sh7]").val())+(parseInt((jQuery("#summabluat").val())*0.04)));
            jQuery("label[for=shipping_method_7").html("Почта России, первым классом ("+jQuery("input[name=sh7]").val()+" руб)");
        }else{
            jQuery("input[name=sh7]").val(parseInt(was34));
            jQuery("label[for=shipping_method_7").html("Почта России, первым классом ("+was34+" руб)");
        }
     });
});



</script>
<br/>
<?php print $this->_tmp_ext_html_shipping_end?>
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
<style type="text/css">
.shipping_descr{display: none;}
</style>
<!--<p>!!!!!<?php echo $this->zipp;?></p>-->
<?php 

/*if((strripos("!".$this->zipp, "Волгоград")!=false) || 
(strripos("!".$this->zipp, "Москва")!=false) || 
(strripos("!".$this->zipp, "Уфа")!=false) || 
(strripos("!".$this->zipp, "Ульяновск")!=false) ||
(strripos("!".$this->zipp, "Нижний Новгород")!=false))*/
if (strripos("!".$this->zipp, "Москва")!=false) 
{
    ?>
<script>
jQuery(document).ready(function(){
    jQuery("#table_shippings tbody tr td").each(function(){
                        jQuery(this).css("display", "none");
                    });
    jQuery(".shipping_weight_to_price tbody tr td").each(function(){
                        jQuery(this).css("display", "table-cell");
                    });
    jQuery("#shipping_method_3").parent("td").css("display", "table-cell");
    jQuery("#shipping_method_3").prop('checked', true);
    jQuery("#shipping_method_21").parent("td").css("display", "table-cell");
    jQuery("#shipping_method_25").parent("td").css("display", "table-cell");
    });
</script>    
<?php }else{?>
    <script>
        jQuery(document).ready(function(){
                jQuery("#shipping_method_25").parent("td").css("display", "none");
            });
    </script>
<?php }
?>
<input type = "submit" class = "button" value = "<?php print _JSHOP_NEXT ?>" />
</form>
</div>
