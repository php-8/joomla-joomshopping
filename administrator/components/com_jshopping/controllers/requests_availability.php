<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerRequests_availability extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );
        checkAccessController("requests_availability");
        addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false){
        $addon = JSFactory::getTable('addon');
        $addon->loadAlias('inform_availability');
        $addonParams = $addon->getParams();
        
        $mainframe = JFactory::getApplication();
        $id_vendor_cuser = getIdVendorForCUser();
        $reviews_model = $this->getModel("requests_availability");
        $products_model = $this->getModel("products");
        $context = "jshoping.list.admin.request_availability";
        $limit = $mainframe->getUserStateFromRequest( $context.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
        $limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
        $category_id = $mainframe->getUserStateFromRequest( $context.'category_id', 'category_id', 0, 'int' );            
        $sent_email = $mainframe->getUserStateFromRequest( $context.'sent_email', 'sent_email', 0, 'int' ); 
        $text_search = $mainframe->getUserStateFromRequest( $context.'text_search', 'text_search', '');
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "pr_rew.date", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "desc", 'cmd');
        
        if ($category_id){
            $product_id = $mainframe->getUserStateFromRequest( $context.'product_id', 'product_id', 0, 'int' );
        }
        
        $products_select = "";
        
        $sent_email_option[] = JHTML::_('select.option', '0', _JSHOP_ALL , 'id', 'name');
        $sent_email_option[] = JHTML::_('select.option', '1', _JSHOP_YES , 'id', 'name');
        $sent_email_option[] = JHTML::_('select.option', '2', _JSHOP_NO , 'id', 'name');
        $sent_email_select = JHTML::_('select.genericlist', $sent_email_option, 'sent_email', 'class = "inputbox" onchange="document.adminForm.submit();" size = "1" ', 'id', 'name', $sent_email);

        if ($category_id){
            $prod_filter = array("category_id"=>$category_id);
            if ($id_vendor_cuser) $prod_filter['vendor_id'] = $id_vendor_cuser;
            $products = $products_model->getAllProducts($prod_filter, 0, 100);
            if (count($products)) {
                $start_pr_option = JHTML::_('select.option', '0', _JSHOP_SELECT_PRODUCT , 'product_id', 'name');
                array_unshift($products, $start_pr_option);   
                $products_select = JHTML::_('select.genericlist', $products, 'product_id', 'class = "inputbox" onchange="document.adminForm.submit();" size = "1" ', 'product_id', 'name', $product_id);
            }
        }
        $total = $reviews_model->getAllRequests_availability($category_id, $product_id, $sent_email, NULL, NULL, $text_search, "count", $id_vendor_cuser, $filter_order, $filter_order_Dir, $addonParams);
        
        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);
    
        $reviews = $reviews_model->getAllRequests_availability($category_id, $product_id, $sent_email, $pagination->limitstart, $pagination->limit, $text_search, "list", $id_vendor_cuser, $filter_order, $filter_order_Dir, $addonParams);

        $start_option = JHTML::_('select.option', '0', _JSHOP_SELECT_CATEGORY,'category_id','name'); 
        
        $categories_select = buildTreeCategory(0,1,0);
        array_unshift($categories_select, $start_option);

        $categories = JHTML::_('select.genericlist', $categories_select, 'category_id', 'class = "inputbox" onchange="document.adminForm.submit();" size = "1" ', 'category_id', 'name', $category_id);
        $view=$this->getView("requests_availability", 'html');
        $view->setLayout("list");
        $view->assign('categories', $categories);
        $view->assign('reviews', $reviews); 
        $view->assign('limit', $limit);
        $view->assign('limitstart', $limitstart);
        $view->assign('text_search', $text_search); 
        $view->assign('pagination', $pagination); 
        $view->assign('sent_email_select', $sent_email_select);
        $view->assign('products_select', $products_select);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
        $view->assign('addonParams', $addonParams);
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayRequests_availability', array(&$view));
        $view->displayList();
     }
     
     function remove(){
        $reviews_model = $this->getModel("Requests_availability");   
        $cid = JRequest::getVar('cid');
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeRemoveRequests_availability', array(&$cid) );
        
        foreach ($cid as $key => $value) {
             $reviews_model->deleteRequests_availability($value);             
             
        }
        $dispatcher->trigger('onAfterRemoveRequests_availability', array(&$cid));
        
        $this->setRedirect("index.php?option=com_jshopping&controller=requests_availability");       
     }
}