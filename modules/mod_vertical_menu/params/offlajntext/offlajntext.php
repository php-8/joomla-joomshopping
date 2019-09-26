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

class JElementOfflajnText extends JOfflajnFakeElementBase{
  var	$_name = 'OfflajnText';
  
  function universalfetchElement($name, $value, &$node){
    $document =& JFactory::getDocument();
    $this->loadFiles();
    $attr = $node->attributes();
    $units = $node->children();
    $attachunit = '';
    $mode = "";
    $minus = 0;
    $scale = (isset($attr['scale'])) ? $attr['scale'] : "";
    $onoff = (isset($attr['onoff'])) ? $attr['onoff'] : "";
    $v = explode('||', $value);
    $html = '<div class="offlajntextcontainer" id="offlajntextcontainer'.$this->id.'">';
    $html.= '<input '.(isset($attr['disabled']) ? 'disabled="disabled"':'').' size="'.(isset($attr['size'])? $attr['size'] : 10).'" class="offlajntext" type="text" id="'.$this->id.'input" value="'.$v[0].'">';
    if(count($units) == 1){
      $html.= '<div class="unit">'.$units[0]->data().'</div>';
      $attachunit = $units[0]->data();
    }
    if(@$attr['mode'] == "increment" && ($attr['validation'] == "int" || $attr['validation'] == "float")) {
      $html .= '<div class="offlajntext_increment">
                <div class="offlajntext_increment_up arrow"></div>
                <div class="offlajntext_increment_down arrow"></div>
      </div>';
      $mode = "increment";
      if($attr['allowminus']) $minus = $attr['allowminus'];
    }
    $html.= '</div>';

    $placeholder = isset($attr['placeholder']) ? JText::_($attr['placeholder']) : '';

    if(count($units) == 2){
      $node->addAttribute('type', 'offlajnswitcher');
      $switcher = new JElementOfflajnSwitcher();
      $switcher->id = $this->generateId($name.'[unit]');
      $html.= $switcher->universalfetchElement($name.'[unit]', @$v[1], $node);
    }
    $html.= '<input type="hidden" name="'.$name.'" id="'.$this->id.'" value="'.$value.'">';
    DojoLoader::addScript('
      new OfflajnText({
        id: "'.$this->id.'",
        validation: "'.(isset($attr['validation'])? $attr['validation'] : '').'",
        attachunit: "'.$attachunit.'",
        mode: '.json_encode($mode).',
        scale: '.json_encode($scale).',
        minus: '.json_encode($minus).',
        onoff: '.json_encode($onoff).',
        placeholder: "'.$placeholder.'"
      });
    ');
    return $html;
  }
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnText extends JElementOfflajnText {}
}