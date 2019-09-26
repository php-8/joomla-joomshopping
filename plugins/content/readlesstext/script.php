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

class plgContentReadlesstextInstallerScript
{
  /**
   * Constructor
   * @param JAdapterInstance $adapter The object responsible for running this script1
   */
  public function __constructor(JAdapterInstance $adapter)
  {
    //void
  }

  /**
   * Called before any type of action
   * @param string $route Which action is happening (install|uninstall|discover_install)
   * @param JAdapterInstance $adapter The object responsible for running this script
   * @return boolean True on success
   */
  public function preflight($route, JAdapterInstance $adapter)
  {
    return true;
  }

  /**
   * Called after any type of action
   * @param string $route Which action is happening (install|uninstall|discover_install)
   * @param JAdapterInstance $adapter The object responsible for running this script
   * @return boolean True on success
   */
  public function postflight($route, JAdapterInstance $adapter)
  {
    echo "
        <h2>v5.2 (r274)</h2>
        <dl>
        <dt><em>read less text</em> will control the article text:</dt>
        <dd>preview size, formatting and image placement can be adjusted to your liking, precisely on those pages and
          for those articles or items you want. <em>read less text</em> will not alter your article table in any way.
          Only the display is controlled: uninstalling or disabling will bring back the original text.</dd>
        <dt>Take your time</dt>
        <dd>to read all the helpful tooltips and fully configure
          <a href='index.php?option=com_plugins&view=plugins&filter_search=read'><em>read less text</em></a>
          to your liking. Use the discover mode if you need extreme flexibility in controlling which articles get
          shortened - and when.</dd>
        <dt>This version does not affect the article&acute;s titles.</dt>
        <dd>If you want to control the title length, prepend or append specific information, and adjust the casing of
          the title, you can install and use the separate extension
          <a href='http://extensions.joomla.org/extensions/style-a-design/titles/16619/'><em>read less title.</em></a>
          </dd>
        </dl>

        <h2>Important notes affecting your configuration:</h2>
        <dl>

        <dt><strong>Since v5.2</strong></dt>
        <dd><em>Experimental</em> support has been added for some image gallery plugins. When gallery plugins
          are used in an article, some text that looks like<code>{gallery}path/to/images{/gallery}</code> is inserted.
          You can instruct <em>read less text</em> to scan that referenced location for images as well.<br/>
          The list of content gallery plugins that is recognized is: <tt>SIGE</tt>, <tt>sigplus</tt>,
          <tt>VSIG (Very Simple Image gallery)</tt>, <tt>pPGallery</tt>, <tt>CSS Gallery</tt><br/>
          There are also quite some limitations:
          <ul>
            <li>Can not link to the gallery with a word, used at the article</li>
            <li>Can not load image information from a file.</li>
            <li>The option 'root' is not supported (sigplus)</li>
            <li>Picasaweb albums are not supported</li>
            <li>Paths with spaces are not supported.</li>
            </ul>
          </dd>

        <dt><strong>Since v5.1</strong></dt>
        <dd>The option <code>Show Intro Text</code> as set in the
          <a href='index.php?option=com_content'>Article Manager</a> &gt; <code>Options</code> &gt;
          <code>Show Intro Text</code> &gt; <code>Articles</code> can now be used to determine whether the full article
          or only the intro text may be considered for shortening.
          <br />See also the tooltip that comes with the option
          <code>Length</code> &gt; <code>Respect Position Existing Read More</code>.
          <br/><strong>You may want to check that setting</strong>.</dd>

        <dt><strong>Since v5.1</strong></dt>
        <dd>The field that replaces the default 'Read more' value - in the <em>Read More (Suffix)</em> section - has
          been split in two.
          The first field is added at the end of the text, before the paragraph is closed;
          the other field is added at the end of article, after all HTML tags (including the paragraph tag
          <code>p</code>) have been closed.
          <br/><strong>You may want to (re-)configure these fields</strong>.</dd>

        <dt><strong>Since v5.0</strong></dt>
        <dd><em>read less text</em> provides two <code>Date Format</code> fields, used while replacing tokens in the
          prefix and suffix fields. The way these date formats must be constructed has been changed. You can review
          <a href='http://php.net/manual/en/function.date.php'>the manual for the function date</a>
          for a list of formatting characters and their explanation, and for a list of examples.
          <br/><strong>You may want to review these fields, or clear them to load the new, correct, default value.
          </strong>
        </dd>

        </dl>";
    return true;
  }

  /**
   * Called on installation
   * @param JAdapterInstance $adapter The object responsible for running this script
   * @return boolean True on success
   */
  public function install(JAdapterInstance $adapter)
  {
    $this->_CreateTable();
    return true;
  }

  /**
   * Called on update
   * @param JAdapterInstance $adapter The object responsible for running this script
   * @return boolean True on success
   */
  public function update(JAdapterInstance $adapter)
  {
    $this->_CreateTable();
    return true;
  }

  /**
   * Called on uninstallation
   * @param JAdapterInstance $adapter The object responsible for running this script
   */
  public function uninstall(JAdapterInstance $adapter)
  {
    $this->_DestroyTable();
    return true;
  }

  private function _CreateTable()
  {
    $db = JFactory::getDBO();
    $db->setQuery( self::_createTableSql );
    $db->query();
  }

  private function _DestroyTable()
  {
    $db = JFactory::getDBO();
    $db->setQuery( self::_destroyTableSql );
    $db->query();
  }

  const _createTableSql = "CREATE TABLE IF NOT EXISTS `#__readlesstext` (
    `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `rtable` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'The component name where the item resides in. Also called option',
    `rid` INT(10) NOT NULL COMMENT 'The unique id of the item in that component. e.g. the article id for com_content',
    `hash` VARCHAR(255) DEFAULT '' COMMENT 'The fingerprint of the full item text.',
    `char` INTEGER UNSIGNED DEFAULT 0 COMMENT 'Count in the full item text.',
    `word` INTEGER UNSIGNED DEFAULT 0 COMMENT 'Count in the full item text.',
    `sentence` INTEGER UNSIGNED DEFAULT 0 COMMENT 'Count in the full item text.',
    `paragraph` INTEGER UNSIGNED DEFAULT 0 COMMENT 'Count in the full item text.',
    `image_tag_start_pos` INTEGER UNSIGNED DEFAULT 0 COMMENT 'Start position of the image tag where the thumbnail was created from.',
    `image_tag_length` INTEGER UNSIGNED DEFAULT 0 COMMENT 'Length of the image tag in nr of UTF8 chars.',
    `image_url` VARCHAR(1023) NOT NULL DEFAULT '' COMMENT 'Url to the image where the thumbnail was created from.',
    `thumbnail_url` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Url to the thumbnail.',
    `last_update` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Debug information.',
    UNIQUE (`rtable`, `rid`) ) DEFAULT CHARSET=utf8;";

  const _destroyTableSql = "DROP TABLE IF EXISTS `#__readlesstext`";
}

?>
