<?php
/**
 * mod_vertical_menu - Vertical Menu
 *
 * @author    Balint Polgarfi
 * @copyright 2014-2019 Offlajn.com
 * @license   https://gnu.org/licenses/gpl-2.0.html
 * @link      https://offlajn.com
 */
?><?php
defined('_JEXEC') or die('Restricted access');

?>
<div class="legend panel">
  <h3 class="title pane-toggler"><span><?php echo $header; ?></span></h3>
  <div class="pane-slider content pane-down" style="padding-top: 0px; border-top: medium none; padding-bottom: 0px; border-bottom: medium none; overflow: hidden; height: 0;">		
    <fieldset class="panelform">
      <?php echo @$render; ?>
    </fieldset>			
    <div class="clr"></div>	
  </div>
</div>