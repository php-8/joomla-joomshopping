<?php
/**
 * mod_vertical_menu - Vertical Menu
 *
 * @author    Balint Polgarfi
 * @copyright 2014-2019 Offlajn.com
 * @license   https://gnu.org/licenses/gpl-2.0.html
 * @link      https://offlajn.com
 */
?><?php
// no direct access
defined('_JEXEC') or die('Restricted access');

if (!class_exists('OfflajnParser')) {

  class OfflajnParser
  {
    public static function parse($s, $def = null)
    {
      $v = explode("|*|", is_string($s) ? $s : $def);
      if ($v[count($v) - 1] == '') {
        unset($v[count($v) - 1]);
      }

      if (is_string($s) && is_string($def)) {
        $d = explode("|*|", $def);
        if (!is_string($v) && !is_string($d)) {
          for ($i = count($v); $i--;) {
            array_shift($d);
          }
          $v = array_merge($v, $d);
        }
      }
      for ($i = 0; $i < count($v); $i++) {
        if (strpos($v[$i], "||") !== false) {
          $v[$i] = explode("||", $v[$i]);
        }
      }

      // if ($def=='#eeeeee|*|rgba(0, 0, 0, 0.53)|*|50||px|*|0||px|*|0||px'){
      //   echo'<pre>';print_r(count($v) == 1 ? $v[0] : $v);exit;
      // }

      return count($v) == 1 ? $v[0] : $v;
    }

    public static function parseUnit($v, $concat = '')
    {
      if (!is_array($v)) {
        $v = self::parse($v);
      }

      $unit = @$v[count($v) - 1];
      unset($v[count($v) - 1]);
      $r = '';
      foreach ($v as $m) {
        $r .= $m . $unit . $concat;
      }
      return $r;
    }

    public static function parseBorder($s)
    {
      $v = self::parse($s);
      return array(self::parseUnit(array_splice($v, 0, 5), ' '), $v[0], $v[1]);
    }

    public static function parseColorizedImage($s)
    {
      global $MiniImageHelper;
      $v = self::parse($s);
      $img = '';
      $v[0] = JPATH_SITE . preg_replace('#^.*?(/modules/)#', '$1', $v[0]);
      if (file_exists($v[0])) {
        if ($v[2] == '#000000') $v[2] = '#000001';
        if ($v[3] == '#000000') $v[3] = '#000001';
        $img = $MiniImageHelper->colorizeImages($v[0], $v[2], $v[3], '548722');
      }
      return array($img, $v[1]);
    }
  }

}
