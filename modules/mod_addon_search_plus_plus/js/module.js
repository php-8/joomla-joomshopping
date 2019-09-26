    /*
    * @version      1.1.9 26.01.2019
    * @author       Garry
    * @package      update.php
    * @copyright    Copyright (C) 2019 joom-shopping.com. All rights reserved.
    * @license      GNU/GPL
    */

jQuery(document).ready(function($) {
    $(function() {
        $(document).click(function(e) {
            var el = $('.addon_search_plus_plus .popup');
            if ($(e.target).closest(el).length) {
                return;
            }
            el.hide();
        });
    });
    $('.addon_search_plus_plus #jshop_search').on(
        'input',
        function() {
            if (!$(this).val().length) {
                $(this).closest('.addon_search_plus_plus').find('.reset_search').removeClass('visible');
            }
        }
    );
    $('.addon_search_plus_plus .reset_search').click(function() {
        $(this).closest('.addon_search_plus_plus').find('#jshop_search').val('').trigger('input');
    });
    $('.addon_search_plus_plus .popup').on(
        'click',
        '.suggestions a',
        function() {
            $('.addon_search_plus_plus #jshop_search').val($(this).html().trim()).trigger('input');
            return false;
        }
    );
});

var searchppareq = null;

var AddonSearchPlusPlus = (function($) {

    return new function AddonSearchPlusPlus() {

        this.alias   = 'addon_search_plus_plus';
        this.options = Joomla.getOptions(this.alias);
        this.qty_min = Joomla.getOptions('changable_qty').qty_min;
        this.qty_max = Joomla.getOptions('changable_qty').qty_max;

        this.addTo = function(el, inp) {
            var btn    = $(el),
                item   = btn.closest('.product'),
                id     = +item.find('[name^="product_id"]').val(),
                el_qty =  item.find('[name^="quantity"]');
            btn.attr(
                'href',
                btn.attr('href').replace(
                    /&quantity=[0-9]+/g,
                    '&quantity=' + Math.abs(+inp.value)
                )
            );
            if (
                typeof AddonShoppingUnderage !== 'undefined' &&
                !AddonShoppingUnderage.showConfirmWindow(btn, [id])
            ) {
                return false;
            }
            if (
                btn.hasClass('add_to_cart') &&
                typeof AddonAjaxCartLight !== 'undefined' &&
                !AddonAjaxCartLight.addToCart(
                    btn,
                    [
                        {
                            id       : id,
                            quantity : el_qty.length ? +el_qty.val() : 1
                        }
                    ]
                )
            ) {
                return false;
            }
            return true;
        };

        this.ajax = function(controller, task, args, callback, extras, admin) {
            if (searchppareq){
				searchppareq.abort();
			}
			searchppareq = $.post(
                this.options.root + (admin === true ? 'administrator/' : ''),
                jQuery.extend(
                    extras,
                    {
                        option     : 'com_jshopping',
                        controller : this.alias + (controller.length ? ('_' + controller) : ''),
                        task       : task,
                        args       : JSON.stringify(args),
                        ajax       : true
                    }
                ),
                callback,
                'json'
            );
        };

        this.changeQty = function(el, inp) {
            var val = Math.abs(+inp.value) + ($(el).hasClass('qty-plus') ? 1 : -1);
            if (val >= this.qty_min && (val <= this.qty_max || !this.qty_max)) {
                $(inp).val(val);
            }
        };

        this.getPopupEl = function() {
            return $('.' + this.alias + ' .popup');
        };

        this.search = function(
            el,
            search_type,
            category_id,
            include_subcat,
            manufacturer_id,
            price_from,
            price_to,
            date_from,
            date_to
        ) {
            var self   = this,
                search = String($(el).val()),
                data   = {
                    search          : search,
                    search_type     : search_type,
                    category_id     : category_id,
                    include_subcat  : include_subcat,
                    manufacturer_id : manufacturer_id,
                    price_from      : price_from,
                    price_to        : price_to,
                    date_from       : date_from,
                    date_to         : date_to,
                    setsearchdata   : false
                };
            if (search.length) {
				$(el).addClass('load');
				$(el).closest('.addon_search_plus_plus').find('.reset_search').removeClass('visible');
                this.ajax(
                    '',
                    'search',
                    Object.values(data),
                    function(res) {
                        self.getPopupEl().html(res).show();
						$(el).removeClass('load');
						$(el).closest('.addon_search_plus_plus').find('.reset_search').addClass('visible');
                    },
                    data
                );
            }
        };

    };

})(jQuery);