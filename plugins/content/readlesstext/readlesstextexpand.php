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
jimport( 'joomla.utilities.date' );
require_once 'readlesstexthelper.php';

class ReadLessTextExpand
{
  function __construct()
  {
    $this->_expandables[ '{author_id}' ] = 0;
    $this->_expandables[ '{author}' ] = '';
    $this->_expandables[ '{created}' ] = '';
    $this->_expandables[ '{modified}' ] = '';
    $this->_expandables[ '{publish_up}' ] = '';
    $this->_expandables[ '{hits}' ] = '';
    $this->_expandables[ '{title}' ] = '';
    $this->_expandables[ '{words}' ] = '';
  }

  /**
   * Prepares a future call to Expand(). Based on the given arguments, the
   * expandables are set. Existing exandables are overwritten.
   * @param JTableContent $article RO. The item/article being prepared for display. May be Null.
   * @param string $plaintext RO. The plain text version of the full,
   *   unshortened article. May be Null.
   * @param string $dateFormat Determines the format for the date fields to expand
   *   in the pre- and suffixes. May be Null.
   * @param $overrides Overrides information deduced from $article with given
   *   values. Keys are one of {author}, {author_id}, {words}, {created},
   *   {modified}, {publish_up}, {hits}, {category}, {category_id}, {id},
   *   {component}, {title}, {url}. May be Null.
   * @note {words} can only be expanded correctly if it is set via @c $overrides
   */
  public function SetExpandables( $article, $plaintext, $dateFormat, $overrides )
  {
    if ( $article )
    {
      /* {id} */
      $this->_expandables[ '{id}' ] = ReadLessTextHelper::GetArticleId( $article );

      /* {component} */
      $this->_expandables[ '{component}' ] = JRequest::getWord( 'option' );

      /* {category_id} */
      $this->_expandables[ '{category_id}' ] = ReadLessTextHelper::GetCategoryId( $article );

      /* {category} */
      if ( isset( $article->category_title ) )
      {
        $this->_expandables[ '{category}' ] = $article->category_title;
      }

      /* {author_id} */
      if ( isset( $article->created_by ) )
      {
        $this->_expandables[ '{author_id}' ] = $article->created_by;
      }

      /* {author} */
      if ( isset( $article->created_by_alias ) and $article->created_by_alias )
      {
        $this->_expandables[ '{author}' ] = $article->created_by_alias;
      }
      else if ( isset( $article->author ) and $article->author )
      {
        $this->_expandables[ '{author}' ] = $article->author;
      }

      /* {created} */
      if ( isset( $article->created ) )
      {
        $this->_created = new JDate( $article->created );
      }

      /* {modified} */
      if ( isset( $article->modified ) and ( $article->modified != '0000-00-00 00:00:00' ) )
      {
        $this->_modified = new JDate( $article->modified );
      }
      else
     {
        $this->_modified = $this->_created;
      }

      /* {publish_up} */
      if ( isset( $article->publish_up ) )
      {
        $this->_publishUp = new JDate( $article->publish_up );
      }

      /* {hits} */
      if ( isset( $article->hits ) )
      {
        $this->_expandables[ '{hits}' ] = $article->hits;
      }

      /* {title} */
      if ( isset( $article->title ) )
      {
        $this->_expandables[ '{title}' ] = $article->title;
      }
    }

    if ( $dateFormat )
    {
      if ( $this->_created )
      {
        $this->_expandables[ '{created}' ] = $this->_created->Format( $dateFormat );
      }
      if ( $this->_modified )
      {
        $this->_expandables[ '{modified}' ] = $this->_modified->Format( $dateFormat );
      }
      if ( $this->_publishUp )
      {
        $this->_expandables[ '{publish_up}' ] = $this->_publishUp->Format( $dateFormat );
      }
    }

    /* {words} must be set via $overrides */

    if ( $overrides )
    {
      $this->_expandables = array_merge( $this->_expandables, $overrides );
    }
  }

  /**
   * Expands the string according to the expandables, set in a previous call to SetExpandables
   * @param string $string
   */
  public function Expand( $string )
  {
    return JString::str_ireplace( array_keys( $this->_expandables ), array_values( $this->_expandables ), $string );
  }

  private $_expandables = array();
}
?>
