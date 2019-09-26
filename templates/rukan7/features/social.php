<?php
/**
 * @package Helix3 Framework
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2014 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
*/

defined ('_JEXEC') or die('resticted aceess');

class Helix3FeatureSocial {
	private $helix3;
	public $position;
	public function __construct( $helix3 ){
		$this->helix3 = $helix3;
		$this->position = $this->helix3->getParam('social_position');
	}
	public function renderFeature() {
		$facebook 	= $this->helix3->getParam('facebook');
		$twitter  	= $this->helix3->getParam('twitter');
		$googleplus = $this->helix3->getParam('googleplus');
		$pinterest 	= $this->helix3->getParam('pinterest');
		$youtube 	= $this->helix3->getParam('youtube');
		$linkedin 	= $this->helix3->getParam('linkedin');
		$dribbble 	= $this->helix3->getParam('dribbble');
		$behance 	= $this->helix3->getParam('behance');
		$skype 		= $this->helix3->getParam('skype');
		$flickr 	= $this->helix3->getParam('flickr');
		$vk 		= $this->helix3->getParam('vk');
		if( $this->helix3->getParam('show_social_icons') && ( $facebook || $twitter || $googleplus || $pinterest || $vk || $youtube || $linkedin || $dribbble || $behance || $skype || $flickr ) ) {
			$html  = '<ul class="social-icons">';
			if( $facebook ) {
				$html .= '<li><a target="_blank" href="'. $facebook .'"><i class="icon-social-facebook"></i></a></li>';
			}
			if( $twitter ) {
				$html .= '<li><a target="_blank" href="'. $twitter .'"><i class="icon-social-twitter"></i></a></li>';
			}
			if( $googleplus ) {
				$html .= '<li><a target="_blank" href="'. $googleplus .'"><i class="icon-social-google"></i></a></li>';
			}
			if( $pinterest ) {
				$html .= '<li><a target="_blank" href="'. $pinterest .'"><i class="icon-social-pinterest"></i></a></li>';
			}
			if( $youtube ) {
				$html .= '<li><a target="_blank" href="'. $youtube .'"><i class="icon-social-youtube"></i></a></li>';
			}
			if( $linkedin ) {
				$html .= '<li><a target="_blank" href="'. $linkedin .'"><i class="icon-social-linkedin"></i></a></li>';
			}
			if( $dribbble ) {
				$html .= '<li><a target="_blank" href="'. $dribbble .'"><i class="icon-social-dribbble"></i></a></li>';
			}
			if( $behance ) {
				$html .= '<li><a target="_blank" href="'. $behance .'"><i class="icon-social-behance"></i></a></li>';
			}
			if( $flickr ) {
				$html .= '<li><a target="_blank" href="'. $flickr .'"><i class="icon-social-flickr"></i></a></li>';
			}
			if( $vk ) {
				$html .= '<li><a target="_blank" href="'. $vk .'"><i class="icon-social-vk"></i></a></li>';
			}
			if( $skype ) {
				$html .= '<li><a href="skype:'. $skype .'?chat"><i class="icon-social-skype"></i></a></li>';
			}
			$html .= '</ul>';
			return $html;
		}
	}
}