<?php
/**
 * mod_vertical_menu - Vertical Menu
 *
 * @author    Balint Polgarfi
 * @copyright 2014-2019 Offlajn.com
 * @license   https://gnu.org/licenses/gpl-2.0.html
 * @link      https://offlajn.com
 */

defined('_JEXEC') or die('Restricted access');

if (!class_exists('OfflajnUniversalImageCaching')) {

  require_once dirname(__FILE__) . '/image.php';

  class OfflajnUniversalImageCaching
  {
    public $cacheDir;
    public $cacheUrl;

    public function __construct($folder = '')
    {
      if ($folder) {
        $folder .= '/';
      }

      $path = JPATH_SITE . '/images/' . $folder;
      if (!is_dir($path)) {mkdir($path);}
      $this->cacheDir = $path;
      $this->cacheUrl = JURI::root() . "images/" . $folder;
    }

    public function generateImage($path, $w, $h, $transparent = true)
    {
      $cacheName = $this->generateImageCacheName(array($path, $w, $h, $transparent));
      if (!$this->checkImageCache($cacheName)) {
        if (!$this->createImage($path, $this->cacheDir . $cacheName, $w, $h, $transparent)) {
          return '';
        }
      }
      return $this->cacheUrl . $cacheName;
    }

    public function createImage($in, $out, $w, $h, $transparent)
    {
      $img = null;
      $img = new OfflajnUniversalImageTool($in);
      if ($img->res === false) {
        return false;
      }
      $img->convertToPng();
      if ($transparent) {
        $img->resize($w, $h);
      } else {
        $img->resize2($w, $h);
      }

      $img->write($out);
      $img->destroy();
      return true;
    }

    public function convertToPng()
    {
      $this->contenttype = IMAGETYPE_PNG;
    }

    public function checkImageCache($cacheName)
    {
      return is_file($this->cacheDir . $cacheName);
    }

    public function generateImageCacheName($pieces)
    {
      return md5(implode('-', $pieces)) . '.png';
    }

    public function resize($newW, $newH)
    {
      if ($this->res === false) {
        return false;
      }
      $src_width = imagesx($this->res);
      $src_height = imagesy($this->res);
      $newX = 0;
      $newY = 0;
      $dst_w = 0;
      $dst_h = 0;
      $wRatio = $src_width / $newW;
      $hRatio = $src_height / $newH;
      if ($wRatio > $hRatio) {
        $dst_w = $newW;
        $dst_h = $src_height / $wRatio;
        $newY = ($newH - $dst_h) / 2;
      } else {
        $dst_h = $newH;
        $dst_w = $src_width / $hRatio;
        $newX = ($newW - $dst_w) / 2;
      }
      $dst_im = imagecreatetruecolor($newW, $newH);
      $this->prepare($dst_im);
      $transparent = imagecolorallocatealpha($dst_im, 255, 255, 255, 127);
      imagefilledrectangle($dst_im, 0, 0, $newW, $newH, $transparent);
      imagecopyresampled($dst_im, $this->res, $newX, $newY, 0, 0, $dst_w, $dst_h, $src_width, $src_height);
      imagedestroy($this->res);
      $this->res = $dst_im;
    }

    public function resize2($newW, $newH)
    {
      if ($this->res === false) {
        return false;
      }
      $src_width = imagesx($this->res);
      $src_height = imagesy($this->res);
      $newX = 0;
      $newY = 0;
      $dst_w = 0;
      $dst_h = 0;
      $wRatio = $src_width / $newW;
      $hRatio = $src_height / $newH;
      if ($wRatio > $hRatio) {
        $dst_w = round($newW * $hRatio);
        $dst_h = $src_height;
        $newX = ($src_width - $dst_w) / 2;
      } else {
        $dst_w = $src_width;
        $dst_h = round($newH * $wRatio);
        $newY = ($src_height - $dst_h) / 2;
      }
      $dst_im = imagecreatetruecolor($newW, $newH);
      $this->prepare($dst_im);
      imagecopyresampled($dst_im, $this->res, 0, 0, $newX, $newY, $newW, $newH, $dst_w, $dst_h);
      imagedestroy($this->res);
      $this->res = $dst_im;
    }

  }

}
