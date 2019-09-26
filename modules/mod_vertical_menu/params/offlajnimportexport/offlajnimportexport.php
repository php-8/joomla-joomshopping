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

class JElementOfflajnImportExport extends JOfflajnFakeElementBase{
  var	$_name = 'OfflajnImportExport';

  function universalfetchElement($name, $value, &$node){
    $document = JFactory::getDocument();
    $this->loadFiles();
    $attr = $node->attributes();
    //$html = '<div class="" id="offlajniocontainer'.$this->id.'">';
    $html.= '<input type="file" accept="application/zip,application/x-zip,application/x-zip-compressed" name="offlajnimport" id="'.$this->id.'" class="offlajnimportexportbtn" value="">';
    $html.= '<a id="'.$this->id.'import" class="offlajnimportbtn"></a> <a id="'.$this->id.'export" class="offlajnexportbtn"></a>';
    //$html.= '</div>';

    DojoLoader::addScript('
      new OfflajnImportExport({
        id: "'.$this->id.'",
        modPath: "'.JURI::root().'modules/'.$this->_moduleName.'/",
        downloadName: "'.(isset($attr['download']) ? $attr['download'] : $this->_moduleName).'",
        exclude: "'.@$attr['exclude'].'"
      });
    ');
    return $html;
  }
}

if(version_compare(JVERSION,'1.6.0','ge')) {
  class JFormFieldOfflajnImportExport extends JElementOfflajnImportExport {}
}