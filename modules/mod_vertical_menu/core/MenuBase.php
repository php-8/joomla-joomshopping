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

if (!class_exists('OfflajnMenuBase2')) {
  require_once dirname(__FILE__) . '/imageCache.php';

  class OfflajnMenuBase2
  {
    public $_template;
    public $_module;
    public $_params;
    public $items;
    public $allItems;
    public $active;
    public $pointer;
    public $itemsCount;
    public $stack;
    public $level;
    public $endLevel;
    public $startLevel;
    public $improvedStartLevel;
    public $opened = 0;
    public $openedlevels = 0;

    public function __construct($module, $params)
    {
      $this->_module = $module;
      $this->_params = $params;
      $this->endLevel = $params->get('endLevel', 1000);
      if ($this->endLevel == 0) {
        $this->endLevel = 1000;
      }

      $this->startLevel = $params->get('startLevel', 0);
      $this->improvedStartLevel = $params->get('improvedstartlevel', 1);
      $this->imageCache = new OfflajnUniversalImageCaching($module->module);
    }

    public function generateItems()
    {
      $options = array();
      $cache = JFactory::getCache();
      $cache->setCaching($this->_params->get('caching', 0));
      $this->allItems = $cache->call(array($this, 'getAllItems'));
      $this->active = $this->getActiveItem();
      $this->items = $cache->call(array($this, 'getItemsTree'));
    }

    public function getItems()
    {
      /*
      If COOKIE tracking enabled
       */
      if ($this->opened == 3) {
        $cookie = JRequest::get('COOKIE');
        foreach ($cookie as $k => $v) {
          if ($v == 1 && strpos($k, $this->_module->instanceid) !== false) {
            $val = (int) str_replace($this->_module->instanceid . '-' . $this->_module->navClassPrefix, '', $k);
            //print_r($this->allItems[$val]);
            if ($val > 0 && isset($this->allItems[$val])) {
              $this->allItems[$val]->opened = true;
            }
          }
        }
      }

      $this->filterItems();

      $root = 0;
      if (isset($this->active)) {
        $i = $this->active->id;
        $stack = array($this->active->id);
        $el = $this->active;
        while ($i > 0) {
          $el = isset($this->allItems[$i]) ? $this->allItems[$i] : null;
          $i = isset($el->parent) ? $el->parent : null;
          $stack[] = $i;
        }
        $c = count($stack);
        if ($c > 0) {
          switch ($this->_params->get('active', 1)) {
            case 1:
              if (!isset($this->allItems[$stack[0]])) {
                $this->allItems[$stack[0]] = new stdClass();
              }

              $this->allItems[$stack[0]]->active = true;
              $this->allItems[$stack[0]]->opened = true;
              break;
            case 2:
              foreach ($stack as $s) {
                if (!isset($this->allItems[$s])) {
                  $this->allItems[$s] = new stdClass();
                }

                $this->allItems[$s]->active = true;
                $this->allItems[$s]->opened = true;
              }
              break;
          }
/*
switch($this->opened){
case 1:
$this->allItems[$stack[0]]->opened = true;
break;
case 2:
foreach($stack AS $s){
$this->allItems[$s]->opened = true;
}
break;
}
 */
        }
        if ($this->startLevel > 0) {
          if ($this->improvedStartLevel) {
            while ($this->startLevel != 0) {
              if (isset($stack[$c - $this->startLevel - 1]) && isset($this->helper[$stack[$c - $this->startLevel - 1]])) {
                $root = $stack[$c - $this->startLevel - 1];
                break;
              }
              $this->startLevel--;
            }
          } else {
            $root = -1;
            if (isset($stack[$c - $this->startLevel - 1])) {
              $root = $stack[$c - $this->startLevel - 1];
            }
          }
        }
      }

      $p = new stdClass();
      if ($root > 0 && isset($this->allItems[$root])) {
        $p = $this->allItems[$root];
      } else {
        $p->id = $root;
      }
      return $this->getChilds($p, 1);
    }

    public function filterItems()
    {
      $this->helper = array();
      foreach ($this->allItems as $item) {
        if (!is_object($item)) {
          continue;
        }

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
      }
    }

    public function getChilds(&$parent, $level)
    {
      $items = array();
      if (isset($this->helper[$parent->id])) {
        $helper = &$this->helper[$parent->id];
        //echo'<pre>';print_r($helper);exit;
        //usort($helper, array($this, "menuOrdering")); // It can slow down the proccess. Not required every time... With this the process half as fast...
        $helper[0]->fib = true;
        $helper[count($helper) - 1]->lib = true;

        if ($level <= $this->endLevel) {
          $i = 0;
          $h = null;
          $keys = array_keys($helper);
          for ($j = 0; $j < count($keys); $j++) {
            $h = &$helper[$keys[$j]];
            $h->parent = &$parent;
            $childs = $this->getChilds($h, $level + 1);
            if (count($childs) > 0) {
              $h->p = true;
            }

            $h->level = $level;
            $items[] = &$h;
            $i = count($items);
            array_splice($items, $i, 0, $childs);
          }
          // add module positions
          $last = clone $h;
          $h->lib = $last->fib = $last->p = 0;
          $last->modpos = 1;
          $items[] = $last;
        }
      }
      return $items;
    }

    public function filterItem(&$item)
    {
      $item->nname = '<span>' . stripslashes($item->name) . '</span>';
    }

    public function menuOrdering(&$a, &$b)
    {
      return 0;
    }

    public function render($template)
    {
      $this->pointer = 0;
      $this->itemsCount = count($this->items);
      $this->_template = $template;
      $this->stack = array();
      $this->level = 1;
      $this->up = false;
      $this->renderItem();
    }

    public function renderItem()
    {
      if (0 && $this->items[0]->menutype == 'test') {
        echo '<pre>';
        print_r($this->items);exit;
      }
      while ($this->pointer < $this->itemsCount) {
        $item = &$this->items[$this->pointer++];
        $this->filterItem($item);
        include $this->_template;
      }
    }
  }
}
