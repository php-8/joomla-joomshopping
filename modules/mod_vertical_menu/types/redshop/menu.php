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

if (!class_exists('OfflajnRedshopMenu2')) {

  if (!is_dir(JPATH_ROOT . '/components/com_redshop/controllers')) {
    echo JText::_("RedShop component is not installed!");
    return;
  }

  require_once dirname(__FILE__) . '/../../core/MenuBase.php';

  class OfflajnRedshopMenu2 extends OfflajnMenuBase2
  {

    public function __construct($module, $params)
    {
      parent::__construct($module, $params);
    }

    public function getAllItems()
    {
      $db = JFactory::getDBO();

      $categoryid = explode("|", $this->_params->get('redshopcategoryid'));

      $query = "SELECT DISTINCT
        category_id AS id,
        category_name AS name, ";
      if ($this->_params->get('displaynumprod', 0) != 0) {
        $query .= "(SELECT COUNT(*) FROM #__redshop_product_category_xref AS ax LEFT JOIN #__redshop_product AS bp ON ax.product_id = bp.product_id WHERE ax.category_id = id";
        if (CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
          $query .= " AND bp.product_in_stock > 0 ";
        }
        $query .= ") AS productnum, ";
      } else {
        $query .= "0 AS productnum, ";
      }
      if (!is_array($categoryid) && $categoryid != 0) {
        $query .= "IF(f.category_parent_id = " . $categoryid . ", 0 , IF(f.category_parent_id = 0, -1, f.category_parent_id)) AS parent, ";
      } elseif (count($categoryid) && is_array($categoryid) && !in_array('0', $categoryid)) {
        $query .= "IF(a.category_id in (" . implode(',', $categoryid) . "), 0 , IF(f.category_parent_id = 0, -1, f.category_parent_id)) AS parent, ";
      } else {
        $query .= "f.category_parent_id AS parent, ";
      }
      $query .= "'cat' AS typ ";
      $query .= " FROM #__redshop_category AS a, #__redshop_category_xref AS f
                WHERE published=1 AND a.category_id = f.category_child_id ";
      if ($this->_params->get('elementorder', 0) == 0) {
        $query .= "ORDER BY ordering ASC, name ASC";
      } else if ($this->_params->get('elementorder', 0) == 1) {
        $query .= "ORDER BY name ASC";
      } else if ($this->_params->get('elementorder', 0) == 2) {
        $query .= "ORDER BY name DESC";
      }

      $db->setQuery($query);

      $allItems = $db->loadObjectList('id');

      if ($this->_params->get('showcontents') == 1) {
        $query = "
          SELECT DISTINCT b.product_id, concat( a.category_id, '-', a.product_id ) AS id, b.product_name AS name, a.category_id AS parent, 'prod' AS typ, 0 AS productnum
          FROM #__redshop_product_category_xref AS a
          LEFT JOIN #__redshop_product AS b ON a.product_id = b.product_id
          WHERE b.product_parent_id =0 ";
        if ($this->_params->get('elementorder', 0) == 1) {
          $query .= "ORDER BY name ASC";
        } else {
          $query .= "ORDER BY name DESC";
        }

        $db->setQuery($query);
        $allItems += $db->loadObjectList('id');
      }
      return $allItems;
    }

    public function getActiveItem()
    {
      $active = null;
      if (JRequest::getVar('option') == 'com_redshop') {
        $content_id = 0;
        $category_id = 0;
        if (JRequest::getVar('view') == "category") {
          $category_id = JRequest::getInt('cid');
        } elseif (JRequest::getVar('view') == "product") {
          $content_id = JRequest::getInt('pid');
          $category_id = JRequest::getInt('cid');
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
      return $this->getItems();
    }

    public function filterItem(&$item)
    {
      $item->nname = $item->title = stripslashes($item->name);

      $length = strlen($item->productnum) == 1 ? "one" : "more";
      if ($this->_params->get('displaynumprod', 0) == 1 && $item->typ == 'cat' && $item->productnum > 0) {
        $item->number = '<span class="productnum ' . $length . '">' . $item->productnum . '</span>';
        $item->nname .= $item->number;
      } elseif ($this->_params->get('displaynumprod', 0) == 2 && $item->typ == 'cat') {
        $item->number = '<span class="productnum ' . $length . '">' . $item->productnum . '</span>';
        $item->nname .= $item->number;
      }
      $item->nname = '<span>' . $item->nname . '</span>';

      $item->anchorAttr = '';
      if ($item->typ == 'cat') {
        if ($this->_params->get('parentlink') == 0 && $item->p) {
          $item->nname = '<a>' . $item->nname . '</a>';
        } else {
          $item->anchorAttr = 'href="' . JRoute::_('index.php?option=com_redshop&view=category&cid=' . $item->id . '&layout=detail&Itemid=' . JRequest::getVar('Itemid')) . '"';
          $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
        }
      } elseif ($item->typ == 'prod') {
        $id = explode("-", $item->id);
        $item->anchorAttr = 'href="' . JRoute::_('index.php?option=com_redshop&view=product&pid=' . $id[1] . '&cid=' . $id[0] . '&Itemid=' . JRequest::getVar('Itemid')) . '"';
        $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
      }
    }

  }
}
