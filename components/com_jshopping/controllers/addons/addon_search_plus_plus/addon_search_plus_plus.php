<?php
     /*
    * @version      1.1.9 26.01.2019
    * @author       Garry
    * @package      update.php
    * @copyright    Copyright (C) 2019 joom-shopping.com. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die;

    class AddonSearchPlusPlus extends AddonSearchPlusPlusAddonCorext {

        /**
         * @return AddonSearchPlusPlus
         */
        public static function getInst($id = 0, $cached = true) {
            return parent::getInst($id, $cached);
        }

    }
