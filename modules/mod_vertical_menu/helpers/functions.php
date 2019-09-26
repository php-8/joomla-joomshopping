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

jimport('joomla.filesystem.folder');
$path = dirname(__FILE__);

foreach(JFolder::files($path, '.php', false, false) AS $f){
  require_once($path.'/'.$f);
}

if(!function_exists('o_flat_array')){

  /* Multidimensional to flat array */
  function o_flat_array($array){
    if(!is_array($array)) return array();
   $out=array();
   foreach($array as $k=>$v){
    if(is_array($array[$k]) && o_isAssoc($array[$k])){
     $out+=o_flat_array($array[$k]);
    }else{
     $out[$k]=$v;
    }
   }
   return $out;
  }
}

if(!function_exists('o_isAssoc')){
  function o_isAssoc($arr){
    return array_keys($arr) !== range(0, count($arr) - 1);
  }
}


?>