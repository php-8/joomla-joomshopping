<?php
defined ('_JEXEC') or die('resticted aceess');

AddonParser::addAddon('sp_testimonialpro','sp_testimonialpro_addon');
AddonParser::addAddon('sp_testimonialpro_item','sp_testimonialpro_item_addon');

$sppbSlideArray = array();

function sp_testimonialpro_addon($atts, $content){

	global $sppbSlideArray;

	extract(spAddonAtts(array(
		'autoplay'=>'',
		'arrows'=>'',
		'controllers'=> true,
		"class"=>'',
		), $atts));

	AddonParser::spDoAddon($content);

	$carousel_autoplay = ($autoplay)?'data-sppb-ride="sppb-carousel"':'';

	$output  = '<div class="sppb-carousel sppb-testimonial-pro sppb-slide ' . $class . ' sppb-text-center" ' . $carousel_autoplay . '>';
	$output .= '<div class="sppb-carousel-icon">';
	$output .= '</div>';

	$output .= '<div class="sppb-carousel-maz-testi-inner">';

	foreach ($sppbSlideArray as $key => $slideItem) {

		$output  .= '<div class="sppb-item">';
		$output  .= '<div class="sppb-testimonial-message">';
		
			
			$output  .= '<p class="testi_message">' . $slideItem['message'] . '</p>';
			if($slideItem['title']) $output .= '<p class="testi_client">' . $slideItem['title'] . '</p>';
		$output .= '</div>';
			$output  .= '<span class="testimonials_arrow_down"></span>';
		
			$title = '<strong class="pro-client-name">'. $slideItem['title'] .'</strong>';

			if($slideItem['url']) $title .= ' - <span class="pro-client-url">'. $slideItem['url'] .'</span>';
		//if($slideItem['avatar']) $output .= '<img class="sppb-img-responsive sppb-avatar '. $slideItem['avatar_style'] .'" src="'. $slideItem['avatar'] .'" alt="">';
		$output  .= '</div>';
	}
	
	
	//$output .= AddonParser::spDoAddon($content);
	$output	.= '</div>';
	if($controllers) {
		$output .= '<ol class="sppb-carousel-indicators">';
			foreach ($sppbSlideArray as $key => $slideItem) {
				$active_item = ($key == 0) ? 'active' : '' ;
				$output .= '<li data-sppb-target=".sppb-testimonial-pro" class="sppb-tm-indicators ' . $active_item . '" data-sppb-slide-to="'. $key . '">';
					$output .= '<span><img src="' . $slideItem['avatar'] . '"/></span>';
				$output .= '</li>';
			}
		$output .= '</ol>';
	}
	if($arrows) {
		$output	.= '<a class="left sppb-carousel-control" role="button" data-slide="prev"><i class="fa fa-angle-left"></i></a>';
		$output	.= '<a class="right sppb-carousel-control" role="button" data-slide="next"><i class="fa fa-angle-right"></i></a>';
	}
	$output .= '</div>';
	$sppbSlideArray = array();

	return $output;

}

function sp_testimonialpro_item_addon( $atts ){

	global $sppbSlideArray;

	extract(spAddonAtts(array(
		"title"=>'',
		"avatar"=>'',
		"avatar_style"=>'',
		'message'=>'',
		"url"=>'',
		), $atts));

	$sppbSlideArray[] = array(
		'title'			=>$title,
		'avatar'		=>$avatar,
		'avatar_style'	=>$avatar_style,
		'message'		=>$message,
		'url'			=>$url

	);

	//return $output;

}