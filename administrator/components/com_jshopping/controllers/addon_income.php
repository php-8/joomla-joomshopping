<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerAddon_income extends JControllerLegacy
{
    function __construct( $config = array() )
    {
        parent::__construct($config);
        addSubmenu("other");
        checkAccessController("addon_income");
    }
    
    function display($cachable = false, $urlparams = false)
    {
        $document = JFactory::getDocument();
        $mainframe = JFactory::getApplication();
        
        $option = "income_paging";
        $list_limit = $mainframe->getCfg('list_limit');
        $tab_active = JRequest::getVar("tab");
        $limit = JRequest::getVar("limit", $list_limit);
        $limitstart = JRequest::getVar("limitstart", 0);
        
		// filter
		$filter = array();
		$from_date = JRequest::getVar("from_date");
		$to_date = JRequest::getVar("to_date");
		$filter['from_date'] = $from_date;
		$filter['to_date'] = $to_date;
		
        if ( $tab_active == "products" )
        {
            $mainframe->setUserState("$option.limit_pr", $limit);
            $mainframe->setUserState("$option.limitstart_pr", $limitstart);
        } else {
            $mainframe->setUserState("$option.limit_or", $limit);
            $mainframe->setUserState("$option.limitstart_or", $limitstart);
        }
           
        $jshopConfig = JSFactory::getConfig();
        $payment_status_arr = $jshopConfig->payment_status_enable_download_sale_file;
        $payment_status = implode(",", $payment_status_arr);
                        
        $model = $this->getModel("addon_income");
        
        $IOCount = $model->getIncomeOrdersCount($payment_status, $filter);
        $IPCount = $model->getIncomeProductsCount($payment_status, $filter);
        
        $limit_or = $mainframe->getUserState("$option.limit_or", $list_limit);
        $limitstart_or = $mainframe->getUserState("$option.limitstart_or");
        
        $limit_pr = $mainframe->getUserState("$option.limit_pr", $list_limit);
        $limitstart_pr = $mainframe->getUserState("$option.limitstart_pr");
            
        jimport('joomla.html.pagination');
        $IOpageNav = new JPagination($IOCount, $limitstart_or, $limit_or);
        $IPpageNav = new JPagination($IPCount, $limitstart_pr, $limit_pr);  
        
        $income_orders = $model->getIncomeOrders($limitstart_or, $limit_or, $payment_status, $filter);  
        $income_products = $model->getIncomeProducts($limitstart_pr, $limit_pr, $payment_status, $filter);  
        
        $IGtoday = $model->getIncomeGeneral('day', $payment_status);
        $IGweek = $model->getIncomeGeneral('week', $payment_status);
        $IGmonth = $model->getIncomeGeneral('month', $payment_status);  
        $IGyear = $model->getIncomeGeneral('year', $payment_status);
        
        $js_path_root = JUri::root()."/components/com_jshopping/addons/addon_income/js/";
        if ( version_compare(JVERSION, '3.0.0', '>=') ) {
            $js_file_name = "joomla30.js";
        }else{
            $js_file_name = "joomla25.js";            
        }
        $js_path = $js_path_root . $js_file_name;
        $document->addScript($js_path);
        
        $view = $this->getView("addon_income", 'html');
        $view->assign('today', $IGtoday);
        $view->assign('week', $IGweek); 
        $view->assign('month', $IGmonth);
        $view->assign('from_date', $from_date);
        $view->assign('to_date', $to_date);
        $view->assign('year', $IGyear); 
        $view->assign('income_orders', $income_orders); 
        $view->assign('income_products', $income_products);
        $view->assign('IOpageNav', $IOpageNav);
        $view->assign('IPpageNav', $IPpageNav); 
        $view->assign('tabactive', $tab_active); 
        $view->display();
    }
}

?>