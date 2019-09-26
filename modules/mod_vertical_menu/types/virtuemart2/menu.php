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

global $mosConfig_absolute_path, $VM_LANG, $database;

if (!class_exists('OfflajnVirtuemart2Menu2')) {

  if (!class_exists('VmConfig')) {
    require JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php';
  }
  $config = VmConfig::loadConfig();
  if (!class_exists('TableCategories')) {
    require JPATH_VM_ADMINISTRATOR . '/tables/categories.php';
  }
  if (!class_exists('VirtueMartModelCategory')) {
    require JPATH_VM_ADMINISTRATOR . '/models/category.php';
  }
  if (!class_exists('VirtueMartModelProduct')) {
    require JPATH_VM_ADMINISTRATOR . '/models/product.php';
  }

  require_once dirname(__FILE__) . '/../../core/MenuBase.php';

  class OfflajnVirtuemart2Menu2 extends OfflajnMenuBase2
  {

    public function __construct($module, $params)
    {
      parent::__construct($module, $params);
      $this->root = JURI::root(true);
      if ($this->root != '/') {
        $this->root .= '/';
      }

    }

    public function getAllItems()
    {
      $options = array();

      $db = JFactory::getDBO();

      $currLang = VMLANG;
      if ($this->_params->get('vmLangFix') || !VMLANG) {
        $lang = JFactory::getLanguage();
        $currLang = strtolower(str_replace("-", "_", $lang->getTag()));
      }

      $categoryid = explode("|", $this->_params->get('vm2categoryid'));

      $query = "SELECT DISTINCT
        a.virtuemart_category_id AS id,
        a.category_description  AS description,
        m.file_url AS categoryimg,
        a.category_name AS name, ";

      if (!is_array($categoryid) && $categoryid != 0) {
        $query .= "IF(f.category_parent_id = " . $categoryid . ", 0 , IF(f.category_parent_id = 0, -1, f.category_parent_id)) AS parent, ";
      } elseif (count($categoryid) && is_array($categoryid) && !in_array('0', $categoryid)) {
        $query .= "IF(a.virtuemart_category_id in (" . implode(',', $categoryid) . "), 0 , IF(f.category_parent_id = 0, -1, f.category_parent_id)) AS parent, ";
      } else {
        $query .= "f.category_parent_id AS parent, ";
      }

      $query .= "'cat' AS typ, ";
      if ($this->_params->get('displaynumprod', 0) != 0) {
        $query .= "(SELECT COUNT(*) FROM #__virtuemart_product_categories AS ax LEFT JOIN #__virtuemart_products AS bp ON ax.virtuemart_product_id = bp.virtuemart_product_id WHERE ax.virtuemart_category_id = a.virtuemart_category_id AND bp.published='1' ";
        if (VmConfig::get('check_stock') && Vmconfig::get('show_out_of_stock_products') != '1') {
          $query .= " AND bp.product_in_stock > 0 ";
        }
        $query .= ") AS productnum";
      } else {
        $query .= "0 AS productnum";
      }
      $query .= " FROM #__virtuemart_categories_" . $currLang . " AS a
                LEFT JOIN #__virtuemart_category_categories AS f ON a.virtuemart_category_id = f.category_child_id
                LEFT JOIN #__virtuemart_categories AS b ON a.virtuemart_category_id = b.virtuemart_category_id
                LEFT JOIN #__virtuemart_category_medias AS cm ON a.virtuemart_category_id = cm.virtuemart_category_id
                LEFT JOIN #__virtuemart_medias AS m ON cm.virtuemart_media_id = m.virtuemart_media_id
                WHERE b.published='1' AND a.virtuemart_category_id = f.category_child_id ";
      if ($this->_params->get('elementorder', 0) == 0) {
        $query .= "ORDER BY b.ordering ASC";
      } elseif ($this->_params->get('elementorder', 0) == 1) {
        $query .= "ORDER BY a.category_name ASC";
      } elseif ($this->_params->get('elementorder', 0) == 2) {
        $query .= "ORDER BY a.category_name DESC";
      }

      $db->setQuery($query);
      $allItems = $db->loadObjectList('id');

      /*
      Get products for the categories
       */
      if ($this->_params->get('showproducts', 0)) {
        $query = "
          SELECT DISTINCT
            a.virtuemart_product_id,
            concat(a.virtuemart_category_id,'-',a.virtuemart_product_id) AS id,
            c.product_name AS name,
            a.virtuemart_category_id AS parent,
            'prod' AS typ,
            0 AS productnum
                  FROM #__virtuemart_product_categories AS a
                  LEFT JOIN #__virtuemart_products AS b ON a.virtuemart_product_id = b.virtuemart_product_id
                  LEFT JOIN #__virtuemart_products_" . $currLang . " AS c ON a.virtuemart_product_id = c.virtuemart_product_id

                  WHERE b.product_parent_id = 0 AND b.published = '1'";
        if (VmConfig::get('check_stock') && Vmconfig::get('show_out_of_stock_products') != '1') {
          $query .= " AND b.product_in_stock > 0 ";
        }
        if ($this->_params->get('elementorder', 0) == 0) {
          $query .= " ORDER BY a.ordering ASC";
        } elseif ($this->_params->get('elementorder', 0) == 1) {
          $query .= " ORDER BY name ASC";
        } elseif ($this->_params->get('elementorder', 0) == 2) {
          $query .= " ORDER BY name DESC";
        }

        $db->setQuery($query);
        $allItems += $db->loadObjectList('id');
      }

      return $allItems;
    }

    public function getActiveItem()
    {
      $active = null;
      if (JRequest::getVar('option') == 'com_virtuemart') {
        $product_id = JRequest::getInt('virtuemart_product_id');
        $category_id = JRequest::getInt('virtuemart_category_id');
        if ($product_id > 0 && $this->_params->get('showproducts')) {
          if ($category_id > 0) {
            $active = new stdClass();
            $active->id = $category_id . '-' . $product_id;
          } else {
            $active = new stdClass();
            $productModel = new VirtueMartModelProduct();
            $r = $productModel->getProductSingle($product_id)->categories;
            if (is_array($r)) {
              $r = $r[0];
            }
            $active->id = $r . '-' . $product_id;
          }
        } else {
          if ($category_id > 0) {
            $active = new stdClass();
            $active->id = $category_id;
          } elseif ($product_id > 0) {
            $active = new stdClass();
            $productModel = new VirtueMartModelProduct();
            $r = $productModel->getProductSingle($product_id)->categories;
            if (is_array($r)) {
              $r = $r[0];
            }
            $active->id = $r;
          }
        }
      }
      return $active;
    }

    public function getItemsTree()
    {
      $items = $this->getItems();
      if ($this->_params->get('displaynumprod', 0) == 2) {
        for ($i = count($items); $i--;) {
          if (!isset($items[$i]->parent->productnum)) {
            $items[$i]->parent->productnum = 0;
          }

          $items[$i]->parent->productnum += empty($items[$i]->productnum) ? 0 : $items[$i]->productnum;
        }
      }
      return $items;
    }

    public function filterItem(&$item)
    {
      global $sess;
      $item->nname = $item->title = stripslashes($item->name);

      $length = strlen($item->productnum) == 1 ? "one" : "more";
      if ($this->_params->get('displaynumprod', 0) == 1 && $item->typ == 'cat' && $item->productnum > 0) {
        $item->number = '<span class="productnum ' . $length . '">' . $item->productnum . '</span>';
        $item->nname .= $item->number;
      } elseif ($this->_params->get('displaynumprod', 0) == 2 && $item->typ == 'cat' && $item->productnum > 0) {
        $item->number = '<span class="productnum ' . $length . '">' . $item->productnum . '</span>';
        $item->nname .= $item->number;
      }

      $item->nname = '<span>' . $item->nname . '</span>';

      $item->image = $image = '';
      if ($this->_params->get('menu_images')) {
        //if category has image, else try to parse it from the description
        if ($item->categoryimg) {
          $image = $item->categoryimg;
        } elseif (!empty($item->description)) {
          preg_match('/<img.*?src=["\'](.*?((jpg)|(png)|(jpeg)))["\'].*?>/i', $item->description, $out);
          if ($out[1]) {
            $image = $out[1];
          }

        }
        if ($image) {
          $item->image = $this->root . $image;
          $image = '<img src="' . $item->image . '" ' . (isset($imgalign) ? $imgalign : '') . ' height="16" />';
        }
        switch ($this->_params->get('menu_images_align', 0)) {
          case 1:
            $item->nname = $item->nname . $image;
            break;
          default:
            $item->nname = $image . $item->nname;
            break;
        }
      }

      if (!empty($item->description)) {
        // badges
        if ($this->_params->get('badge')) {
          $desc = strip_tags($item->description);
          if (preg_match('/^[\[\(](.+?)[\]\)]/', $desc, $m)) {
            $item->description = substr($desc, strlen($m[0]));
            $class = $m[0][0] == '[' ? '"sm-square-badge"' : '"sm-round-badge"';
            $item->badge = "<span class=$class>{$m[1]}</span>";
          }
        }

        $subHeader = OfflajnParser::parse($this->_params->get('subheader'));
        if ($subHeader[0]) {
          $desc = strip_tags($item->description);
          $item->description = strlen($desc) <= $subHeader[1] ? $desc : substr($desc, 0, $subHeader[1]) . '...';
          $item->nname .= '<span class="subname">' . $item->description . '</span>';
        } else {
          $item->description = '';
        }
      }

      $item->anchorAttr = '';
      if ($item->typ == 'cat') {
        if ($this->_params->get('parentlink') == 0 && $item->p) {
          $item->nname = '<a>' . $item->nname . '</a>';
        } else {
          $url = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $item->id);
          if (defined('DEMO') && strstr($url, 'Itemid') === false) {
            $url .= '&Itemid=' . $_REQUEST['Itemid'];
          }
          $item->anchorAttr = 'href="' . $url . '"';
          $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
        }
      } elseif ($item->typ == 'prod') {
        $ids = explode('-', $item->id);
        $url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_category_id=' . $ids[0] . '&virtuemart_product_id=' . $ids[1]);
        if (defined('DEMO') && strstr($url, 'Itemid') === false) {
          $url .= '&Itemid=' . $_REQUEST['Itemid'];
        }
        $item->anchorAttr = 'href="' . $url . '"';
        $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
      }
    }

  }
}
