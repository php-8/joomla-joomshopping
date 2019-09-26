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

if (!class_exists('OfflajnMenuThemeCache2')) {

  jimport('joomla.registry.registry');
  jimport('joomla.filesystem.path');
  jimport('joomla.filesystem.file');

  class OfflajnMenuThemeCache2 extends JRegistry
  {
    public $module;
    public $params;
    public $env;
    public $cachePath;
    public $cacheUrl;
    public $cssCompress;
    public $jsCompress;
    public $js;
    public $css;
    public $style;

    public function __construct($namespace, &$_module, &$_params)
    {
      $this->cssCompress = 1;
      $this->jsCompress = 1;
      $this->js = array();
      $this->css = array();
      $this->style = array();
      $this->module = &$_module;
      $this->params = &$_params;
      $this->env = array('params' => &$_params);

      $writeError = false;
      $folder = $this->module->id;
      $registry = JFactory::getConfig();
      if (version_compare(JVERSION, '3.0', 'ge')) {
        $curLanguage = $registry->get("joomfish.language");
      } else {
        $curLanguage = $registry->getValue("joomfish.language");
      }

      if (is_object($curLanguage)) {
        $folder .= '-lang' . $curLanguage->get('lang_id');
      } else if (is_string($curLanguage) && $curLanguage != '') {
        $folder .= '-lang' . $curLanguage;
      }
      $this->cachePath = JPath::clean(JPATH_SITE . '/modules/' . $this->module->module . '/cache/' . $folder . '/');
      if (!JFolder::exists($this->cachePath)) {
        JFolder::create($this->cachePath);
      }

      if (!JFolder::exists($this->cachePath)) {
        $writeError = true;
      }
      if ($writeError) {
        JText::printf("%s is unwriteable or non-existent, because the system does not allow the operation from PHP. Please create the directory and set the writing access!", $this->cachePath);
        exit;
      }
      $this->cacheUrl = JURI::base(true) . '/modules/' . $this->module->module . '/cache/' . $folder . '/';
      $this->moduleUrl = JURI::base(true) . '/modules/' . $this->module->module . '/';
    }

    /*
    return the two url in an array
     */
    public function generateCache()
    {
      return array($this->generateCSSCache(), $this->generateJSCache());
    }

    public function assetsAdded()
    {
      $cssName = $this->generateCssName();
      if (defined('DEMO')) {
        $subdir = substr($cssName, 0, 2) . '/' . substr($cssName, 2, 2) . '/' . substr($cssName, 4, 2) . '/' . substr($cssName, 0, 32);
        $this->cachePath = JPath::clean($this->cachePath . '/' . $subdir . '/');
        if (!JFolder::exists($this->cachePath)) {
          JFolder::create($this->cachePath);
        }

        $this->cacheUrl = $this->cacheUrl . str_replace('\\', '/', $subdir) . '/';
      }
    }

    public function addCss($css)
    {
      if (!in_array($css, $this->css)) {
        $this->css[] = $css;
      }
    }

    public function addStyle($style)
    {
      $this->style[] = $style;
    }

    /*
    This vars will be available in the CSS as $$k
     */
    public function addCssEnvVars($k, &$v)
    {
      $this->env[$k] = &$v;
    }

    public function generateCssName()
    {
      $cachetext = '';
      foreach ($this->css as $css) {
        $cachetext .= $css . filemtime($css);
      }
      $hash = md5($cachetext . serialize($this->params->toArray()));
      return $hash . '.css';
    }

    public function generateCSSCache()
    {
      $cssName = $this->generateCssName();
      $file = $this->cachePath . $cssName;
      if (!is_file($file)) {
        $needToDelete = JFolder::files($this->cachePath, '(css)|(png)|(jpg)|(svg)$', false, true);
        if (is_array($needToDelete) && count($needToDelete) > 0) {
          JFile::delete($needToDelete); // CSS cache cleaned
        }
/*        $ks = array_keys($this->env);
for($i = 0;$i < count($ks); $i++ ){
$$ks[$i] = &$this->env[$ks[$i]];
}   */
        foreach (array_keys($this->env) as $key) {
          $$key = &$this->env[$key];
        }
        ob_start();
        foreach ($this->css as $css) {
          include $css;
        }
        foreach ($this->style as $style) {
          echo $style;
        }
        $rawcss = ob_get_contents();
        ob_end_clean();

        file_put_contents($file, $rawcss);
      }
      return $this->cacheUrl . $cssName; // url to the CSS
    }

    public function addJs($js)
    {
      if (!in_array($js, $this->js)) {
        $this->js[] = $js;
      }
    }

    public function generateJsName()
    {
      $cachetext = '';
      foreach ($this->js as $js) {
        $cachetext .= $js . filemtime($js);
      }
      $hash = md5($cachetext);
      return $hash . '.js';
    }

    public function generateJSCache()
    {
      $jsName = $this->generateJsName();
      $file = $this->cachePath . $jsName;
      if (!is_file($file)) {
        $needToDelete = JFolder::files($this->cachePath, 'js$', false, true);
        if (is_array($needToDelete) && count($needToDelete) > 0) {
          JFile::delete($needToDelete); // JS cache cleaned
        }
        $jst = "(function(){";
        foreach ($this->js as $js) {
          $jst .= file_get_contents($js) . "\n";
        }
        $jst .= "})();";
        file_put_contents($file, $jst);
      }
      return $this->cacheUrl . $jsName; // url to the JS
    }

    public function getFilesFromCache()
    {

    }

    public function set($key, $value = '', $group = '_default')
    {
      return $this->setValue($group . '.' . $key, (string) $value);
    }

    public function get($key, $default = '', $group = '_default')
    {
      $value = $this->getValue($group . '.' . $key);
      $result = (empty($value) && ($value !== 0) && ($value !== '0')) ? $default : $value;
      return $result;
    }

    public function def($key, $default = '', $group = '_default')
    {
      $value = $this->get($key, (string) $default, $group);
      return $this->set($key, $value);
    }
  }
}
