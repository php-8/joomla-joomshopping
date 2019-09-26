<?php
    /*
    * @version      1.1.9 26.01.2019
    * @author       Garry
    * @package      update.php
    * @copyright    Copyright (C) 2019 joom-shopping.com. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die;

    class PlgSystemAddon_search_plus_plus extends JPlugin {

        public function onAfterInitialise() {
            include_once JPATH_SITE . '/components/com_jshopping/lib/' . $this->_name . '/autoload.php';
        }

    }
