<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class Excel2jsViewYml extends JViewLegacy {

	function display($tpl = null) {
		$db = JFactory::getDBO();

		$title = $GLOBALS['component_name'].'. '.JText::_('YML');
		$model =  $this->getModel();



		//$this->assign('config', $model->config);
		//$this->assign('fields', $model->active);



		$this->assign('profiles', $model->profile_list_yml(true));
		$this->assign('yml_config', $model->getYmlConfig());
		$this->assign('yml_export_config', $model->getYmlExportConfig());

        @$this->assign('currencies', $this->get('Currencies'));
        @$this->assign('profile_data', $this->get('Profile'));
        @$this->assign('languages', $this->get('Languages'));

        @$this->assign('manufacturers', $this->get('Manufacturers'));
        @$this->assign('export_categories', $model->getCategoryList(@$this->yml_export_config->export_categories?$this->yml_export_config->export_categories:0));
		JToolBarHelper :: title($title, 'logo');
		JToolBarHelper :: preferences('com_excel2js',500);


		parent :: display($tpl);
	}

}

?>