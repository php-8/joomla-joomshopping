<?php
/**
 * mod_vertical_menu - Vertical Menu
 *
 * @author    Balint Polgarfi
 * @copyright 2014-2019 Offlajn.com
 * @license   https://gnu.org/licenses/gpl-2.0.html
 * @link      https://offlajn.com
 */

defined('_JEXEC') or die('Restricted access');

if (!class_exists('OfflajnJshoppingMenu2')) {

  if (!is_dir(JPATH_ROOT . '/components/com_jshopping/controllers')) {
    echo JText::_("JShopping component is not installed!");
    return;
  }

  require_once dirname(__FILE__) . '/../../core/MenuBase.php';

  class OfflajnJshoppingMenu2 extends OfflajnMenuBase2
  {
    public function __construct($module, $params)
    {
      parent::__construct($module, $params);
    }

    public function getAllItems()
    {
      $db = JFactory::getDBO();
      $lang = JFactory::getLanguage()->getTag();
      $categoryid = $this->_params->get('categoryid');
      $query = "SELECT DISTINCT
        category_id AS id,
        `name_$lang` AS name, ";
      if ($this->_params->get('displaynumprod', 0) != 0) {
        $query .= "(SELECT COUNT(*) FROM #__jshopping_products_to_categories AS ax LEFT JOIN #__jshopping_products AS bp ON ax.product_id = bp.product_id WHERE ax.category_id = id AND bp.product_publish=1";
        $query .= ") AS productnum, ";
      } else {
        $query .= "0 AS productnum, ";
      }
      if (!is_array($categoryid) && $categoryid != 0) {
        $query .= "IF(category_parent_id = " . $categoryid . ", 0 , IF(category_parent_id = 0, -1, category_parent_id)) AS parent, ";
      } elseif (count($categoryid) && is_array($categoryid) && !in_array('0', $categoryid)) {
        $query .= "IF(category_id in (" . implode(',', $categoryid) . "), 0 , IF(category_parent_id = 0, -1, category_parent_id)) AS parent, ";
      } else {
        $query .= "category_parent_id AS parent, ";
      }
      $query .= "'cat' AS typ ";
      $query .= " FROM #__jshopping_categories WHERE category_publish =1 ";
      if ($this->_params->get('elementorder', 0) == 0) {
        $query .= "ORDER BY ordering ASC, `name_$lang` DESC";
      } else if ($this->_params->get('elementorder', 0) == 1) {
        $query .= "ORDER BY `name_$lang` ASC";
      } else if ($this->_params->get('elementorder', 0) == 2) {
        $query .= "ORDER BY `name_$lang` DESC";
      }

      $db->setQuery($query);
      $allItems = $db->loadObjectList('id');

      if ($this->_params->get('showcontents') == 1) {
        $query = "
          SELECT DISTINCT b.product_id, concat( a.category_id, '-', a.product_id ) AS id, b.product_name AS name, a.category_id AS parent, 'prod' AS typ, 0 AS productnum
          FROM #__jshopping_products_to_categories AS a
          LEFT JOIN #__jshopping_products AS b ON a.product_id = b.product_id
          WHERE product_publish = 1 ";
        if ($this->_params->get('elementorder', 0) == 2) {
          $query .= "ORDER BY name_$lang DESC";
        } else {
          $query .= "ORDER BY name_$lang ASC";
        }

        $db->setQuery($query);
        $allItems += $db->loadObjectList('id');
      }
      return $allItems;
    }

    public function getActiveItem()
    {
      $active = null;
      if (JRequest::getVar('option') == 'com_jshopping') {
        $content_id = 0;
        $category_id = 0;
        if (JRequest::getString('controller') == "category") {
          $category_id = JRequest::getInt('category_id');
        } elseif (JRequest::getString('controller') == "product") {
          $content_id = JRequest::getInt('product_id');
          $category_id = JRequest::getInt('category_id');
        }
        if ($content_id > 0 && $this->_params->get('showcontents')) {
          $active = new StdClass();
          $active->id = $category_id . "-" . $content_id;
        } elseif ($category_id > 0) {
          $active = new StdClass();
          $active->id = $category_id;
        }
      }
      return $active;
    }

    public function getItemsTree()
    {
      $items = $this->getItems();

      return $items;
    }

    public function filterItem(&$item)
    {
      $item->nname = $item->title = stripslashes($item->name);

      if (!empty($item->productnum)) {
        $length = strlen($item->productnum) == 1 ? "one" : "more";
        if ($this->_params->get('displaynumprod', 0) == 1 && $item->typ == 'cat' && $item->productnum > 0) {
          $item->number = '<span class="productnum ' . $length . '">' . $item->productnum . '</span>';
          $item->nname .= $item->number;
        } elseif ($this->_params->get('displaynumprod', 0) == 2 && $item->typ == 'cat') {
          $item->number = '<span class="productnum ' . $length . '">' . $item->productnum . '</span>';
          $item->nname .= $item->number;
        }
      }
      $item->nname = '<span>' . $item->nname . '</span>';

      $item->anchorAttr = '';
      if ($item->typ == 'cat') {
        if ($this->_params->get('parentlink') == 0 && $item->p) {
          $item->nname = '<a>' . $item->nname . '</a>';
        } else {
          $item->anchorAttr = 'href="' . JRoute::_('index.php?option=com_jshopping&controller=category&task=view&category_id=' . $item->id) . '"';
          $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
        }
      } elseif ($item->typ == 'prod') {
        $id = explode("-", $item->id);
        $item->anchorAttr = 'href="' . JRoute::_('index.php?option=com_jshopping&controller=product&task=view&product_id=' . $id[1] . '&category_id=' . $id[0]) . '"';
        $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
      }
    }

  }
}
