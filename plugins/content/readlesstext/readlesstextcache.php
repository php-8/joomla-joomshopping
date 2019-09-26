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

class ReadLessTextCache
{
  /**
   * Constructor
   * @param string $rtable The table name where the item is stored.
   * @param uint $rid The item id in $rtable.
   * @param string $hash a fingerprint of the article. When the fingerprint doesn't match with the
   *   value stored in the database, the other fields are reset.
   */
  function __construct( $table, $rtable, $rid, $hash )
  {
    /* Must be set before calling _GetDataFromDb */
    $this->_table = $table;
    $this->_data[ 'rtable' ] = $rtable;
    $this->_data[ 'rid' ] = $rid;

    $this->_GetDataFromDb( $rtable, $rid );
    if ( array_key_exists( 'hash', $this->_data )
        and $this->_data[ 'hash' ]
        and ( $this->_data[ 'hash' ] == $hash ) )
    {
      /* Ok. Use the cached data. */
      $this->_dirty = false;
    }
    else
    {
      foreach ( array_keys( $this->_data ) as $key )
      {
        if ( $key != 'id' )
        {
          $this->_data[ $key ] = 0;
        }
      }
      $this->_data[ 'rtable' ] = $rtable;
      $this->_data[ 'rid' ] = $rid;
      $this->_data[ 'hash' ] = $hash;
      $this->_dirty = false; /* There is no need to store reset values. */
    }
  }

  /**
   * Retrieves the stored value for the given field.
   * @param string $field A field name, as stored in the database.
   * @note Possible fields are: 'char', 'word', 'sentence', 'paragraph', 'begin', 'end', 'url'.
   * @return @li 0 when the requested field or its field value could not be found. A valid value otherwise.
   */
  public function Get( $field )
  {
    $value = 0;
    if ( array_key_exists( $field, $this->_data ) )
    {
      $value = $this->_data[ $field ];
    }
    return $value;
  }

  /**
   * Sets or updates (a) value(s) for the given field(s).
   * @param mixed $field Either a field name, as stored in the database.
   *   Or an array of field names.
   * @param mixed value Either a fields value. It is assumed the values given can be converted to the stored database
   *   type.
   *   Or an array of field values.
   * @return void
   * @note The values are not yet stored in the database. Use @c Store() to make the changes permanent.
   * @see Store
   * @return void
   */
  public function Set( $field, $value )
  {
    if ( is_string( $field ) )
    {
      $set = array( $field => $value );
    }
    else
    {
      /* Assume is_array() */
      $set = array_combine( $field, $value );
    }
    foreach ( $set as $k => $v )
    {
      if ( isset( $this->_data[ $k ] ) and ( $this->_data[ $k ] == $v ) )
      {
        /* Nothing changed. No need to mark the data as changed. */
      }
      else
      {
        $this->_data[ $k ] = $v;
        $this->_dirty = true;
      }
    }
  }

  /**
   * Makes all changes permanent.
   */
  public function Store()
  {
    if ( $this->_dirty )
    {
      $this->_SetDataToDb();
      $this->_dirty = false;
    }
    else
    {
      /* Nothing changed. No need to write to the database. */
    }
  }

  /**
   * Fetches all known readlesstext data from the database of given article/item
   * @return void.
   * @post @c $this->_data will have been set to an associative array, with the
   *   field names as keys, and the field values as values, or to an empty array
   *   (when no stored data was found).
   */
  private function _GetDataFromDb()
  {
    $db = JFactory::getDBO();
    $query = 'SELECT *
        FROM ' . $this->_table . '
        WHERE rtable = ' . $db->Quote( $this->_data[ 'rtable' ] ) . '
        AND rid = ' . $db->Quote( $this->_data[ 'rid' ] );
    $db->setQuery( $query );
    $this->_data = $db->loadAssoc();
    if ( $this->_data )
    {
      unset( $this->_data[ 'last_update' ] );
    }
    else
    {
      $this->_data = array();
      /* rtable, rid, and hash will be filled in by the caller */
    }
  }

  /**
   * Writes all locally stored readlesstext data to the database of given article/item.
   * @note Both @c rtable and @c rid, given during construction, will be stored too.
   * @note If a record already exists, it will be updated; if not, a new one will be added.
   * @return void.
   */
  private function _SetDataToDb()
  {
    $db = JFactory::getDBO();

    $inserts = array();
    foreach ( $this->_data as $key => $value )
    {
      $inserts[ $db->quoteName( $key ) ] = $db->Quote( $value );
    }

    $updates = array();
    foreach ( $this->_data as $key => $value )
    {
      $updates[] = $db->quoteName( $key ) . '=' . $db->Quote( $value );
    }

    $query = 'INSERT INTO ' . $this->_table . ' (' . implode( ',', array_keys( $inserts ) ) . ')
        VALUES (' . implode( ',', $inserts ) . ')
        ON DUPLICATE KEY UPDATE ' . implode( ',', $updates );

    $db->setQuery( $query );
    $db->query();
  }

  private $_data = array(); /**< Local storage for all the readlesstext data. Keys correspond to the table fieldnames. */
  private $_dirty = false; /**< When @c False, no database write will be done. This eliminates needless writes. */
  private $_table = '#__readlesstext'; /**< The table name where readlesstext stores extra data about an article/item */
}
?>
