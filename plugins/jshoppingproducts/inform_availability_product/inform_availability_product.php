<?php
defined('_JEXEC') or die('Restricted access');
class plgJshoppingProductsInform_availability_product extends JPlugin{

    function __construct(& $subject, $config){
        parent::__construct($subject, $config);
        JSFactory::loadExtLanguageFile('addon_inform_availability_product');
        JHTML::_('behavior.modal', 'a.jsh_modal');
    }
	
    public function onBeforeLoadProduct(){
        $document = JFactory::getDocument();
        $document->addCustomTag('<script type ="text/javascript">
            reloadAttribEvents[reloadAttribEvents.length] = function(json){
                if (json.available=="0"){
                    jQuery("#product_attr_id").val(json.product_attr_id);
                    jQuery("#iap_show_link").show();
                } else {
                    jQuery("#iap_show_link").hide()
                }
            }
        </script>');
    }
    
    public function onBeforeDisplayAjaxAttrib(&$rows, &$product){
        $product_attr_id = '';
        if (isset($product->attribute_active_data->product_attr_id)){
            $product_attr_id = $product->attribute_active_data->product_attr_id;
        }
        $rows[] = '"product_attr_id":"'.$product_attr_id.'"';
    }
    
	public function _getUrlShowForm($product_id){
        return SEFLink('index.php?option=com_jshopping&controller=inform_availability_product&task=showform&tmpl=component&prod_id='.$product_id,1);
	}
	
	public function _getLinkShowForm($product_id) {
        $params = $this->getParams();
        $popupWidth = (isset($params['popup_size_width']) && $params['popup_size_width'] > 0) ? $params['popup_size_width'] : 250;
        $popupHeight = (isset($params['popup_size_height']) && $params['popup_size_height'] > 0) ? $params['popup_size_height'] : 200;

		return '<a class="jsh_modal inform_availability" href="'.self::_getUrlShowForm($product_id).'" rel="{handler: \'iframe\', size: {x: '.$popupWidth.', y: '.$popupHeight.'}}">'._JSHOP_PRODUCT_AVAILE.'</a>';        
	}
    
    public function onBeforeDisplayProductView(&$view){
        $template_place = '_tmp_product_html_before_buttons';
        if (!$view->$template_place) $view->$template_place = '';
        $style = 'style="display:none"';
        if (($view->product->getQty() <= 0) && $view->product->product_quantity >0 ){
            $style = '';
        }elseif ($view->product->product_quantity <= 0){
            $style = '';
        }
        $view->_tmp_product_html_before_buttons .= 
        '<div id="iap_show_link" '.$style.'>'.
            self::_getLinkShowForm($view->product->product_id).
            '<input type="hidden" id="product_attr_id" name="product_attr_id" value="'.$view->product->attribute_active_data->product_attr_id.'">'.
        '</div>';
    }
    
    public function onBeforeDisplayProductList(&$products){
        $params = $this->getParams();
        
		if (count($products) && isset($params['show_on_products_list']) && $params['show_on_products_list'] == 1) {
			$template_place = '_tmp_var_top_buttons';
			foreach ($products as $k=>$v) {
				if ($v->product_quantity <= 0){
					if (!$products[$k]->$template_place) $products[$k]->$template_place = '';
					$products[$k]->$template_place .= self::_getLinkShowForm($v->product_id);
				}
			}
        }
    }
    
    public function getParams(){
        static $addonParams = null;
        
        if ($addonParams === null){
            $addon = JSFactory::getTable('addon');
            $addon->loadAlias('inform_availability');
            $addonParams = $addon->getParams();
        }
        
        return $addonParams;
    }
}