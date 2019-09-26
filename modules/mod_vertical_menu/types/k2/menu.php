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

if (!class_exists('OfflajnK2Menu2')) {

  if (!is_dir(JPATH_SITE . '/components/com_k2/helpers')) {
    echo JText::_("K2 component is not installed!");
    return;
  }

  require_once dirname(__FILE__) . '/../../core/MenuBase.php';
  require_once JPATH_SITE . '/components/com_k2/helpers/route.php';

  class OfflajnK2Menu2 extends OfflajnMenuBase2
  {

    public function __construct($module, $params)
    {
      parent::__construct($module, $params);
    }

    public function getAllItems()
    {
      $db = JFactory::getDBO();
      $categoryid = explode("|", $this->_params->get('k2categoryid'));

      //for user level check
      $user = JFactory::getUser();
      $aid = $user->get('aid');

      $query = "SELECT DISTINCT
        id AS id,
        name AS name,
        image as itemimage, ";
      if (!is_array($categoryid) && $categoryid != 0) {
        $query .= "IF(parent = " . $categoryid . ", 0 , IF(parent = 0, -1, parent)) AS parent, ";
      } elseif (count($categoryid) && is_array($categoryid) && !in_array('0', $categoryid)) {
        $query .= "IF(id in (" . implode(',', $categoryid) . "), 0 , IF(parent = 0, -1, parent)) AS parent, ";
      } else {
        $query .= "parent AS parent, ";
      }
      $query .= "'cat' AS typ ";
      $query .= " FROM #__k2_categories
                WHERE published=1 AND trash=0";

      if (K2_JVERSION != '15') {
        $query .= " AND access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ") ";
      } else {
        $query .= " AND access<={$aid} ";
      }

      if ($this->_params->get('elementorder', 0) == 0) {
        $query .= "ORDER BY ordering ASC, name DESC";
      } else if ($this->_params->get('elementorder', 0) == 1) {
        $query .= "ORDER BY name ASC";
      } else if ($this->_params->get('elementorder', 0) == 2) {
        $query .= "ORDER BY name DESC";
      }

      $db->setQuery($query);

      $allItems = $db->loadObjectList('id');

      if ($this->_params->get('showcontents') == 1) {
        $query = "
          SELECT concat(a.catid,'-',a.id) AS id, a.id AS id2,a.title AS name, a.catid AS parent, a.access, a.alias, 'con' AS typ, ";

        if ($this->_params->get('displaynumprod', 0) != 0) {
          $query .= "(SELECT COUNT(*) FROM #__k2_categories AS bp LEFT JOIN #__k2_items AS ax  ON ax.catid = bp.id WHERE bp.id=a.catid AND ax.published = 1 AND ax.trash = 0 ";
          $query .= ") AS productnum";
        } else {
          $query .= "0 AS productnum";
        }

        $query .= " FROM #__k2_items AS a
            WHERE a.published = 1 AND
            a.trash = 0 ";

        if (K2_JVERSION != '15') {
          $query .= " AND a.access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ") ";
        } else {
          $query .= " AND a.access<={$aid} ";
        }

        if ($this->_params->get('elementorder', 0) == 0) {
          $query .= "ORDER BY a.ordering ASC, a.title DESC";
        } else if ($this->_params->get('elementorder', 0) == 1) {
          $query .= "ORDER BY a.title ASC";
        } else if ($this->_params->get('elementorder', 0) == 2) {
          $query .= "ORDER BY a.title DESC";
        }

        $db->setQuery($query);
        $rows = $db->loadObjectList('id');
        $cats = array();
        $unset = "";
        $keys = array_keys($rows);
        for ($x = 0; $x < count($keys); ++$x) {
          $value = $rows[$keys[$x]];
          if (!isset($cats[$value->parent])) {
            $cats[$value->parent] = 0;
          }

          $cats[$value->parent]++;
          /*if ($cats[$value->parent]>$this->_params->get('maxitemsincat', 20))
        unset($rows[$keys[$x]]);  */
        }
        $allItems += $rows;
      }
      return $allItems;
    }

    public function getActiveItem()
    {
      $db = JFactory::getDBO();
      $active = null;
      if (JRequest::getVar('option') == 'com_k2') {
        $content_id = 0;
        $category_id = 0;
        if (JRequest::getVar('task') == "category") {
          $category_id = JRequest::getInt('id');
        } elseif (JRequest::getVar('view') == "item") {
          $content_id = JRequest::getInt('id');
          $query = "SELECT catid FROM #__k2_items WHERE id=" . $content_id;
          $db->setQuery($query);
          $category_id = $db->loadResult();
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
      $displaynumprod = $this->_params->get('displaynumprod', 0);
      if ($displaynumprod > 0) {
        for ($i = count($items); $i--;) {
          if (!($items[$i]->typ == "con" && $items[$i - 1]->typ == "con" && $items[$i]->parent == $items[$i - 1]->parent)) {
            if (!isset($items[$i]->parent->productnum)) {
              $items[$i]->parent->productnum = -1;
            }

            if ($displaynumprod == 1) {
              $items[$i]->parent->productnum++;
            } else {
              $items[$i]->parent->productnum += isset($items[$i]->productnum) ? $items[$i]->productnum : 1;
            }

          }
        }
      }
      return $items;
    }

    public function filterItem(&$item)
    {
      $item->title = $item->name;

      if (!empty($item->productnum)) {
        $length = strlen($item->productnum) == 1 ? "one" : "more";
        if ($this->_params->get('displaynumprod', 0) == 1 && $item->typ == 'cat' && @$item->productnum > 0) {
          $item->number = '<span class="productnum ' . $length . '">' . $item->productnum . '</span>';
          $item->nname .= $item->number;
        } elseif ($this->_params->get('displaynumprod', 0) == 2 && $item->typ == 'cat') {
          $item->number = '<span class="productnum ' . $length . '">' . $item->productnum . '</span>';
          $item->nname .= $item->number;
        }
      }
      $item->nname = '<span>' . $item->title . '</span>';

      $image = '';
      if ($this->_params->get('menu_images') && $item->itemimage != '') {
        $item->image = JUri::Root(false) . '/media/k2/categories/' . $item->itemimage;
        $image = '<img src="' . $item->itemimage . '" />';
        switch ($this->_params->get('menu_images_align', 0)) {
          case 1:
            $item->nname = $item->nname . $image;
            break;
          default:
            $item->nname = $image . $item->nname;
            break;
        }
      }

      $item->anchorAttr = '';
      if ($item->typ == 'cat') {
        if ($this->_params->get('parentlink') == 0 && $item->p) {
          $item->nname = '<a>' . $item->nname . '</a>';
        } else {
          $item->anchorAttr = 'href="' . JRoute::_(K2HelperRoute::getCategoryRoute($item->id)) . '"';
          $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
        }
      } elseif ($item->typ == 'con') {
        $id = explode("-", $item->id);
        $item->anchorAttr = 'href="' . JRoute::_(K2HelperRoute::getItemRoute($id[1], $id[0])) . '"';
        $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
      }
    }

  }
}
