<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

class JshoppingViewRequests_availability extends JViewLegacy
{
    function displayList($tpl = null){        
        JToolBarHelper::title(_JSHOP_REQUESTS_AVAILABILITY, 'generic.png' ); 
        JToolBarHelper::deleteList();        
        parent::display($tpl);
	}
    
}
?>