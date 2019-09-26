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

if (!class_exists('OfflajnJoomlaMenu2')) {
  jimport('joomla.application.menu');
  jimport('joomla.html.parameter');

  require_once dirname(__FILE__) . '/../../core/MenuBase.php';

  class OfflajnJoomlaMenu2 extends OfflajnMenuBase2
  {
    public function __construct($module, $params)
    {
      parent::__construct($module, $params);
      $this->alias = array();
      $this->parentName = 'parent_id';
      $this->name = 'title';
    }

    public function getAllItems()
    {
      $options = array();
      $menu = JMenu::getInstance('site', $options);
      $items = $menu->getMenu();
      $keys = array_keys($items);
      $allItems = array();
      for ($x = 0; $x < count($keys); $x++) {
        //Check J! 3.5 new feature if the menu item set to not show in menu module
        if (version_compare(JVERSION, '3.5', 'ge')) {
          $params = new OfflajnBaseJParameter($items[$keys[$x]]->params);
          if (!$params->get('menu_show', 1)) {
            continue;
          }

        }
        $allItems[$keys[$x]] = clone ($items[$keys[$x]]);
      }
      return $allItems;
    }

    public function getActiveItem()
    {
      $options = array();
      $menu = JMenu::getInstance('site', $options);
      return $menu->getActive();
    }

    public function getItemsTree()
    {
      $items = $this->getItems();
      $displaynum = $this->_params->get('displaynumprod', 0);

      if ($displaynum > 0) {
        for ($i = count($items) - 1; $i >= 0; $i--) {
          if (!property_exists($items[$i]->parent, 'productnum')) {
            $items[$i]->parent->productnum = -1;
          }
          if (!property_exists($items[$i], 'productnum')) {
            $items[$i]->productnum = -1;
            $items[$i]->parent->productnum++;
          } elseif ($displaynum == 1) {
            $items[$i]->parent->productnum++;
          } elseif ($displaynum == 2) {
            $items[$i]->parent->productnum += $items[$i]->productnum;
          }
        }
      }
      return $items;
    }

    public function filterItems()
    {
      $this->helper = array();
      $user = JFactory::getUser();
      $aid = $user->getAuthorisedViewLevels();
      //Get current language
      $lang = JFactory::getLanguage();
      $curLang = $lang->getTag();
      $menutype = $this->_params->get('joomlamenu');
      $ids = $this->_params->get('joomlamenutype');

      $ids = explode("|", $ids);

      // if (!is_array($ids) && is_string($ids)) $ids = array($ids)
      // if (!is_array($ids)) $ids = array();

      if (!in_array(0, $ids) && count($ids) > 0) {
        if (count($ids) == 1) {
          $keys = array_keys($this->allItems);
          $newParent = $ids[0];
          for ($x = 0; $x < count($keys); $x++) {
            $el = &$this->allItems[$keys[$x]];
            if ($el->{$this->parentName} == $newParent) {
              $el->{$this->parentName} = 1;
            } elseif ($el->{$this->parentName} == 1) {
              $el->{$this->parentName} = -1;
            }

          }
        } else {
          $keys = array_keys($this->allItems);
          for ($x = 0; $x < count($keys); $x++) {
            $el = &$this->allItems[$keys[$x]];
            if (in_array($el->id, $ids)) {
              $el->{$this->parentName} = 1;
            } elseif ($el->{$this->parentName} == 1) {
              $el->{$this->parentName} = -1;
            }

          }
        }
      }
      $keys = array_keys($this->allItems);
      for ($x = 0; $x < count($keys); $x++) {
        $item = &$this->allItems[$keys[$x]];
        if (!is_object($item)) {
          continue;
        }

        $item->parent = $item->{$this->parentName} == 1 ? 0 : $item->{$this->parentName};
        $item->ordering = $x;
        if ($item->menutype == $menutype && (is_array($aid) ? in_array($item->access, $aid) : $item->access <= $aid) && ($item->language == "*" || $item->language == $curLang)) {
          $item->p = false; // parent
          $item->fib = false; // First in Branch
          $item->lib = false; // Last in Branch
          if (!property_exists($item, 'opened')) {
            if ($this->opened == -1) {
              $item->opened = true; // Opened
            } else {
              $item->opened = false; // Opened
            }
          }
          $item->active = false; // Active
          $this->helper[$item->parent][] = $item;
          $item->cparams = version_compare(JVERSION, '3.0', 'ge') ? new OfflajnBaseJParameter($item->params) : new JParameter($item->params);
          if ($item->type == 'menulink' || $item->type == 'alias') {
            $itemid = $item->cparams->get('aliasoptions');
            if (!isset($this->alias[$itemid])) {
              $this->alias[$itemid] = $item->id;
            }

          }
        }
      }
    }

    public function filterItem(&$item)
    {
      $item->cparams = version_compare(JVERSION, '3.0', 'ge') ? new OfflajnBaseJParameter($item->params) : new JParameter($item->params);
      if ($item->type == 'menulink' || $item->type == 'alias') {
        $itemid = $item->cparams->get('aliasoptions');
        if (isset($this->allItems[$itemid])) {
          $newItem = $this->allItems[$itemid];
          $item->link = $newItem->link;
          $item->ttype = $newItem->type;
          $item->id = $newItem->id;
        } else {
          $item->ttype = 'separator';
        }
      } else {
        $item->ttype = $item->type;
      }

      $item->title = $item->{$this->name};
      $item->nname = '<span>' . $item->title . '</span>';

      if ($this->_params->get('displaynumprod', 0) > 0 && $item->productnum > 0) {
        $length = strlen($item->productnum) == 1 ? "one" : "more";
        $item->number = '<span class="productnum ' . $length . '">' . $item->productnum . '</span>';
        $item->nname .= $item->number;
      }

      $item->image = $image = '';

      $imgalign = "";
      switch ($this->_params->get('menu_images_align', 0)) {
        case 0:
          $imgalign = "align='left'";
          break;
        case 1:
          $imgalign = "align='right'";
          break;
        default:
          $imgalign = "";
          break;
      }
      if ($this->_params->get('menu_images') && $item->params->get('menu_image') && $item->params->get('menu_image') != -1) {
        $item->image = JURI::root() . $item->params->get('menu_image');
        $image = '<img src="' . $item->image . '" ' . $imgalign . ' alt="' . $item->alias . '" />';
        if ($this->_params->get('menu_images_link')) {
          $item->nname = null;
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

      if (!empty($item->note)) {
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
          $desc = strip_tags($item->note);
          $item->description = strlen($desc) <= $subHeader[1] ? $desc : substr($desc, 0, $subHeader[1]) . '...';
        }
      }

      if ($this->_params->get('parentlink') == 0 && $item->p) {
        $item->ttype = 'separator';
      }

      switch ($item->ttype) {
        case 'separator':
        case 'heading':
          $item->url = '';
          return true;
        case 'url':
          if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
            $item->url = $item->link . '&amp;Itemid=' . $item->id;
          } else {
            $item->url = $item->link;
          }
          break;
        default:
          $router = JSite::getRouter();
          $item->url = $router->getMode() == JROUTER_MODE_SEF ? 'index.php?Itemid=' . $item->id : $item->link . '&Itemid=' . $item->id;
          break;
      }

      $item->anchorAttr = '';
      //Get the additional CSS class from the menu manager
      if ($item->params->get('menu-anchor_css')) {
        $item->anchorAttr .= 'class="' . $item->params->get('menu-anchor_css') . '" ';
      }

      //Get link title if set from the menu manager
      if ($item->params->get('menu-anchor_title')) {
        $item->anchorAttr .= ' title="' . $item->params->get('menu-anchor_title') . '" ';
      }

      if ($item->url != '') {
        // Handle SSL links
        $iSecure = $item->cparams->def('secure', 0);
        if ($item->home == 1) {
          $item->url = JURI::base();
        } elseif (strcasecmp(substr($item->url, 0, 4), 'http') && (strpos($item->link, 'index.php?') !== false)) {
          $item->url = JRoute::_($item->url, true, $iSecure);
        } else {
          $item->url = str_replace('&', '&amp;', $item->url);
        }

        switch ($item->browserNav) {
          default:
          case 0: // _top
            $item->anchorAttr .= 'href="' . $item->url . '"';
            $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
            break;
          case 1: // _blank
            $item->anchorAttr .= 'href="' . $item->url . '" target="_blank"';
            $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
            break;
          case 2: // window.open
            $specs = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,' . $this->_params->get('window_open');
            $link = str_replace('index.php', 'index2.php', $item->url);
            $item->anchorAttr .= 'href="' . $item->url . '" onclick="window.open(this.href,\'targetWindow\',\'' . $specs . '\');return false;"';
            $item->nname = '<a ' . $item->anchorAttr . '>' . $item->nname . '</a>';
            break;
        }
      } else {
        $item->nname = '<a>' . $item->nname . '</a>';
      }
    }

    public function menuOrdering(&$a, &$b)
    {
      if ($a->ordering == $b->ordering) {
        return 0;
      }
      return ($a->ordering < $b->ordering) ? -1 : 1;
    }
  }
}
