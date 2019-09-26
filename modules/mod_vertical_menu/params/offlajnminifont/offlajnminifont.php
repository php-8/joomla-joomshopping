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

jimport('joomla.filesystem.file');

class JElementOfflajnMiniFont extends JOfflajnFakeElementBase
{
  var $_moduleName = '';

	var	$_name = 'OfflajnMiniFont';

	var $_node = '';

	function universalfetchElement($name, $value, &$node){
    $this->_node = &$node;

    $html = "";
    $attrs = $node->attributes();
    $alpha = isset($attrs['alpha'])? $attrs['alpha'] : 0;
    $tabs = explode('|', $attrs['tabs']);

    $s = json_decode($value);
    if(isset($attrs['tabs']) && $attrs['tabs'] != "")  @$def = (array)$s->{$tabs[0]};
    $elements = array();

    $stack = & JsStack::getInstance();
    $stack->startStack();

    // TABS
    $elements['tab']['name'] = $name.'tab';
    $elements['tab']['id'] = $this->generateId($elements['tab']['name']);


    $tabxml = new JSimpleXML();
    $tabxml->loadString('<param/>');
    $tabxml = $tabxml->document;
    $tabxml->addAttribute('name', $elements['tab']['name']);
    $tabxml->addAttribute('type', 'offlajnradio');
    $tabxml->addAttribute('mode', 'button');
    foreach($tabs AS $t){
      $tabxml->addChild('option', array('value'=>$t))->setData($t);
    }
    $tab = new JElementOfflajnRadio();
    $tab->id = $elements['tab']['id'];
    $elements['tab']['html'] = $tab->universalfetchElement($elements['tab']['name'], $tabs[0], $tabxml);
    // END TABS

    // TYPE
    $elements['type']['name'] = $name.'type';
    $elements['type']['id'] = $this->generateId($elements['type']['name']);
    $typexml = new JSimpleXML();
    $typexml->loadString('<param/>');
    $typexml = $typexml->document;
    $typexml->addAttribute('name', $elements['type']['name']);
    $typexml->addAttribute('type', 'offlajnlist');
		$typexml->addAttribute('height', '12');
    $typexml->addChild('option', array('value'=>'0'))->setData('Alternative fonts');

    $subsets = array('latin', 'latin_ext', 'greek', 'greek_ext', 'hebrew', 'vietnamese', 'arabic', 'devanagari', 'cyrillic', 'cyrillic_ext', 'khmer', 'tamil', 'thai', 'telugu', 'bengali', 'gujarati');
    foreach($subsets as $t){
      $typexml->addChild('option', array('value'=>$t))->setData($t);
      $stack->startStack();
      // FAMILY
      $elements['type'][$t]['name'] = $name.'family';
      $elements['type'][$t]['id'] = $this->generateId($elements['type'][$t]['name']);
      $familyxml = new JSimpleXML();
      $familyxml->loadString('<param/>');
      $familyxml = $familyxml->document;
      $familyxml->addAttribute('name', $elements['type'][$t]['name']);
      $familyxml->addAttribute('type', 'offlajnlist');
      $familyxml->addAttribute('height', '12');
      $familyxml->addAttribute('fireshow', '1');
      $familyxml->addAttribute('width', '164');
      $familyxml->addAttribute('json', 'OfflajnFont_'.$t);
      $family = new JElementOfflajnList();
      $family->id = $elements['type'][$t]['id'];
      $elements['type'][$t]['html'] = $family->universalfetchElement($elements['type'][$t]['name'], isset($def['family'])?$def['family']:'Open Sans', $familyxml);
      $elements['type'][$t]['script'] = $stack->endStack(true);
      // END FAMILY
    }
    $type = new JElementOfflajnList();
    $type->id = $elements['type']['id'];
    $elements['type']['html'] = $type->universalfetchElement($elements['type']['name'], isset($def['type'])?$def['type']:'0', $typexml);
    // END TYPE

    // SIZE
    $elements['size']['name'] = $name.'size';
    $elements['size']['id'] = $this->generateId($elements['size']['name']);

    $sizexml = new JSimpleXML();
    $sizexml->loadString('<param size="1" validation="int" mode="increment" scale="1" allowminus="0"><unit value="px" imsrc="">px</unit><unit value="em" imsrc="">em</unit></param>');
    $sizexml = $sizexml->document;
    $sizexml->addAttribute('name', $elements['size']['name']);
    $sizexml->addAttribute('type', 'offlajntext');
    $size = new JElementOfflajnText();
    $size->id = $elements['size']['id'];
    $elements['size']['html'] = $size->universalfetchElement($elements['size']['name'], isset($def['size'])?$def['size']:'14||px', $sizexml);
    // END SIZE

    // COLOR
    $elements['color']['name'] = $name.'color';
    $elements['color']['id'] = $this->generateId($elements['color']['name']);

    $colorxml = new JSimpleXML();
    $colorxml->loadString('<param/>');
    $colorxml = $colorxml->document;
    $colorxml->addAttribute('name', $elements['color']['name']);
    $colorxml->addAttribute('type', 'offlajnminicolor');
    //$colorxml->addAttribute('alpha', $alpha);
    $color = new JElementOfflajnMiniColor();
    $color->id = $elements['color']['id'];
    $elements['color']['html'] = $color->universalfetchElement($elements['color']['name'], isset($def['color'])?$def['color']:'#000000', $colorxml);
    // END COLOR

    // TEXT-DECORATION
//    $stack->startStack();
    $elements['textdecor']['name'] = $name.'textdecor';
    $elements['textdecor']['id'] = $this->generateId($elements['textdecor']['name']);
    $textdecorxml = new JSimpleXML();
    $textdecorxml->loadString('<param/>');
    $textdecorxml = $textdecorxml->document;
    $textdecorxml->addAttribute('name', $elements['textdecor']['name']);
    $textdecorxml->addAttribute('type', 'offlajnlist');
    //$textdecorxml->addAttribute('height', '8');
    $textdecorxml->addAttribute('fireshow', '0');
		$textdecorxml->addChild('option', array('value'=>'100'))->setData('thin');
    $textdecorxml->addChild('option', array('value'=>'200'))->setData('extra-light');
    $textdecorxml->addChild('option', array('value'=>'300'))->setData('light');
    $textdecorxml->addChild('option', array('value'=>'400'))->setData('normal');
    $textdecorxml->addChild('option', array('value'=>'500'))->setData('medium');
    $textdecorxml->addChild('option', array('value'=>'600'))->setData('semi-bold');
    $textdecorxml->addChild('option', array('value'=>'700'))->setData('bold');
    $textdecorxml->addChild('option', array('value'=>'800'))->setData('extra-bold');
		$textdecorxml->addChild('option', array('value'=>'900'))->setData('ultra-bold');
    $textd = new JElementOfflajnList();
    $textd->id = $elements['textdecor']['id'];

    $elements['textdecor']['html'] = $textd->universalfetchElement($elements['textdecor']['name'], isset($def['textdecor'])?$def['textdecor']:'Normal', $textdecorxml);
//    $elements['textdecor']['script'] = $stack->endStack(true);

    // END TEXT-DECORATION

    // italic
    $elements['italic']['name'] = $name.'italic';
    $elements['italic']['id'] = $this->generateId($elements['italic']['name']);

    $italicxml = new JSimpleXML();
    $italicxml->loadString('<param mode="button" imsrc="italic.png" actsrc="italic_act.png" description=""/>');
    $italicxml = $italicxml->document;
    $italicxml->addAttribute('name', $elements['italic']['name']);
    $italic = new JElementofflajnonoff();
    $italic->id = $elements['italic']['id'];
    $elements['italic']['html'] = $italic->universalfetchElement($elements['italic']['name'], isset($def['italic'])?$def['italic']:0, $italicxml);
    // END italic

    // underline
    $elements['underline']['name'] = $name.'underline';
    $elements['underline']['id'] = $this->generateId($elements['underline']['name']);

    $underlinexml = new JSimpleXML();
    $underlinexml->loadString('<param mode="button" imsrc="underline.png" actsrc="underline_act.png" description=""/>');
    $underlinexml = $underlinexml->document;
    $underlinexml->addAttribute('name', $elements['underline']['name']);
    $underline = new JElementofflajnonoff();
    $underline->id = $elements['underline']['id'];
    $elements['underline']['html'] = $underline->universalfetchElement($elements['underline']['name'], isset($def['underline'])?$def['underline']:0, $underlinexml);
    // END underline

    // linethrough
    $elements['linethrough']['name'] = $name.'linethrough';
    $elements['linethrough']['id'] = $this->generateId($elements['linethrough']['name']);

    $linethroughxml = new JSimpleXML();
    $linethroughxml->loadString('<param mode="button" imsrc="linethrough.png" description=""/>');
    $linethroughxml = $linethroughxml->document;
    $linethroughxml->addAttribute('name', $elements['linethrough']['name']);
    $linethrough = new JElementofflajnonoff();
    $linethrough->id = $elements['linethrough']['id'];
    $elements['linethrough']['html'] = $linethrough->universalfetchElement($elements['linethrough']['name'], isset($def['linethrough'])?$def['linethrough']:0, $linethroughxml);
    // END linethrough

    // uppercase
    $elements['uppercase']['name'] = $name.'uppercase';
    $elements['uppercase']['id'] = $this->generateId($elements['uppercase']['name']);

    $uppercasexml = new JSimpleXML();
    $uppercasexml->loadString('<param mode="button" imsrc="uppercase.png" description=""/>');
    $uppercasexml = $uppercasexml->document;
    $uppercasexml->addAttribute('name', $elements['uppercase']['name']);
    $uppercase = new JElementofflajnonoff();
    $uppercase->id = $elements['uppercase']['id'];
    $elements['uppercase']['html'] = $uppercase->universalfetchElement($elements['uppercase']['name'], isset($def['uppercase'])?$def['uppercase']:0, $uppercasexml);
    // END uppercase

    // ALIGN
    $elements['align']['name'] = $name.'align';
    $elements['align']['id'] = $this->generateId($elements['align']['name']);

    $alignxml = new JSimpleXML();
    $tsxml = <<<EOD
<param type="offlajnradio" mode="image">
  <option value="left" imsrc="left_align.png"></option>
  <option value="center" imsrc="center_align.png"></option>
  <option value="right" imsrc="right_align.png"></option>
</param>
EOD;
    $alignxml->loadString($tsxml);
    $alignxml = $alignxml->document;
    $alignxml->addAttribute('name', $elements['align']['name']);
    $align = new JElementOfflajnRadio();
    $align->id = $elements['align']['id'];
    $elements['align']['html'] = $align->universalfetchElement($elements['align']['name'], isset($def['align'])?$def['align']:'left', $alignxml);
    // ALIGN

    // Alternative font
    $elements['afont']['name'] = $name.'afont';
    $elements['afont']['id'] = $this->generateId($elements['afont']['name']);

    $afontxml = new JSimpleXML();
    $afontxml->loadString('<param onoff="1"><unit value="1" imsrc="">ON</unit><unit value="0" imsrc="">OFF</unit></param>');
    $afontxml = $afontxml->document;
    $afontxml->addAttribute('name', $elements['afont']['name']);
    $afontxml->addAttribute('type', 'offlajntext');
    $afontxml->addAttribute('size', '10');
    $afont = new JElementOfflajnText();
    $afont->id = $elements['afont']['id'];
    $elements['afont']['html'] = $afont->universalfetchElement($elements['afont']['name'], isset($def['afont'])?$def['afont']:'Arial||1', $afontxml);
    // END Alternative font

    // TEXT SHADOW
    $elements['tshadow']['name'] = $name.'tshadow';
    $elements['tshadow']['id'] = $this->generateId($elements['tshadow']['name']);

    $tshadowxml = new JSimpleXML();
    $tsxml = <<<EOD
<param>
  <param size="1" validation="float" type="offlajntext"><unit value="px" imsrc="">px</unit></param>
  <param size="1" validation="float" type="offlajntext"><unit value="px" imsrc="">px</unit></param>
  <param size="1" validation="float" type="offlajntext"><unit value="px" imsrc="">px</unit></param>
  <param type="offlajnminicolor" alpha="$alpha"/>
  <param type="offlajnswitcher" onoff="1"><unit value="1" imsrc="">ON</unit><unit value="0" imsrc="">OFF</unit></param>
</param>
EOD;
    $tshadowxml->loadString($tsxml);
    $tshadowxml = $tshadowxml->document;
    $tshadowxml->addAttribute('name', $elements['tshadow']['name']);
    $tshadowxml->addAttribute('type', 'offlajncombine');
    $tshadow = new JElementOfflajnCombine();
    $tshadow->id = $elements['tshadow']['id'];
    $elements['tshadow']['html'] = $tshadow->universalfetchElement($elements['tshadow']['name'], isset($def['tshadow'])?$def['tshadow']:'0|*|0|*|0|*|#000000|*|0', $tshadowxml);
    // TEXT SHADOW

    // LINE HEIGHT
    $elements['lineheight']['name'] = $name.'lineheight';
    $elements['lineheight']['id'] = $this->generateId($elements['lineheight']['name']);

    $lineheightxml = new JSimpleXML();
    $lineheightxml->loadString('<param></param>');
    $lineheightxml = $lineheightxml->document;
    $lineheightxml->addAttribute('name', $elements['lineheight']['name']);
    $lineheightxml->addAttribute('type', 'offlajntext');
    $lineheightxml->addAttribute('size', '5');
    $lineheight = new JElementOfflajnText();
    $lineheight->id = $elements['lineheight']['id'];
    $elements['lineheight']['html'] = $lineheight->universalfetchElement($elements['lineheight']['name'], isset($def['lineheight'])?$def['lineheight']:'normal', $lineheightxml);
    // END LINE HEIGHT

    $this->loadFiles();

    $id = $this->generateId($name);

    $script = $stack->endStack(true);

    $settings = array();
    if($value == '' || $value[0] != '{'){
      foreach($tabs AS $t){
        $settings[$t] = new StdClass();
      }
      $settings = json_encode($settings);
    }else{
      $settings = $value;
    }

    $document = JFactory::getDocument();
    DojoLoader::addScript('
        new MiniFontConfigurator({
          id: "'.$this->id.'",
          defaultTab: "'.$tabs[0].'",
          origsettings: '.$settings.',
          elements: '.json_encode($elements).',
          script: '.json_encode($script).'
        });
    ');
    $html.="<a style='float: left;' id='".$id."change' href='#' class='font_select'></a>&nbsp;&nbsp;";
    if($this->_parent->get('admindebug', 0) == 1){
      $html.='<span>Raw font data: </span><input type="text" name="'.$name.'" id="'.$id.'" value="'.str_replace('"',"'",$value).'" />';
    }else{
      if($value != "")
        if($value[0] != '{') $value = $settings;
      $html.='<input type="hidden" name="'.$name.'" id="'.$id.'" value=\''.str_replace("'",'&#39;',$value).'\' />';
    }

    return $html;
	}
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnMiniFont extends JElementOfflajnMiniFont {}
}