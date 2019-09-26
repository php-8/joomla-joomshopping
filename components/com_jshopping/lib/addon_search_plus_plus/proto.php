<?php
     /*
    * @version      1.1.9 26.01.2019
    * @author       Garry
    * @package      update.php
    * @copyright    Copyright (C) 2019 joom-shopping.com. All rights reserved.
    * @license      GNU/GPL
    */
    defined('_JEXEC') or die;

    abstract class AddonSearchPlusPlusProto {

        protected function typify($var) {
            if (is_string($var)) {
                $var = trim($var);
                return is_numeric($var) ? (1 * $var) : $var;
            }
            if (is_array($var)) {
                $arr = [];
                foreach ($var as $key => $val) {
                    $arr[$this->typify($key)] = $this->typify($val);
                }
                return $arr;
            }
            return $var;
        }

        protected function setProp($prop, $val) {
            if (!property_exists($this, $prop)) {
                return false;
            }
            $val  = $this->typify($val);
            $type = gettype($this->$prop);
            if ($type != 'NULL' && !settype($val, $type)) {
                return false;
            }
            $this->$prop = $val;
            return true;
        }

        protected function setProps($props) {
            $res = [];
            foreach ($props as $prop => $val) {
                $res[] = $this->setProp($prop, $val);
            }
            return !in_array(false, $res);
        }

        protected function dropProps() {
            $res = [];
            foreach (get_object_vars($this) as $prop => $val) {
                $res[] = $this->setProp($prop, null);
            }
            return !in_array(false, $res);
        }

    }
