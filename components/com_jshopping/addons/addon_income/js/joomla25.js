jQuery(document).ready(function()
{
    jQuery("#pane dt").click(function()
    {
        var pane_class = jQuery(this).attr("class");

        var pane_subclass = pane_class.split(" ");

        if ( pane_subclass[0] == "products" ) 
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