var jshuserscart = {
	init:function(json){
		jQuery("form[name='adminForm'] input[name='cid[]']").each(function(i,e){
			e = jQuery(e);
			var cid = e.val();
			if(typeof json[cid] === "object" && json[cid]['cart']){
				jshuserscart.initRow(e,json[cid]['cart']);
			}
			if(typeof json[cid] === "object" && json[cid]['wishlist']){
				jshuserscart.initRowWishList(e,json[cid]['wishlist']);
			}                        
		});
	},
	initRow:function(row,data){
		tr = row.parents('tr').addClass('userCart');
		tr.find('td').each(function(i,e){
			e = jQuery(e);
			if(i == 2){
				e.prepend('<div class="userCartInfo"><span><b>'+data.date+'</b><br/>'+data.list+'<br/><b class="total">'+data.total+'</b>'+data.email+'</span></div>');
				var a = e.children('div.userCartInfo');
				var b = a.children('span');
				a.hover(
					function(){
						b.css('display','block');
					},
					function(){
						b.css('display','none');
					}
				);
			}
		});
		//console.log(data);
	},
        initRowWishList:function(row,data){
        tr = row.parents('tr').addClass('userCart');
        tr.find('td').each(function(i,e){
                e = jQuery(e);
                if(i == 2){
                        e.prepend('<div class="userWishlistInfo"><span><b>'+data.date+'</b><br/>'+data.list+'<br/><b class="total">'+data.total+'</b>'+data.email+'</span></div>');
                        var a = e.children('div.userWishlistInfo');
                        var b = a.children('span');
                        a.hover(
                                function(){
                                        b.css('display','block');
                                },
                                function(){
                                        b.css('display','none');
                                }
                        );
                }
        });
		
	}
}