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

if (!class_exists('OfflajnHikashopMenu2')) {

  if (!is_dir(JPATH_ROOT . '/components/com_hikashop/controllers')) {
    echo JText::_("Hikashop component is not installed!");
    return;
  }

  require_once dirname(__FILE__) . '/../../core/MenuBase.php';
  require_once JPATH_ADMINISTRATOR . '/components/com_hikashop/helpers/helper.php';

  class OfflajnHikashopMenu2 extends OfflajnMenuBase2
  {
    public function __construct($module, $params)
    {
      parent::__construct($module, $params);
      $this->hikaMenu = hikashop_get('class.menus');
      $this->hikaProduct = hikashop_get('class.product');
    }

    public function getAllItems()
    {
      $db = JFactory::getDBO();
      $filters = array(); //get ACL filters
      hikashop_addACLFilters($filters, "category_access");
      hikashop_addACLFilters($filters, "product_access");

      $filter = "";
      if (isset($filters[0])) {
        $filter = " AND " . $filters[0] . " ";
      }

      $categoryid = explode("|", $this->_params->get('hikashopcategoryid'));
      $query = "SELECT DISTINCT
        category_id AS id,
        category_name AS name, category_alias AS alias, ";
      if ($this->_params->get('displaynumprod', 0) != 0) {
        $query .= "(SELECT COUNT(*) FROM #__hikashop_product_category AS ax LEFT JOIN #__hikashop_product AS bp ON ax.product_id = bp.product_id WHERE ax.category_id = id AND bp.product_published=1";
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
      $query .= " FROM #__hikashop_category
                WHERE ((category_published =1 AND category_type='product') OR (category_type='root' AND category_published =0)) " . $filter;
      if ($this->_params->get('elementorder', 0) == 0) {
        $query .= "ORDER BY category_ordering ASC, category_name DESC";
      } else if ($this->_params->get('elementorder', 0) == 1) {
        $query .= "ORDER BY category_name ASC";
      } else if ($this->_params->get('elementorder', 0) == 2) {
        $query .= "ORDER BY category_name DESC";
      }

      $db->setQuery($query);

      $allItems = $db->loadObjectList('id');

      if ($this->_params->get('showproducts') == 1) {
        if ($filters[1]) {
          $filter = " AND " . $filters[1] . " ";
        }

        $query = "
          SELECT DISTINCT b.product_id, concat( a.category_id, '-', a.product_id ) AS id, b.product_name AS name, a.category_id AS parent, 'prod' AS typ, 0 AS productnum
          FROM #__hikashop_product_category AS a
          LEFT JOIN #__hikashop_product AS b ON a.product_id = b.product_id
          WHERE product_published = 1 " . $filter;
        if ($this->_params->get('elementorder', 0) == 2) {
          $query .= "ORDER BY product_name DESC";
        } else {
          $query .= "ORDER BY product_name ASC";
        }

        $db->setQuery($query);

        $allItems += $db->loadObjectList('id');
      }
      return $allItems;
    }

    public function getActiveItem()
    {
      $active = null;
      if (JRequest::getVar('option') == 'com_hikashop') {
        $content_id = 0;
        $category_id = 0;
        if (JRequest::getString('ctrl') == "category") {
          $category_id = JRequest::getInt('cid');
        } elseif (JRequest::getString('ctrl') == "product") {
          $content_id = JRequest::getInt('cid');
          $category_id = JRequest::getInt('categoryp');
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
          $Itemid = (int) $this->_params->get('hikashopitemid');
          if (!$Itemid) {
            $Itemid = (int) $this->hikaMenu->getItemidFromCategory($item->id, "category");
          }
          $item->anchorAttr = 'href="' . JRoute::_("index.php?option=com_hikashop&ctrl=category&task=listing&name={$item->alias}&cid={$item->id}&Itemid=$Itemid") . '"';
          $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
        }
      } elseif ($item->typ == 'prod') {
        $id = explode("-", $item->id);
        $product = $this->hikaProduct->get((int) $id[1]);
        $Itemid = (int) $this->_params->get('hikashopitemid');
        if (!$Itemid) {
          $Itemid = (int) $this->hikaMenu->getItemidFromCategory($product->product_id, "product");
        }
        $url_itemid = $Itemid ? '&Itemid=' . $Itemid : '';
        $this->hikaProduct->addAlias($product);
        $item->anchorAttr = 'href="' . hikashop_contentLink('product&task=show&cid=' . $product->product_id . '&name=' . $product->alias . $url_itemid, $product) . '"';
        $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
      }
    }

  }
}
