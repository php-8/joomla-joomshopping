<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_search
 * @copyright	Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="icon-top-wrapper">
	<i class="search-icon icon-search icon-magnifier"></i>
	<div id="search_close" class="remove-search icon-remove"><div class="close_search_block">x</div></div>
</div>

<div class="searchwrapper top-search">
	<div class="top-search-box">
		<form action="<?php echo JRoute::_('index.php');?>" method="post">
			<div class="search<?php echo $moduleclass_sfx ?>">

				<div class="search-input-box">
					<?php
						$output = '<div class="top-search-wrapper"><div class="radon_search_input"><input name="searchword" id="mod-search-searchword" maxlength="'.$maxlength.'"  class="inputbox search-inputbox '.$moduleclass_sfx.'" type="text" size="'.$width.'" value="'.$text.'"  onblur="if (this.value==\'\') this.value=\''.$text.'\';" onfocus="if (this.value==\''.$text.'\') this.value=\'\';" /></div>';

						if ($button) :
							if ($imagebutton) :
								$output .= '<input type="image" value="'.$button_text.'" class="button'.$moduleclass_sfx.'" src="'.$img.'" onclick="this.form.searchword.focus();"/>';
							else :
								$output .= '<button type="submit" value="'.$button_text.'" class="button search-submit '.$moduleclass_sfx.'" onclick="this.form.searchword.focus();"><i class="fa fa-search"></i></button>';
							endif;
						endif;

						// switch ($button_pos) :
						// 	case 'top' :
						// 		$button = $button.'<br />';
						// 		$output = $button;
						// 		break;

						// 	case 'bottom' :
						// 		$button = '<br />'.$button;
						// 		$output = $output;
						// 		break;

						// 	case 'right' :
						// 		$output = $output;
						// 		break;

						// 	case 'left' :
						// 	default :
						// 		$output = $button;
						// 		break;
						// endswitch;

						echo $output;
					?>
				</div><!-- /.search-input-box -->	

				<input type="hidden" name="task" value="search" />
				<input type="hidden" name="option" value="com_search" />
				<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
</div>
			</div>
		</form>
	</div> <!-- /.top-search-box -->	
</div>