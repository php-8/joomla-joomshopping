<?php

defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_jshopping/lib/factory.php';
require_once JPATH_SITE.'/components/com_jshopping/lib/functions.php';

class addon_ajax_cart_with_relatedInstallerScript {
	
	private $minimum_php_release = '5.3.0';
	private $usekey = 0;
	private $install_extension = array (
		array (
			'type' => 'plugin',
			'element' => 'ajax_cart_with_related',
			'folder' => 'jshoppingcheckout',
			'enabled' => 1
		),
		array (
			'type' => 'module',
			'element' => 'mod_jshopping_ajax_cart_with_related',
			'folder' => '',
			'enabled' => 0
		)
	);
	private $install_folders = array (
	);
	private $install_files = array (
	);
	private $old_folders = array (
	);
	private $old_files = array (
	);
	private $name;
	private $scriptfile;
	private $element;

	private function setVar($parent) {
		$manifest = $parent->get('manifest');
		$this->name = (string)$manifest->name;
		$this->scriptfile = (string)$manifest->scriptfile;
		$this->element = substr($this->scriptfile, 0, -4);
		$this->version = (string)$manifest->version;
	}

	private function updateDataBase() {
	}

	function preflight($type, $parent) {
		$this->setVar($parent);
		$error = 0;
		$app = JFactory::getApplication();
		if (version_compare(phpversion(),$this->minimum_php_release,'<')) {
			$app->enqueueMessage($this->name.' requires PHP '.$this->minimum_php_release.' or later version!', 'error');
			$error = 1;
		}
		if ($this->usekey && !extension_loaded('bcmath')) {
			$app->enqueueMessage($this->name.' requires requires PHP BCMath extension!', 'error');
			$error = 1;
		}
		if ($error) {
			$app->enqueueMessage('The installation was canceled', 'error');
			return false;
		}
	}
	
	function install($parent) {
	}

	function update($parent) {
	}

	function postflight($type, $parent) {
		$installer = new JInstaller;
		$install_folder = JPATH_ROOT.'/tmp/'.$this->element;
		foreach($this->install_extension as $extension){
			if ($extension['type'] == 'plugin') {
				$folder = 'plugins/'.$extension['folder'].'/'.$extension['element'];
			} else {
				$folder = 'modules/'.$extension['element'];
			}
			if ($extension['checkversion'] && file_exists(JPATH_ROOT.'/'.$folder.'/'.$extension['element'].'.xml')) {
				$oldXML = JFactory::getXML(JPATH_ROOT.'/'.$folder.'/'.$extension['element'].'.xml');
				$xml = JFactory::getXML($install_folder.'/'.$folder.'/'.$extension['element'].'.xml');
				if (version_compare(trim($xml->version), trim($oldXML->version), '<')) {
					continue;
				}
			}
			$installer->install($install_folder.'/'.$folder);
			if ($extension['enabled']) {
				$t_extension = JTable::getInstance('Extension');
				$extension_id = $t_extension->find(array('type'=>$extension['type'], 'element'=>$extension['element'], 'folder'=>$extension['folder']));
				if ($extension_id) {
					$t_extension->load($extension_id);
					$t_extension->enabled = 1;
					$t_extension->store();
				}
			}
		}
		if (file_exists($install_folder)) {
			@JFolder::delete($install_folder);
		}
		
		$extension_root = $parent->getParent()->getPath('extension_root');
		$extension_source = $parent->getParent()->getPath('source');
		@JFile::copy($extension_source.'/'.$this->scriptfile, $extension_root.'/'.$this->scriptfile);
		
		$this->updateDataBase();

		foreach($this->old_folders as $folder){
			if (file_exists(JPATH_ROOT.'/'.$folder)) {
				@JFolder::delete(JPATH_ROOT.'/'.$folder);
			}
		}

		foreach ($this->old_files as $file) {
			if (file_exists(JPATH_ROOT.'/'.$file)) {
				@JFile::delete(JPATH_ROOT.'/'.$file);
			}
		}
		
		$manifest = $parent->getParent()->getManifest();
		$addon = JTable::getInstance('Addon', 'jshop');
		$addon->loadAlias($this->element);
		$addon->name = ''.JString::ucfirst(str_replace('_', ' ', $this->name)).'';
		$addon->version = $this->version;
		if (property_exists($addon, 'usekey')) {
			$addon->usekey = $this->usekey;
		}
		$addon->uninstall = str_replace(JPATH_ROOT,'',$parent->getParent()->getPath('extension_root')).'/'.$this->scriptfile;
		$addon->store();
		if ($this->usekey && !$addon->key) {
			$parent->getParent()->setRedirectURL('index.php?option=com_jshopping&controller=licensekeyaddon&alias='.$this->element.'&back='.base64_encode('index.php?option=com_jshopping&controller=addons'));
		} else {
			$parent->getParent()->setRedirectURL('index.php?option=com_jshopping&controller=addons');
		}
	}
	
	function uninstall($parent) {
		$this->setVar($parent);
		$installer = new JInstaller;
		foreach($this->install_extension as $extension){
			$extension_id = JTable::getInstance('Extension')->find(array('type'=>$extension['type'], 'element'=>$extension['element'], 'folder'=>$extension['folder']));
			if ($extension_id) {
				$installer->uninstall($extension['type'], $extension_id);
			}
		}

		foreach($this->install_folders as $folder){
			if (file_exists(JPATH_ROOT.'/'.$folder)) {
				@JFolder::delete(JPATH_ROOT.'/'.$folder);
			}
		}

		foreach($this->install_files as $file){
			if (file_exists(JPATH_ROOT.'/'.$file)) {
				@JFile::delete(JPATH_ROOT.'/'.$file);
			}
		}

		if (file_exists($parent->getParent()->getPath('extension_root').'/'.$this->scriptfile)) {
			@JFile::delete($parent->getParent()->getPath('extension_root').'/'.$this->scriptfile);
		}

		if (JFactory::getApplication()->input->getCmd('option') != 'com_jshopping') {
			$addon = JTable::getInstance('Addon', 'jshop');
			$addon->loadAlias($this->element);
			if ($addon->id) {
				$addon->delete();
			}
		}
	}
	
}

if (JFactory::getApplication()->input->getCmd('option') == 'com_jshopping') {
	$extension_id = JTable::getInstance('Extension')->find(array('type'=>'file', 'element'=>$row->alias));
	if ($extension_id) {
		JInstaller::getInstance()->uninstall('file', $extension_id);
	}
}
?>