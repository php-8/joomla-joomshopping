<?php
/**
 * @package readlesstext
 * @copyright 2008-2014 Parvus
 * @license http://www.gnu.org/licenses/gpl-3.0.html
 * @link http://joomlacode.org/gf/project/cutoff/
 * @author Parvus
 *
 * readless is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License, or (at your option)
 * any later version.
 *
 * readless is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with readless. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @version $Id$
 */

defined( '_JEXEC' ) or die;
jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.utilities.date' );

class ReadLessTextThumb
{
  /**
   * Checks if the given url is still a valid thumbnail url.
   * Determines the correct url to the corresponding thumbnail.
   * If the thumbnail does not exist, it is created
   * @param string $url The url to the thumbnail image
   * @param dict $minimum Associative array as given to @c GetThumbnail().
   *   used to recreate the expected thumbnail url.
   * @param dict $crop Associative array as given to @c GetThumbnail().
   *   used to recreate the expected thumbnail url.
   * @param int $thumbWidth A number. May be zero or negative. When positive, it
   *   indicates the maximum width of the resized thumbnail. Else, there is no
   *   restriction on image width, and will be chosen in function of the
   *   height.
   *   OUT: If @c true is returned, this variable will contain the extact thumbnail width, in pixels.
   * @param int $thumbHeight A number. May be zero or negative. When positive, it
   *   indicates the maximum height of the resized thumbnail. Else, there is
   *   no restriction on image height, and will be chosen in function of the
   *   width.
   *   OUT: If @c true is returned, this variable will contain the exact thumbnail height, in pixels.
   * @return @c true if the given thumbnail url is still correct. @c false
   *   when some checks failed or could not be performed.
   * @see GetThumbnail
   */
  public static function ValidateThumbnail( $imageUrl, $thumbnailUrl, $minimum, $crop, & $thumbWidth, & $thumbHeight )
  {
    $success = false;

    $ext = strrchr( $thumbnailUrl, '.'); /* e.g.: .png */
    $expectedThumbnailPath = self::_DetermineThumbnailPath( $imageUrl, $minimum, $crop, $thumbWidth, $thumbHeight, $ext );
    $expectedThumbnailUrl = self::_DetermineUrlFromPath( $expectedThumbnailPath );

    if ( $thumbnailUrl == $expectedThumbnailUrl )
    {
      if ( @file_exists( $expectedThumbnailPath ) )
      {
        /* The given thumbnail exists, is located in the cache, and
         * the minimum configuration settings haven't been changed since.
         * we can re-use it.
         */
        $sizeArray = @getimagesize( $expectedThumbnailPath );
        $thumbWidth = $sizeArray[ 0 ];
        $thumbHeight = $sizeArray[ 1 ];

        $success = true;
      }
    }

    return $success;
  }

  private static function _DetermineImageType( $url )
  {
    $type = false;
    if ( function_exists( 'exif_imagetype' ) )
    {
      $type = @exif_imagetype( $url );
    }
    else if ( function_exists( 'getimagesize' ) )
    {
      $list = @getimagesize( $url );
      if ( $list )
      {
        $type = $list[2];
      }
    }

    if ( !/*NOT*/$type )
    {
      /* Fallback: try to determine the correct image type by checking for an extension in the url. */
      $ext = '';
      $parts = explode( '.', $url );
      if ( count( $parts ) > 1 )
      {
        $parts = explode( '?', $parts[ count( $parts ) - 1 ], 2 );
        $ext = JString::strtolower( $parts[ 0 ] );
      }
      if ( array_key_exists( $ext, ReadLessTextThumb::$_extToType ) )
      {
        $type = ReadLessTextThumb::$_extToType[ $ext ];
      }
    }

    return $type;
  }

  private static function _DetermineThumbnailPath( $imageUrl, $minimum, $crop, $thumbWidth, $thumbHeight, $ext )
  {
    $path = JPATH_CACHE . '/plg_readlesstext/';
    if ( !/*NOT*/@file_exists( $path ) )
    {
      @mkdir( $path );
    }

    if ( !/*NOT*/@is_dir( $path ) or !/*NOT*/@is_writable( $path ) )
    {
      /* Insufficient write permissions. Use fall-back. */
      $thumbnailUrl = $imageUrl;
    }
    else
    {
      $string = $imageUrl . $minimum[ 'width' ] . $minimum[ 'height' ] . $minimum[ 'ratio' ]
        . $crop[ 'horizontal_position' ] . $crop[ 'vertical_position' ]
        . $thumbWidth . $thumbHeight . 'v5.2 (r274)';
      $thumbnailPath = $path . md5( $string ) . $ext;
    }
    return $thumbnailPath;
  }

  private static function _DetermineUrlFromPath( $path )
  {
    $host = parse_url( $path, PHP_URL_HOST );
    if ( $host )
    {
      /* $path is already a url. */
      $url = false;
    }
    else if ( JString::strpos( $path, JPATH_BASE . '/' ) === 0 )
    {
      $url = JString::str_ireplace( JPATH_BASE . '/', JURI::base(), $path );
    }
    else
    {
      $url = JURI::base() . $path;
    }
    return $url;
  }

  private static function _DeterminePathFromUrl( $url )
  {
    $host = parse_url( $url, PHP_URL_HOST );
    if ( $host )
    {
      if ( JString::strpos( $url, JURI::base() . '/' ) === 0 )
      {
        $url = JString::str_ireplace( JURI::base(), JPATH_BASE . '/', $path );
      }
      else
      {
        $path = false; /* Can not be converted to a local path. */
      }
    }
    else
    {
      $path = JPATH_BASE . '/' . $url;
      $path = JString::str_ireplace( '//', '/', $path );
    }
    return $path;
  }

  /**
   * Find the resized dimensions, keeping the proportions.
   * @param uint $width
   * @param uint $height
   * @param uint $thumbWidth
   * @param uint $thumbHeight
   * @param dict $crop Associative array, with keys 'horizontal_position',
   *   'vertical_position' and values 'left', 'right', 'center' or 'no'.
   */
  private static function _DetermineResizeFactor( $width, $height, $thumbWidth, $thumbHeight, $crop )
  {
    /* There are four different ways to resize:
     * A: resize full width to thumbnail width,
     *     resize height with same ratio,
     *      crop height to thumbnail height (top, bottom, evenly both)
     * B: resize full height to thumbnail height,
     *     resize width with same ratio,
     *      crop width to thumbnail width (left, right, evenly both)
     * C: resize full width to thumbnail width,
     *     resize height with same ratio,
     *      resized height <= thumbnail height
     * D: resize full height to thumbnail height,
     *     resize width with same ratio,
     *      resized width <= thumbnail width
     *
     *  Based on
     * - the actual image dimensions: ix, iy
     * - the desired thumbnail dimensions: tx, ty
     *  - the crop options:
     *     crop horizontal: yes (left/right/evenly) or no (do not crop horizontally)
     *      crop vertical: yes (left/right/evenly) or no (do not crop vertically)
     * we need to determine the resize factor.
     *
     * Determine the horizontal and vertical ratio's: rx, ry
     * - If rx < ry: If resized using ry, the horizontal width will be
     *     greater than the thumbnail width. So either the width must
     *     be cropped (if allowed), either the image must be resized
     *     using rx (and thus the resized height will be less than the
     *     desired thumbnail height ty).
     * - If rx == ry: highly unlikely. Crop options are not needed here.
     *     Just resize using the single resize factor that was calculated.
     * - If rx > ry: Similar to the first case.
     *     Replace width <> height, rx <> ry and ty <> tx
     * Thus:
     * rx, ry = tx/ix, ty/iy
     * rx < ry
     *   ? crop horizontal ? r = ry : r = rx
     *   : crop vertical ? r = rx : r = ry
     */

    if ( $thumbWidth > 0 )
    {
      $resizeFactorWidth = min( 1, $thumbWidth / $width );
    }
    else
    {
      $thumbWidth = $width;
      $resizeFactorWidth = 1;
    }
    if ( $thumbHeight > 0 )
    {
      $resizeFactorHeight = min( 1, $thumbHeight / $height );
    }
    else
    {
      $thumbHeight = $height;
      $resizeFactorHeight = 1;
    }
    $resizeFactor = 1; /* Default value */
    if ( $resizeFactorWidth < $resizeFactorHeight )
    {
      /* Width is (relatively) greater than the height. */
      if ( in_array( $crop[ 'horizontal_position' ], array( 'left', 'right', 'center' ) ) )
      {
        /* Horizontal cropping is allowed. We may resize less,
         * and crop the extraneous part.
        */
        $resizeFactor = $resizeFactorHeight;
      }
      else
      {
        /* Horizontal cropping is not allowed. The full width must be
         * resized: the resized height will be less than the intended
        * thumbnail height.
        */
        $resizeFactor = $resizeFactorWidth;
      }
    }
    else
    {
      /* Vertical cropping is allowed. We may resize less,
       * and crop the extraneous part.
      */
      if ( in_array( $crop[ 'vertical_position' ], array( 'top', 'bottom', 'center' ) ) )
      {
        $resizeFactor = $resizeFactorWidth;
      }
      else
      {
        /* Vertical cropping is not allowed. The full height must be
         * resized: the resized width will be less than the intended
        * thumbnail width.
        */
        $resizeFactor = $resizeFactorHeight;
      }
    }
    return $resizeFactor;
  }

  /**
   * Determine the start positions sx, sy: everything lower and
   * everything higher than that plus the image width/height will be
   * thrown away (cropped).
   * @param uint $width
   * @param uint $height
   * @param uint $thumbWidth
   * @param uint $thumbHeight
   * @param dict $crop Associative array, with keys 'horizontal_position',
   *   'vertical_position' and values 'left', 'right', 'center' or 'no'.
   * @param unknown_type $resizeFactor
   */
  private static function _DetermineCroppedRectangle( $width, $height, & $thumbWidth, & $thumbHeight, $crop, $resizeFactor )
  {
    /*
     * Default value is 0, 0, to be used when cutting on the
     * right/bottom, or when cropping is disabled.
     *
     * If there is something to be thrown away, i.e.
     * if r * ix > tx
     *   cut left, retain right ? sx = ix - tx / r
     *   cut right, retain left ? sx = 0
     *   cut evenly ? sx = (ix - tx / r) / 2
     *
     * Likewise for sy
     */

    $horizontalStart = 0; /* Default value */
    $usedWidth = min( $width, $thumbWidth / $resizeFactor );
    $thumbWidth = intval( ( $usedWidth * $resizeFactor ) + 0.01 );
    if ( $usedWidth + 1 < $width )
    {
      switch ( $crop[ 'horizontal_position' ] )
      {
        case 'center':
          $horizontalStart = max( 0, ( $width - $usedWidth ) / 2 );
          break;

        case 'right':
          $horizontalStart = max( 0, $width - $usedWidth );
          break;

        case 'left':
          /* $horizontalStart remains 0 */
          break;

        default:
          /* Do not crop the width after all. We should never get here! */
          break;
      }
    }

    $verticalStart = 0; /* Default value */
    $usedHeight = min( $height, $thumbHeight / $resizeFactor );
    $thumbHeight = intval( ( $usedHeight * $resizeFactor ) + 0.01 );
    if ( $usedHeight + 1 < $height )
    {
      switch ( $crop[ 'vertical_position' ] )
      {
        case 'center':
          $verticalStart = max( 0, ( $height - $usedHeight ) / 2 );
          break;

        case 'bottom':
          $verticalStart = max( 0, $height - $usedHeight );
          break;

        case 'top':
          /* $verticalStart remains 0 */
          break;

        default:
          /* Do not crop the height after all. We should never get here! */
          break;
      }
    }

    return array( $horizontalStart, $verticalStart, $usedWidth, $usedHeight );
  }

  private static function _LoadImageUsingCurl( $url, $maxImageLoadTime )
  {
    $image = false;
    $curl = false;
    if ( function_exists( 'curl_init' ) )
    {
      $curl = curl_init();
    }
    if ( $curl )
    {
      curl_setopt( $curl, CURLOPT_URL, $url );
      curl_setopt( $curl, CURLOPT_HEADER, false );
      curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
      curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, $maxImageLoadTime );
      if ( ( ini_get( 'open_basedir' ) == '') and
          ( ( ini_get( 'safe_mode' ) == 'Off' ) or ( !/*NOT*/ini_get( 'safe_mode' ) ) ) )
      {
        /* The follow location option can not be activated when either dafe_mode or open_basedir
         * is set in php.ini - as a security measure. Trying to set it anyway results in a warning.
         * @todo If this is not set, redirection is not possible. A workaround for this is
         *   described here: http://stackoverflow.com/a/6918742/911550
         *   To implement?
         */
        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
      }
      curl_setopt( $curl, CURLOPT_MAXREDIRS, 11/*just a number that seems plenty enough*/ );
      curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER,  FALSE );
      $contents = curl_exec( $curl );
      curl_close( $curl );
      $image = @imagecreatefromstring( $contents );
    }
    return $image;
  }

  /**
   * Tries various ways to open an image and read its contents.
   * @param string $url References a local image with relative or full path, or a remote image.
   * @param function $loadFunction
   * @return resource image upon success, false on failure.
   */
  private static function _LoadImage( $url, $loadFunction, $maxImageLoadTime )
  {
    $originalDefaultSocketTimeoutValue = false;

    /* Always first try the url as given.
     * If that fails, try to convert the url to a local absolute path.
     * If that can not be done, or loading fails again, try to convert to a full url.
     * Try loading both using imagecreatefrom.+, and using curl combined with imagecreatefromstring.
     * If that all fails, give up.
     */

    $fullPath = self::_DeterminePathFromUrl( $url );
    $fullUrl = self::_DetermineUrlFromPath( $url );

    $tries = array();
    $tries[] = 'curl';
    $tries[] = 'normal';
    if ( $fullPath )
    {
      $tries[] = 'fullPath';
    }
    /* Always include at least one way to fetch an image based on the full url. I had a report from a user who
     * apparently couldn't load images referenced locally, but could load images referenced via a url (???).
     */
    if ( $fullUrl and ini_get( 'allow_url_fopen' ) )
    {
      $tries[] = 'fullUrl';
      if ( $maxImageLoadTime > 0 )
      {
        $originalDefaultSocketTimeoutValue = ini_set( 'default_socket_timeout', $maxImageLoadTime );
      }
    }
    $tries[] = 'curl';

    $image = false;
    foreach ( $tries as $try )
    {
      if ( !/*NOT*/$image )
      {
        switch ( $try )
        {
          case 'normal':
            $image = @call_user_func( $loadFunction, $url );
            break;

          case 'fullPath':
            $image = @call_user_func( $loadFunction, $fullPath );
            break;

          case 'fullUrl':
            $image = @call_user_func( $loadFunction, $fullUrl );
            break;

          case 'curl':
            if ( $fullUrl )
            {
              $image = self::_LoadImageUsingCurl( $fullUrl, $maxImageLoadTime );
            }
            else
            {
              $image = self::_LoadImageUsingCurl( $url, $maxImageLoadTime );
            }
            break;

          default:
            /* May never come here. */
            break;
        }
      }
    }

    if ( $originalDefaultSocketTimeoutValue /* is FALSE when ini_set failed or was not executed */ )
    {
      ini_set( 'default_socket_timeout', $originalDefaultSocketTimeoutValue );
    }

    return $image;
  }

  /**
   * Determines the correct url to the corresponding thumbnail.
   * If the thumbnail does not exist, it is created
   * @param string $url The path to the image
   * @param dict $minimum Associative array, with keys 'width', 'height', 'ratio',
   *   and values 0 or positive numbers, expressed in pixels.
   *   Looked at both to find a previously created thumbnail; and when the
   *   thumbnail does not exist yet and has to be created.
   * @param dict $crop Associative array, with keys 'horizontal_position',
   *   'vertical_position' and values 'left', 'right', 'center' or 'no'.
   * @param int $thumbWidth A number. May be zero or negative. When positive, it
   *   indicates the maximum width of the resized thumbnail. Else, there is no
   *   restriction on image width, and will be chosen in function of the
   *   height.
   *   OUT: If the path to the thumbnail is returned, this variable will contain
   *   the extact thumbnail width, in pixels.
   * @param int $thumbHeight A number. May be zero or negative. When positive, it
   *   indicates the maximum height of the resized thumbnail. Else, there is
   *   no restriction on image height, and will be chosen in function of the
   *   width.
   *   OUT: If the path to the thumbnail is returned, this variable will contain
   *   the extact thumbnail height, in pixels.
   * @param int $lifetime The lifetime of the thumbnail in seconds to set when it
   *   is created by calling this function. Not used to check if the existing
   *   thumbnail is still valid. Default: 4 weeks (2419200 seconds).
   * @return false if an error occurred or if the given $url is incorrect. The
   *   path to the thumbnail otherwise.
   */
  public static function GetThumbnail( $url, $minimum, $crop, & $thumbWidth, & $thumbHeight, $lifetime = 2419200, $maxImageLoadTime = 60 )
  {
    $type = self::_DetermineImageType( $url );
    if ( $type and array_key_exists( $type, ReadLessTextThumb::$_image ) )
    {
      $ext = ReadLessTextThumb::$_image[ $type ][ 'ext' ];
      $thumbnailPath = self::_DetermineThumbnailPath( $url, $minimum, $crop, $thumbWidth, $thumbHeight, $ext );
      $thumbnailUrl = self::_DetermineUrlFromPath( $thumbnailPath );

      if ( @file_exists( $thumbnailPath ) )
      {
        /* Thumbnail already exists.
         * The image resource $url has been examined during a previous execution;
         * and according to the settings, it is fit to serve as a thumbnail.
         */
        $sizeArray = @getimagesize( $thumbnailPath );
        $thumbWidth = $sizeArray[ 0 ];
        $thumbHeight = $sizeArray[ 1 ];
      }
      else
      {
        $image = self::_LoadImage( $url, ReadLessTextThumb::$_image[ $type ][ 'load' ], $maxImageLoadTime );
        $width = -1;
        $height = -1;
        if ( $image )
        {
          $width = max( 1, @imagesx( $image ) ); /* Ensure a division is possible. */
          $height = max( 1, @imagesy( $image ) ); /* Ensure a division is possible. */
        }
        $ratio = min( $width / $height, $height / $width );
        if ( !/*NOT*/$image
                or ( $width < $minimum[ 'width' ] )
                or ( $height < $minimum[ 'height' ] )
                or ( $ratio < $minimum[ 'ratio' ] ) )
        {
          /* Thumbnail may not be created.
           * According to the settings, it is not fit to serve as a thumbnail.
          */
          $thumbnailUrl = false;
        }
        else
        {
          $resizeFactor = self::_DetermineResizeFactor( $width, $height, $thumbWidth, $thumbHeight, $crop );
          /* $thumbWidth and $thumbHeight is now updated as well. */

          /* The image width to use is equal to r * tx
           * The image height to use is equal to r * ty
           */

          $start = self::_DetermineCroppedRectangle( $width, $height, $thumbWidth, $thumbHeight, $crop, $resizeFactor );
          $horizontalStart = $start[0];
          $verticalStart = $start[1];
          $usedWidth = $start[2];
          $usedHeight = $start[3];
          /* $thumbWidth and $thumbHeight is now updated as well. */

          /* Create thumbnail */
          $thumbnail = call_user_func( ReadLessTextThumb::$_image[ $type ][ 'create' ], $thumbWidth, $thumbHeight );
          if ( $type == 1 /* IMAGETYPE_GIF */ )
          {
            /* Make the thumbnail initially transparent if the original was transparent too.
             * Otherwise, fill it initially up with all white.
             */
            $transparentColorIdentifier = @imagecolortransparent( $image );
            if ( $transparentColorIdentifier >= 0 )
            {
              $colors = @imagecolorsforindex( $image, $transparentColorIdentifier );
              $transcolorindex = @imagecolorallocate( $thumbnail, $colors[ 'red' ], $colors[ 'green' ], $colors[ 'blue' ] );
              @imagefill( $thumbnail, 0, 0, $transcolorindex );
              @imagecolortransparent( $thumbnail, $transcolorindex ); /* Needed? */
            }
            else
            {
              $whiteColorIdentifier = @imagecolorallocate( $thumbnail, 255, 255, 255 );
              @imagefill( $thumbnail, 0, 0, $whitecolorindex);
            }
          }

          if ( ReadLessTextThumb::$_image[ $type ][ 'create_alpha' ] )
          {
            call_user_func( ReadLessTextThumb::$_image[ $type ][ 'create_alpha' ], $thumbnail, false );
          }
          call_user_func( ReadLessTextThumb::$_image[ $type ][ 'copy' ], $thumbnail, $image,
              0, 0, $horizontalStart, $verticalStart,
              $thumbWidth, $thumbHeight, $usedWidth, $usedHeight );
          if ( ReadLessTextThumb::$_image[ $type ][ 'save_alpha' ] )
          {
            call_user_func( ReadLessTextThumb::$_image[ $type ][ 'save_alpha' ], $thumbnail, true );
          }
          call_user_func( ReadLessTextThumb::$_image[ $type ][ 'save' ], $thumbnail, $thumbnailPath );

          /* The expiration information is not used directly, but it is still
           * added to allow Joomla's core garbage collection functionality to work.
           */
          $expirePath = $thumbnailPath . '_expire';
          @file_put_contents( $expirePath, ( time() + $lifetime) );
        }
      }
    }
    else
    {
      /* To me, the remaining image types are esoteric. Some of them I never
       * even heard of.
       * OR
       * Determining the image type failed.
       */
      $thumbnailUrl = false;
    }

    return $thumbnailUrl;
  }

  private static $_extToType = array(
      'gif' => 1 /* IMAGETYPE_GIF */,
      'jpg' => 2 /* IMAGETYPE_JPEG */,
      'jpeg' => 2 /* IMAGETYPE_JPEG */,
      'png' => 3 /* IMAGETYPE_PNG */,
      'bmp' => 6 /* IMAGETYPE_PNG */
  );

  private static $_image = array(
      1 /* IMAGETYPE_GIF */ => array(
          'ext' => '.gif',
          'load' => 'imagecreatefromgif',
          'create' => 'imagecreate',
          'create_alpha' => '',
          'copy' => 'imagecopyresampled',
          'save_alpha' => '',
          'save' => 'imagegif'
      ),
      2 /* IMAGETYPE_JPEG */ => array(
          'ext' => '.jpg',
          'load' => 'imagecreatefromjpeg',
          'create' => 'imagecreatetruecolor',
          'create_alpha' => '',
          'copy' => 'imagecopyresampled',
          'save_alpha' => '',
          'save' => 'imagejpeg'
      ),
      3 /* IMAGETYPE_PNG */ => array(
          'ext' => '.png',
          'load' => 'imagecreatefrompng',
          'create' => 'imagecreatetruecolor',
          'create_alpha' => 'imagealphablending',
          'copy' => 'imagecopyresampled',
          'save_alpha' => 'imagesavealpha',
          'save' => 'imagepng'
      ),
      6000 /* IMAGETYPE_BMP */ => array(
          'ext' => '.bmp',
          'load' => 'imagecreatefromwbmp',
          'create' => 'imagecreate',
          'create_alpha' => '',
          'copy' => 'imagecopyresampled',
          'save_alpha' => '',
          'save' => 'imagewbmp'
      ) );
}
?>
