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

class OfflajnUniversalImageTool
{
  public $res = null;
  public $source = null;
  public $contenttype = IMAGETYPE_PNG;

  /***
   * Constructor
   * $src_or_resource: src is the path to an image.  If it exists, the image will be automatically opened
   *    can also be an already created image resource
   */
  public function __construct($src_or_resource = null)
  {
    if (!is_null($src_or_resource)) {
      if (is_resource($src_or_resource)) {
        $this->resource($src_or_resource);
      } else {
        $this->open($src_or_resource);
      }
    }

  }

  public function createImage($w, $h)
  {
    $this->res = imagecreatetruecolor($w, $h);
    if ($this->res === false) {
      return false;
    }
  }

  public function convertToPng()
  {
    $this->contenttype = IMAGETYPE_PNG;
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

  /***
   * Open an image from a file on disk
   */
  public function open($src)
  {
    $this->source = $src;
    switch (($this->contenttype = exif_imagetype($src))) {
      case IMAGETYPE_PNG:
        $this->res = imagecreatefrompng($src);
        break;
      case IMAGETYPE_GIF:
        $this->res = imagecreatefromgif($src);
        break;
      case IMAGETYPE_JPEG:
        $this->res = imagecreatefromjpeg($src);
        break;
    }

    $this->prepare($this->res);
  }

  // takes an image resource and a content type and prepares the resource
  public function prepare($res, $contenttype = null)
  {
    if (is_null($contenttype)) {
      $contenttype = $this->contenttype;
    }

    if ($contenttype == IMAGETYPE_PNG) {
      imagealphablending($res, false);
      imagesavealpha($res, true);
    }
  }

  /***
   * Get/set image resource
   */
  public function resource($res = null)
  {
    if (is_null($res)) {
      return $this->res;
    } else {
      $this->res = $res;
    }

  }

  /***
   * Save the image back to the original file
   */
  public function save($out = null)
  {
    if (!is_null($this->source)) {
      $this->write($this->source, $out);
    }

  }

  /***
   * Save the image to a different location
   */
  public function write($dest, $out = null)
  {
    if (is_null($out)) {
      $out = $this->contenttype;
    }

    switch ($out) {
      case IMAGETYPE_PNG:
        imagepng($this->res, $dest);
        break;
      case IMAGETYPE_GIF:
        imagegif($this->res, $dest);
        break;
      case IMAGETYPE_JPEG:
        imagejpeg($this->res, $dest);
        break;
    }
  }

  /***
   * Output the image to the stream (browser)
   */
  public function output($out = null)
  {
    switch ($out) {
      default:
      case IMAGETYPE_PNG:
        $contenttype = 'png';
        break;
      case IMAGETYPE_GIF:
        $contenttype = 'gif';
        break;
      case IMAGETYPE_JPEG:
        $contenttype = 'jpeg';
        break;
    }

    header('Content-type: image/' . $contenttype);
    $this->write(null, $out);
  }

  /***
   * Kill self
   */
  public function destroy()
  {
    imagedestroy($this->res);
    $this->source = null;
    $this->contenttype = null;
  }
}

if (!function_exists('exif_imagetype')) {
  function exif_imagetype($f)
  {
    if (false !== (list(, , $type) = getimagesize($f))) {
      return $type;
    }

    return IMAGETYPE_PNG; // meh
  }
}
