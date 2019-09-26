<?php

//no direct access
defined('_JEXEC') or die('Restricted Access');

$params 	= JFactory::getApplication()->getTemplate(true)->params;

if( $params->get('social_share') ) {
	
	$url        =  JRoute::_(ContentHelperRoute::getArticleRoute($displayData->id . ':' . $displayData->alias, $displayData->catid, $displayData->language));
	$root       = JURI::base();
	$root       = new JURI($root);
	$url        = $root->getScheme() . '://' . $root->getHost() . $url;

echo '<div class="sp-social-share clearfix">';
	echo '<ul>';
	echo "<div class='social_block facebook_section'>".JLayoutHelper::render( 'joomla.content.social_share.social.facebook', array( 'item'=>$displayData, 'params'=>$params, 'url'=>$url ) )."</div>";
	echo "<div class='social_block twitter_section'>".JLayoutHelper::render( 'joomla.content.social_share.social.twitter', array( 'item'=>$displayData, 'params'=>$params, 'url'=>$url ) )."</div>";
	echo "<div class='social_block google_section'>".JLayoutHelper::render( 'joomla.content.social_share.social.google_plus', array( 'item'=>$displayData, 'params'=>$params, 'url'=>$url ) )."</div>";
	echo "<div class='social_block pinterest_section'>".JLayoutHelper::render( 'joomla.content.social_share.social.pinterest', array( 'item'=>$displayData, 'params'=>$params, 'url'=>$url ) )."</div>";
	echo '</ul>';
	echo '</div>';
}