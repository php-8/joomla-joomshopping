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

if (!class_exists('OfflajnEasyBlogMenu2')) {

  require_once dirname(__FILE__) . '/../../core/MenuBase.php';
  require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

  class OfflajnEasyBlogMenu2 extends OfflajnMenuBase2
  {
    public function __construct(&$menu, &$module)
    {
      parent::__construct($menu, $module);
    }

    public function getAllItems()
    {
      $lang = JFactory::getLanguage()->getTag();
      $db = JFactory::getDBO();
      $categoryid = explode("|", $this->_params->get('easyblogcategoryid'));
      $user = JFactory::getUser(); //get user for ACL
      $groups = implode(',', $user->getAuthorisedViewLevels());

      $query = "SELECT DISTINCT a.id AS id, a.title AS name, a.alias, a.description as description, a.avatar as categoryimg, ";
      if (!is_array($categoryid) && $categoryid != 0) {
        $query .= "IF(a.parent_id = " . $categoryid . ", 0 , IF(a.parent_id = 1, -1, a.parent_id)) AS parent, ";
      } else if (count($categoryid) && is_array($categoryid) && !in_array('0', $categoryid)) {
        $query .= "IF(a.id in (" . implode(',', $categoryid) . "), 0 , IF(a.parent_id = 0, -1, a.parent_id)) AS parent, ";
      } else {
        $query .= "IF(a.parent_id = 1, 0, a.parent_id) AS parent, ";
      }
      if ($this->_params->get('displaynumprod', 0) != 0) {
        $query .= "(SELECT COUNT(*) FROM #__easyblog_post AS ax WHERE ax.category_id = a.id AND ax.published = 1) AS productnum";
      } else {
        $query .= "0 AS productnum";
      }
      $query .= ",'cat' AS typ ";
      $query .= " FROM #__easyblog_category AS a WHERE a.published = 1 ";

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
        $query = "SELECT concat(a.category_id,'-',a.id) AS id, a.id AS id2, a.title AS name, a.intro AS description, a.category_id AS parent, a.permalink as link, 'prod' AS typ, 0 AS productnum
                  FROM #__easyblog_post AS a
                  WHERE a.published = 1 ";

        if ($this->_params->get('elementorder', 0) == 1) {
          $query .= "ORDER BY a.title ASC";
        } else if ($this->_params->get('elementorder', 0) == 2) {
          $query .= "ORDER BY a.title DESC";
        } else {
          $query .= "ORDER BY a.ordering ASC, a.title DESC";
        }

        $db->setQuery($query);
        $rows = $db->loadObjectList('id');
        $allItems += $rows;
      }
      return $allItems;
    }

    public function getActiveItem()
    {
      $db = JFactory::getDBO();
      $active = null;
      if (JRequest::getVar('option') == 'com_easyblog') {
        $content_id = 0;
        $category_id = 0;
        if (JRequest::getVar('view') == "categories") {
          $category_id = JRequest::getInt('id');
        } elseif (JRequest::getVar('view') == "entry") {
          $content_id = JRequest::getInt('id');
          $query = "SELECT category_id FROM #__easyblog_post WHERE id=" . $content_id;
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
            $item->image = JUri::Root() .'/images/easyblog_cavatar/'. $item->categoryimg;
        }
        $link = EBR::_('index.php?option=com_easyblog&view=categories&id=' . $item->id . '&layout=listings');
        $item->anchorAttr = 'href="' . $link . '"';

      } else if ($item->typ == 'prod') {
      	$post = EB::post($item->id2);
        $item->anchorAttr = 'href="' . $post->getPermalink() . '"';
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
