<?php
/**
 * mod_vertical_menu - Vertical Menu
 *
 * @author    Balint Polgarfi
 * @copyright 2014-2019 Offlajn.com
 * @license   https://gnu.org/licenses/gpl-2.0.html
 * @link      https://offlajn.com
 */
?><?php
/*------------------------------------------------------------------------
# offlajnvm2categories - Offlajn Virtuemart 2.x.x Category Selector
# ------------------------------------------------------------------------
# author    Jeno Kovacs
# copyright Copyright (C) 2012 Offlajn.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.offlajn.com
-------------------------------------------------------------------------*/

defined('_JEXEC') or die('Restricted access');

if(!class_exists('JElementOfflajnMultiSelectList')) {
  require_once( (dirname(__FILE__)) . '/offlajnmultiselectlist.php');
}

if (!JComponentHelper::isEnabled('com_virtuemart')) return;
if (!class_exists( 'VmConfig' ) && file_exists(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php') ) require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
if(!class_exists( 'VmConfig' )) return;
$config= VmConfig::loadConfig();
if(!class_exists('TableCategories')) require(JPATH_VM_ADMINISTRATOR.'/tables/categories.php');
if (!class_exists( 'VirtueMartModelCategory' )) require(JPATH_VM_ADMINISTRATOR.'/models/category.php');

class fixedVirtueMartModelCategory extends VirtueMartModelCategory{
	function GetTreeCat($id=0,$maxLevel = 1000) {
		self::treeCat($id ,$maxLevel) ;
		return $this->container ;
	}

	function treeCat($id=0,$maxLevel =1000) {
		static $level = 0;
		$db = & JFactory::getDBO();
		$q = 'SELECT `category_child_id`, `category_name` FROM `#__virtuemart_categories_'.VMLANG.'` vcl
		LEFT JOIN `#__virtuemart_categories` vc ON vcl.`virtuemart_category_id` = vc.`virtuemart_category_id`
		LEFT JOIN `#__virtuemart_category_categories` vcc ON vcl.`virtuemart_category_id` = vcc.`category_child_id`
		WHERE vc.`published`=1 AND vcc.`category_parent_id`='.(int)$id.' ORDER BY vc.`ordering`';
		$db->setQuery($q);
		// if it is a leaf (no data underneath it) then return
		$childs = $db->loadObjectList();
		if ($level==$maxLevel) return;
		if ($childs) {
			$level++;
			foreach ($childs as $child) {
				$this->container[] = (object) array(
					'id' => $child->category_child_id,
					'name' => $child->category_name,
					'level' => $level
				);
				self::treeCat($child->category_child_id,$maxLevel );
			}
			$level--;
		}
	}
}

class JElementOfflajnVm2Categories extends JElementOfflajnMultiSelectList {

  function getItems(&$node) {
/*
    if($value == NULL) {
      $value = array();
    } elseif (!is_array($value)) {
      $value = array($value);
    }
    $values = array_flip($value);
    array_walk($values, 'ofSetOne');
*/
    $categoryModel = new fixedVirtueMartModelCategory();
    $cats = $categoryModel->GetTreeCat();

   // $multiple = 1 ? "multiple=\"multiple\"" : "";
    ob_start();
	//	echo "<select class=\"inputbox\" size=\"10\" $multiple name=\"".$name."[]\" id=\"".$this->generateId($name)."\">\n";
		if( 1 ) {
		//	$selected = (@$values[0] == "1") ? "selected=\"selected\"" : "";
		//	echo "<option ".$selected." value=\"0\">Top Level</option>\n";
		}
		foreach($cats AS $cat){
  		$selected = '';
  		if( $selected == "" && @$values[$cat->id] == "1") {
  		  //$selected = "selected=\"selected\"";
  		}
      //echo "<option $selected value=\"$cat->id\">\n";
      echo "///".$cat->id."///";
  		for ($i=0;$i<$cat->level;$i++) {
  			echo "&#160;";
  		}
  		echo "|$cat->level|";
  		//echo "&nbsp;" . $cat->name . "</option>";
  		echo "&nbsp;" . $cat->name;
		}
		//echo "</select>\n";
    $elements = ob_get_clean();
    $el = explode("///", $elements);

    $items = array();

    for($i=1;$i<count($el);$i += 2) {
      $items[$el[$i]] = $el[$i+1];
    }



    $groupedList = array();
    $i = 0;
    foreach($items as $k => $element) {
      $item = new stdClass();
      $item->treename = $element;
      $item->id = $k;
      //$groupedList['vm1'][$k] = $item;
      $groupedList['vm1'][$i] = $item;
      $i++;
    }


    //return ob_get_clean();

		// assemble into menutype groups
/*		$n = count( $list );
		$groupedList = array();
		foreach ($list as $k => $v) {
			$groupedList[$v->menutype][] = &$list[$k];
		}
*/
 return $groupedList;
  }

 /* if(!function_exists('ofSetOne')){
    function ofSetOne(&$item, $key){
      $item = 1;
  }
}*/
}

if(!function_exists('ofSetOne')){
  function ofSetOne(&$item, $key){
      $item = 1;
  }
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnVm2Categories extends JElementOfflajnVm2Categories {}
}