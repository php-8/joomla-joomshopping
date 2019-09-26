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

if (!class_exists('OfflajnJoomlaContentMenu2')) {

  require_once dirname(__FILE__) . '/../../core/MenuBase.php';

  class OfflajnJoomlaContentMenu2 extends OfflajnMenuBase2
  {
    public function __construct(&$menu, &$module)
    {
      parent::__construct($menu, $module);
    }

    public function getAllItems()
    {
      $lang = JFactory::getLanguage()->getTag();
      $db = JFactory::getDBO();
      $categoryid = explode("|", $this->_params->get('joomlacategoryid'));
      $user = JFactory::getUser(); //get user for ACL
      $groups = implode(',', $user->getAuthorisedViewLevels());
      $query = "SELECT DISTINCT a.id AS id, a.title AS name, a.alias, a.note, params, ";
      if (!is_array($categoryid) && $categoryid != 0) {
        $query .= "IF(a.parent_id = " . $categoryid . ", 0 , IF(a.parent_id = 1, -1, a.parent_id)) AS parent, ";
      } else if (count($categoryid) && is_array($categoryid) && !in_array('0', $categoryid)) {
        $query .= "IF(a.id in (" . implode(',', $categoryid) . "), 0 , IF(a.parent_id = 1, -1, a.parent_id)) AS parent, ";
      } else {
        $query .= "IF(a.parent_id = 1, 0, a.parent_id) AS parent, ";
      }
      $query .= "'cat' AS typ, ";
      if ($this->_params->get('displaynumprod', 0) != 0) {
        $query .= "(SELECT COUNT(*) FROM #__content AS ax WHERE ax.catid = a.id AND ax.state = 1 AND ax.access IN ($groups) AND (ax.language = '*' OR ax.language = '$lang')) AS productnum";
      } else {
        $query .= "0 AS productnum";
      }

      $query .= " FROM #__categories AS a WHERE a.published = 1 AND a.extension = 'com_content' AND access IN ($groups) AND (a.language = '*' OR a.language = '$lang') ";

      if ($this->_params->get('elementorder', 0) == 1) {
        $query .= "ORDER BY name ASC";
      } else if ($this->_params->get('elementorder', 0) == 2) {
        $query .= "ORDER BY name DESC";
      } else {
        $query .= "ORDER BY a.lft ASC, name DESC";
      }

      $db->setQuery($query);
      $allItems = $db->loadObjectList('id');

      if ($this->_params->get('showproducts') == 1) {

        $query = "SELECT concat(a.catid,'-',a.id) AS id, a.id AS id2, a.title AS name, a.introtext AS description, a.catid AS parent, a.alias, a.access, 'prod' AS typ, 0 AS productnum, '' AS image
                FROM #__content AS a
                WHERE a.state = 1 AND a.access IN ($groups) AND (a.language = '*' OR a.language = '$lang') ";

        if ($this->_params->get('elementorder', 0) == 1) {
          $query .= "ORDER BY a.title ASC";
        } else if ($this->_params->get('elementorder', 0) == 2) {
          $query .= "ORDER BY a.title DESC";
        } else {
          $query .= "ORDER BY a.ordering ASC, a.title DESC";
        }

        $db->setQuery($query);
        $rows = $db->loadObjectList('id');
        /*
        if ($this->_config['maxitemsincat'] > 0) {
        $cats = array();
        $keys = array_keys($rows);
        for ($x = 0; $x < count($keys); ++$x) {
        $value = $rows[$keys[$x]];
        if (!isset($cats[$value->parent])) {
        $cats[$value->parent] = 0;
        }
        $cats[$value->parent] ++;
        if ($cats[$value->parent] > $this->_config['maxitemsincat']) {
        unset($rows[$keys[$x]]);
        }
        }
        }
         */
        $allItems += $rows;
      }
      return $allItems;
    }

    public function getActiveItem()
    {
      $db = JFactory::getDBO();
      $active = null;
      if (JRequest::getVar('option') == 'com_content') {
        $content_id = 0;
        $category_id = 0;
        if (JRequest::getVar('view') == "category") {
          $category_id = JRequest::getInt('id');
        } elseif (JRequest::getVar('view') == "article") {
          $content_id = JRequest::getInt('id');
          $query = "SELECT catid FROM #__content WHERE id=" . $content_id;
          $db->setQuery($query);
          $category_id = $db->loadResult();
        }
        if ($content_id > 0 && $this->_params->get('showproducts')) {
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
      if ($this->_params->get('displaynumprod') == 2) {
        for ($i = count($items) - 1; $i >= 0; $i--) {
          $items[$i]->parent->productnum += $items[$i]->productnum;
        }
      }
      /*
      if (!$this->_config['emptycategory']) {
      for ($i = count($items) - 1; $i >= 0; $i--) {
      if ($items[$i]->productnum == 0 && $items[$i]->typ == 'cat') {

      $parent = &$this->helper[$items[$i]->parent->id];

      if ($items[$i]->lib) {
      array_splice($parent, count($parent) - 1, 1);
      if (count($parent) != 0) {
      $parent[count($parent) - 1]->lib = true;
      }
      } else if ($items[$i]->fib) {
      array_splice($parent, 0, 1);
      if (count($parent) != 0) {
      $parent[0]->fib = true;
      }
      } else {
      $key = array_search($items[$i], $parent);
      if ($key !== false) {
      array_splice($parent, $key, 1);
      }
      }
      array_splice($items, $i, 1);
      }
      }
      }
       */
      return $items;
    }

    public function filterItem(&$item)
    {
      $item->nname = $item->title = stripslashes($item->name);
      $item->nname = '<span>' . $item->nname . '</span>';
      if ($this->_params->get('displaynumprod') && $item->productnum != 0) {
        $length = strlen($item->productnum) == 1 ? "one" : "more";
        $item->number = '<span class="productnum ' . $length . '">' . $item->productnum . '</span>';
      }
      $item->anchorAttr = '';
      if ($item->typ == 'cat') {

        if ($this->_params->get('menu_images')) {
          $params = new JRegistry($item->params);
          if (($image = $params->get('image', '')) != '') {
            $item->image = JUri::Root() . $image;
          }
        }

        $item->anchorAttr = 'href="' .
        JRoute::_('index.php?option=com_content&view=category&id=' .
          $item->id . ($this->_params->get('categorylayout') != '' ? '&layout=' . $this->_params->get('categorylayout') : '')) . '"';

      } else if ($item->typ == 'prod') {
        $id = explode("-", $item->id);
        $item->anchorAttr = 'href="' .
        JRoute::_('index.php?option=com_content&view=article&id=' . $id[1] . ':' . $item->alias . '&catid=' . $id[0] . ':' . $item->parent->alias) . '"';
      }
      if (isset($item->note)) {
        // badges
        if ($this->_params->get('badge')) {
          if (preg_match('/^[\[\(](.+?)[\]\)]/', $item->note, $m)) {
            $item->note = trim(substr($item->note, strlen($m[0])));
            $class = $m[0][0] == '[' ? '"sm-square-badge"' : '"sm-round-badge"';
            $item->badge = "<span class=$class>{$m[1]}</span>";
          }
        }
      }
      if (isset($item->note) || isset($item->description)) {
        $subHeader = OfflajnParser::parse($this->_params->get('subheader'));
        if ($subHeader[0]) {
          $desc = !empty($item->note) ? $item->note : strip_tags($item->description);
          $item->description = strlen($desc) <= $subHeader[1] ? $desc : substr($desc, 0, $subHeader[1]) . '...';
        } else {
          $item->description = '';
        }

      }
    }

  }
}
