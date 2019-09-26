var prevAjaxHandler = null;
var reloadAttribEvents = [];
var extdataurlupdateattr = {};

function reloadAttribSelectAndPrice(id_select){
    var product_id = jQuery("#product_id").val();
    var qty = jQuery("#quantity").val();
    var data = {};
    data["change_attr"] = id_select;
    data["qty"] = qty;    
    for(var i=0;i<attr_list.length;i++){
        var id = attr_list[i];
        data["attr["+id+"]"] = attr_value[id];
    }
    for(extdatakey in extdataurlupdateattr){
        data[extdatakey] = extdataurlupdateattr[extdatakey];
    }

    if (prevAjaxHandler){
        prevAjaxHandler.abort();
    }

    prevAjaxHandler = jQuery.getJSON(
        urlupdateprice,
        data,
        function(json){
            var reload_atribut = 0;
            for(var i=0;i<attr_list.length;i++){
                var id = attr_list[i];
                if (reload_atribut){
                    jQuery("#block_attr_sel_"+id).html(json['id_'+id]);
                }
                if (id == id_select) reload_atribut = 1;
            }
			
            jQuery("#block_price").html(json.price);
            jQuery("#pricefloat").val(json.pricefloat);
            
			if (json.basicprice){
                jQuery("#block_basic_price").html(json.basicprice);
            }
			
            for(key in json){
                if (key.substr(0,3)=="pq_"){
                    jQuery("#pricelist_from_"+key.substr(3)).html(json[key]);
                }
            }

            if (json.available=="0"){
                jQuery("#not_available").html(translate_not_available);
            }else{
                jQuery("#not_available").html("");
            }
			
			if (json.displaybuttons=="0"){
                jQuery(".productfull .prod_buttons").hide();
            }else{
                jQuery(".productfull .prod_buttons").show();
            }

            if (json.ean){
                jQuery("#product_code").html(json.ean);
            }

            if (json.weight){
                jQuery("#block_weight").html(json.weight);
            }
            if (json.pricedefault){
                jQuery("#pricedefault").html(json.pricedefault);
            }
            if (json.qty){
                jQuery("#product_qty").html(json.qty);
            }
            if (json.oldprice){
                jQuery("#old_price").html(json.oldprice);
                jQuery(".old_price").show();
            }else{
                jQuery(".old_price").hide();
            }

            if (json.images && json.images.length>0){
                var count_prod_img = json.images.length;
                var html_thumb_img = "";
                var html_middle_img = "";
				var html_zoom_img = '';
                if (typeof(jshop_product_hide_zoom_image)==='undefined') jshop_product_hide_zoom_image = 0;
                if (!jshop_product_hide_zoom_image){
                    html_zoom_img = ' <div class="text_zoom"><img alt="zoom" src="'+liveimgpath+'/search.png" /> '+translate_zoom_image+'</div>';
                }
                for(var j=0;j<count_prod_img;j++){
                    html_thumb_img+='<img class="jshop_img_thumb" src="'+liveproductimgpath+'/thumb_'+json.images[j]+'" onclick = "showImage('+j+')" />';
                    tmp = 'style="display:none"';
                    if (j==0) tmp = '';
                    html_middle_img+='<a class="lightbox" id="main_image_full_'+j+'" href="'+liveproductimgpath+'/full_'+json.images[j]+'" '+tmp+'><img id="main_image_'+j+'" src="'+liveproductimgpath+'/'+json.images[j]+'" />'+html_zoom_img+'</a>';
                }
                if (json.displayimgthumb=="1") 
                    jQuery("#list_product_image_thumb").html(html_thumb_img);
                else
                    jQuery("#list_product_image_thumb").html("");
                jQuery("#list_product_image_middle").html(html_middle_img);
                initJSlightBox();
            }

			if (json.block_image_thumb || json.block_image_middle){
                jQuery("#list_product_image_thumb").html(json.block_image_thumb);            
                jQuery("#list_product_image_middle").html(json.block_image_middle);
                //initJSlightBox();
            }
			
			
			if (typeof(json.demofiles)!='undefined'){
				jQuery("#list_product_demofiles").html(json.demofiles);
			}
            
            if (json.showdeliverytime){
                if (json.showdeliverytime=="0"){
                    jQuery(".productfull .deliverytime").hide();
                }else{
                    jQuery(".productfull .deliverytime").show();
                }
            }

            jQuery.each(reloadAttribEvents, function(key, handler){
                handler.call(this, json);
            });

            reloadAttrValue();
			jQuery(document).ready(function() {
				jQuery('#list_product_image_middle').magnificPopup({
				  delegate: 'a',
				  type: 'image'
				});
			});
        }
    );
}

function setAttrValue(id, value){
    attr_value[id] = value;
    reloadAttribSelectAndPrice(id);
    reloadAttribImg(id, value);
}

function reloadAttribImg(id, value){
    var path = "";
    var img = "";
    if (value=="0"){
        img = "";
    }else{
        if (attr_img[value]){
            img = attr_img[value];
        }else{
            img = "";
        }
    }
    
    if (img==""){
        path = liveimgpath;
        img = "blank.gif";
    }else{
        path = liveattrpath;
    }
    jQuery("#prod_attr_img_"+id).attr('src', path+"/"+img);
}

function reloadAttrValue(){    
    for(var id in attr_value){
        if (jQuery("input[name=jshop_attr_id\\["+id+"\\]]").attr("type")=="radio"){
            attr_value[id] = jQuery("input[name=jshop_attr_id\\["+id+"\\]]:checked").val();
        }else{
            attr_value[id] = jQuery("#jshop_attr_id"+id).val();
        }
    }
		jQuery(function($) {
			$("select").niceSelect();
		});
}

function reloadPrices(){
    var qty = jQuery("#quantity").val();
    if (qty!=""){
        reloadAttribSelectAndPrice(0);
    }
}