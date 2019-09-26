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

jimport( 'joomla.html.parameter' );

@JOfflajnParams::load('offlajnlist');

class JElementOfflajnMenutype extends JElementOfflajnList{

  var $_name = 'offlajnmenutype';

  function universalFetchElement($name, $value, &$node){
    $this->loadFiles();
    $attrs = $node->attributes();
    $f = isset($attrs['folder']) ? $attrs['folder'] : 'types';
    $this->label = isset($attrs['label']) ? $attrs['label'] : 'Type';
    $this->typesdir = dirname(__FILE__).'/../../'.$f.'/';
    $document =& JFactory::getDocument();

    return $this->generateTypeSelector($name, $value);
  }

  function generateTypeSelector($name, $value){
    $id = $this->generateId($this->label);

    $types = JFolder::folders($this->typesdir);
    $this->typeParams = array('default' => '');
    $this->typeScripts = array('default' => '');
    $node = new JSimpleXMLElement('list');

    $data = $this->_parent->toArray();



    preg_match('/(.*)\[([a-zA-Z0-9]*)\]$/', $name, $out);
    @$control = $out[1];
    @$orig_name = $out[2];

    $document =& JFactory::getDocument();
    $stack = & JsStack::getInstance();

    $formdata = array();
    $c = $control;
    if(version_compare(JVERSION,'1.6.0','ge')) {
      if(isset($data[$orig_name]) && is_array($data[$orig_name]) ){
        $formdata = $data[$orig_name];
      }
      $c = $name;
    }else{
      $formdata = $data;
    }

    ${'_SESSION'}[$id] = array(
      'typesdir' => $this->typesdir,
      'formdata' => $formdata,
      'c' => $c,
      'module' => $this->_moduleName
    );

    if ( is_array($types) ){
      foreach($types as $type){
        if($n = $this->checkExtension($type)){
          $node->addChild('option',array('value' => $type))->setData(ucfirst($n));

          $key = md5($type);
          ${'_SESSION'}[$id]['forms'][$key] = $type;

          $this->typeParams[$type] = $key;
        }
    	}
    }

    if(version_compare(JVERSION,'1.6.0','ge')) {
      $name.= '['.$orig_name.']';
    }
    //select
    //$typeField = JHTML::_('select.genericlist',  $options, $name, 'class="inputbox"', 'value', 'text', $value);

    $typeField = parent::universalfetchElement($name, version_compare(JVERSION,'1.6.0','ge') ? @$value[$orig_name] : $value, $node);


    plgSystemOfflajnParams::addNewTab($id, $this->label.' Parameters', '');

    $document =& JFactory::getDocument();
    DojoLoader::addScript('
        new TypeConfigurator({
          selectorId: "'.$this->generateId($name).'",
          typeParams: '.json_encode($this->typeParams).',
          typeScripts: '.json_encode($this->typeScripts).',
          joomfish: 0,
          control: "'.$id.'"
        });
    ');

    return $typeField;
  }



  function checkExtension($name){

     switch($name) {
      case 'virtuemart1':
        if(!is_dir(JPATH_ROOT.'/components/com_virtuemart') || !file_exists(JPATH_ROOT.'/components/com_virtuemart/virtuemart_parser.php') || version_compare(JVERSION,'1.6.0','ge')) {
          return false;
        }
        return "virtuemart";

      case 'virtuemart2':
        if(!is_dir(JPATH_ROOT.'/components/com_virtuemart/controllers')){
          return false;
        }
        return "virtuemart";

      case 'k2':
        if(!is_dir(JPATH_ROOT.'/components/com_k2/controllers')){
          return false;
        }
        return $name;

      case 'tienda':
        if(!is_dir(JPATH_ROOT.'/components/com_tienda/controllers')){
          return false;
        }
        return $name;

      case 'redshop':
        if(!is_dir(JPATH_ROOT.'/components/com_redshop/controllers')){
          return false;
        }
        return $name;
      
      case 'hikashop':
      case 'hikashopbrands':
        if(!is_dir(JPATH_ROOT.'/components/com_hikashop/controllers')){
          return false;
        }
        return $name;
        
      case 'djclassified':
        if(!is_dir(JPATH_ROOT.'/components/com_djclassifieds/controllers')){
          return false;
        }
        return $name;
  
      case 'jshopping':
        if(!is_dir(JPATH_ROOT.'/components/com_jshopping/controllers')){
          return false;
        }
        return $name;
        
      case 'easyblog':
        if(!is_dir(JPATH_ROOT.'/components/com_easyblog/controllers')){
          return false;
        }
        return $name;
  
      case 'mijoshop':
       if(!is_dir(JPATH_ROOT.'/components/com_mijoshop/mijoshop')){
         return false;
       }
        return $name;

      case 'zoo':
        if(!is_dir(JPATH_ROOT.'/components/com_zoo/controllers')){
          return false;
        }
        return $name;
     }

    return $name;
  }

}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnMenutype extends JElementOfflajnMenutype {}
}
?>