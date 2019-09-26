<?php
defined('_JEXEC') or die('Restricted access');

class plgSystemAvangardInstallerScript{
	function preflight($route, $x){
		if ($route == 'install') {
			JFolder::move(dirname(__FILE__).'/pm_avangard',JPATH_ROOT .'/components/com_jshopping/payments/pm_avangard');
		}
    if ($route == 'uninstall') {
      JFolder::delete(JPATH_ROOT .'/components/com_jshopping/payments/pm_avangard');
    }
	}

  function install($x){
    $db = JFactory::getDBO();
    $db->setQuery('insert into #__jshopping_payment_method (payment_code, payment_class,  payment_publish,  payment_type, price,  price_type,show_descr_in_email,`name_ru-RU`,`name_en-GB`) values("avangard","pm_avangard",0,2,0.00,0,0,"Оплата по карте","Оплата по карте")');
    $db->query();
    $id = $db->insertid();
    $db->setQuery('UPDATE #__extensions set enabled=1 where `type`="plugin" and (
        (element="avangard" and folder="system"))');
    $db->query();
    echo "<a href='index.php?option=com_jshopping&controller=payments&task=edit&payment_id=".$id."'>Перейти к настройке</a>";
  }

  function uninstall($x){
    $db = JFactory::getDBO();
    $db->setQuery('delete from  #__jshopping_payment_method where payment_class="pm_avangard"');
    $db->query();
  }


}