jQuery(document).ready(function()
{
    jQuery("ul.nav li a").click(function()
    {
        var href_attr = jQuery(this).attr("href");

        if ( href_attr == "#tab_products" ) 
        {
            jQuery(".products-form").attr("name", "adminForm");
            jQuery(".products-form").attr("id", "adminForm");
            jQuery(".orders-form").attr("name", "none");
            jQuery(".orders-form").attr("id", "none");
        } else {
              jQuery(".products-form").attr("name", "none");
              jQuery(".products-form").attr("id", "none");
              jQuery(".orders-form").attr("name", "adminForm");  
              jQuery(".orders-form").attr("id", "adminForm");  
        }
    });
});

function checkTabActive(tabactive)
{
    if ( tabactive.length > 0 )
      {
          jQuery("#editdata-document > div").attr("class", "tab-pane");
          jQuery("#tab_" + tabactive).attr("class", "tab-pane active");

          jQuery("ul.nav li").attr("class", "");
          var tabid_active = "#tab_" + tabactive;

          jQuery("ul.nav.nav-tabs li").each(function() 
            {
               var href_attr = jQuery(this).children().attr("href");

               if (tabid_active == href_attr)
                   {
                       jQuery(this).attr("class", "active");
                       return false;
                   }
             });

        if ( tabactive == "products" ) 
        {
            jQuery(".products-form").attr("name", "adminForm");
            jQuery(".products-form").attr("id", "adminForm");
            jQuery(".orders-form").attr("name", "none");
            jQuery(".orders-form").attr("id", "none");
        } else {
              jQuery(".products-form").attr("name", "none");
              jQuery(".products-form").attr("id", "none");
              jQuery(".orders-form").attr("name", "adminForm");  
              jQuery(".orders-form").attr("id", "adminForm");  
        }
      }
}