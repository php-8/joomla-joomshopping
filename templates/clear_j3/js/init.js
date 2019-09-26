jQuery(function ($) {
    $('.jshop .jshop_categ .category_name, .jshop .block_product .name, .jshop .block_item .name, .jshop_related .name').matchHeight();
    $('.jshop .block_item .description').matchHeight();

    $('.slider .slider-inner').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: true,
        infinite: true,
        arrows: true,
        autoplay: true,
        autoplaySpeed: 8000,
        prevArrow: $('.slider .slick-prev'),
        nextArrow: $('.slider .slick-next')
    });

    $('.manufactuter_list .manufactuter_list_inner').slick({
        slidesToShow: 3,
        slidesToScroll: 3,
        dots: false,
        infinite: false,
        arrows: true,
        responsive: [
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
});
