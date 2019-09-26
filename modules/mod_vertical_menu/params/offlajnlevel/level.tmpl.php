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
<div class="legend">  
  <h3 style="background: #F6F6F6;" id="<?php echo $control; ?>theme-page" class="jpane-toggler title"><span><?php echo $header; ?></span></h3>  
  <div class="jpane-slider content" style="height:0;overflow:hidden;">
    <?php echo @$render; ?>
  </div>
</div>