<?php 
/**
* @version      4.9.2 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php if ($this->allow_review || $this->config->show_hits){?>
<div class="block_rating_hits">
            
            
            <?php if ($this->allow_review){?>
         
                    <div class="prod_ratings">
					<div class="prod_rat_text"><?php print _JSHOP_RATING?>: </div>
          

                    <?php print showMarkStar($this->product->average_rating);?>
					</div>
            <?php } ?>
			
			
			<?php if ($this->config->show_hits){?>
                <div class="prod_hits">
				
				<span><?php print _JSHOP_HITS?></span>: 
				<?php print $this->product->hits;?>
				</div>

            <?php } ?>
			
</div>
<?php } ?>