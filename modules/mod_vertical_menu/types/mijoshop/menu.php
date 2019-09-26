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

if (!class_exists('OfflajnMijoShopMenu2')) {

  require_once dirname(__FILE__) . '/../../core/MenuBase.php';
  require_once JPATH_ROOT . '/components/com_mijoshop/mijoshop/mijoshop.php';

  class OfflajnMijoShopMenu2 extends OfflajnMenuBase2
  {

    public function __construct($module, $params)
    {
      parent::__construct($module, $params);
    }

    public function getAllItems()
    {
      //MijoShop init
      $config = MijoShop::get('opencart')->get('config');
      $this->_config['lang'] = 1;
      if (is_object($config)) {
        $this->_config['lang'] = intval($config->get('config_language_id'));
      }
      $this->_router = MijoShop::get('router');
      $this->_mijoshopmenu = $this->_router->getMenu();
      $component = JComponentHelper::getComponent('com_mijoshop');
      $this->mijoshopitems = $this->_mijoshopmenu->getItems('component_id', $component->id);
      $this->mijoshopstoreid = MijoShop::get('base')->getStoreId();
      $this->mijoshopitemid = $this->getHomeItemid();
      $this->_config['root'] = explode("|", $this->_params->get('mijoshopcategories'));
      if (count($this->_config['root']) == 0) {
        $this->_config['root'] = array(0);
      }$this->_config['showproducts'] = $this->_params->get('showproducts');
      $this->_config['emptycategory'] = $this->_params->get('emptycategory');
      $this->_config['order'] = $this->_params->get('elementorder');
      $db = JFactory::getDBO();

      $query = "SELECT DISTINCT c.category_id AS id, cd.name, cd.description, '" . $this->mijoshopitemid . "' AS itemid, ";
      if ($this->_config['displaynum'] || !$this->_config['emptycategory']) {
        $query .= "( SELECT COUNT(*) FROM #__mijoshop_product_to_category AS ax LEFT JOIN #__mijoshop_product AS bp ON ax.product_id = bp.product_id WHERE ax.category_id = c.category_id AND bp.status=1";
        $query .= ") AS productnum, ";
      } else {
        $query .= "0 AS productnum, ";
      }if (!$this->_config['rootasitem'] && count($this->_config['root']) == 1) {
        $query .= "IF(c.parent_id = " . $this->_config['root'][0] . ", 0 , IF(c.parent_id = 0, -1, c.parent_id)) AS parent, ";
      } else if (!in_array('0', $this->_config['root'])) {
        $query .= "IF(c.category_id in (" . implode(',', $this->_config['root']) . "), 0 , IF(c.parent_id = 0, -1, c.parent_id)) AS parent, ";
      } else {
        $query .= "c.parent_id AS parent, ";
      }$query .= "'cat' AS typ ";
      $query .= " FROM #__mijoshop_category AS c LEFT JOIN #__mijoshop_category_description AS cd ON cd.category_id = c.category_id WHERE c.status = 1 AND cd.language_id = " . $this->_config['lang'] . " ";
      if ($this->_config['order'] == "asc") {
        $query .= "ORDER BY cd.name ASC";
      } else if ($this->_config['order'] == "desc") {
        $query .= "ORDER BY cd.name DESC";
      } else {
        $query .= "ORDER BY sort_order ASC, cd.name ASC";
      }$db->setQuery($query);
      $allItems = $db->loadObjectList('id');

      if ($this->_config['showproducts']) {
        $query = " SELECT DISTINCT p.product_id, '" . $this->mijoshopitemid . "' AS itemid, pd.description AS description, concat( pc.category_id, '-', p.product_id ) AS id, pd.name, pc.category_id AS parent, 'prod' AS typ, 0 AS productnum FROM #__mijoshop_product AS p LEFT JOIN #__mijoshop_product_description AS pd ON p.product_id = pd.product_id LEFT JOIN #__mijoshop_product_to_category AS pc ON p.product_id = pc.product_id WHERE p.status = 1 AND pd.language_id = " . $this->_config['lang'] . " ";
        if ($this->_config['order'] == "desc") {
          $query .= "ORDER BY pd.name DESC";
        } else {
          $query .= "ORDER BY pd.name ASC";
        }$db->setQuery($query);
        $allItems += $db->loadObjectList('id');
      }
      return $allItems;
    }

    public function getActiveItem()
    {
      $active = null;
      if (JRequest::getVar('option') == 'com_mijoshop') {
        $product_id = 0;
        $category_id = 0;
        if (JRequest::getVar('route') == 'product/category' && JRequest::getVar('path') != '') {
          $cats = explode('_', JRequest::getVar('path'));
          $category_id = $cats[count($cats) - 1];
        } else if (JRequest::getVar('route') == 'product/product' && JRequest::getInt('product_id') > 0) {
          $product_id = JRequest::getInt('product_id');
          $db = JFactory::getDBO();
          $db->setQuery('SELECT category_id FROM #__mijoshop_product_to_category WHERE product_id = "' . $product_id . '"');
          $categories = $db->loadRowList();
          foreach ($categories as $c) {
            if (isset($this->allItems[$c[0] . '-' . $product_id])) {
              $category_id = $c[0];
              break;
            }
          }
        }if ($product_id > 0 && $this->_config['showproducts']) {
          $active = new StdClass();
          $active->id = $category_id . "-" . $product_id;
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

      if (!empty($item->description)) {
        // badges
        if ($this->_params->get('badge')) {
          if (preg_match('/^[\[\(](.+?)[\]\)]/', $item->note, $m)) {
            $item->note = substr($item->note, strlen($m[0]));
            $class = $m[0][0] == '[' ? '"sm-square-badge"' : '"sm-round-badge"';
            $item->badge = "<span class=$class>{$m[1]}</span>";
          }
        }

        $subHeader = OfflajnParser::parse($this->_params->get('subheader'));
        if ($subHeader[0]) {
          $desc = strip_tags(html_entity_decode($item->description));
          $item->description = strlen($desc) <= $subHeader[1] ? $desc : substr($desc, 0, $subHeader[1]) . '...';
        } else {
          $item->description = "";
        }
      }

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
          $item->itemid = $this->getCategoryItemid($item);
          $item->anchorAttr = 'href="' . $this->route('index.php?option=com_mijoshop&route=product/category&path=' . $item->id . '&Itemid=' . $item->itemid) . '"';
          $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
        }
      } elseif ($item->typ == 'prod') {
        $id = explode("-", $item->id);
        $item->itemid = $this->getCategoryItemid($item);
        $item->anchorAttr = 'href="' . $this->route('index.php?option=com_mijoshop&route=product/product&product_id=' . $id[1] . '&Itemid=' . $item->itemid) . '"';
        $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
      }
    }

    public function route($url)
    {
      $url = JRoute::_($url);
      $url = str_replace('&amp;', '&', $url);
      $url = str_replace('component/mijoshop/shop', 'component/mijoshop', $url);
      return $url;
    }

    public function getCategoryItemid($it)
    {
      $menu_id = null;
      foreach ($this->mijoshopitems as $item) {
        $params = $item->params instanceof JRegistry ? $item->params : $menu->getParams($item->id);

        if ($params->get('mijoshop_store_id', 0) != $this->mijoshopstoreid) {
          continue;
        }
        if (isset($item->query['view']) && $item->query['view'] == 'category') {
          if (isset($item->query['path']) && $item->query['path'] == $it->id) {
            $menu_id = $item->id;
            break;
          }
        }
      }
      if (empty($menu_id)) {
        if (empty($it->parent->itemid)) {
          $it->parent->itemid = $this->mijoshopitemid;
        }

        return $it->parent->itemid;
      }
      return $menu_id;
    }

    public function getProductItemid($it)
    {
      $menu_id = null;
      foreach ($this->mijoshopitems as $item) {
        $params = $item->params instanceof JRegistry ? $item->params : $menu->getParams($item->id);

        if ($params->get('mijoshop_store_id', 0) != $this->mijoshopstoreid) {
          continue;
        }
        if (isset($item->query['view']) && $item->query['view'] == 'product') {
          if (isset($item->query['path']) && $item->query['path'] == $it->id) {
            $menu_id = $item->id;
            break;
          }
        }
      }
      if (empty($menu_id)) {
        if (empty($it->parent->itemid)) {
          $it->parent->itemid = $this->mijoshopitemid;
        }

        return $it->parent->itemid;
      }
      return $menu_id;
    }

    public function getHomeItemid()
    {
      $home_id = null;
      foreach ($this->mijoshopitems as $item) {
        $params = $item->params instanceof JRegistry ? $item->params : $menu->getParams($item->id);

        if ($params->get('mijoshop_store_id', 0) != $this->mijoshopstoreid) {
          continue;
        }
        if (isset($item->query['view']) && $item->query['view'] == 'home') {
          $home_id = $item->id;
          break;
        }
      }
      return $home_id;
    }

  }
}
