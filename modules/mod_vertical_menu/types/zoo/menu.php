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

if (!class_exists('OfflajnZooMenu2')) {

  require_once dirname(__FILE__) . '/../../core/MenuBase.php';
  require_once JPATH_ADMINISTRATOR . '/components/com_zoo/config.php';

  class OfflajnZooMenu2 extends OfflajnMenuBase2
  {

    public function __construct($module, $params)
    {
      parent::__construct($module, $params);
    }

    public function getAllItems()
    {
      $db = JFactory::getDBO();
      $categoryid = explode("|", $this->_params->get('zoocategories', 0));
      $appname = $this->_params->get('zooapps');
      $query = "SELECT DISTINCT
        c.id AS id,
        c.name AS name, ";
      if (!is_array($categoryid) && $categoryid != 0) {
        $query .= "IF(c.parent = " . $categoryid . ", 0 , IF(c.parent = 0, -1, c.parent)) AS parent, ";
      } elseif (count($categoryid) && is_array($categoryid) && !in_array('0', $categoryid)) {
        $query .= "IF(c.parent in (" . implode(',', $categoryid) . "), 0 , IF(c.parent = 0, -1, parent)) AS parent, ";
      } else {
        $query .= "c.parent AS parent, ";
      }

      $query .= "'cat' AS typ, ";
      if ($this->_params->get('displaynumprod', 0) != 0) {
        $query .= "(SELECT COUNT(*) FROM #__zoo_category_item AS bp LEFT JOIN #__zoo_item AS ax ON ax.id = bp.item_id WHERE bp.category_id=c.id AND ax.state = 1 ";
        $query .= ") AS productnum";
      } else {
        $query .= "0 AS productnum";
      }
      $query .= " FROM #__zoo_category AS c
          LEFT JOIN #__zoo_application AS app ON c.application_id = app.id
                WHERE published=1 AND app.name ='" . $appname . "' ";
      if ($this->_params->get('elementorder', 0) == 0) {
        $query .= "ORDER BY ordering ASC, name DESC";
      } else if ($this->_params->get('elementorder', 0) == 1) {
        $query .= "ORDER BY name ASC";
      } else if ($this->_params->get('elementorder', 0) == 2) {
        $query .= "ORDER BY name DESC";
      }

      $db->setQuery($query);

      $allItems = $db->loadObjectList('id');

      if ($this->_params->get('showitems') == 1) {
        $query = "
          SELECT concat(c.category_id,'-',a.id) AS id, a.id AS id2,a.name AS name, c.category_id AS parent, a.access, a.alias, 'con' AS typ ";
        $query .= " FROM #__zoo_item AS a
                      LEFT JOIN #__zoo_category_item AS c ON c.item_id = a.id
            WHERE a.state = 1 AND c.category_id <> 0 ";
        if ($this->_params->get('elementorder', 0) == 0) {
          $query .= "ORDER BY a.id ASC, a.name DESC";
        } else if ($this->_params->get('elementorder', 0) == 1) {
          $query .= "ORDER BY a.name ASC";
        } else if ($this->_params->get('elementorder', 0) == 2) {
          $query .= "ORDER BY a.name DESC";
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
          if ($cats[$value->parent] > $this->_params->get('maxitemsincat', 20)) {
            unset($rows[$keys[$x]]);
          }

        }
        $allItems += $rows;
      }
      return $allItems;
    }

    public function getActiveItem()
    {
      $db = JFactory::getDBO();
      $active = null;
      if (JRequest::getVar('option') == 'com_zoo') {
        $content_id = 0;
        $category_id = 0;
        $zoo = App::getInstance('zoo');
        if ($item = $zoo->table->item->get((int) $zoo->request->getInt('item_id', $zoo->system->application->getParams()->get('item_id', 0)))) {
          $content_id = $item->id;
          $category_id = $item->getPrimaryCategoryId();
        } else {
          $category_id = (int) $zoo->request->getInt('category_id', $zoo->system->application->getParams()->get('category'));
        }
        if ($content_id > 0 && $this->_params->get('showitems')) {
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
      if ($this->_params->get('displaynumprod', 0) == 2) {
        for ($i = count($items) - 1; $i >= 0; $i--) {
          if (!($items[$i]->typ == "con" && $items[$i - 1]->typ == "con" && $items[$i]->parent == $items[$i - 1]->parent)) {
            $items[$i]->parent->productnum += $items[$i]->productnum;
          }
        }
      }
      return $items;
    }

    public function filterItem(&$item)
    {
      $item->title = $item->name;
      if ($this->_params->get('displaynumprod', 0) > 0 && $item->productnum != 0) {
        $length = strlen($item->productnum) == 1 ? "one" : "more";
        $item->number = '<span class="productnum ' . $length . '">' . $item->productnum . '</span>';
        $item->nname .= $item->number;
      }
      $item->nname = '<span>' . $item->title . '</span>';

      $item->anchorAttr = '';
      if ($item->typ == 'cat') {
        if ($this->_params->get('parentlink') == 0 && $item->p) {
          $item->nname = '<a>' . $item->nname . '</a>';
        } else {
          //$item->anchorAttr = 'href="'.JRoute::_('index.php?option=com_zoo&task=category&category_id='.$item->id).'"';
          $cat = App::getInstance('zoo')->table->category->get($item->id);
          $item->anchorAttr = 'href="' . App::getInstance('zoo')->route->category($cat, true) . '"';
          $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
        }
      } elseif ($item->typ == 'con') {
        $id = explode("-", $item->id);
        //$item->anchorAttr = 'href="'.JRoute::_('index.php?option=com_zoo&view=item&layout=item&item_id='.$item->id2).'"';
        $zooitem = App::getInstance('zoo')->table->item->get($item->id2);
        $item->anchorAttr = 'href="' . App::getInstance('zoo')->route->item($zooitem) . '"';
        $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
      }
    }

  }
}
