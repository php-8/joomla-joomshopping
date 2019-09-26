<?php
/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2016 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('restricted aceess');

class SppagebuilderAddonCarousel extends SppagebuilderAddons {

	public function render() {

		$class = (isset($this->addon->settings->class) && $this->addon->settings->class) ? ' ' . $this->addon->settings->class : '';

		//Addons option
		$autoplay = (isset($this->addon->settings->autoplay) && $this->addon->settings->autoplay) ? ' data-sppb-ride="sppb-carousel"' : 0;
		$controllers = (isset($this->addon->settings->controllers) && $this->addon->settings->controllers) ? $this->addon->settings->controllers : 0;
		$arrows = (isset($this->addon->settings->arrows) && $this->addon->settings->arrows) ? $this->addon->settings->arrows : 0;
		$alignment = (isset($this->addon->settings->alignment) && $this->addon->settings->alignment) ? $this->addon->settings->alignment : 0;
		$carousel_autoplay = ($autoplay) ? ' data-sppb-ride="sppb-carousel"':'';
		$output  = '<div class="carousel_block">';
		$output  .= '<div id="sppb-carousel-'. $this->addon->id .'" class="sppb-carousel sppb-slide' . $class . '"'. $carousel_autoplay .'>';

		if($controllers) {
			$output .= '<ol class="sppb-carousel-indicators">';
				foreach ($this->addon->settings->sp_carousel_item as $key1 => $value) {
					$output .= '<li data-sppb-target="#sppb-carousel-'. $this->addon->id .'" '. (($key1 == 0) ? ' class=""': '' ) .'  data-sppb-slide-to="'. $key1 .'"></li>' . "\n";
				}
			$output .= '</ol>';
		}
if($arrows) {
			$output	.= '<a href="#sppb-carousel-'. $this->addon->id .'" class="sppb-carousel-arrow left sppb-carousel-control" data-slide="prev"><i class="icon-arrow-left"></i></a>';
			}
		$output .= '<div class="sppb-carousel-maz-inner owl-carousel-brands ' . $alignment . '">';

		foreach ($this->addon->settings->sp_carousel_item as $key => $value) {
			if($value->button_url){ $output   .= '<a href="' . $value->button_url . '" target="' . $button_target . '">';}
			$output   .= '<div class="sppb-item '. (($value->bg) ? ' sppb-item-has-bg' : '') . (($key == 0) ? ' active' : '') .'">';
			$output  .= ($value->bg) ? '<img src="' . $value->bg . '" alt="' . $value->title . '">' : '';

			$output  .= '</div>';
			if($value->button_url){ $output  .= '</a>';}
		}

		$output	.= '</div>';

		if($arrows) {
			$output	.= '<a href="#sppb-carousel-'. $this->addon->id .'" class="sppb-carousel-arrow right sppb-carousel-control" data-slide="next"><i class="icon-arrow-right"></i></a>';
		}

		$output .= '</div>';
		$output  .= '</div>';
		return $output;
	}

	public function css() {
		$addon_id = '#sppb-addon-' . $this->addon->id;
		$layout_path = JPATH_ROOT . '/components/com_sppagebuilder/layouts';
		$css = '';

		// Buttons style
		foreach ($this->addon->settings->sp_carousel_item as $key => $value) {
			if($value->button_text) {
				$css_path = new JLayoutFile('addon.css.button', $layout_path);
				$css .= $css_path->render(array('addon_id' => $addon_id, 'options' => $this->addon->settings, 'id' => 'btn-' . ($this->addon->id + $key) ));
			}
		}

		return $css;
	}
}
