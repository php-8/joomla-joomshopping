<?php
     /*
    * @version      1.1.9 26.01.2019
    * @author       Garry
    * @package      update.php
    * @copyright    Copyright (C) 2019 joom-shopping.com. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die;

    $db            = JFactory::getDbo();
    $addon         = call_user_func(JStringNormalise::toCamelCase(basename(__DIR__)) . '::getInst');
    $alias         = $addon->getAlias();
    $table_addon   = JTable::getInstance('addon', 'jshop');
    $tables_prefix = $db->getPrefix();
    $tables        = [];
    $folders       = [];
    $files         = [];
    $modules       = [];
    $plugins       = [];
    foreach ($db->getTableList() as $table) {
        if (stripos($table, $tables_prefix . 'jshopping_' . $alias) === 0) {
            $tables[] = $table;
        }
    }
    foreach ($addon->getParam('dirs') as $key => $dir) {
        $dir_path = $addon->getParam('dirs_pathes[' . $key . ']');
        if (!is_dir($dir_path)) {
            continue;
        }
        if (basename($dir) == $alias) {
            $folders[] = $dir_path;
        }
        else {
            foreach (JFolder::files($dir_path) as $file) {
                if (stripos($file, $alias) === 0) {
                    $files[] = JPath::clean($dir_path . '/' . $file);
                }
            }
        }
    }
    $db->setQuery('
        SELECT ' . $db->qn('element') . '
        FROM '   . $db->qn('#__extensions') . '
        WHERE '  . $db->qn('type')    . ' LIKE ' . $db->q('module') . '
        AND '    . $db->qn('element') . ' LIKE ' . $db->q('mod_' . $alias . '%')
    );
    $modules = array_merge($modules, (array) $db->loadColumn());
    if ($modules) {
        foreach($modules as $module) {
            $table_addon->unInstallJoomlaExtension('module', $module, '');
            $folders[] = JPath::clean(JPATH_ROOT . '/modules/' . $module);
        }
    }
    $db->setQuery('
        SELECT ' . $db->qn('folder') . '
        FROM '   . $db->qn('#__extensions') . '
        WHERE '  . $db->qn('type')    . ' LIKE ' . $db->q('plugin') . '
        AND '    . $db->qn('element') . ' LIKE ' . $db->q($alias)
    );
    $plugins = array_merge($plugins, (array) $db->loadColumn());
    if ($plugins) {
        foreach($plugins as $plugin) {
            $table_addon->unInstallJoomlaExtension('plugin', $alias, $plugin);
            $folders[] = JPath::clean(JPATH_ROOT . '/plugins/' . $plugin . '/' . $alias);
        }
    }
    foreach ($tables as $table) {
        $db->setQuery('DROP TABLE ' . $db->qn($table));
        $db->execute();
    }
    foreach ($folders as $folder) {
        JFolder::delete($folder);
    }
    foreach ($files as $file) {
        JFile::delete($file);
    }
