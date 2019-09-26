<?php
     /*
    * @version      1.1.9 26.01.2019
    * @author       Garry
    * @package      update.php
    * @copyright    Copyright (C) 2019 joom-shopping.com. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die;

    abstract class AddonSearchPlusPlusSingleton extends AddonSearchPlusPlusProto {

        private static $instances  = [];

        private function __construct() {}

        /**
         * @throws Exception
         */
        public static function getInst($id, $cached = true) {
            $called_class = get_called_class();
            if ($cached && isset(self::$instances[$called_class][$id])) {
                return self::$instances[$called_class][$id];
            }
            $inst = new static($id);
            if (!$inst->getId()) {
                if ($id) {
                    throw new Exception('
                        No instance \'' . get_called_class() . '\'
                        with ' . static::getIdName() . ' ' . $id
                    );
                }
                return $inst;
            }
            return self::$instances[$called_class][$id] = $inst;
        }

    }
