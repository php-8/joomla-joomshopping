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
/*-------------------------------------------------------------------------
# mod_vertical_menu - Vertical Menu
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2015 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
defined('_JEXEC') or die('Restricted access');

@JOfflajnParams::load('offlajnonoff');

class JElementOfflajnMiniGradient extends JOfflajnFakeElementBase
{
  var $_moduleName = '';

	var	$_name = 'MiniGradient';

	function universalfetchElement($name, $value, &$node)
	{
    $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);
		$alpha = $node->attributes('alpha') == 1 ? true : false;
		$position = $node->attributes('position') ? $node->attributes('position') : 'bottom';
    $attrs = $node->attributes();

    $oo = @$attrs['onoff'] === '0' ? 0 : 1;

    $document =& JFactory::getDocument();
    DojoLoader::addScriptFile('/modules/'.$this->_moduleName.'/params/offlajnonoff/offlajnonoff/offlajnonoff.js');
    DojoLoader::addScriptFile('/modules/'.$this->_moduleName.'/params/offlajnminigradient/offlajnminigradient/offlajnminigradient.js');
    $document->addStyleSheet(JURI::base().'../modules/'.$this->_moduleName.'/params/offlajnminigradient/offlajnminigradient/offlajnminigradient.css');
    $document->addStyleSheet(JURI::base().'../modules/'.$this->_moduleName.'/params/offlajnminicolor/offlajnminicolor/offlajnminicolor.css');

    $id = $this->generateId($name);

    $v = explode('-', $value);
    $f = "";
    $onoff = new JElementOfflajnOnOff();
    $onoff->id = $onoff->generateId($id.'onoff');
    $f.= $onoff->universalfetchElement($onoff->id,$v[0],new JSimpleXMLElement('param'));
    $f.= '<div class="gradient_container"><div id="gradient'.$id.'" class="minigradient_bg"><input type="hidden" name="'.$name.'" id="'.$id.'" value="'.$value.'"/>';
    $f.= '<div class="gradient_left"><input type="text" name="a'.$name.'[start]" id="'.$id.'start" value="'.@$v[1].'" class="minicolor" /></div>';
    $f.= '<div class="gradient_right"><input type="text" name="a'.$name.'[stop]" id="'.$id.'stop" value="'.@$v[2].'" class="minicolor" /></div>';
    $f.= '<div style="clear: both;"></div></div></div><div style="clear: both;"></div>';

    DojoLoader::addScript('
      new OfflajnMiniGradient({
        hidden: dojo.byId("'.$id.'"),
        switcher: dojo.byId("'.$onoff->id.'"),
        onoff: '.$oo.',
        start: dojo.byId("'.$id.'start"),
        end: dojo.byId("'.$id.'stop"),
				alpha: '.($alpha ? 'true' : 'false').',
				position: "'.$position.'"
      });
    ');

		return $f;
	}
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnMiniGradient extends JElementOfflajnMiniGradient {}
}

