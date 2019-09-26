<?php
     /*
    * @version      1.1.9 26.01.2019
    * @author       Garry
    * @package      update.php
    * @copyright    Copyright (C) 2019 joom-shopping.com. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die;

    jimport('joomla.filesystem.file');
    jimport('joomla.filesystem.folder');
    JModelLegacy::addIncludePath(JPATH_SITE          . '/components/com_jshopping/models');
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jshopping/models');
    spl_autoload_register(function($classname) {
        $alias       = strtolower(basename(__DIR__));
        $alias_camel = JStringNormalise::toCamelCase($alias);
        $dirs        = [];
        $type        = '';
        foreach ([
            'addon'      => $alias_camel,
            'controller' => 'JshoppingController' . ucfirst($alias),
            'model'      => 'JshoppingModel' . ucfirst($alias)
        ] as $key => $val) {
            if (strpos($classname, $val) === 0) {
                $type = $key;
                break;
            }
        }
        switch ($type) {
            case 'addon':
                $filename = str_replace($alias_camel, '', $classname);
                $filename = $filename ? $filename : $alias;
                $filename = JStringNormalise::fromCamelCase($filename);
                $filename = str_replace(' ', '_', $filename);
                $filename = strtolower($filename);
                foreach ([
                    'addons',
                    'entities',
                    'lib'
                ] as $dir) {
                    $dirs[] = JPATH_SITE . '/components/com_jshopping/' . $dir . '/'   . $alias . '/';
                }
                break;
            case 'controller':
            case 'model':
                $filename = str_replace(
                    [
                        'JshoppingController',
                        'JshoppingModel',
                        'jshop'
                    ],
                    '',
                    $classname
                );
                $filename = strtolower($filename);
                $dirs[]   = JPATH_SITE          . '/components/com_jshopping/' . $type . 's/';
                $dirs[]   = JPATH_ADMINISTRATOR . '/components/com_jshopping/' . $type . 's/';
                break;
        }
        foreach ($dirs as $dir) {
            $dir       = JPath::clean($dir . '/');
            $file_path = $dir . $filename . '.php';
            if (is_readable($file_path)) {
                include_once $file_path;
                return true;
            }
        }
        return false;
    });
