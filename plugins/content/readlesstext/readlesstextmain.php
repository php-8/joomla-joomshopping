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
require_once 'readlesstextcache.php';
require_once 'readlesstextexpand.php';
require_once 'readlesstexthelper.php';
require_once 'readlesstextthumb.php';

if ( !/*NOT*/ class_exists( 'ContentHelperRoute' ) )
{
  /* There have been some reports of people that somehow got errors by having
   * this class not being included by default (not only with this plugin; also
   * with other completely unrelated plugins.
   * Although this block should never be entered, it serves as a workaround
   * for those people having this issue.
   */
  require_once( JPATH_SITE . '/components/com_content/helpers/route.php' );
}

class ReadLessTextMain extends JPlugin
{
  /**
   * Constructor
   * @param object &$subject The object to observe
   * @param array $config An optional associative array of configuration settings.
   */
  public function __construct( &$subject, $config = array() )
  {
    parent::__construct( $subject, $config );
  }

  /**
   * Main entry function. The configuration parameters set in the constructor are used to fully exert read less text's
   * functionality.
   * @param JTableContent $article The item/article being prepared for display.
   * @param number $callCount The number of times this function has been
   *   called by the same plugin. This value is used to determine whether
   *   the given article may be shortened.
   *   Will have been incremented by one when this function returns.
   * @param string $pluginName
   */
  public function ReadLessText( &$article, &$callCount, $pluginName = 'plg_content_readlesstext', $cacheTable = '#__readlesstext' )
  {
//    jimport( 'joomla.error.profiler' );
//    $this->profiler = new JProfiler();
//    $this->profiles = array();
//    $this->profiles[] = $this->profiler->mark( ' IN ' . $article->id );

    JPlugin::loadLanguage( $pluginName );

    /* The two blocks below are a workaround for issues in the Joomla core.
     * - In a 'featured' view, the fulltext is not set or empty, even when it
     *   is present in the database
     * - In a single article view, the readmore property is not set.
     * Seen in 1.6.6, 1.7.2 & 2.5.4
     *
     * It also ensures that when this plugin is invoked on other components (e.g. com_media),
     * There are no php notices.
     */
    foreach ( array( 'fulltext', 'readmore' ) as $var )
    {
      if ( !/*NOT*/ isset( $article->$var ) )
      {
        $article->$var = '';
      }
    }
    if ( isset( $article->id ) and $article->readmore and ( !/*NOT*/ $article->fulltext ) )
    {
      $db = JFactory::getDBO();
      $query = "SELECT c.fulltext
          FROM #__content c
          WHERE c.id = " . $article->id;
      $db->setQuery( $query );
      $article->fulltext = $db->loadResult();
    }

    $this->_isShortened = false;
    $this->_prefix = '';
    $this->_suffix = '';

    $alwaysActiveForGuests = $this->params->get( 'alwaysActiveForGuests', '0' );
    if ( $alwaysActiveForGuests )
    {
      $extraDiscoverInfo = 'Guests can only see the shortened article, unless a context in the <em>Disallowed</em> field matches this item.';
      $activeByDefaultOnAllContentItems = ( JFactory::getUser()->guest == 1 );
    }
    else
    {
      $extraDiscoverInfo = '';
      $activeByDefaultOnAllContentItems = false;
    }

//    $this->profiles[] = $this->profiler->mark( ' before filter ' );
    $options = array();
    if ( ReadLessTextHelper::Filter( $callCount, $article, $this->params, $options,
        $activeByDefaultOnAllContentItems, $pluginName, $extraDiscoverInfo ) )
    {
      /* Entering this block means: read less text must be active. */
//      $this->profiles[] = $this->profiler->mark( ' after filter ' );

      if ( $options[ 'discover' ] )
      {
        /* Discover mode is active: the article's text has been replaced with
         * discover information and may not be altered.
         * No need to update the private variables - they will not be used.
         */
      }
      else
      {
//        $this->profiles[] = $this->profiler->mark( ' before prepare ' );
        $length = $this->_PrepareArticleText( $article, $this->params, $cacheTable );
//        $this->profiles[] = $this->profiler->mark( ' after prepare ' );

        $imageHtml = '';
        $prefix = '';
        $shortened = $article->text;
        $inlineSuffix = '';
        $closingTags = '';
        $suffix = '';

        $expand = new ReadLessTextExpand();
        $expand->SetExpandables( $article, $this->_plaintext, Null, Null );
        $this->_GetParams( $article, $length, $expand );

        if ( ( $this->_applyFormatting == 'when_active' )
            or ( ( $this->_applyFormatting == 'when_long_enough' ) and $length ) )
        {
//          $this->profiles[] = $this->profiler->mark( ' before strip ' );
          $article->text = $this->_StripTokens( $article->text );
//          $this->profiles[] = $this->profiler->mark( ' after strip ' );

//          $this->profiles[] = $this->profiler->mark( ' before cutoff ' );
           $a = $this->_CutOff( $article->text, $length );
           $shortened = $a[0];
           $closingTags = $a[1];
//          $this->profiles[] = $this->profiler->mark( ' after cutoff ' );
        }

        if ( ( $this->_createThumbnail == 'when_active' )
            or ( ( $this->_createThumbnail == 'when_shortened' ) and $this->_isShortened ) )
        {
//          $this->profiles[] = $this->profiler->mark( ' before thumbnail ' );
          $imageHtml = $this->_GetThumbnailHtml( $article );
//          $this->profiles[] = $this->profiler->mark( ' after thumbnail ' );
        }

        if ( ( $this->_addPrefix == 'when_active' )
            or ( ( $this->_addPrefix == 'when_shortened' ) and $this->_isShortened ) )
        {
          $prefix = $this->_prefix;
        }
        if ( ( $this->_addInlineSuffix == 'when_active' )
            or ( ( $this->_addInlineSuffix == 'when_shortened' ) and $this->_isShortened ) )
        {
          $inlineSuffix = $this->_inlineSuffix;
        }
        if ( ( $this->_addSuffix == 'when_active' )
            or ( ( $this->_addSuffix == 'when_shortened' ) and $this->_isShortened ) )
        {
          $suffix = $this->_suffix;
        }

        if ( $this->_cache )
        {
          $this->_cache->Store();
        }

        $article->text = $this->_wrapperTag[ 'open' ] . $imageHtml . $prefix
            . $shortened
            . $inlineSuffix . $closingTags . $suffix . $this->_wrapperTag[ 'close' ];
      }

      $article->introtext = $article->text;
      $article->fulltext = '';

//       $this->profiles[] = $this->profiler->mark( ' OUT ' . $article->id );
//       $article->text .= '<div>';
//       foreach ( $this->profiles as $profile )
//       {
//         $article->text .= "<br/>" . $profile;
//       }
//       $article->text .= '<br/></div>';
//       $article->introtext = $article->text;
//       $article->fulltext = '';
    }
  }

  /**
   * Determines the html version of the intro and the full field,
   * irrespective the component.
   * @param JTableContent $article The item/article being prepared for display.
   * @return An array, with the first element containing the intro text,
   *   if any, and the second element the remainder of the text.
   */
  private static function _DetermineArticleText( $article )
  {
    /* Be extra careful, so that other components do not give PHP notices
     * and warnings.
     * - Some components do not have the introtext and/or the fulltext field
     *   but only the text field,
     *   e.g. ??
     * - Others have none of the three, but use a special name (why o why),
     *   e.g. in com_eventlist:
     *     datdescription for an event.
     *     catdescription for a category.
     *     locdescription for a venue.
     *     description for a group.
     * - whereas others have all three of them, and full info is to be
     *   fetched from introtext and fulltext.
     */
    $introtext = '';
    $fulltext = '';
    if ( isset( $article->introtext ) )
    {
      $introtext = $article->introtext;
    }
    if ( isset( $article->fulltext ) )
    {
      $fulltext = $article->fulltext;
    }
    if ( !/*NOT*/ $introtext and !/*NOT*/ $fulltext)
    {
      if ( isset( $article->text ) and $article->text )
      {
        $fulltext = $article->text;
      }
    }
    if ( !/*NOT*/ $introtext and !/*NOT*/ $fulltext)
    {
      /* No text has been found yet. Check if it is stored in a variable named
       * xxxdescription, like eventlist does.
       */
      $values = array();
      foreach ( get_object_vars( $article ) as $key => $value )
      {
        if ( strstr( $key, 'description') !== false )
        {
          $values[] = $value;
        }
      }
      /* Only accept the text from a xxxdescription when it is the only one
       * found: e.g. eventlist uses one object to store the text of both
       * the event and the venue, and it is impossible to choose the correct
       * one.
       */
      if ( count( $values ) == 1 )
      {
        $fulltext = $values[0];
      }
    }

    $readmoreStartPos = JString::strpos( $introtext, '<p class="readmore">' );
    if ( $readmoreStartPos !== false )
    {
      /* Another plugin has already tampered with this article, and somehow a readmore token got included.
       * e.g. NoNumber's ArticleAnywhere can cause this.
       * Consider only the part before that token as the intro text.
       */
      $introtext = JString::substr_replace( $introtext, '', $readmoreStartPos );
    }

    return array( $introtext, $fulltext );
  }

  /**
   * Determines the length of @c $this->_htmltext in the given unit.
   * The length is fetched from the cache. If it is not available in the cache, it is calculated and then added to the
   * cache - but not yet committed in the cache, use @c Store() for that.
   * @param string $lengthUnit One of 'char', 'word', 'sentence', 'paragraph'
   * @return int the requested length, in number of @c lengthUnit's
   */
  private function _DetermineLength( $string, $lengthUnit )
  {
    $length = $this->_cache->Get( $lengthUnit );
    if ( !/*NOT*/ $length )
    {
      $end = true; /* Just to define it; it is used as output parameter in the next call, but ignored by us. */
      $length = ReadLessTextHelper::DetermineLength( $this->_htmltext, $lengthUnit, $end );
      if ( $length )
      {
        $this->_cache->Set( $lengthUnit, $length );
      }
    }
    return $length;
  }

  /**
   * Determines the correct text to operate on. Sets the private variables plaintext and htmltext.
   * @param JTableContent $article IN OUT. The item/article being prepared for display. The properties introtext, text,
   *   fulltext and readmore will have been set or adapted.
   * @param params $params the parameters to use
   * @return mixed boolean false to indicate article may not be shortened, or a number indicating the nr of units to
   *   retain. It is still possible this number is greater than the entire article text length.
   * @post @c $article->text contains the text to be shortened or to display.
   * @note Even when @c false is returned, @c $article->text may have been changed - a manually inserted read more
   *   request is then overridden.
   */
  private function _PrepareArticleText( &$article, $params, $cacheTable )
  {
    $a = self::_DetermineArticleText( $article );
    $introtext = $a[0];
    $fulltext = $a[1];
    $plainintrotext = strip_tags( $introtext );
    $plainfulltext = strip_tags( $fulltext );
    $this->_plaintext = $plainintrotext . ' ' . $plainfulltext;
    $this->_htmltext = $introtext . ' ' . $fulltext;

    /* It is tempting to execute the line below, but this yields wrong results.
     * There are Russian (e.g. Р 0xd0 0xa0) and Greek (e.g. Π 0xce 0xa0) multi-byte characters
     * that are stripped from the 0xa0 byte, giving a rubbish character.
     * $this->_htmltext will - after further adaptation - eventually be output,
     * so all characters must at all times remain intact.
     * - btw, at the very least, the u modifier should be used. See e.g. ReadLessTextHelper::Rtrim()
     */
    //$this->_htmltext = preg_replace( '/\s+/muis', ' ', $this->_htmltext )

    $this->_cache = new ReadLessTextCache( $cacheTable, JRequest::getWord( 'option' ), $article->id,
        'v5.2 (r274)' . md5( $this->_htmltext ) );

    $this->_wordCount = $this->_DetermineLength( $this->_htmltext, 'word' );
    $this->_lengthUnit = $params->get( 'lengthUnit', 'char' );

    /* True when a 'read more' link has been explicitly inserted, and fulltext is not empty.
     * - the full text is made by combining introtext, (closing the paragraph), a
     *   hr tag (id 'system-readmore') (opening the paragraph) and fulltext.
     */
    $readmoreMarkerPresent = ( $fulltext and isset( $article->readmore ) and $article->readmore );
    if ( $readmoreMarkerPresent and ( $this->_respectExistingReadmoreLink == 'respectShowIntro' ) )
    {
      /* Shorten the article's intro text. */
      $article->text = $introtext;
      $cutOffLength = $params->get( 'cutOffTextLength', 1 );
    }
    else if ( $readmoreMarkerPresent and $this->_respectExistingReadmoreLink ) /* 1 / true */
    {
      /* Use the article's full intro text; do not try to shorten it. This is
       * accomplished by requesting to cut off at a ridiculously high value.
       */
      $article->text = $introtext;
      $this->_isShortened = true;
      $cutOffLength = PHP_INT_MAX;
    }
    else /* No read more present, or 0 / false */
    {
      $minimumLength = $params->get( 'minimumTextLength', 1 );
      $cutOffLength = $params->get( 'cutOffTextLength', 1 );
      $length = $this->_DetermineLength( $this->_htmltext, $this->_lengthUnit );
      if ( $length < $minimumLength )
      {
        $cutOffLength = false;
      }
      else if ( $length <= $cutOffLength )
      {
        $cutOffLength = false;
      }
      else
      {
        /* Shorten the article's full text. */
        $article->text = $this->_htmltext;
      }
    }

    $article->readmore = 0; /* We do not want Joomla to insert his own 'read more' token */
    if ( !/*NOT*/ $cutOffLength )
    {
      /* The article may not be shortened. Ensure the full article is present,
       * even when a manual to-be-ignored read more is present.
       */
      $article->text = $this->_htmltext;
    }

    $this->_wrapperTag = array( 'open' => '', 'close' => '' );
    $wrapperTag = $params->get( 'wrapperTag', '' );
    if ( $wrapperTag )
    {
      $wrapperClass = $params->get( 'wrapperClass', '' );
      if ( $wrapperClass )
      {
        $this->_wrapperTag[ 'open' ] = '<' . $wrapperTag . ' class="' . $wrapperClass . '">';
      }
      else
      {
        $this->_wrapperTag[ 'open' ] = '<' . $wrapperTag . '>';
      }

      $this->_wrapperTag[ 'close' ] = '</' . $wrapperTag . '>';
    }

    return $cutOffLength;
  }

  /**
   * Determines various options, which are stored in the private variables.
   * @note Not all private variables will be set. Those are covered in the @c _PrepareArticleText function
   * @param JTableContent $article RO. The item/article being prepared for display.
   * @params mixed $length Boolean false or a positive number. Indicates whether it is already determined if the article
   *   is to be shortened and how long the shortened article must become.
   * @param class $expand Instance of ReadLessTextExpand.
   * @pre _PrepareArticleText() must have been called beforehand: e.g. @c _plaintext is used (while expanding).
   */
  private function _GetParams( &$article, $length, $expand )
  {
    $this->_applyFormatting = $this->params->get( 'applyFormatting', 'when_active' );
    $this->_addPrefix = $this->params->get( 'addPrefix', 'when_active' );
    $this->_addInlineSuffix = $this->params->get( 'addInlineSuffix', 'when_active' );
    if ( $this->_addInlineSuffix == 'when_active_use_article_manager_option' )
    {
      if ( JComponentHelper::getParams( 'com_content' )->get( 'show_readmore' ) )
      {
        $this->_addInlineSuffix = 'when_active';
      }
      else
      {
        $this->_addInlineSuffix = 'no';
      }
    }
    $this->_addSuffix = $this->params->get( 'addSuffix', 'when_active' );
    if ( $this->_addSuffix == 'when_active_use_article_manager_option' )
    {
      if ( JComponentHelper::getParams( 'com_content' )->get( 'show_readmore' ) )
      {
        $this->_addSuffix = 'when_active';
      }
      else
     {
        $this->_addSuffix = 'no';
      }
    }
    $this->_respectExistingReadmoreLink = $this->params->get( 'respectExistingReadmoreLink', true );
    if ( $this->_respectExistingReadmoreLink == 'respectShowIntro')
    {
      if ( JComponentHelper::getParams( 'com_content' )->get( 'show_intro' ) )
      {
        $this->_respectExistingReadmoreLink = false;
      }
      else
      {
        /* The user does _not_ want to resepect an existing 'read more' token,
         * and likes to have the intro text separated from the full text after it.
         * Only shorten the intro text
         */
        $this->_respectExistingReadmoreLink = 'shortenIntroOnly';
      }
    }

    /* An array of tags or tokens which are used later on. */
    if ( ( $this->_applyFormatting == 'when_active' )
        or ( ( $this->_applyFormatting == 'when_long_enough' ) and $length ) )
    {
      $list = array(
          'extraSelfClosingTags',
          'tagsToRemove',
          'tagsToRemoveWithContents',
          'squareTokensToRemove',
          'curlyTokensToRemove',
          'squareTokensToRemoveWithContents',
          'curlyTokensToRemoveWithContents' );
    }
    else
    {
      $list = array(
          'extraSelfClosingTags');
    }
    foreach ( $list as $parameter )
    {
      $string = JString::strtolower( $this->params->get( $parameter, '' ) );
      $string = JString::str_ireplace( ' ', '', $string );
      if ( $string )
      {
        $parameter = '_' . $parameter;
        $this->$parameter = explode( ',', $string );
      }
    }

    $this->_articleUrl = JRoute::_( ContentHelperRoute::getArticleRoute(
        ReadLessTextHelper::GetArticleSlug( $article ), ReadLessTextHelper::GetCategorySlug( $article ) ) );
    $expand->SetExpandables( Null, Null, Null, array( '{url}' => $this->_articleUrl, '{words}' => $this->_wordCount ) );

    /* prefix */
    $translateAdditions = $this->params->get( 'translateAdditions', false );
    $prefixLinksToFullArticle = false;
    if ( JFactory::getUser()->guest == 0 )
    {
      $this->_prefix = $this->params->get( 'userPrefix', '' );
      $prefixLinksToFullArticle = $this->params->get( 'userPrefixLinksToFullArticle', true );
    }
    else
    {
      $this->_prefix = $this->params->get( 'guestPrefix', '' );
      $prefixLinksToFullArticle = $this->params->get( 'guestPrefixLinksToFullArticle', true);
    }
    if ( $translateAdditions )
    {
      $this->_prefix = JText::_( $this->_prefix );
    }
    $expand->SetExpandables( Null, Null, $this->params->get( 'prefixDateFormat', '%m/%d' ), null );
    $this->_prefix = $expand->expand( $this->_prefix );
    if ( $prefixLinksToFullArticle and $this->_prefix )
    {
      $this->_prefix = '<a href="' . $this->_articleUrl . '">' . $this->_prefix . '</a>';
    }

    /* inline suffix */
    $inlineSuffixLinksToFullArticle = false;
    if ( JFactory::getUser()->guest == 0 )
    {
      $this->_inlineSuffix = $this->params->get( 'userInlineSuffix', '' );
      $inlineSuffixLinksToFullArticle = $this->params->get( 'userInlineSuffixLinksToFullArticle', true );
    }
    else
    {
      $this->_inlineSuffix = $this->params->get( 'guestInlineSuffix', '' );
      $inlineSuffixLinksToFullArticle = $this->params->get( 'guestInlineSuffixLinksToFullArticle', true );
    }
    if ( $translateAdditions )
    {
      $this->_inlineSuffix = JText::_( $this->_inlineSuffix );
    }
    $expand->SetExpandables( Null, Null, $this->params->get( 'suffixDateFormat', '%m/%d' ), null );
    $this->_inlineSuffix = $expand->expand( $this->_inlineSuffix );
    if ( $inlineSuffixLinksToFullArticle and $this->_inlineSuffix )
    {
      $this->_inlineSuffix = '<a href="' . $this->_articleUrl . '">' . $this->_inlineSuffix . '</a>';
    }

    /* suffix */
    $suffixLinksToFullArticle = false;
    if ( JFactory::getUser()->guest == 0 )
    {
      $this->_suffix = $this->params->get( 'userSuffix', '' );
      $suffixLinksToFullArticle = $this->params->get( 'userSuffixLinksToFullArticle', true );
    }
    else
    {
      $this->_suffix = $this->params->get( 'guestSuffix', '' );
      $suffixLinksToFullArticle = $this->params->get( 'guestSuffixLinksToFullArticle', true );
    }
    if ( $translateAdditions )
    {
      $this->_suffix = JText::_( $this->_suffix );
    }
    $expand->SetExpandables( Null, Null, $this->params->get( 'suffixDateFormat', '%m/%d' ), null );
    $this->_suffix = $expand->expand( $this->_suffix );
    if ( $suffixLinksToFullArticle and $this->_suffix )
    {
      $this->_suffix = '<a href="' . $this->_articleUrl . '">' . $this->_suffix . '</a>';
    }

    /* Use the title or one of the -fixes as thumbnail tooltip. */
    switch ( $this->params->get( 'thumbnailTitle', 'articleTitle' ) )
    {
      case '0':
        $this->_thumbnailTitle = '';

      case 'prefix':
        $this->_thumbnailTitle = $this->_prefix;
        break;

      case 'suffix':
        $this->_thumbnailTitle = $this->_suffix;
        break;

      case 'inlineSuffix':
        $this->_thumbnailTitle = $this->_inlineSuffix;
        break;

      case 'articleTitle':
      default:
        $this->_thumbnailTitle = $article->title;
        break;
    }
    $this->_thumbnailTitle = strip_tags( $this->_thumbnailTitle );

    $this->_createThumbnail = $this->params->get( 'createThumbnail', 'when_active' );
    $this->_linkThumbnail = $this->params->get( 'linkThumbnail', true );
    $this->_defaultThumbnail = $this->params->get( 'defaultThumbnailTemplate', '' );
    $this->_defaultThumbnail = $expand->expand( $this->_defaultThumbnail );

    $this->_retainWholeWords = $this->params->get( 'retainWholeWords', false );

    $this->_crop[ 'horizontal_position' ] = $this->params->get( 'cropHorizontalPosition', 'no' );
    $this->_crop[ 'vertical_position' ] = $this->params->get( 'cropVerticalPosition', 'no' );

    $this->_cacheTime = $this->params->get( 'thumbCacheTime', 2419200 /* 4 weeks */ );
    $this->_maxImageLoadTime = $this->params->get( 'maxImageLoadTime', 0 );
    if ( $this->_maxImageLoadTime <= 0 )
    {
      $this->_maxImageLoadTime = 9999; /* Ridicously high: clamped below. */
    }
    $this->_maxImageLoadTime = max( 1, min( 11 /* seconds */, $this->_maxImageLoadTime ) );
    $this->_thumbWidth = $this->params->get( 'thumbWidth', 0 );
    $this->_thumbHeight = $this->params->get( 'thumbHeight', 0 );
    $this->_minimum[ 'width' ] = $this->params->get( 'minimumImageWidth', 0 );
    $this->_minimum[ 'height' ] = $this->params->get( 'minimumImageHeight', 0 );
    $this->_minimum[ 'ratio' ] = max( 0.05, min( 0.95, $this->params->get( 'minimumImageRatio', 0 ) ) );
  }

  /**
   * Searches for an image that passes all constraints set in the configuration settings; removes that image from the
   * article's text - if present - and returns HTML code containing a thumbnail to that image, readily suitable for
   * display.
   * @param JTableContent $article IN OUT. The item/article being prepared for display.
   * @param string $articleText IN, OUT. $article->text may have been adapted when this function returns.
   * @return String. The HTML code for displaying the styled, resized and linked first image according to the
   *   configuration settings, the empty string otherwise.
   */
  private function _GetThumbnailHtml( $article )
  {
    /* - First try to get all data from the cached information. If this succeeds, we can avoid using regular expressions.
     * - Else, try to find one in the full unshortened article.
     * - Else, try to find one in a possibly referenced gallery.
     * - Else, try to find a default thumbnail.
     */
    $thumbnailUrl = $this->_GetCachedThumbnail( $article->text );
    if ( !/*NOT*/ $thumbnailUrl )
    {
      $thumbnailUrl = $this->_PopImage( $this->_htmltext );
      if ( !/*NOT*/ $thumbnailUrl )
      {
        $thumbnailUrl = $this->_PopGalleryImage( $this->_htmltext );
        if ( !/*NOT*/ $thumbnailUrl )
        {
          $thumbnailUrl = $this->_GetDefaultThumbnail();
        }
      }
    }

    if ( $thumbnailUrl )
    {
      $attributes = $this->_GetImageAttributes( $this->_thumbnailTitle, $this->_thumbWidth, $this->_thumbHeight );
      if ( $this->_linkThumbnail )
      {
        $thumbnailHtml = '<a href="' . $this->_articleUrl . '"><img src="' . $thumbnailUrl. '"' . $attributes . '/></a>';
      }
      else
      {
        $thumbnailHtml = '<img src="' . $thumbnailUrl. '"' . $attributes . '/>';
      }
    }
    else
    {
      $thumbnailHtml = false;
    }

    return $thumbnailHtml;
  }

  /**
   * Fetches all cached image information. If the cached information is valid, it is used to construct the HTML code
   * containing a thumbnail, readily suitable for display. The image used to create the thumbnail, if present in the
   * article, will be stripped from the article's text.
   * @param string $articleText OUT. the string to adapt.
   * @return String. The HTML code for displaying the styled, resized and
   *   linked image according to the configuration settings, the empty
   *   string otherwise.
   */
  private function _GetCachedThumbnail( &$articleText )
  {
    $isValid = false;
    if ( $this->_cache )
    {
      $imageTagStartPos = $this->_cache->Get( 'image_tag_start_pos' );
      $imageTagLength = $this->_cache->Get( 'image_tag_length' );
      $imageUrl = $this->_cache->Get( 'image_url' );
      $thumbnailUrl = $this->_cache->Get( 'thumbnail_url' );

      if ( $thumbnailUrl )
      {
        $isValid = ReadLessTextThumb::ValidateThumbnail( $imageUrl, $thumbnailUrl, $this->_minimum, $this->_crop,
            $this->_thumbWidth, $this->_thumbHeight );
      }
    }

    if ( $isValid )
    {
      /* Check if the thumbnail was derived from an image referenced in the article text
       * i.e., check if it is not a default thumbnail.
       */
      if ( ( $imageTagStartPos >= 0 ) and ( $imageTagLength > 0 ) )
      {
        /* Remove the code for the image on the old location.
         * Don't do this blindly: it can be (it is likely, as I think it is a often used option)
         * that all images already have been removed from the given text.
         */
        if ( ( JString::substr( $articleText, $imageTagStartPos, 1 ) == '<' )
            and ( JString::substr( $articleText, $imageTagStartPos + $imageTagLength, 1 ) == '>' ) )
        {
          $imageUrlStartPos = strpos( $articleText, $imageUrl );
          if ( ( $imageTagStartPos < $imageUrlStartPos )
              and ( $imageUrlStartPos < $imageTagStartPos + $imageTagLength ) )
          {
            /* Ok, the portion we want to strip down does contain the url, and
             * does start and end with an opening resp. closing tag.
             * That's all we do to ascertain we will remove the correct portion.
             */
            $articleText = JString::substr_replace( $articleText, '', $imageTagStartPos, $imageTagLength );
          }
        }
      }
    }
    else
    {
      $thumbnailUrl = false;
    }

    return $thumbnailUrl;
  }

  /**
   * Searches for an image according to the default thumbnail template, and
   * returns HTML code containing a thumbnail to that image, readily
   * suitable for display.
   * @return String. The HTML code for displaying the styled, resized and
   *   linked image according to the configuration settings, the empty
   *   string otherwise.
   */
  private function _GetDefaultThumbnail()
  {
    if ( $this->_defaultThumbnail )
    {
      /* Be more lenient with respect to the default thumbnail: ensure it does not get rejected due to size and
       * dimension constraints.
       * The minimum contraints are not used any more after this (so no need to restore them afterwards).
       */
      $minimum[ 'width' ] = 0;
      $minimum[ 'height' ] = 0;
      $minimum[ 'ratio' ] = 0;
      $thumbnailUrl = ReadLessTextThumb::GetThumbnail( $this->_defaultThumbnail, $minimum, $this->_crop,
          $this->_thumbWidth, $this->_thumbHeight, $this->_cacheTime );

      if ( $thumbnailUrl )
      {
        $this->_cache->Set( 'image_tag_start_pos', -1 );
        $this->_cache->Set( 'image_tag_length', 0 );
        $this->_cache->Set( 'image_url', $this->_defaultThumbnail );
        $this->_cache->Set( 'thumbnail_url', $thumbnailUrl );
      }
    }
    else
    {
      $thumbnailUrl = false;
    }
    return $thumbnailUrl;
  }

  /**
   * Searches for an image in the article's text which is big enough according
   * to the configuration settings; removes that image from the article's
   * text, and returns HTML code containing a thumbnail to that image, readily
   * suitable for display.
   * @param string $articleText IN, OUT. the string to search in and to adapt.
   * @return String. The HTML code for displaying the styled, resized and
   *   linked first image according to the configuration settings, the empty
   *   string otherwise.
   * @note When an image is found, the results will have been cached when this function returns.
   */
  private function _PopImage( &$articleText )
  {
    $thumbnailUrl = false;
    $matchCount = preg_match( self::_imgPattern, $articleText, $matches, PREG_OFFSET_CAPTURE, 0 );
    while ( $matchCount )
    {
      $imageCode = $matches[0][0];
      $imageUrl = $matches[1][0];

      /* No acceptable image has been found yet, try this one. */
      $thumbnailUrl = ReadLessTextThumb::GetThumbnail( $imageUrl, $this->_minimum, $this->_crop, $this->_thumbWidth,
          $this->_thumbHeight, $this->_cacheTime, $this->_maxImageLoadTime );
      if ( $thumbnailUrl )
      {
        $imageTagStartPos = JString::strpos( $articleText, $imageCode );
        $imageTagLength = JString::strlen( $imageCode );

        /* Remove the code for the image on the old location. */
        $articleText = JString::substr_replace( $articleText, '', $imageTagStartPos, $imageTagLength );

        $this->_cache->Set( 'image_tag_start_pos', $imageTagStartPos );
        $this->_cache->Set( 'image_tag_length', $imageTagLength );
        $this->_cache->Set( 'image_url', $imageUrl );
        $this->_cache->Set( 'thumbnail_url', $thumbnailUrl );

        $matchCount = false; /* end the loop */
      }
      else
      {
        /* Prepare the next iteration
         * - The value of $offset determines where the next search starts.
         */
        $offset = $matches[ count( $matches ) - 1 ][1] + 1;
        $matchCount = preg_match( self::_imgPattern, $articleText, $matches, PREG_OFFSET_CAPTURE, $offset );
      }
    }

    return $thumbnailUrl;
  }

  /**
   * Searches for a tag which indicates a content gallery plugin is running. If one is found, it will search through the
   * content gallery for a suitable image (i.e. an image that satisfies the configuration constraints), and returns HTML
   * code containing a thumbnail to that image, readily suitable for display.
   * @param string $articleText IN, OUT. the string to search in and to adapt.
   * @return String. The HTML code for displaying the styled, resized and linked first image according to the
   *   configuration settings, the empty string otherwise.
   * @note When an image is found, the results will have been cached when this function returns.
   */
  private function _PopGalleryImage( &$articleText )
  {
    $thumbnailUrl = false;
    $matchCount = preg_match( self::_galleryPattern, $articleText, $matches, PREG_OFFSET_CAPTURE, 0 );
    while ( $matchCount )
    {
      $galleryCode = $matches[0][0];
      $galleryPath = $matches[2][0];

      /* No acceptable image has been found yet, try this one. */
      if ( $directoryContents = @dir( JPATH_SITE . '/images/' . $galleryPath ) )
      {
        while ( !/*NOT*/ $thumbnailUrl and $imagePath = $directoryContents->read() )
        {
          $imagePath = JPATH_SITE . '/images/' . $imagePath;
          $thumbnailUrl = ReadLessTextThumb::GetThumbnail( $imagePath, $this->_minimum, $this->_crop,
              $this->_thumbWidth, $this->_thumbHeight, $this->_cacheTime, $this->_maxImageLoadTime );
        }
        $directoryContents->close();
      }

      if ( $thumbnailUrl )
      {
        $imageTagStartPos = JString::strpos( $articleText, $galleryCode );
        $imageTagLength = JString::strlen( $galleryCode );

        /* Remove the code for the image on the old location. */
        $articleText = JString::substr_replace( $articleText, '', $imageTagStartPos, $imageTagLength );

        $this->_cache->Set( 'image_tag_start_pos', $imageTagStartPos );
        $this->_cache->Set( 'image_tag_length', $imageTagLength );
        $this->_cache->Set( 'image_url', $imagePath );
        $this->_cache->Set( 'thumbnail_url', $thumbnailUrl );

        $matchCount = false; /* end the loop */
      }
      else
      {
        /* Prepare the next iteration
         * - The value of $offset determines where the next search starts.
        */
        $offset = $matches[ count( $matches ) - 1 ][1] + 1;
        $matchCount = preg_match( self::_imgPattern, $articleText, $matches, PREG_OFFSET_CAPTURE, $offset );
      }
    }

    return $thumbnailUrl;
  }

  /**
   * Composes the attributes for the moved image, inclusive the inline CSS
   * attribute @c style.
   * This function does not make any modification.
   * @param alt: A string. The alternative text to be used for the image. Is also used as image title
   * @param $width: A Number. The size of the image in pixels. If 0 or negative, this value is not set.
   * @param $height: A Number. The size of the image in pixels. If 0 or negative, this value is not set.
   * @return string: Either the empty string when no attributes are needed;
   *   either a string ready for insertion as an attribute in a HTML tag.
   * @note Double quotes are used to quote the value.
   */
  private function _GetImageAttributes( $alt, $width = 0, $height = 0 )
  {
    $attributes = '';
    $styleValue = '';

    $attributes .= ' alt="' . $alt . '"';
    $attributes .= ' title="' . $alt . '"';
    if ( $width > 0 )
    {
      $attributes .= ' width="' . $width . '"';
    }
    if ( $height > 0 )
    {
      $attributes .= ' height="' . $height . '"';
    }

    $class = $this->params->get( 'thumbClass', '' );
    if ( $class )
    {
      $attributes .= ' class="' . $class . '"';
    }

    $imagePosition = $this->params->get( 'thumbPosition', 'left' );
    if ( $imagePosition )
    {
      $styleValue .= ' float:' . $imagePosition . ';';
    }
    $padding = $this->params->get( 'thumbPadding', -1 );
    if ( $padding >= 0 )
    {
      $styleValue .= ' padding:' . $padding . 'px;';
    }
    $margin = $this->params->get( 'thumbMargin', '' );
    if ( $margin and ( $margin[0] !== '-' /* a negative number starts with - */ ) )
    {
      $margin = JString::strtolower( $margin );
      $search = array( ' ', ';' );
      $replace = array( ',', ',' );
      $margin = JString::str_ireplace( $search, $replace, $margin );
      $marginList = explode( ',', $margin );
      $n = 0;
      while ( $n < count( $marginList ) )
      {
        if ( $marginList[ $n ] or ( $marginList[ $n ] === '0' ) )
        {
          if ( JString::strpos( '0123456789', $marginList[ $n ][ JString::strlen( $marginList[ $n ] ) - 1 ] ) === FALSE )
          {
            /* Ok, a unit is already appended */
          }
          else
          {
            $marginList[ $n ] .= "px";
          }
          $marginList[ $n ] = ' ' . $marginList[ $n ];
          $n++;
        }
        else
        {
          unset( $marginList[ $n ] );
          $marginList = array_values( $marginList ); /* re-index */
        }
      }
      $margin = implode( '', $marginList );
      $styleValue .= ' margin:' . $margin . ';';
    }
    $borderWidth = $this->params->get ( 'thumbBorderWidth', - 1 );
    if ($borderWidth >= 0) {
      $borderColor = $this->params->get ( 'thumbBorderColor', '#cccccc' );
      $borderStyle = $this->params->get ( 'thumbBorderStyle', '' );
      $styleValue .= ' border: ' . $borderWidth . 'px ' . $borderStyle . ' ' . $borderColor . ';';
    }
    if ( $styleValue )
    {
      $attributes .= ' style="' . $styleValue . '"';
    }

    return $attributes;
  }

  /**
   * Searches the given text for [token]...[/token] or {token}...{/token} constructs that must be removed.
   * @param string $text The text too operate on.
   * @note Possibly writes isShortened.
   * @return The stripped text.
   */
  private function _StripTokens( $text )
  {
    $settings = array (
        array ( 'pattern' => self::_tokenPattern,
            'opening' => '\[',
            'closing' => '\]',
            'tokens' => $this->_squareTokensToRemove
        ),
        array ( 'pattern' => self::_tokenPatternWithContents,
            'opening' => '\[',
            'closing' => '\]',
            'tokens' => $this->_squareTokensToRemoveWithContents
        ),
        array ( 'pattern' => self::_tokenPattern,
            'opening' => '{',
            'closing' => '}',
            'tokens' => $this->_curlyTokensToRemove
        ),
        array ( 'pattern' => self::_tokenPatternWithContents,
            'opening' => '{',
            'closing' => '}',
            'tokens' => $this->_curlyTokensToRemoveWithContents
        ) );
    /* Remove first without contents, then remove with contents. */
    foreach ( $settings as $setting )
    {
      foreach ( $setting[ 'tokens' ] as $token )
      {
        $search = array( self::_tokenOpeningReplacer, self::_tokenClosingReplacer, self::_tokenReplacer );
        $replace = array( $setting[ 'opening' ], $setting[ 'closing' ], $token );
        $pattern = JString::str_ireplace( $search, $replace, $setting[ 'pattern' ] );
        $count = 0;
        $text = preg_replace( $pattern, '', $text, -1, $count );
        if ( $count )
        {
          /* We have stripped away a - likely fancy - part of the article. The user must
           * get the possibility to access the full article - and thus that fancy part.
           */
          $this->_isShortened = true;
        }
      }
    }
    return $text;
  }

  /**
   * Cuts off the article text, retaining the full formatting. It uses regular expressions to find tags, and a simple
   * push/pop system to retain the opened but still-unclosed tags. When the text has been cut off after a
   * admin-configurable unit count, all the opened and still-unclosed tags are then closed.
   * @note No prefix nor suffix will be added in this function.
   * @post If the shortened string differs from the given @c text, @c _isShortened is set to @c true.
   * @param string $text RO. The HTML text to shorten.
   * @param integer $cutOffLength The size in number of @c _lengthUnit units of the plain text to retain, or @c false
   *   to indicate all text must be retained (safe the tags that must be removed according to the configuraion
   *   settings).
   * @return An array of strings: first 'the shortened text', second all 'the closing tags'. The second string may be
   *   empty - this indicates all text was to be retained, safe for the @c _tagsToRemove and
   *   @c _tagsToRemoveWithContents.
   */
  private function _CutOff( $text, $cutOffLength )
  {
    /* Treated as a fifo stack. A list of tags that have been opened but not yet closed. */
    $openTags = array();

    /* Portions of $text is continuously appended until the $cutOffLength is reached.
     * Will be returned as 'the shortened text'.
     */
    $shortened = array();

    /* Also the tag that follows whitespace is not immediately added to $shortened.
     * It is first stored in here.
     * When a new portion is found, this string is fully appended to $shortened.
     * When the loops ends, this string is expanded with all closing tags that still
     * need to be added (one for each opened tag that is still not yet closed) are
     * appended here at the end of this fucmtion, before returning.
     * Will be returned as 'the closing tags'
     */
    $closingTags = '';

    /* The total length of the plain text strings may not be bigger than the
     * maximum article length setting.
     */
    if ( $cutOffLength )
    {
      $nrOfUnitsYetToRetain = $cutOffLength;
    }
    else
    {
      $nrOfUnitsYetToRetain = PHP_INT_MAX;
    }

    /* Use an integer here, not a boolean.
     * If a tag is found which has to be removed with its content - i.e. it is
     * listed in @c _tagsToRemoveWithContents - we search for that specific
     * tagin the next loop. If can be that that same tag is nested, and that
     * an opening tag is found again. By using a counter we can be sure when
     * the outer closing tag has been found.
     */
    $removeAllUntilEndOfTag = 0;

    /* The position in bytes from where the search for the next match of the regular expression must start. */
    $offset = 0;

    /* A tag does not necessarily indicate the end of the 'word', 'sentence', or 'paragraph'.
     * When this boolean is false, the (first portion of the) next plaintext indicates the start of a new unit.
     * When this boolean is true, the (first portion of the) next plaintext belongs to the same 'word', etc.
     */
    $currentUnitIsOngoing = false;

    /* This is not a constant: normally we search for any tag, but when we know we have to skip portions
     * we can as well directly search for the corresponding end tag. This can be achieved by adapting the
     * tag pattern.
     */
    $tagPattern = JString::str_ireplace( self::_tagReplacer, self::_anyTag, self::_tagPattern );

    $continue = true;
    while ( $continue )
    {
      /* Find HTML tags. Each match is an array of (mostly) interesting data
       * about the tag - see @c _tagPattern. Using that, we can find the plain
       * text in front of it.
       */
      /* By using PREG_OFFSET_CAPTURE, each entry in the array returned will
       * be an array itself, containing the matched (sub)string and the starting
       * offset.
       */
      $matchCount = preg_match( $tagPattern, $text, $matches, PREG_OFFSET_CAPTURE, $offset );
      if ( !/*NOT*/ $matchCount or ( count( $matches ) < 4 ) )
      {
        /* No (more) HTML tags were found. We can not just assume that the last part
         * of the string is always a HTML tag, especially not when no wysiwig editor
         * has been used to create this article.
         * Fetch the remainder of text, and set the needed variables to some dummy value,
         * so that the loop can finish correctly.
         */
        $matchCount = preg_match ( "/.*/is", $text, $matches, PREG_OFFSET_CAPTURE, $offset );
        $plainText = $matches[0][0];
        $fullTag = "";
        $tag = "";
        $isSelfClosingTag = false;
        $isClosingTag = false;
        //$offset = /* Don't care */
        $continue = false;
      }
      else
      {
        $plainText = $matches[1][0];
        $fullTag = $matches[2][0];
        $tag = JString::strtolower( $matches[4][0] );

        /* Determine whether the tag we found is an opening tag (e.g. <abc>), a closing tag (e.g. </abc>)
         * or a self-closing tag (e.g. <abc />).
         * If a closing slash is present, it is captured by the regular expression, either in index 3 (<abc />),
         * either in the last-but-one index (<abc />) - and the last-but-one index is always greater than 3.
         */
        $isSelfClosingTag = $matches[ count( $matches) - 2 ][0] == '/';
        $isClosingTag = $matches[3][0] == '/';

        /* Those pesky html4 tags are still lingering around. Be sure to
         * correctly determine a tag as self-closing, even when no closing
         * slash was present. e.g. <br> vs. <br/>
         */
        if ( !/*NOT*/ $isClosingTag )
        {
          if ( in_array( $tag, $this->_extraSelfClosingTags ) )
          {
            $isSelfClosingTag = true;
          }
        }

        /* Prepare the next loop: the value of $offset determines where the next search starts. */
        $offset = $matches[ count( $matches ) - 1 ][1] + 1;
        /* $continue remains true for now. May become false below. */
      }

      if ( $removeAllUntilEndOfTag )
      {
        /* We're inside a block of code of which nothing may be retained in the cut-off text.
         * Do not add the plaintext and tag strings.
         */
        if ( $isClosingTag )
        {
          $removeAllUntilEndOfTag--;
          if ( $removeAllUntilEndOfTag <= 0 )
          {
            $tagPattern = JString::str_ireplace( self::_tagReplacer, self::_anyTag, self::_tagPattern );
          }
        }
        else
        {
          $removeAllUntilEndOfTag++;
        }
      }
      else
      {
        /* Determine the length of the just extracted portion of $text.
         * Depending on the outcome, and the current value of $currentUnitIsOngoing
         * that may need to be tweaked a bit.
         * e.g. we need to count in paragraphs, the match contains no plaintext and a paragraph ending:
         *   the current ongoing paragraph is then closed and must be counted.
         * e.g. we need to count in words, the match equals 'def ghi?<b>':
         *   the returned value will be two, but we may only count one if '?' equals 'j' (two if it equals ' ').
         * Also update $currentUnitIsOngoing to reflect the state after the current portion has been added.
         */
        $end = true;
        $length = ReadLessTextHelper::DetermineLength( $plainText . $fullTag, $this->_lengthUnit, $end );
        if ( $currentUnitIsOngoing and $end )
        {
          if ( !/*NOT*/ $length )
          {
            $length = 1;
          }
        }
        if ( $length and !/*NOT*/ $end )
        {
          $length--;
        }
        if ( $currentUnitIsOngoing )
        {
          if ( $end )
          {
            $currentUnitIsOngoing = false;
          }
        }
        else
       {
         if ( $length and !/*NOT*/ $end )
         {
           $currentUnitIsOngoing = true;
         }
        }

        /* Add the new plaintext. */
        if ( $length >= $nrOfUnitsYetToRetain )
        {
          $plainText = ReadLessTextHelper::Substr( $plainText, $nrOfUnitsYetToRetain, $this->_lengthUnit, $this->_retainWholeWords );
          $shortened[] = $plainText;
          $this->_isShortened = true;
          //$nrOfUnitsYetToRetain = /* Don't care */
          $continue = false;
        }
        else
        {
          $shortened[] = $plainText;
          $nrOfUnitsYetToRetain -= $length;
          /* $continue remains true. */
        }

        /* Determine what to append to the just-added plain text.
         * Also:
         * - update the list of opened-and-not-yet-closed tags and
         * - prepare the next loop (if there is another one).
         */
        if ( in_array( $tag, $this->_tagsToRemoveWithContents ) )
        {
          /* We don't know what will be stripped away in the shortened text:
           * it could be markup, a title or a table.
           * For sure is that the author wants this tag with its contents in the
           * full article, and that it may not taken along in the shortened
           * version. Even when all the remainder of the article fits in the
           * shortened text, it is best to ensure a pre- and/or suffix is
           * appended when done shortening.
           */
          $this->_isShortened = true;

          /* Do not add the tag. */
          if ( $isClosingTag or $isSelfClosingTag )
          {
            /* Nothing more to do. */
          }
          else
          {
            $removeAllUntilEndOfTag++;
            $tagPattern = JString::str_ireplace( self::_tagReplacer, $tag, self::_tagPattern );
          }
        }
        else if ( in_array( $tag, $this->_tagsToRemove )
            or in_array( 'all', $this->_tagsToRemove ) )
        {
          /* Do not add the tag. */
        }
        else if ( $isSelfClosingTag )
        {
          $shortened[] = $fullTag;
        }
        else if ( $isClosingTag )
        {
          $shortened[] = $fullTag;
          /* For simplicity, just assume at this point the text only contains
           * valid HTML, i.e. that all opening tags are properly closed in the
           * correct order. That means we do not need to check whether some
           * pushed opening tag matches with this closing tag: it just has to
           * be.
           */
          unset( $openTags[ count( $openTags ) - 1 ] );
          $openTags = array_values( $openTags ); /* re-index */
        }
        else if ( $tag )
        {
          if ( $continue )
          {
            /* The tag found is a valid opening tag that must remain in the cut-off text. */
            $shortened[] = $fullTag;
            $openTags[] = $tag;
          }
          else
          {
            /* No need to open a tag just at the point where we will stop. */
          }
        }
      }
    } /* while ( $continue ) */

    /* Not all parts of $shortened will become part of 'the shortened text'.
     * All last portions that are either
     * - a tag,
     * - an empty string,
     * - a whitespace string
     * will be added to 'the closing tags'.
     * Plus - after the removal of these elements - the then last plaintext string is right trimmed.
     * All this to allow to 'glue' the inline suffix right after the last retained plaintext
     * character.
     */
    $continue = true;
    $i = count( $shortened ) - 1;
    while ( $continue and ( $i > 0 ) )
    {
      if ( ( $shortened[ $i ] === '' ) /* an empty string */
          or ( substr_compare( $shortened[ $i ], '<', 0, 1 ) == 0 ) /* a tag */
          or ( ctype_space( $shortened[ $i ] ) ) ) /* a whitespace string */
      {
        $closingTags = $shortened[ $i ] . $closingTags;
        $shortened[ $i ] = '';
        $i--;
      }
      else
      {
        $continue = false;
      }
    }
    if ( $i > 0 )
    {
      $shortened[ $i ] = ReadLessTextHelper::Rtrim( $shortened[ $i ] );
    }
    $shortened = implode( '', $shortened );

    /* All tags that were opened and not yet closed are now closed here
     * in the correct order: i.e. in reverse order.
     */
    if ( count( $openTags ) > 0 )
    {
      $openTags = array_reverse( $openTags );
      $closingTags .= '</' . implode( '></', $openTags ) . '>';
    }

    return array ( $shortened, $closingTags );
  }

  /* *********************************************************************** */

  /**
   * Various options needed while operating on the text.
   * May only be set in _PrepareArticleText or _GetParams.
   * Once set, it is to be considered RO.
   * @{
   */
  private $_cache = null; /* Set correctly in _PrepareArticleText(). */

  private $_addPrefix = 'no'; /**< Must be checked after shortening, (if applicable) in combination with _isShortened. */
  private $_addInlineSuffix = 'no'; /**< Must be checked after shortening, (if applicable) in combination with _isShortened. */
  private $_addSuffix = 'no'; /**< Must be checked after shortening, (if applicable) in combination with _isShortened. */
  private $_respectExistingReadmoreLink = true; /* Set correctly in _GetParams() */
  private $_applyFormatting = 'no'; /**< Must be checked after checking the article's length, (if applicable) in combination with _isShortened. */
  private $_createThumbnail = 'no'; /**< Must be checked after shortening, (if applicable) in combination with _isShortened. */
  private $_linkThumbnail = true; /* Set correctly in _GetParams() */
  private $_thumbnailTitle = ''; /* Set correctly in _GetParams() */
  private $_prefix = ''; /* Set correctly in _GetParams() */
  private $_inlineSuffix = ''; /* Set correctly in _GetParams() */
  private $_suffix = ''; /* Set correctly in _GetParams() */
  private $_articleUrl = '.';
  private $_retainWholeWords = false;
  private $_crop = array( 'horizontal_position' => 'no', 'vertical_position' => 'no' );

  private $_cacheTime = 2419200 /* 4 weeks */;
  private $_thumbWidth = 0;
  private $_thumbHeight = 0;
  private $_minimum = array( 'width' => 0, 'height' => 0, 'ratio' => 0.05 );

  private $_extraSelfClosingTags = array();
  private $_tagsToRemove = array();
  private $_tagsToRemoveWithContents = array();
  private $_squareTokensToRemove = array();
  private $_curlyTokensToRemove = array();
  private $_squareTokensToRemoveWithContents = array();
  private $_curlyTokensToRemoveWithContents = array();

  private $_htmltext = ''; /* Set correctly in _PrepareArticleText() */
  private $_plaintext = ''; /* Set correctly in _PrepareArticleText() */
  private $_lengthUnit = 'char'; /* Set correctly in _PrepareArticleText() */
  private $_wordCount = 0; /* Set correctly in _PrepareArticleText() */
  private $_hash = 0; /* Set correctly in _PrepareArticleText(). */

  private $_wrapperTag = array( 'open' => '', 'close' => '' ); /* Set correctly in _PrepareArticleText(). */
  /** @} */

  /**
   * Can be set to true, when an existing manually inserted read more token
   * is found; or later on, when the formatting options cause the removal of
   * parts of the article; or when the article is automatically shortened and
   * some remainder of the article is excluded.
   * Once set to true, the variable may not be set to false again.
   */
  private $_isShortened = false; /* Reset in _GetParams() */
  /** @} /*

  /* *********************************************************************** */

  /**
   * Used to find an image tag in a text.
   *
   * A match found using this regular expression will return at each index:
   * [0] The complete @c img tag, inclusive the brackets and the attributes
   * [1] The value of @src attribute, i.e. the URL where the image can be fetched.
   * [last] The closing bracket. Used to know the precise end byte
   *     offset of the matched tag. Especially needed for multi byte strings
   *     (some of the attributes inside the tag might very well be that),
   *     since JString::strlen returns the number characters, not the number
   *     of bytes. The offset given to preg_match needs to be a byte offset.
   *
   * @note: It is not possible (or: I could not get it to work) to include correct
   *   positions together with the found matches when using non-ASCII UTF8 strings
   *   when using this pattern with preg_match.
   *   The matches returned seem correct though, and strpos can be used to fetch
   *   the starting UTF8 character index.
   * @note This is a simplified version of _tagPattern. Potentially this can
   *   match a great portion of the text, crossing several html tags, _if_ an img
   *   tag is given _without_ a src attribute. If this happens, give them what they
   *   are asking for. Or, in other words: don't worry about that.
   */
  const _imgPattern = '/<img.+?src\s*=\s*["\']([^"\']+)["\'][^>]*(>)/muis';
  /*                                           1111111            -1    */
  /*                    00000000000000000000000000000000000000000000    */

  /**
   * Used to find an content gallery token in a text.
   *
   * A match found using this regular expression will return at each index:
   * [0] The complete @c content gallery token, inclusive the gallery data and the closing token.
   * [1] The gallery data, i.e. the relative URL where the gallery images can be fetched.
   * [last] The closing bracket. Used to know the precise end byte offset of the matched tag. Especially needed for
   *   multi byte strings since JString::strlen returns the number characters, not the number of bytes. The offset given
   *   to preg_match needs to be a byte offset.
   */
  const _galleryPattern = '/{(gallery|vsig|ppgallery|becssg)}\s*([^<]+)\s*{\/\1}/muis';
  /*                                                             11111        -1     */
  /*                        0000000000000000000000000000000000000000000000000000     */

  /**
   * Used to parse an html text by finding all the HTML tags.
   * Thanks to http://kev.coolcavemen.com/2007/03/ultimate-regular-expression-for-html-tag-parsing-with-php/
   * It is a bit modified, to catch the tag without attributes and brackets,
   * the closing tag character '/', and the last char in the match as well.
   * @note Before usage, the string @c _tagReplacer in @c _tagPattern must be replaced with the tag to search for,
   *   or with @c _anyTag
   *
   * A match found using this regular expression will return at each index:
   * [0] The full match; i.e. the concatenation of everything below
   * [1] The plaintext preceding the tag
   * [2] The complete tag, inclusive the brackets and the attributes
   * [3] Either empty, either '/' when the tag is a closing tag.
   * [4] The tag name
   * [5] The full attributes - possibly not present
   * [6] ...
   * [last but one] If the tag is self closing: '/'. Else, and if attributes
   *     are present: the last attribute. Else: the empty string.
   * [last] The closing bracket. Used to know the precise end byte
   *     offset of the matched tag. Especially needed for multi byte strings
   *     (some of the attributes inside the tag might very well be that),
   *     since JString::strlen returns the number characters, not the number
   *     of bytes. The offset given to preg_match needs to be a byte offset.
   *
   * @note the @c xxx part is to be replaced
   * - replace with @c _anyTag to catch any tag
   * - replace with 'abc' to catch tag abc
   *
   * @{
   */
  const _tagPattern = "/(.*?)(<(\/?)(xxx)((\s+(\w|\w[\w-]*\w)(\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)(\/?)(>))/muis";
  /*                     111    333  444   --- one attribute --------------------------------------           -2   -1      */
  /*                                      --- all attributes from index 5 until index -3 ------------                      */
  /*                          222222222222222222222222222222222222222222222222222222222222222222222222222222222222222      */
  /*                    0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000     */
  const _tagReplacer = 'xxx';
  const _anyTag = '\w+';
  /** @} */

  /**
   * @li _tokenPattern Used to find third party token indications like
   *   {slider=MySlider}
   *   {gallery path/to/images}
   * @li _tokenPatternWithContents Used to find third party token indications like
   *   [pgn]...[/pgn]
   *   {slide}...{slide}...{slides}
   *
   * What can not be detected:
   * @li nested token codes
   * @li self closing token codes
   * @li escaped starts of token codes
   *
   * A match found using this regular expression will return at each index:
   * @li _tokenPattern
   *   [0] The complete token code, inclusive the opening and closing character.
   * @li _tokenPatternWithContents
   *   [0] The complete opening token, the complete closign token, and all text in between.
   *
   * @note the @c xxx, @c yyy and @c zzz parts are to be replaced
   * - replace @c xxx with [ or { or ... to catch the start of a token
   * - replace @c yyy with ] or } or ... to catch the end of a token
   *
   * @{
   */
  const _tokenPattern = "/xxx\s*.?\s*zzz.*?yyy/muis";
  const _tokenPatternWithContents = "/xxx\s*zzz.*?yyy.*?xxx\s*\/\s*zzzs?\s*yyy/muis";
  const _tokenOpeningReplacer = 'xxx';
  const _tokenClosingReplacer = 'yyy';
  const _tokenReplacer = 'zzz';
  /** @} */

//     private $profiler = null;
//     private $profiles = null;
}

?>
