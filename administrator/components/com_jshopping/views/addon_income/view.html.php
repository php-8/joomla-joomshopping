<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

class JshoppingViewAddon_income extends JViewLegacy
{
    function display($tpl = null)
    {
        JToolBarHelper::title(_INCOME, 'generic.png' ); 
        
        parent::display($tpl);
	}
}
?>