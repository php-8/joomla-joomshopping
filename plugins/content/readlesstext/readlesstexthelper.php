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

class ReadLessTextHelper
{
  /**
   * Checks whether it is allowed to run.
   * This function does not make any modification, except when in discover
   * mode: @c $article->text will then be set to the discover information.
   * @param JTableContent $article the item fetched from the database
   * @param params $params the parameters to use
   * @param dict $options OUT the key 'discover' with a boolean value will be
   *   filled in.
   * @param bool $activeByDefaultOnAllContentItems True if all content items
   *   must be shortened by 'read less', unless explicitly disallowed.
   * @param string $pluginName When Discover mode is active, this string
   *   will be added to the discover information.
   * @param string $extraDiscoverInfo When Discover mode is active, this string
   *   will be added to the discover information.
   * @return Boolean value
   */
  public static function Filter( &$callCount, $article, $params, &$options,
      $activeByDefaultOnAllContentItems = false, $pluginName, $extraDiscoverInfo = '' )
  {
    $app = JFactory::getApplication();

    $current = array();
    $current[ 'component' ] = JRequest::getWord( 'option' );
    $current[ 'scope' ] = $app->scope;
    $current[ 'view' ] = JRequest::getWord( 'view' );
    $current[ 'viewId' ] = JRequest::getInt( 'id' );
    $current[ 'layout' ] = JRequest::getWord( 'layout' );
    $current[ 'articleId' ] = self::GetArticleId( $article );
    $current[ 'articleSlug' ] = self::GetArticleSlug( $article );
    $current[ 'articleCategoryId' ] = self::GetCategoryId( $article );

    $articleNumberSkipCount = max( 0, $params->get( 'articleNumberSkipCount' ) );
    $articleNumberShortenCount = max( 0, $params->get( 'articleNumberShortenCount' ) );

    $discoverTextAboutScopeType = '';
    $discoverTextAboutCallCount = '';
    $lastMatchingContext = false;
    $contexts = array(); /* Used to contain all contexts that match the item for this code is executed. */
    $contextDescriptions = array(); /* Only used to facilitate Discover mode. */
    $possiblyInterchangeable = array(); /* Only used to facilitate Discover mode. */

    $scope = array( 'com_' => $params->get( 'componentScope' ), 'mod_' => $params->get( 'moduleScope' ) );
    $key = JString::substr( $current[ 'scope' ], 0, 4 /* com_ or mod_ */ );
    if ( !/*NOT*/key_exists( $key, $scope ) )
    {
      $scope[ $key ] = 'never';
    }

    switch ( $scope[ $key ] )
    {
      case 'always':
        $discoverTextAboutScopeType .= "<br/>This plugin must always be active on <tt>" . $current[ 'scope' ] . "</tt>.";
        $allowed = true;
        break;

      case 'never':
        $discoverTextAboutScopeType .= "<br/>This plugin may never be active on <tt>" . $current[ 'scope' ] . "</tt>.";
        $allowed = false;
        break;

      case 'accordingToContexts':
      default:
        $allowed = self::_CheckCallCount( $callCount, $articleNumberSkipCount, $articleNumberShortenCount, $discoverTextAboutCallCount );
        $callCount++;

        if ( $allowed )
        {
          if ( $params->get( 'when', '0' ) == '0' )
          {
            /* common usage */

            if ( $activeByDefaultOnAllContentItems )
            {
              $allowedFilters = 'com_content';
              $disallowedFilters = '';
            }
            else
            {
              $allowedFilters = 'com_content:blog, com_content:categories, com_content:category, com_content:featured, com_content:frontpage, com_content:section';
              $disallowedFilters = '';
            }
          }
          else
          {
            /* specific usage */
            $allowedFilters = $params->get( 'allowed', '' );
            $disallowedFilters = $params->get( 'disallowed', '' );
          }

          if ( $activeByDefaultOnAllContentItems and ( $current[ 'component' ] == 'com_content' ) )
          {
            $allowedFilters = 'com_content, ' . $allowedFilters;
          }

          $contexts = self::_GetContexts( $current, $contextDescriptions, $possiblyInterchangeable );
          $allowed = self::_CheckContexts( $contexts, $allowedFilters, $disallowedFilters, $lastMatchingContext );
        }
        break;
    }

    /* An extra performance penalty is present in Joomla 2.5.5+
     * Since that version, all articles on a page are processed by content plugins,
     * even when it is known their content is not going to be visible.
     * See http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_item_id=28591
     * Examples are the list of article titles at the bottom of an article category blog,
     * or an article list page.
     * For read less text (again), these articles can better be skipped immediately.
     * Both view and layout are checked to see if one of these two situations occur.
     * It is also limited for component articles of com_content, as I'm uncertain whether other components
     * behave the same. Better the (for most people small) performance penalty than a faulty behavior.
     * * Thank you for this, gabs087. *
     */
    if ( ( $key == 'com_' ) and ( $current[ 'component' ] == 'com_content' ) )
    {
      if ( ( $current[ 'view' ] == 'category' ) and ( $current[ 'layout' ] != 'blog' ) )
      {
        /* Don't process articles in category lists. */
        $allowed = false;
      }
      if ( ( $current[ 'view' ] == 'featured' ) or ( $current[ 'layout' ] == 'blog' ) )
      {
        $maxVisibleArticleCount = $article->params->get( 'num_leading_articles', 0 )
            + $article->params->get( 'num_intro_articles', 0 );
        if ( ( $maxVisibleArticleCount > 0 ) and ( $callCount > $maxVisibleArticleCount ) )
        {
          /* Don't process articles on blog pages whose content will not be part of the page. */
          $allowed = false;
        }
      }
    }

    $discover = $params->get( 'discover', false );
    if ( $discover )
    {
      $version = new JVersion();
      $discover = JFactory::getUser()->authorise( 'core.login.admin' );
    }
    if ( $options !== NULL )
    {
      /* Store this value so that the same logic in this function doen't need to be performed later on. */
      $options[ 'discover' ] = $discover;
    }
    if ( $discover )
    {
      $article->text = self::_GetDiscoverText( $pluginName, $allowed, $current, $contexts, $contextDescriptions,
          $possiblyInterchangeable, $lastMatchingContext, $discoverTextAboutScopeType, $discoverTextAboutCallCount,
          $extraDiscoverInfo );
      $article->introtext = $article->text;
      $article->fulltext = "";
    }

    return $allowed;
  }

  private static function _CheckContexts( $contexts, $allowedFilters, $disallowedFilters, &$lastMatchingContext )
  {
    $allowed = true;

    /* Loop over key (may be active if filter does not match) value (parameter name) pairs */
    $loop = array( false => $allowedFilters, true => $disallowedFilters);
    foreach ( $loop as $defaultAllowed => $filters )
    {
      if ( $filters )
      {
        /* A bit more manipulation is required here: $filters can be given in
         * different formats
         * @li {component}:{view}:id targets a specific article displayed in all the views of the given type.
         * @li {component}:{view}=nr:id targets a specific article displayed in the given view only.
         * @li {component}:id targets a specific article displayed in any view.
         * @li {component}:{view} targets all articles displayed in all the views of the given type.
         * @li {component}:{view}=nr targets all articles displayed in the given view .
         * @li {component} targets all articles of that component.
         * @note If {component}: is not given, com_content: is assumed.
         * @note If {view} is not given, it is not checked for.
         * Plus contexts may be given on different lines.
         * The string manipulations below ensure that all filters start with a component name
         * followed by a view (with or without nr) and/or an id.
         */
        $filters = ',' . JString::strtolower( $filters );
        $filters = preg_replace( '/[\r\n]+/', ',', $filters );
        $search = array(  ' ', ',',  '+com_', '+all', '+' );
        $replace = array( '',  ',+', 'com_',  'all',  'com_content:' );
        /*                  A    BB    CCCCC    DDDD    EEEEEEEEEEE
         * A: remove all whitespaces
         * B: append a + (a character that can not occur in a correct context) after each ,
         * C: remove all + if it was already followed by a component name
         * D: remove all + if it was already followed by the special keyword all
         * E: all remaining + chars indicate the absence of a component name. Fill in the default
         */
        $filters = JString::str_ireplace( $search, $replace, $filters );
        $filterList = explode( ',', $filters );

        $filterAllows = $defaultAllowed;
        foreach ( $contexts as $c )
        {
          if ( in_array( $c, $filterList ) !== FALSE )
          {
            $filterAllows = !/*NOT*/$defaultAllowed;
            $lastMatchingContext = $c;
            break;
          }
        }
        $allowed &= $filterAllows;
      }
      else
      {
        /* There is no restriction set. Retain the default or already determined value for $allowed. */
      }
    }

    return $allowed;
  }

  private static function _GetContexts( $current, &$contextDescriptions, &$possiblyInterchangeable )
  {
    $contexts = array();

    /* Category/section/other descriptions have to be explicitly enabled.
     * Do not include the more general compact indications of the current
     * page/article in that case.
     */
    if ( $current[ 'articleId' ] != 0 )
    {
      $contexts[] = $current[ 'component' ];
      $contextDescriptions[] = 'all pages of this component';

      $contexts[] = $current[ 'component' ] . ':' . $current[ 'view' ];
      $contextDescriptions[] = 'all similar pages';

      if ( $current[ 'viewId' ] )
      {
        $contexts[] = $current[ 'component' ] . ':' . $current[ 'view' ] . '=' . $current[ 'viewId' ];
        $contextDescriptions[] = 'all items on this page only';
        $possiblyInterchangeable[] = $contexts[ count( $contexts ) - 1 ];
      }
    }
    $contexts[] = $current[ 'component' ] . ':' . $current[ 'articleId' ];
    $contextDescriptions[] = 'this item only on all pages';

    $contexts[] = $current[ 'component' ] . ':' . 'all-in-' . $current[ 'articleCategoryId' ];
    $contextDescriptions[] = 'all items from category ' . $current[ 'articleCategoryId' ] . ' on all pages';
    $contexts[] = $current[ 'component' ] . ':' . $current[ 'view' ] . ':' . $current[ 'articleId' ];
    $contextDescriptions[] = 'this item only on all similar pages';
    $contexts[] = $current[ 'component' ] . ':' . $current[ 'view' ] . ':' . 'all-in-' . $current[ 'articleCategoryId' ];
    $contextDescriptions[] = 'all items from category ' . $current[ 'articleCategoryId' ] . ' on all similar pages';
    $possiblyInterchangeable[] = $contexts[ count( $contexts ) - 1 ];
    if ( $current[ 'viewId' ] )
    {
      $contexts[] = $current[ 'component' ] . ':' . $current[ 'view' ] . '=' . $current[ 'viewId' ] . ':' . $current[ 'articleId' ];
      $contextDescriptions[] = 'this item only on this page only';
      $contexts[] = $current[ 'component' ] . ':' . $current[ 'view' ] . '=' . $current[ 'viewId' ] . ':' . 'all-in-' . $current[ 'articleCategoryId' ];
      $contextDescriptions[] = 'all items from category ' . $current[ 'articleCategoryId' ] . ' on this page only';
      $possiblyInterchangeable[] = $contexts[ count( $contexts ) - 1 ];
    }

    return $contexts;
  }

  private static function _CheckCallCount( $callCount, $articleNumberSkipCount, $articleNumberShortenCount, &$discoverTextAboutCallCount )
  {
    $allowed = false;

    if ( $callCount < $articleNumberSkipCount )
    {
      $discoverTextAboutCallCount .= "<br/>This plugin may only become active on this page after skipping "
          . $articleNumberSkipCount . " article(s) or item(s) on this page (still "
          . ( $articleNumberSkipCount - $callCount ) . " to skip).";
    }
    else if ( ( $articleNumberShortenCount == 0 )
        or ( $callCount < $articleNumberSkipCount + $articleNumberShortenCount ) )
    {
      $allowed = true;
    }
    else
    {
      $discoverTextAboutCallCount .= "<br/>This plugin may only become active on this page for "
          . $articleNumberShortenCount . " articles or items on this page.";
    }

    return $allowed;
  }

  private static function _GetDiscoverText( $pluginName, $allowed, $current, $contexts, $contextDescriptions,
      $possiblyInterchangeable, $lastMatchingContext, $discoverTextAboutScopeType, $discoverTextAboutCallCount,
      $extraDiscoverInfo )
  {
    $enableOrDisable = array( true => "disable", false => "enable" );
    $activeOrNot = array( true => "<strong>active</strong>", false => "<strong>not active</strong>" );

    $text = "<p>";
    $text .= "<tt>" . $pluginName . "</tt> is " . $activeOrNot[ $allowed ] . " on this item ";
    if ( $allowed )
    {
      $text .= "(provided the contents' length is large enough).";
    }
    $text .= "</dt><dd>";
    $text .= "&nbsp;&nbsp;" . $discoverTextAboutScopeType . "<br>";
    $text .= "&nbsp;&nbsp;" . $discoverTextAboutCallCount . "<br>";
    $text .= "</p>";

    $text .= "<dl><dt><strong>Information</strong> you can use to create your own contexts:</dt><dd>";
    $text .= "&nbsp;&nbsp;component: <tt>" . $current[ 'component' ] . "</tt></br>";
    $text .= "&nbsp;&nbsp;scope: <tt>" . $current[ 'scope' ] . "</tt></br>";
    $text .= "&nbsp;&nbsp;layout: <tt>" . $current[ 'layout' ] . "</tt></br>";
    $text .= "&nbsp;&nbsp;view: <tt>" . $current[ 'view' ] . "</tt></br>";
    $text .= "&nbsp;&nbsp;view id: <tt>" . $current[ 'viewId' ] . "</tt></br>";
    $text .= "&nbsp;&nbsp;item id: <tt>" . $current[ 'articleId' ] . "</tt></br>";
    $text .= "&nbsp;&nbsp;category id of item: <tt>" . $current[ 'articleCategoryId' ] . "</tt>";
    $text .= "</dd></dl>";

    if ( $lastMatchingContext )
    {
      /* Maybe active, maybe not, but at least one context matched. */
      $text .= "<strong>The last context you configured that matched the current item is <tt>" . $lastMatchingContext . "</tt></strong>";
      if ( !/*NOT*/$allowed )
      {
        $text .= "<br/>If you want to enable the plugin on this item on this page, you minimally need to remove or change this context.";
      }
    }
    else if ( $allowed )
    {
      /* Active, but no context ever matched. */
      $text .= "<br/>There are no contexts listed where <tt>" . $pluginName . "</tt> is allowed to be active, so it is <strong>active by default</strong>.";
    }
    else
    {
      if ( count( $contexts ) == 0 )
      {
        /* Not active, and no contexts have been reseaRched. Do not print anything about contexts. */
      }
      else
      {
        /* Not active, but no context ever matched. */
        $text .= "<br/>No context matches the current item, so it is <strong>not active by default</strong>.";
      }
    }
    $text .= "</p>";
    if ( count( $contexts ) == 0 )
    {
      /* Not active, and no contexts have been reserached. Do not print anything about contexts. */
    }
    else
    {
      $text .= "<dl><dt><strong>Contexts matching this article</strong>: to " . $enableOrDisable[ $allowed ] . " <tt>" . $pluginName . "</tt> on</dt><dd>";
      for ( $i = 0; $i < count( $contexts ); $i++)
      {
        $text .= "&nbsp;&nbsp;" . $contextDescriptions[$i] . ", use <tt>" . $contexts[$i] . "</tt><br/>";
      }
      $text .= "</dd></dl>";
    }

    $text .= "<p>";
    if ( $current[ 'articleId' ] == $current[ 'viewId' ] )
    {
    $text .= "<strong>Note</strong>: if the view name <tt>"
        . $current[ 'view' ]
        . "</tt> serves to display a single item/article, the contexts <tt>"
        . implode( '</tt>, <tt>n', $possiblyInterchangeable )
        . "</tt> may yield the same result and are interchangeable.<br/>";
    }
    $text .= "<strong>Note</strong>: this discover information is only displayed to users with back-end permissions and can be disabled in the back-end.<br/>";
    if ( count( $contexts ) == 0 )
    {
      /* Not active, and no contexts have been researched. Do not print anything about contexts. */
    }
    else
    {
      $text .= "<dl><dt><strong>General</strong>: all contexts follow this syntax: <tt>component:view:item</tt></dt><dd>";
          $text .= "&nbsp;&nbsp;if the <tt>ncomponent</tt> is left out, <tt>com_content:</tt> is assumed;<br/>";
          $text .= "&nbsp;&nbsp;if the <tt>nview</tt> is left out, <tt>all</tt> views match;<br/>";
          $text .= "&nbsp;&nbsp;if the <tt>item</tt> is left out, <tt>all</tt> items match.<br/>";
          $text .= "</dd></dl>";
    }
    if ( $extraDiscoverInfo )
    {
      $text .= "<br/>";
      $text .= "<strong>" . $extraDiscoverInfo . "</strong>";
    }
    $text .= "</p>";

    return $text;
  }

  public static function Trim( $str )
  {
    return preg_replace( "/(^\s+)|(\s+$)/us", "", $string );
  }

  public static function Rtrim( $string )
  {
    return preg_replace( "/\s+$/us", "", $string );
  }

  /**
   * Determines if @c $string ends with $lastPart.
   * @param string $string The string to examine
   * @param string $lastPart The substring to find at the end of @c $string
   * @return true if @c $string ends with @c $lastPart, false otherwise
   */
  private static function _EndsWith( $string, $lastPart )
  {
    if ( strlen( $string ) < strlen( $lastPart ) )
    {
      $endsWith = false;
    }
    else
    {
      if ( substr_compare( $string, $lastPart, -1 * strlen( $lastPart ) ) )
      {
        $endsWith = false;
      }
      else
      {
        $endsWith = true;
      }
    }
    return $endsWith;
  }

  /**
   * Determines the length of the given article text.
   * @param string $htmltext The html text to consider.
   * @param string $lengthUnit Unit of the length to determine. One of 'char',
   *   'word', 'sentence', 'paragraph'.
   * @param bool $end OUT If true, the last part of @c htmlText ends the
   *   ongoing length unit.
   * @return the length expressed in the given unit.
   * @pre it is assumed subsequent whitespace has already been removed.
   */
  public static function DetermineLength( $htmltext, $lengthUnit, &$end )
  {
    switch ( $lengthUnit )
    {
      case 'sentence':
        /* Calculation is done using a list of sentences.
         * Exclude empty sentences, exclude sentences with only markup,
         * exclude consecutive punctuation characters.
         * The text is also trimmed to determine afterwards whether the last
         * sentence has ended.
         * Paragraph, row and list item demarcations also mark the end of a sentence.
         *
         * Ensure that all sentence endings can be treated alike.
         */
        $search = array( '</p>', '</li>', '</dt>', '</dl>', '</tr>', '#', '.', '?', '!', '¿' );
        $replace = array( '.', '.', '.', '.', '.', '_', '#', '#', '#', '#' );
        $htmltext = JString::str_ireplace( $search, $replace, $htmltext );
        $text = rtrim( strip_tags( $htmltext ) );
        $length = 0;
        foreach ( explode( '#', $text ) as $sentence )
        {
          if ( preg_split( '/\s+/', $sentence, 1, PREG_SPLIT_NO_EMPTY ) )
          {
            $length++;
          }
        }
        $end = self::_EndsWith( $text, '#' );
        break;

      case 'paragraph':
        /* Calculation is done using a list of non-empty paragraphs.
         * Exclude empty paragraphs, exclude paragraps with only markup.
         * The text is trimmed first to determine whether the last paragraph
         * has ended afterwards.
         * Table and list demarcations also mark the end of a sentence.
         * Ensure that all paragraph endings can be treated alike.
         */
        $search = array( '</ul>', '</ol>', '</dl>' );
        $replace = array( '</p>', '</p>', '</p>' );
        $htmltext = JString::str_ireplace( $search, $replace, $htmltext );
        $htmltext = rtrim( $htmltext );
        $length = 0;
        foreach ( explode( '</p>', $htmltext ) as $paragraph )
        {
          $paragraph = strip_tags( $paragraph );
          if ( preg_split( '/\s+/', $paragraph, 1, PREG_SPLIT_NO_EMPTY ) )
          {
            $length++;
          }
        }
        $end = self::_EndsWith( $htmltext, '</p>' );
        break;

      case 'word':
        /* Paragraph, row and list item demarcations also mark the end of a word.
         * Ensure that all word endings can be treated alike.
         */
        $search = array( '</p>', '</li>', '</dt>', '</dl>', '</tr>', '.', '?', '!', '¿' );
        $replace = array( ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ' );
        $htmltext = JString::str_ireplace( $search, $replace, $htmltext );

        /* Calculation only needs the plaintext. */
        $text = strip_tags( $htmltext );

        $length = count( preg_split( '/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY ) );
        $end = self::_EndsWith( $text, ' ' );
        break;

      case 'char':
      default:
        /* Calculation only needs the plaintext. */
        $text = strip_tags( $htmltext );

        /* Subsequent whitespace must be counted as one when $lengthUnit equals 'char' */
        $text = preg_replace( '/\s+/mis', ' ', $text );

        $length = JString::strlen( $text );
        $end = true;
        break;
    }

    return $length;
  }

  /**
   * Returns the id of the article this plugin is being called upon.
   * Works for com_content and com_eventlist items,
   * and others (list?)
   * @param JTableContent $article IN The item/article being prepared for display.
   * @return A number.
   */
  public static function GetArticleId( $article )
  {
    $id = 0;
    foreach ( array( 'id', 'did', 'cid' ) as $field )
    {
      if ( isset( $article->$field ) )
      {
        $id = (int)$article->$field;
        break;
      }
    }

    return $id;
  }

  /**
   * Returns the slug of the article this plugin is being called upon.
   * Works for com_content and com_eventlist items,
   * and others (list?)
   * @param JTableContent $article IN The item/article being prepared for display.
   * @return A string.
   */
  public static function GetArticleSlug( $article )
  {
    $id = self::GetArticleId( $article );

    if ( isset( $article->slug ) and $article->slug )
    {
      $slug = $article->slug;
    }
    else if ( isset( $article->alias ) and $article->alias )
    {
      $slug = $id . ':' . $article->alias;
    }
    else
    {
      if ( isset( $article->title ) and $article->title )
      {
        $slug = $id . ':' . JApplication::stringURLSafe( $article->title );
      }
      else if ( isset( $article->name ) and $article->name )
      {
        $slug = $id . ':' . JApplication::stringURLSafe( $article->name );
      }
      else
      {
        $slug = $id;
      }
    }

    return $slug;
  }

  /**
   * Returns the id of the category of the article this plugin is being called
   * upon.
   * Works for com_content and com_eventlist items,
   * and others (list?)
   * @param JTableContent $article The item/article being prepared for display.
   * @return A number.
   */
  public static function GetCategoryId( &$article )
  {
    $id = 0;
    foreach ( array( 'catid', 'catsid' ) as $field )
    {
      if ( isset( $article->$field ) )
      {
        $id = (int)$article->$field;
        break;
      }
    }
    return $id;
  }

  /**
   * Returns the slug of the category of the article this plugin is being called
   * upon.
   * Works for com_content and com_eventlist items,
   * and others (list?)
   * @param JTableContent $article The item/article being prepared for display.
   * @return A string.
   */
  public static function GetCategorySlug( &$article )
  {
    $id = self::GetCategoryId( $article );

    if ( isset( $article->catslug ) and $article->catslug )
    {
      $slug = $article->catslug;
    }
    else if ( isset( $article->category_alias ) and $article->category_alias )
    {
      $slug = $id . ':' . $article->category_alias;
    }
    else
    {
      $slug = $id;
    }

    return $slug;
  }

  /**
   *
   * @param string $plainText
   * @param integer $length
   * @param string $lengthUnit Unit of the length. One of 'char',
   *   'word', 'sentence', 'paragraph'.
   * @param bool $retainWholeWords Only used when $lengthUnit equals 'char'
   */
  public static function Substr( $plainText, $length, $lengthUnit, $retainWholeWords = false )
  {
    $substr = $plainText;
    switch ( $lengthUnit )
    {
      case 'sentence':
        $substrByteLength = 0;
        $matches = preg_split( '/([.?!¿]+)/mis', $plainText, $length + 1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE );

        /* Retain $length sentences and the line ending of each sentence. */
        $i = min( count( $matches ), $length * 2 );
        $substrByteLength = $matches[ $i ][1]; /* Start position of first match not to retain. */

        $substr = substr( $plainText, 0, $substrByteLength);
        break;

      case 'paragraph':
        /* Default value is correct. Nothing to do. */
        break;

      case 'word':
        $words = preg_split( '/\s+/mis', $plainText, $length + 1, PREG_SPLIT_NO_EMPTY );
        $words = array_slice( $words, 0, $length );
        $substr = implode( ' ', $words );
        break;

      case 'char':
      default:
        $words = preg_split( '/(\s+)/mis', $plainText, $length + 1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
        $i = 0;
        while ( $i < count( $words ) )
        {
          if ( ctype_space( $words[ $i ] ) )
          {
            $wordLength = 1; /* Count whitespace separating the words as one char. */
          }
          else
          {
            $wordLength = JString::strlen( $words[ $i ] );
          }

          if ( $length >= $wordLength )
          {
            $length -= $wordLength;
            $i++;
          }
          else
          {
            break;
          }
        }

        $lastChars = '';
        if ( ( $length > 0 ) and ( $i < count( $words ) ) )
        {
          if ( $retainWholeWords )
          {
            /* The $i-th word is to be cut in half.
             * Retain the whole word if it is the first to retain,
             * Toss it completely away otherwise.
             */
            $i = max( 1, $i );
          }
          else
          {
             $lastChars = JString::substr( $words[ $i ], 0, $length );
          }
        }

        /* $i represents the number of words to retain fully,
         * $lastChars contains a few characters of the last word that is retained only partially.
         */
        $words = array_slice( $words, 0, $i );
        $words[] = $lastChars;
        $substr = implode( ' ', $words );
        break;
    }

    return $substr;
  }
}
?>
