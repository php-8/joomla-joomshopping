<?php

class InformAvailabilityHelper{

    const AADDON = 'inform_availability';

    private static $_addonParams;

    public static function langs(){
		return getAllLanguages();
	}

	public static function htmlLangFlag($lang){
		$lang = is_object($lang) ? $lang->lang : $lang;
		$title = is_object($lang) ? $lang->name.' ('.$lang->language.')' : $lang;
		$alt = '*'.$lang.'*';
		return '<img title="'.$title.'" alt="'.$alt.'" src="/media/mod_languages/images/'.$lang.'.gif">';
	}

    public static function getSubject($lang, $data){
        $text = self::AP('subject_'.$lang);
        foreach($data as $k=>$v){
            $text = str_replace('{'.$k.'}', $v, $text);
        }
        return $text;
    }

    public static function getBody($lang, $data){
        $text = self::AP('text_'.$lang);
        foreach($data as $k=>$v){
            $text = str_replace('{'.$k.'}', $v, $text);
        }
        return $text;
    }

    public static function mailParams(){
		return array(
			'product' => 'product',
			'url' => 'url',
			'email' => 'email',
			'name' => 'name'
		);
	}

	public static function mailParamsKeysWraped(){
		return array_keys(self::arrayKeyWrap(self::mailParams()));
	}
    
	public static function mailParamsImplode(){
		return implode(', ', self::mailParamsKeysWraped());
	}

    public static function arrayKeyWrap($array,$left='{', $right='}'){
		foreach($array as $k=>$v){
			unset($array[$k]);
			$array[$left.$k.$right] = $v;
		}
		return $array;
	}

    static function AP($name=''){
        if (!isset(self::$_addonParams)) {
            $addon = JTable::getInstance('addon', 'jshop');
            $addon->loadAlias(self::AADDON);
            self::$_addonParams = $addon->getParams();
        }
        if ($name !== ''){
            if (array_key_exists($name,self::$_addonParams))
                return self::$_addonParams[$name];
            return false;
        }
        return self::$_addonParams;
	}

}