<?php
defined ('_JEXEC') or die ('restricted aceess');

AddonParser::addAddon('sp_person','sp_person_addon');

function sp_person_addon($atts) {

	extract(spAddonAtts(array(
		'image' 			=> '',
		'name' 				=> '',
		'designation' 		=> '',
		'introtext' 		=> '',
		'facebook' 			=> '',
		'twitter' 			=> '',
		'google_plus' 		=> '',
		'youtube' 			=> '',
		'linkedin' 			=> '',
		'pinterest' 		=> '',
		'flickr' 			=> '',
		'dribbble' 			=> '',
		'behance' 			=> '',
		'instagram' 		=> '',
		'social_position' 	=> '',
		'alignment' 		=> '',
		'class' 			=> '',
		), $atts));

	$output = '';
	$social_icons = '';

	if($facebook || $twitter || $google_plus || $youtube || $linkedin || $pinterest || $flickr || $dribbble || $behance || $instagram) {
		$social_icons  	.= '<div class="sppb-person-social-icons social-position-'. $social_position .'">';
		$social_icons 	.= '<ul class="sppb-person-social">';

		if($facebook) 		$social_icons .= '<li><a target="_blank" href="' . $facebook . '"><i class="icon-social-facebook"></i></a></li>';
		if($twitter) 		$social_icons .= '<li><a target="_blank" href="' . $twitter . '"><i class="icon-social-twitter"></i></a></li>';
		if($google_plus) 	$social_icons .= '<li><a target="_blank" href="' . $google_plus . '"><i class="icon-social-google"></i></a></li>';
		if($youtube) 		$social_icons .= '<li><a target="_blank" href="' . $youtube . '"><i class="icon-social-youtube"></i></a></li>';
		if($linkedin) 		$social_icons .= '<li><a target="_blank" href="' . $linkedin . '"><i class="icon-social-linkedin"></i></a></li>';
		if($pinterest) 		$social_icons .= '<li><a target="_blank" href="' . $pinterest . '"><i class="icon-social-pinterest"></i></a></li>';
		if($flickr) 		$social_icons .= '<li><a target="_blank" href="' . $flickr . '"><i class="icon-social-github"></i></a></li>';
		if($dribbble) 		$social_icons .= '<li><a target="_blank" href="' . $dribbble . '"><i class="icon-social-dribbble"></i></a></li>';
		if($behance) 		$social_icons .= '<li><a target="_blank" href="' . $behance . '"><i class="icon-social-behance"></i></a></li>';
		if($instagram) 		$social_icons .= '<li><a target="_blank" href="' . $instagram . '"><i class="icon-social-instagram"></i></a></li>';

		$social_icons 	.= '</ul>';
		$social_icons 	.= '</div>';
	}


	if($image) {
		$output  .= '<div class="sppb-addon sppb-addon-persion ' . $alignment . ' ' . $class . '">';
		$output  .= '<div class="sppb-addon-content">';

		$output  .= '<div class="sppb-person-image">';
		$output  .= '<img class="sppb-img-responsive" src="' . $image . '" alt="">';

		if($social_position=='before') $output .= $social_icons;

		$output  .= '</div>';

		if($name || $designation) {
			$output  .= '<div class="sppb-person-information">';
			if($name) $output  .= '<div class="sppb-person-name">' . $name . '</div> <div class="rukan-sep-small-dark rukan-sep-center rukan-sep-small"></div>';
			if($designation) $output  .= '<div class="sppb-person-designation">' . $designation . '</div>';
			$output  .= '</div>';
		}
		
		if($introtext) $output  .= '<div class="sppb-person-introtext">' . $introtext . '</div>';

		if($social_position=='after') $output .= $social_icons;

		$output  .= '</div>';
		$output  .= '</div>';

		return $output;

	}

	return;

}