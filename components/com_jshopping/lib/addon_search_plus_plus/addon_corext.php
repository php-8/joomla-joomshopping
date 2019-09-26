<?php
     /*
    * @version      1.1.9 26.01.2019
    * @author       Garry
    * @package      update.php
    * @copyright    Copyright (C) 2019 joom-shopping.com. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die;

    abstract class AddonSearchPlusPlusAddonCorext extends AddonSearchPlusPlusSingleton {

        protected
            $id         = 0,
            $alias      = '',
            $table      = null,
            $ini_params = [],
            $params     = [],
            $langsef    = 'en',
            $log        = [];

        public static function getInst($id = 0, $cached = true) {
            return parent::getInst(0, $cached);
        }

        public function __construct() {
            $this->ini_params = $this->typify(
                parse_ini_file(
                    JPath::clean(JPATH_JOOMSHOPPING . '/addons/' . basename(__DIR__) . '/params.ini'),
                    true
                )
            );
            $this->alias = $this->ini_params['alias'];
            $this->table = JSFactory::getTable('addon');
            $this->table->loadAlias($this->alias);
            $this->id = $this->table->id;
            $this->setParams();
            $langtag = JFactory::getLanguage()->getTag();
            foreach (getAllLanguages() as $lang) {
                if ($lang->language == $langtag) {
                    $this->langsef = $lang->lang;
                    break;
                }
            }
            $log_path = $this->getLogPath();
            $log      = @parse_ini_file($log_path, true);
            if ($log) {
                $this->setProp('log', $log);
            }
            JSFactory::loadExtLanguageFile($this->alias);
            $this->addJsDefaultVars();
        }

        public function typify($var) {
            return parent::typify($var);
        }

        protected function setParams() {
            /* set ini and db data */
            $this->params = $this->typify(
                array_merge(
                    $this->params,
                    $this->ini_params,
                    (array) $this->table->getParams()
                )
            );
            /* add directories */
            foreach (JFolder::folders(JPATH_JOOMSHOPPING_ADMIN) as $dir) {
                switch ($dir) {
                    case 'controllers':
                    case 'models':
                        $this->params['dirs']['admin_' . $dir] = 'administrator/components/com_jshopping/' . $dir;
                        break;
                    default:
                        $this->params['dirs']['admin_' . $dir] = 'administrator/components/com_jshopping/' . $dir . '/' . $this->alias;
                }
            }
            foreach (JFolder::folders(JPATH_JOOMSHOPPING) as $dir) {
                switch ($dir) {
                    case 'controllers':
                    case 'log':
                    case 'models':
                    case 'tables':
                        $this->params['dirs'][$dir] = 'components/com_jshopping/' . $dir;
                        break;
                    case 'css':
                    case 'js':
                    case 'templates':
                        $this->params['dirs'][$dir] = 'components/com_jshopping/' . $dir . '/addons/' . $this->alias;
                        break;
                    default:
                        $this->params['dirs'][$dir] = 'components/com_jshopping/' . $dir . '/' . $this->alias;
                }
            }
            foreach (JFolder::folders(JPATH_JOOMSHOPPING . '/addons/' . $this->alias) as $dir) {
                $this->params['dirs'][$dir] = $this->params['dirs']['addons'] . '/' . $dir;
            }
            $this->params['dirs']['fields'] = $this->params['dirs']['templates'] . '/fields';
            if (empty($this->params['dirs']['tmp'])) {
                $this->params['dirs']['tmp'] = str_replace(
                    JPath::clean(JPATH_ROOT),
                    '',
                    JPath::clean(
                        JFactory::getConfig()->get('tmp_path', JPATH_ROOT . '/tmp') . '/' . $this->alias
                    )
                );
            }
            /* treat specials */
            foreach ($this->params as $key => $val) {
                if ($key === 'dirs') {
                    $dirs = [];
                    foreach ($val as $dir => $path) {
                        $dirs[$dir] = trim(str_replace('\\', '/', JPath::clean($path)), '/');
                    }
                    $this->params[$key] = $dirs;
                }
                else {
                    $this->params[$key] = $val;
                }
            }
            /* extend */
            $this->params = $this->extendParams($this->params);
            return true;
        }

        public function extendParams(array $params) {
            $keys_new = [];
            foreach ($params as $key => $val) {
                if (in_array($key, $keys_new)) {
                    continue;
                }
                if (is_array($val)) {
                    $params[$key] = $this->extendParams($val);
                    continue;
                }
                $underscore = strrpos($key, '_');
                if (!$underscore) {
                    continue;
                }
                $key_new = $this->typify(substr($key, 0, $underscore));
                switch (substr($key, $underscore + 1)) {
                    case 'datetime':
                        $key_new = $key_new . '_timestamp';
                        $val_new = strtotime($val);
                        break;
                    case 'hours':
                        $val_new = $val * 3600;
                        break;
                    case 'json':
                        $val_new = $this->extendParams(
                            (array) json_decode($val, true)
                        );
                        break;
                    case 'minutes':
                        $val_new = $val * 60;
                        break;
                    default:
                        continue 2;
                }
                $keys_new[]       = $key_new;
                $params[$key_new] = $this->typify($val_new);
            }
            return $params;
        }

        public function updateDbParams(array $params) {
            $params_new = [];
            foreach ($params as $key => $val) {
                $this->params[$key] = $this->typify($val);
            }
            $table = JSFactory::getTable('addon');
            $table->loadAlias($this->alias);
            foreach ((array) unserialize($table->params) as $key => $val) {
                $params_new[$key] = isset($params[$key]) ? $params[$key] : $val;
            }
            JPluginHelper::importPlugin('jshoppingadmin');
            JSFactory::getModel('addons')->save([
                'id'     => $this->getId(),
                'task'   => 'apply',
                'params' => $params_new
            ]);
            $this->table = JSFactory::getTable('addon');
            $this->table->loadAlias($this->alias);
            return $this->setParams();
        }

        public function addonExists($alias) {
            return (bool) JSFactory::getTable('addon')->load([
                'alias' => strtolower(trim($alias))
            ]);
        }

        public function arrayMergeRecursiveDistinct($array1, $array2) {
            $res = $array1;
            foreach ($array2 as $key => &$value) {
                if (is_array($value) && isset($res[$key]) && is_array($res[$key])) {
                    $res[$key] = $this->arrayMergeRecursiveDistinct($res[$key], $value);
                }
                else {
                    $res[$key] = $value;
                }
            }
            return $res;
        }

        public function sessionSet($name, $value) {
            return JFactory::getSession()->set($name, $value, $this->alias);
        }

        public function sessionGet($name, $default = null) {
            return JFactory::getSession()->get($name, $default, $this->alias);
        }

        public function sessionClear($name) {
            return JFactory::getSession()->clear($name, $this->alias);
        }

        public function sessionDrop() {
            foreach ((array) JFactory::getSession()->getData()->get('__' . $this->getAlias()) as $name => $value) {
                $this->sessionClear($name);
            }
            return true;
        }

        public function SEFLink($url, $useDefaultItemId = 1, $redirect = 0, $ssl = null) {
            return SEFLink($url, $useDefaultItemId, $redirect, is_null($ssl) ? JSFactory::getConfig()->use_ssl : $ssl);
        }

        public function redirect($url = '', $useDefaultItemId = 1, $redirect = 0, $ssl = null) {
            $url = trim($url ? $url : JUri::base());
            if (stripos($url, 'index.php?option=com_jshopping') == 0) {
                $url = $this->SEFLink($url, $useDefaultItemId, $redirect, $ssl);
            }
            JFactory::getApplication()->redirect($url);
        }

        public function renderField($xml, $val = null, $path = '', $options = []) {
            $xml_el = new SimpleXMLElement($xml);
            $name   = trim($xml_el->attributes()->name);
            $type   = trim($xml_el->attributes()->type);
            $class  = 'JFormField' . ucfirst($type);
            if (isset($xml_el->attributes()->label_vars)) {
                $label_vars = array_map('trim', explode(',', $xml_el->attributes()->label_vars));
                if ($label_vars) {
                    $view        = $this->getView('label_vars');
                    $view->label = trim($xml_el->attributes()->label);
                    $view->vars  = $label_vars;
                    $view->addTemplatePath($this->getParam('dirs_pathes[fields]'));
                    $xml_el->attributes()->label = $view->loadTemplate();
                }
                unset($xml_el['label_vars']);
            }
            foreach ([
                $path ? $path : (JPATH_LIBRARIES . '/joomla/form/fields/'),
                $this->getParam('dirs_pathes[fields]')
            ] as $base) {
                if (file_exists($base . $type . '.php')) {
                    JLoader::import($type, $base);
                    break;
                }
            }
            $field = new $class();
            $field->setForm(new JForm('form'));
            $field->setup($xml_el, is_null($val) ? $this->getParam($name) : $val);
            return $field->renderField($options);
        }

        public function renderPlgView($layout, $vars = [], $type = '') {
            extract($vars);
            if (!$type) {
                $type = debug_backtrace()[1]['class'];
                $type = strtolower($type);
                $type = preg_replace('/^plg/', '', $type);
                $type = preg_replace('/' . $this->alias . '$/i', '', $type);
            }
            ob_start();
            include JPluginHelper::getLayoutPath($type, $this->alias, $layout);
            return ob_get_clean();
        }

        public function addCss($filename = 'addon') {
            if (!file_exists($this->getParam('dirs_pathes[css]') . $filename . '.css')) {
                return false;
            }
            JFactory::getDocument()->addStyleSheet($this->getParam('dirs_links[css]') . $filename . '.css');
            return true;
        }

        public function addCssDeclaration($content, $type = 'text/css') {
            $doc     = JFactory::getDocument();
            $content = trim($content);
            $strlen  = strlen($content);
            $type    = strtolower($type);
            if (
                stripos($content, '<style>') === 0 &&
                stripos($content, '</style>') === ($strlen - 8)
            ) {
                $content = trim(
                    substr($content, 7, $strlen - 15)
                );
            }
            if (!$content) {
                return false;
            }
            if (isset($doc->_style[$type]) && is_int(strpos($doc->_style[$type], $content))) {
                return true;
            }
            $doc->addStyleDeclaration($content, $type);
            return true;
        }

        protected function addJsDefaultVars() {
            $res = [];
            foreach ([
                'root'   => JUri::root(),
                'CANCEL' => JText::_('JCANCEL')
            ] as $name => $val) {
                $res[] = $this->addJsVar($name, $val);
            }
            $prefix = '_JSHOP_' . strtoupper($this->alias) . '_';
            foreach ([
                'OK'
            ] as $name) {
                if (defined($prefix . $name)) {
                    $res[] = $this->addJsVar($name, constant($prefix . $name));
                }
            }
            return !in_array(false, $res);
        }

        public function addJsVar($name, $val) {
            JFactory::getDocument()->addScriptOptions($this->alias, [$name => $val]);
            return true;
        }

        public function addJs($filename = 'addon') {
            if (!file_exists($this->getParam('dirs_pathes[js]') . $filename . '.js')) {
                return false;
            }
            JFactory::getDocument()->addScript($this->getParam('dirs_links[js]') . $filename . '.js');
            return true;
        }

        public function addJsDeclaration($content, $type = 'text/javascript') {
            $doc     = JFactory::getDocument();
            $content = trim($content);
            $strlen  = strlen($content);
            $type    = strtolower($type);
            if (
                stripos($content, '<script>') === 0 &&
                stripos($content, '</script>') === ($strlen - 9)
            ) {
                $content = trim(
                    substr($content, 8, $strlen - 17)
                );
            }
            if (!$content) {
                return false;
            }
            if (isset($doc->_script[$type]) && is_int(strpos($doc->_script[$type], $content))) {
                return true;
            }
            $doc->addScriptDeclaration($content, $type);
            return true;
        }

        public function getParam($name, $default = null) {
            $res      = $this->params;
            $name_arr = preg_replace('/^params\[/', '', $name);
            $name_arr = str_replace(']', '', $name_arr);
            $name_arr = explode('[', $name_arr);
            if (isset($this->params['dirs'][$name_arr[1]])) {
                $dir  = $name_arr[1];
                switch ($name_arr[0]) {
                    case 'dirs_pathes':
                        return $this->params['dirs_pathes'][$dir] = JPath::clean(JPATH_ROOT . '/' . $this->params['dirs'][$dir] . '/');
                        break;
                    case 'dirs_links':
                        return $this->params['dirs_links'][$dir]  = JUri::root() . $this->params['dirs'][$dir] . '/';
                        break;
                }
            }
            foreach ($name_arr as $key) {
                if (!isset($res[$key])) {
                    return $default;
                }
                $res = $res[$key];
            }
            return $res;
        }

        public function getModel($name = '') {
            return JSFactory::getModel(
                strtolower(
                    trim(
                        $name ? ($this->alias . '_' . $name) : $this->alias
                    )
                )
            );
        }

        public function getModule($alias = '', $published_only = true) {
            $alias = $alias !== '' ? $alias : 'mod_' . $this->getAlias();
            $db    = JFactory::getDbo();
            $db->setQuery('
                SELECT *
                FROM '  . $db->qn('#__modules') . '
                WHERE ' . $db->qn('module')     . ' = ' . $db->q($alias) .
                (
                    !$published_only
                        ? ''
                        : ' AND ' . $db->qn('published') . ' = 1'
                )
            );
            $res = $db->loadObject();
            if (!$res) {
                return $res;
            }
            $res->params = new JRegistry($res->params);
            return $res;
        }

        public function getView($layout = '', $front_end = true) {
            $layout_arr = explode('/', trim($layout));
            $layout     = end($layout_arr);
            $subfolders = implode('/', array_slice($layout_arr, 0, -1));
            /* if - for avoidance conflict between front and back end classes */
            if (!class_exists('JshoppingViewAddons')) {
                include_once(
                    ($front_end ? JPATH_JOOMSHOPPING : JPATH_JOOMSHOPPING_ADMIN) .
                    '/views/addons/view.html.php'
                );
            }
            $res = new JshoppingViewAddons([
                'template_path' => [
                    JPath::clean(
                        $front_end
                            ? (
                                JPATH_JOOMSHOPPING . '/templates/addons/' .
                                $this->alias . '/' . $subfolders
                            )
                            : (
                                JPATH_JOOMSHOPPING_ADMIN . '/views/' .
                                $this->alias . '/tmpl/' . $subfolders
                            )
                    ),
                    JPath::clean(
                        JPATH_JOOMSHOPPING . '/templates/' .
                        JFactory::getApplication()->getTemplate() .
                        '/addons/' . $this->alias . '/' . $subfolders
                    )
                ]
            ]);
            if ($layout){
                $res->setLayout($layout);
            }
            $res->set('addon_path_images', $this->getParam('dirs_pathes[images]'));
            $res->addon = $this;
            return $res;
        }

        public function createTmpFile($contents, $extension) {
            $res = '';
            $dir = $this->getParam('dirs_pathes[tmp]');
            if (!JFolder::exists($dir)) {
                if (!JFolder::create($dir)) {
                    return '';
                }
            }
            do {
                $res = $dir . uniqid() . '.' . $extension;
            } while (JFile::exists($res));
            if (!JFile::write($res, $contents)) {
                return '';
            }
            return $res;
        }

        public function logException(Exception $e, array $extras = [], $filepath = null) {
            return $this->log(
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $e->getCode(),
                $extras,
                $filepath
            );
        }

        public function log($msg, $file = '', $line = 0, $code = 0, array $extras = [], $filepath = null) {
            $jshopConfig = JSFactory::getConfig();
            if (!$jshopConfig->savelog || !$this->getParam('logging', true)) {
                return false;
            }
            if ($filepath === null) {
                $filepath = JPath::clean(
                    JSFactory::getConfig()->log_path . '/' . $this->alias . '.log.ini'
                );
            }
            /* collect parts */
            $parts = [
                'message' => $msg
            ];
            if ($file) {
                $parts['file'] = (string) JPath::clean($file);
            }
            if ($line) {
                $parts['line'] = (int) $line;
            }
            if ($code) {
                $parts['code'] = (int) $code;
            }
            $parts      += $extras;
            $parts['ip'] = $_SERVER['REMOTE_ADDR'];
            /* build entry */
            $key        = getJsDate() . str_pad(strstr(microtime(true), '.'), 7, '0');
            $entry      = '[' . $key . ']' . "\r\n";
            $pad_length = max(array_map('strlen', array_keys($parts)));
            foreach ($parts as $k => $v) {
                $value  = preg_replace(['/\s+/', '/"/'], [' ', '\"'], $v);
                $value  = rtrim($value, '\\');
                $value  = $this->typify($value);
                $value  = is_string($value) ? ('"' . $value . '"') : $value;
                $entry .= str_pad($k, $pad_length) . ' = ' . $value . "\r\n";
            }
            $entry .= "\r\n";
            /* update log property */
            if ($filepath == $this->getLogPath()) {
                $this->log[$key] = $parts;
            }
            /* store entry */
            return (bool) JFile::append($filepath, $entry);
        }

        public function msg($msg, $type = 'm') {
            JFactory::getApplication()->enqueueMessage(
                $msg,
                [
                    'e' => 'error',
                    'm' => 'message',
                    'n' => 'notice',
                    'w' => 'warning'
                ][$type]
            );
        }

        public function getId() {
            return $this->id;
        }

        public function getAlias() {
            return $this->alias;
        }

        public function getTable() {
            return $this->table;
        }

        public function getIniParams() {
            return $this->ini_params;
        }

        public function getParams() {
            return $this->params;
        }

        public function getLangsef() {
            return $this->langsef;
        }

        public function getLog() {
            return $this->log;
        }

        public function getLogPath() {
            return JPath::clean(JSFactory::getConfig()->log_path . $this->alias . '.log.ini');
        }

    }
