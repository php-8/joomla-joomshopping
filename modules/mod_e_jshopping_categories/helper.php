<?php
$menu_view = $params->get('view');
if ($menu_view == accordion) {
class jShopCategoriesHelperIce{
	public static function getCategories($order = 'id', $ordering = 'asc', $publish = 0, $params) {
		$db = JFactory::getDBO();
        $lang = JSFactory::getLang(); 
        $add_where = ($publish)?(" category_publish = '1' "):("");        
        if ($order=="id") $orderby = "category_id";
        if ($order=="name") $orderby = "`".$lang->get('name')."`";
        if ($order=="ordering") $orderby = "ordering";
        if (!$orderby) $orderby = "ordering";
        
        $query = "SELECT `".$lang->get('name')."` as name,`".$lang->get('description')."` as description,`".$lang->get('short_description')."` as short_description, category_id, category_publish, ordering, category_image,category_parent_id FROM `#__jshopping_categories`
                   WHERE " . $add_where . "
                   ORDER BY ".$orderby." ".$ordering;
        $db->setQuery($query);
        $categories = $db->loadObjectList();
        $imageWidth = $params->get('image_width', 20);
		$imageHeight = $params->get('image_height', 20); 

		$isThumb = true;
        foreach ($categories as $key => $value){
            $categories[$key]->category_link = SEFLink('index.php?option=com_jshopping&controller=category&task=view&category_id='.$categories[$key]->category_id, 1);
			if( $categories[$key]->category_image &&  $image= self::renderThumb($categories[$key]->category_image, $imageWidth, $imageHeight, $categories[$key]->name, true) ){			
				$categories[$key]->category_image = $image;
			}
        }
		if( empty($categories) ) return '';
		$children = array();
		if ( $categories )
		{
			foreach ( $categories as $v )
			{				
				$pt 	= $v->category_parent_id;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		return $children;
    }
	
	public static function getHtml($order = 'id', $ordering = 'asc', $publish = 0, $params){
		$children = self::getCategories($order, $ordering, $publish, $params);
		$html = '';
		self::getHtmlCate($children,0,$html, 0 ,$params);
		$html = "<ul class='menu'>".$html."</ul>";
		return $html;
	}
	static $_listcates = array();

	public static function getListCates( ){
		static $_listcates;
		if(empty( $_listcates )){
			$lang = JSFactory::getLang();
			$category_id = JRequest::getCmd('category_id', 0);
			if(!empty($category_id)){
				$db = JFactory::getDBO();
				$tmp[ $category_id ] = $category_id;
				/*Select children category ids*/
				$query = "SELECT `".$lang->get('name')."` as name, category_id, category_parent_id, category_publish FROM `#__jshopping_categories`
								where category_publish = '1' ORDER BY category_parent_id, ordering";
				$db->setQuery($query);
				$all_cats = $db->loadObjectList();
				$tmp2 = array();
				if(count($all_cats)) {
					foreach ($all_cats as $key => $value) {
						$tmp2[ $value->category_id ] = $value->category_parent_id;
						if(!empty( $value->category_id ) && in_array($value->category_id, $tmp)){
							$tmp[ $value->category_parent_id ] = $value->category_parent_id;
							foreach($tmp2 as $key=>$val){
								if( !empty($key)  && !empty($val) && in_array($key, $tmp)){
									$tmp[ $val ] = $val;
								}
							}
						}
					}
				}
				$_listcates = $tmp;
				return $_listcates;
			}
		}
		else{
			return $_listcates;
		}
	}
	public static function getHtmlCate($children, $id = 0 , & $str, $leve = 0 , $params){
		$show_image = $params->get('show_image', 0); 
		$showcounter = $params->get('showcounter', 0); 
		$cates = self::getListCates();
		if(empty($cates)){
			$cates = array();
		}
		$leve ++;
		if(!empty($children[$id])){
			foreach($children[$id] as $item){
				$class = "";
				if(in_array($item->category_id, $cates)){
					$class = " current";
				}
				
		
				if(!empty($children[$item->category_id])){$str .= "<li  class='parent item".$leve.$class."'>"; 
				
								$parent = $params->get('parent');
				if ($parent == 1) {
					$parent_images = "<span class='parent_image'>&nbsp;</span>";
					}
					else {
					$parent_images = "";
					}

				if(in_array($item->category_id, $cates)){
				$str .= "<a onClick='return toggleShow(\"#slave".$item->category_id."\" ,this)' href='#' class='open' style='cursor:pointer'>".($show_image ? $item->category_image : "").$item->name.($showcounter ? " <span class=\"counter\">(".self::getTotalItem($children,$item->category_id).") </span>" : "").$parent_images;} else {$str .= "<a onClick='return toggleShow(\"#slave".$item->category_id."\" ,this)' href='#' class='closed' style='cursor:pointer'>".($show_image ? $item->category_image : "").$item->name.($showcounter ? " <span class=\"counter\">(".self::getTotalItem($children,$item->category_id).") </span>" : "").$parent_images;}
				if(!empty($children[$item->category_id])){
					$str .= "<i></i></a>";
					if(in_array($item->category_id, $cates)){
					$str .= "<ul  id='slave".$item->category_id."' " ."style='display: block'>";
				} else {$str .= "<ul  id='slave".$item->category_id."' " ."style='display: none'>";}
					self::getHtmlCate($children, $item->category_id ,$str ,$leve, $params);
					$str .= "</ul>";
				}else{
					$str .= "</a>";
				}} else {
					$str .= "<li  class='item".$leve.$class."'>";
				$str .= "<a href='".$item->category_link."' >".($show_image ? $item->category_image : "").$item->name.($showcounter ? " <span class=\"counter\">(".self::getTotalItem($children,$item->category_id).") </span>" : "");
				if(!empty($children[$item->category_id])){
					$str .= "<i></i></a>";
					$str .= "<ul  id='slave".$item->category_id."' " ."style='display: none'>";
					self::getHtmlCate($children, $item->category_id ,$str ,$leve, $params);
					$str .= "</ul>";
				}else{
					$str .= "</a>";
				}
				$str .="</li>";
			}}
		}
		return $str;
	}
	/*
	* get Total item in  category
	* return integer
	*/
	public static function getTotalItem($children, $category_id){
		$arrCate = array();
		$arrCate = self::getAllSubcates($category_id);
		if(empty($arrCate)){
			return 0;
		}
		if(count($arrCate) == 1){
			$where = " WHERE pc.category_id = ".$arrCate[0]. " ";
		}else{
			$strCate = implode(',',$arrCate);
			$where = " WHERE pc.category_id IN (".$strCate.") ";
		}
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(DISTINCT pc.product_id) AS total FROM `#__jshopping_products_to_categories` pc ".$where;
		$db->setQuery($query);
        $total = $db->loadObject();
		return $total->total;
	}
	/*
	* get all subcategories
	* return array
	*/
	public static function getAllSubcates($category_id){
		$lang = JSFactory::getLang(); 
		$db = JFactory::getDBO();
		$tmp[] = $category_id;
		$query = "SELECT `".$lang->get('name')."` as name, category_id, category_parent_id, category_publish FROM `#__jshopping_categories`
		where category_publish = '1' ORDER BY category_parent_id, ordering";
		$db->setQuery($query);
		$all_cats = $db->loadObjectList();

		if(count($all_cats)) {
			foreach ($all_cats as $key => $value) {
				if(!empty( $value->category_parent_id ) && in_array($value->category_parent_id, $tmp)){
					$tmp[] = $value->category_id;
				}
			}
		}
		return $tmp;
	}
	/**
     *  check the folder is existed, if not make a directory and set permission is 755
     *
     * @param array $path
     * @access public,
     * @return boolean.
     */
       public static function renderThumb( $path, $width = 100, $height = 100, $title = '', $isThumb = true ){
		$jshopConfig = JSFactory::getConfig();
      if( !preg_match("/.jpg|.png|.gif/",strtolower($path)) ) return '&nbsp;';
      if( $isThumb ){
		
        $path = str_replace( JURI::base(), '', $path );
        $imagSource = $jshopConfig->image_category_path.DS. str_replace( '/', DS,  $path );
		
        if( file_exists($imagSource)  ) {
			
          $path =  $width."x".$height.'/'.$path;
          $thumbPath = JPATH_SITE.DS.'images'.DS.'mod_e_jshopping_categories'.DS. str_replace( '/', DS,  $path );
          if( !file_exists($thumbPath) ) {
            $thumb = PhpThumbFactory::create( $imagSource  );  
            if( !self::makeDir( $path ) ) {
                return '';
            }   
            $thumb->adaptiveResize( $width, $height);
            
            $thumb->save( $thumbPath  ); 
          }
          $path = JURI::base().'images/mod_e_jshopping_categories/'.$path;
        } 
      }
      return '<img class="ecatimage" src="'.$path.'" title="'.$title.'" alt="'.$title.'" width="'.$width.'px" height="'.$height. 'px">';
    }
   
	/**
     *  check the folder is existed, if not make a directory and set permission is 755
     *
     * @param array $path
     * @access public,
     * @return boolean.
     */
    function makeDir( $path ){
      $folders = explode ( '/',  ( $path ) );
      $tmppath =  JPATH_SITE.DS.'images'.DS.'mod_e_jshopping_categories'.DS;
      if( !file_exists($tmppath) ) {
        JFolder::create( $tmppath, 0755 );
      }; 
      for( $i = 0; $i < count ( $folders ) - 1; $i ++) {
        if (! file_exists ( $tmppath . $folders [$i] ) && ! JFolder::create( $tmppath . $folders [$i], 0755) ) {
          return false;
        } 
        $tmppath = $tmppath . $folders [$i] . DS;
      }   
      return true;
    }
}
} else 
{class jShopCategoriesHelperIce{
	
	public static function getCategories($order = 'id', $ordering = 'asc', $publish = 0, $params) {
		$db = JFactory::getDBO();
        $lang = JSFactory::getLang(); 
        $add_where = ($publish)?(" category_publish = '1' "):("");        
        if ($order=="id") $orderby = "category_id";
        if ($order=="name") $orderby = "`".$lang->get('name')."`";
        if ($order=="ordering") $orderby = "ordering";
        if (!$orderby) $orderby = "ordering";
        
        $query = "SELECT `".$lang->get('name')."` as name,`".$lang->get('description')."` as description,`".$lang->get('short_description')."` as short_description, category_id, category_publish, ordering, category_image,category_parent_id FROM `#__jshopping_categories`
                   WHERE " . $add_where . "
                   ORDER BY ".$orderby." ".$ordering;
        $db->setQuery($query);
        $categories = $db->loadObjectList();
        $imageWidth = $params->get('image_width', 20);
		$imageHeight = $params->get('image_height', 20); 

		$isThumb = true;
        foreach ($categories as $key => $value){
            $categories[$key]->category_link = SEFLink('index.php?option=com_jshopping&controller=category&task=view&category_id='.$categories[$key]->category_id, 1);
			if( $categories[$key]->category_image &&  $image= self::renderThumb($categories[$key]->category_image, $imageWidth, $imageHeight, $categories[$key]->name, true) ){
				$categories[$key]->category_image = $image;
			}
        }
		if( empty($categories) ) return '';
		$children = array();
		if ( $categories )
		{
			foreach ( $categories as $v )
			{				
				$pt 	= $v->category_parent_id;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		return $children;
    }
	
	public static function getHtml($order = 'id', $ordering = 'asc', $publish = 0, $params){
		$children = self::getCategories($order, $ordering, $publish, $params);
		$html = '';
		self::getHtmlCate($children,0,$html, 0 ,$params);
		$html = "<ul class='lofmenu'>".$html."</ul>";
		return $html;
	}
	static $_listcates = array();

	public static function getListCates( ){
		static $_listcates;
		if(empty( $_listcates )){
			$lang = JSFactory::getLang();
			$category_id = JRequest::getCmd('category_id', 0);
			if(!empty($category_id)){
				$db = JFactory::getDBO();
				$tmp[ $category_id ] = $category_id;
				/*Select children category ids*/
				$query = "SELECT `".$lang->get('name')."` as name, category_id, category_parent_id, category_publish FROM `#__jshopping_categories`
								where category_publish = '1' ORDER BY category_parent_id, ordering";
				$db->setQuery($query);
				$all_cats = $db->loadObjectList();
				$tmp2 = array();
				if(count($all_cats)) {
					foreach ($all_cats as $key => $value) {
						$tmp2[ $value->category_id ] = $value->category_parent_id;
						if(!empty( $value->category_id ) && in_array($value->category_id, $tmp)){
							$tmp[ $value->category_parent_id ] = $value->category_parent_id;
							foreach($tmp2 as $key=>$val){
								if( !empty($key)  && !empty($val) && in_array($key, $tmp)){
									$tmp[ $val ] = $val;
								}
							}
						}
					}
				}
				$_listcates = $tmp;
				return $_listcates;
			}
		}
		else{
			return $_listcates;
		}
	}
	public static function getHtmlCate($children, $id = 0 , & $str, $leve = 0 , $params){
		$show_image = $params->get('show_image', 0); 
		$showcounter = $params->get('showcounter', 0); 
		$cates = self::getListCates();
		if(empty($cates)){
			$cates = array();
		}
		$leve ++;
		if(!empty($children[$id])){
			foreach($children[$id] as $item){
				$class = "";
				if(in_array($item->category_id, $cates)){
					$class = " ice-current ";
				}
				$str .= "<li class='lofitem".$leve.$class."'>";
				$str .= "<a href='".$item->category_link."' >".($show_image ? $item->category_image : "")."<span>".$item->name.($showcounter ? " <span class=\"counter\">(".self::getTotalItem($children,$item->category_id).") </span>" : "")."</span>";
				if(!empty($children[$item->category_id])){
					$str .= "<i></i></a>";
					$str .= "<ul>";
					self::getHtmlCate($children, $item->category_id ,$str ,$leve, $params);
					$str .= "</ul>";
				}else{
					$str .= "</a>";
				}
				$str .="</li>";
			}
		}
		return $str;
	}
	/*
	* get Total item in  category
	* return integer
	*/
	public static function getTotalItem($children, $category_id){
		$arrCate = array();
		$arrCate = self::getAllSubcates($category_id);
		if(empty($arrCate)){
			return 0;
		}
		if(count($arrCate) == 1){
			$where = " WHERE pc.category_id = ".$arrCate[0]. " ";
		}else{
			$strCate = implode(',',$arrCate);
			$where = " WHERE pc.category_id IN (".$strCate.") ";
		}
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(DISTINCT pc.product_id) AS total FROM `#__jshopping_products_to_categories` pc ".$where;
		$db->setQuery($query);
        $total = $db->loadObject();
		return $total->total;
	}
	/*
	* get all subcategories
	* return array
	*/
	public static function getAllSubcates($category_id){
		$lang = JSFactory::getLang(); 
		$db = JFactory::getDBO();
		$tmp[] = $category_id;
		$query = "SELECT `".$lang->get('name')."` as name, category_id, category_parent_id, category_publish FROM `#__jshopping_categories`
		where category_publish = '1' ORDER BY category_parent_id, ordering";
		$db->setQuery($query);
		$all_cats = $db->loadObjectList();

		if(count($all_cats)) {
			foreach ($all_cats as $key => $value) {
				if(!empty( $value->category_parent_id ) && in_array($value->category_parent_id, $tmp)){
					$tmp[] = $value->category_id;
				}
			}
		}
		return $tmp;
	}
	/**
     *  check the folder is existed, if not make a directory and set permission is 755
     *
     * @param array $path
     * @access public,
     * @return boolean.
     */
     public static function renderThumb( $path, $width = 100, $height = 100, $title = '', $isThumb = true ){
		$jshopConfig = JSFactory::getConfig();
      if( !preg_match("/.jpg|.png|.gif/",strtolower($path)) ) return '&nbsp;';
      if( $isThumb ){
		
        $path = str_replace( JURI::base(), '', $path );
        $imagSource = $jshopConfig->image_category_path.DS. str_replace( '/', DS,  $path );
		
        if( file_exists($imagSource)  ) {
			
          $path =  $width."x".$height.'/'.$path;
          $thumbPath = JPATH_SITE.DS.'images'.DS.'mod_e_jshopping_categories'.DS. str_replace( '/', DS,  $path );
          if( !file_exists($thumbPath) ) {
            $thumb = PhpThumbFactory::create( $imagSource  );  
            if( !self::makeDir( $path ) ) {
                return '';
            }   
            $thumb->adaptiveResize( $width, $height);
            
            $thumb->save( $thumbPath  ); 
          }
          $path = JURI::base().'images/mod_e_jshopping_categories/'.$path;
        } 
      }
      return '<img class="ecatimage" src="'.$path.'" title="'.$title.'" alt="'.$title.'" width="'.$width.'px" height="'.$height. 'px">';
    }
	/**
     *  check the folder is existed, if not make a directory and set permission is 755
     *
     * @param array $path
     * @access public,
     * @return boolean.
     */
    function makeDir( $path ){
      $folders = explode ( '/',  ( $path ) );
      $tmppath =  JPATH_SITE.DS.'images'.DS.'mod_e_jshopping_categories'.DS;
      if( !file_exists($tmppath) ) {
        JFolder::create( $tmppath, 0755 );
      }; 
      for( $i = 0; $i < count ( $folders ) - 1; $i ++) {
        if (! file_exists ( $tmppath . $folders [$i] ) && ! JFolder::create( $tmppath . $folders [$i], 0755) ) {
          return false;
        } 
        $tmppath = $tmppath . $folders [$i] . DS;
      }   
      return true;
    }
}}
?>