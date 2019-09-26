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
defined('_JEXEC') or die('Restricted access');

class JElementOfflajnCode extends JOfflajnFakeElementBase{
  var	$_name = 'OfflajnCode';

  function universalfetchElement($name, $value, &$node){
    $document =& JFactory::getDocument();
    $this->loadFiles();
    $attr = $node->attributes();

    $html = '<div class="offlajncodecontainer" id="offlajntextareacontainer'.$this->id.'">';
    $html.= '<textarea cols="' . (isset($attr['cols'])? $attr['cols'] : 10) . '" rows="' . (isset($attr['rows'])? $attr['rows'] : 10) . '" class="offlajncode" type="text" name="'.$name.'" id="'.$this->id.'">'.$value.'</textarea>';
    $html.= '</div>';

    if (isset($node->code)) {
      $id = (int) $_REQUEST['id'];
      $jxmle = get_class($node) == 'JXMLElement';
      foreach ($node->code as $code) {
        $ca = $code->attributes();
        $data = (string)($jxmle ? $code[0] : $code->data());
        $data = str_replace('$id', $id, $data);
        $data = preg_replace('/(\/\*.*?\*\/)/s', '<font class="comment">$1</font>', $data);
        $html .= '<pre class="offlajncodesample" style="width:'.(isset($ca['width']) ? $ca['width'].'px' : 'auto').'; height:'.(isset($ca['height']) ? $ca['height'].'px' : 'auto').';">'.$data.'</pre>';
      }
    }

    DojoLoader::addScript('new OfflajnCode({ id: "'.$this->id.'" });');

    return $html;
  }
}
