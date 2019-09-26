<?php

defined('_JEXEC') or die('Restricted access');
class plgJshoppingAdminWatermark extends JPlugin {
    
    function plgJshoppingAdminWatermark(&$subject, $config) {
        parent::__construct($subject, $config);
    }
    
    function onAfterSaveProductImage($product_id, $name_image) {
        $image1 = $this->params->get('image1','');
        if ($image1=="-1") $image1 = "";
        $image2 = $this->params->get('image2','');
        if ($image2=="-1") $image2 = "";
        $image3 = $this->params->get('image3','');
        if ($image3=="-1") $image3 = "";		

        $position_x = intval($this->params->get('position_x', '2'));
        $indent_x = intval($this->params->get('indent_x', '5'));
        $position_y = intval($this->params->get('position_y', '2'));
        $indent_y = intval($this->params->get('indent_y', '5'));
        $quality = intval($this->params->get('quality', '85'));
        
        if ($image1) $this->_add_watermark('thumb_'.$name_image, $image1, $position_x, $indent_x, $position_y, $indent_y, $quality);
        if ($image2) $this->_add_watermark($name_image, $image2, $position_x, $indent_x, $position_y, $indent_y, $quality);
        if ($image3) $this->_add_watermark('full_'.$name_image, $image3, $position_x, $indent_x, $position_y, $indent_y, $quality);
    }
    
    private function _add_watermark($imagename, $namewatermark, $position_x, $indent_x, $position_y, $indent_y, $quality) {
        $jshopConfig = JSFactory::getConfig();
        
        $img_path = $jshopConfig->image_product_path."/".$imagename;
        $wmark_path = JPATH_ROOT."/images/".$namewatermark;
        
		$img_ext = strtolower(pathinfo($img_path, PATHINFO_EXTENSION));
        $wmark_ext = strtolower(pathinfo($wmark_path, PATHINFO_EXTENSION));
		
        $watermark = $this->_imagecreate($wmark_ext, $wmark_path);
        if (!$watermark) return false;
        $wmark_width = imagesx($watermark);
        $wmark_height = imagesy($watermark);        
        
        $image = $this->_imagecreate($img_ext, $img_path);
        if(!$image) return false;
        $img_width = imagesx($image);
        $img_height = imagesy($image);
        
        switch($position_x) {
            case '1': $dest_x = $indent_x; break;
            case '0': $dest_x = ($img_width - $wmark_width) / 2; break;
            case '2': $dest_x = $img_width - $wmark_width - $indent_x; break;
        }
        switch($position_y) {
            case '1': $dest_y = $indent_y; break;
            case '0': $dest_y = ($img_height- $wmark_height) / 2; break;
            case '2': $dest_y = $img_height - $wmark_height - $indent_y; break;
        }
        imagecopyresampled($image, $watermark, $dest_x, $dest_y, 0, 0, $wmark_width, $wmark_height, $wmark_width, $wmark_height);

        switch($img_ext) {
            case 'jpg':
            case 'jpeg': imagejpeg($image, $img_path, $quality); break;
            case 'gif': imagegif($image, $img_path); break;
            case 'png':
                imagesavealpha($image, true);
                (phpversion() >= '5.1.2') ? imagepng($image, $img_path, 10-max(intval($quality/10),1)) : imagepng($image, $img_path);
            break;
        }
        
        imagedestroy($watermark);
        imagedestroy($image);
    }
    
    private function _imagecreate($ext, $img_source) {
        switch($ext) {
            case 'jpg':
            case 'jpeg': $img = imagecreatefromjpeg($img_source); break;
            case 'gif': $img = imagecreatefromgif($img_source); break;
            case 'png': $img = imagecreatefrompng($img_source); break;
            default: $img = NULL;
        }		
        return $img;
    }
}