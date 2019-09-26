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

class JElementOfflajnHidden extends JOfflajnFakeElementBase{
  function universalfetchElement($name, $value, &$node) {
    return '<input type="hidden" id="'.$this->id.'" name="'.$name.'" value="'.$value.'" />';
  }
}

class JElementOfflajnCombine extends JOfflajnFakeElementBase{
  var	$_name = 'OfflajnCombine';
  
  function universalfetchElement($name, $value, &$node){
    $v = explode('|*|', $value);
    $document =& JFactory::getDocument();
    $this->loadFiles();
    $attr = $node->attributes();
    $i = 0;
    $switcherid = '';
    $hideafter = 0;
    $html = "";
    $html .= '<div id="offlajncombine_outer'.$this->id.'" class="offlajncombine_outer">';
    $hiderdiv = 0;
    $islist = 0;
    foreach($node->children() AS $param){
      $a = $param->attributes();
      if($a['type'] == 'offlajnclear'){
        $html.= '<div class="clear"></div>';
        $hideafter = $i;
        continue; 
      }
      $param->addAttribute('name', $name.$i);
      $class = 'JElement'.$a['type'];
      $p = new $class();
      $p->id = $this->generateId($name.$i);
			$if = isset($a['if']) ? 'data-if="'.$a['if'].'" ' : '';
			$check = isset($a['check']) ? 'data-check="'.$a['check'].'" ' : '';
      $style = isset($a['style']) ? 'style="'.$a['style'].'" ' : '';
      $html.='<div '.$check.$if.$style.'class="offlajncombinefieldcontainer">';
      if(isset($a['label'])){
        if($a['label'] == '-') $a['label'] = '&nbsp;';
        $html.='<label style="float: left;">'.JTEXT::_($a['label']).'</label>';
      }
      $html .= '<div class="offlajncombinefield">'; 
      if(isset($a['onoff'])) {
        $switcherid = $p->id;
        if($a['type']=='offlajnlist') $islist = 1;
      }
      $html.= $p->universalfetchElement($name.$i, @$v[$i], $param); 
      $html.='</div>';
      $html.='</div>';
      if(isset($a['onoff'])) {
        $hiderdiv = 1;
      }
      $i++;
    }
     
    //if(isset($a['onoff'])) { $html .= '<div class="offlajncombine_hider"></div>'; }
    $html .= '</div>';  
    if($hiderdiv) { $html .= '<div class="offlajncombine_hider"></div>'; }
    //$html .= '</div>';        
    $html.= '<input type="hidden" name="'.$name.'" id="'.$this->id.'" value=\''.$value.'\'>';
    DojoLoader::addScript('
      new OfflajnCombine({
        id: "'.$this->id.'",
        num: '.$i.',
        switcherid: "'.$switcherid.'",
        hideafter: "'.$hideafter.'",
        islist: "'.$islist.'"
      }); 
    ');
    return $html;
  }
}