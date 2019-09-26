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
 * @version $Id: readlesstext.php 270 2014-11-24 21:43:01Z parvus $
 */

defined( '_JEXEC' ) or die;
require_once 'readlesstextmain.php';

class plgContentReadLessText extends JPlugin
{
  /**
   * Constructor
   * @param object &$subject The object to observe
   * @param array $config An optional associative array of configuration settings.
   */
  public function __construct( &$subject, $config = array() )
  {
    parent::__construct( $subject, $config );
    $this->loadLanguage();
    $this->_main = new ReadLessTextMain( $subject, $config );
  }

//  This function can not be used reliably.
//  It can be that another plugin altered the text as stored in the database before this plugin get execution time.
//  read less must then work on the altered text, not on the text as stored in the database.
//   /**
//    * Entry function. Will be called each time some article text has been
//    * saved. The article's lengths will be calculated and stored or updated
//    * in the database.
//    * @param string $context ignored
//    * @param JTableContent $article The item/article being prepared for display.
//    * @param bool $isNew If the content has just been created
//    * @note Article is passed by reference, but after the save, so no changes
//    *   will be saved.
//    * @return true
//    */
//   public function onContentAfterSave( $context, &$article, $isNew )
//   {
//     return true;
//   }

  /**
   * Entry function. Will be called each time some article text is to be
   * prepared for display.
   * @param string $context ignored
   * @param JTableContent $article The item/article being prepared for display.
   * @param $params ignored
   * @param integer $limitstart ignored
   * @return void
   */
  function onContentBeforeDisplay( $context, &$article, &$params, $limitstart = 0 )
  {
    $app = JFactory::getApplication();
    $scope = $app->scope;
    if ( !/*NOT*/key_exists( $scope, self::$_callCount ) )
    {
      self::$_callCount[ $scope ] = 0;
    }

    $this->_main->ReadLessText( $article, self::$_callCount[ $scope ], 'plg_content_readlesstext' );
  }
//   onContentPrepare is not used: the call is too limited.
//   $article only contains a text field, not the id and other needed fields.
//   function onContentPrepare( $context, &$article, &$params, $limitstart = 0 )
//   {
//     $app = JFactory::getApplication();
//     $scope = $app->scope;
//     if ( !/*NOT*/in_array( $scope, self::$_callCount ) )
//     {
//       self::$_callCount[ $scope ] = 0;
//     }
//     $this->ReadLessText( $article, self::$_callCount );
//   }

    private $_main = null;
    private static $_callCount = array();
}

?>
