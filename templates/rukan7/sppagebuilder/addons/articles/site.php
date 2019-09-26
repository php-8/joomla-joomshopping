<?php
defined ('_JEXEC') or die ('resticted aceess');

AddonParser::addAddon('sp_articles','sp_articles_addon');
require_once JPATH_ROOT . '/components/com_sppagebuilder/helpers/articles.php';

//Load Helix
$helix3_path = JPATH_PLUGINS.'/system/helix3/core/helix3.php';

if (file_exists($helix3_path)) {
    require_once($helix3_path);
    //helix3::addJS('matchheight.js'); // JS Files
}


function sp_articles_addon($atts){

	extract(spAddonAtts(array(
		'title' 					=> '',
		'heading_selector' 			=> 'h3',
		'title_fontsize' 			=> '',
		'title_fontweight' 			=> '',
		'title_text_color' 			=> '',
		'title_margin_top' 			=> '',
		'title_margin_bottom' 		=> '',	
		'catid' 					=> '',
		'ordering' 					=> 'latest',
		'limit' 					=> '',
		'columns' 					=> '',
		'hide_thumbnail' 			=> '',
		'show_intro' 				=> '',
		'intro_limit' 				=> '',
		'show_author' 				=> '',
		'show_category' 			=> '',
		'show_hits' 				=> '',
		'show_date' 				=> '',
		'show_readmore' 			=> '',
		'readmore_text' 			=> '',
		'link_articles' 			=> '',
		'all_articles_btn_text' 	=> '',
		'all_articles_btn_size' 	=> '',
		'all_articles_btn_type' 	=> '',
		'all_articles_btn_icon' 	=> '',
		'all_articles_btn_block'	=> '',
		'class' 					=> ''
		), $atts));

	$items = SppagebuilderHelperArticles::getArticles($limit, $ordering, $catid);

	if(count($items)) {

		$output  = '<div class="sppb-addon sppb-addon-articles ' . $class . '">';

		if($title) {

			$title_style = '';
			if($title_margin_top !='') $title_style .= 'margin-top:' . (int) $title_margin_top . 'px;';
			if($title_margin_bottom !='') $title_style .= 'margin-bottom:' . (int) $title_margin_bottom . 'px;';
			if($title_text_color) $title_style .= 'color:' . $title_text_color  . ';';
			if($title_fontsize) $title_style .= 'font-size:'.$title_fontsize.'px;line-height:'.$title_fontsize.'px;';
			if($title_fontweight) $title_style .= 'font-weight:'.$title_fontweight.';';

			$output .= '<'.$heading_selector.' class="sppb-addon-title" style="' . $title_style . '">' . $title . '</'.$heading_selector.'>';
		}

		$output .= '<div class="sppb-addon-content">';
		$output .= '<div class="sppb-row">';

		// Animation Delay
		$delay= 200;

		foreach ($items as $key => $item) {
			$output .= '<div class="sppb-col-sm-'. round(12/$columns) .' " data-sppb-wow-delay="' . $delay . 'ms">';
			$output .= '<div class="sppb-addon-article">';

			$output .= '<div class="sppb-article-img">';

			if(!$hide_thumbnail) {
				if($item->post_format=='gallery') {

					if(count($item->imagegallery->images)) {

						$output .= '<div class="sppb-carousel sppb-slide" data-sppb-ride="sppb-carousel">';
						$output .= '<div class="sppb-carousel-inner">';
						foreach ($item->imagegallery->images as $gallery_item) {
							$output .= '<div class="sppb-item">';
							$output .= '<img src="'. $gallery_item['thumbnail'] .'" alt="">';
							$output .= '</div>';
						}
						$output	.= '</div>';

						$output	.= '<a class="left sppb-carousel-control" role="button" data-slide="prev"><i class="fa fa-angle-left"></i></a>';
						$output	.= '<a class="right sppb-carousel-control" role="button" data-slide="next"><i class="fa fa-angle-right"></i></a>';
						
						$output .= '</div>';

					} elseif (isset($item->image_thumbnail) && $item->image_thumbnail) {
						$output .= '<a href="'. $item->link .'" itemprop="url"><img class="sppb-img-responsive" src="'. $item->image_thumbnail .'" alt="'. $item->title .'" itemprop="thumbnailUrl"></a>';
					}
				} else {
					if(isset($item->image_thumbnail) && $item->image_thumbnail) {
						$output .= '<a href="'. $item->link .'" itemprop="url"><img class="sppb-img-responsive" src="'. $item->image_thumbnail .'" alt="'. $item->title .'" itemprop="thumbnailUrl"></a>';
					}
				}
			}
			$output .= '<div class="maz_overlay_wrap">';
			$output .= '<div class="maz_overlay_wrap_box">';
			$output .= '<div>';
			$output .= '<a class="btn-view" href="'. $item->link .'" itemprop="url">' . '<i class="icon-link"></i>' .'</a>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';//end::article-img

			$output .= '<div class="sppb-article-info">';

			$output .= '<h3 class="title blog_title_eli"><a href="'. $item->link .'" itemprop="url">' . $item->title . '</a></h3>';

			if($show_date) {
				$output .= '<div class="sppb-meta-date" itemprop="dateCreated">' . Jhtml::_('date', $item->created, 'DATE_FORMAT_LC3') . '</div>';
				$output .= '<span class="sep_blog_info_front_float">/</span>';
			}

			if($show_author || $show_category) {
				$output .= '<div class="sppb-article-meta">';

				if($show_category) {
					$output .= '<span class="sppb-meta-category"> <a href="'. JRoute::_(ContentHelperRoute::getCategoryRoute($item->catslug)) .'" itemprop="genre">' . $item->category . '</a></span>';
					$output .= '<span class="sep_blog_info_front">/</span>';
				}

				

				if($show_hits) {
					$output .= '<span class="sppb-meta-hit" itemprop="hit">' . JText::_('SPPB_ARTICLE_HITS'). ' ' . $item->hits . '</span>';
				}

				$output .= '</div>';
			}

			$output .= '</div>';//end::article-info

			if($show_intro) {
				$text_handle = substr($item->introtext, 0, $intro_limit - 5);
				
				$output .= '<div class="sppb-article-introtext">'. $text_handle .' ... </div>';	
			}

			if($show_readmore) {
				$output .= '<a class="sppb-readmore" href="'. $item->link .'" itemprop="url">' . $readmore_text . '</a>';
			}

			$output .= '</div>';
			$output .= '</div>';
			$delay += 100;
		}
		$output .= '</div>';

		// See all link
		if($link_articles) {

			if($all_articles_btn_icon !='') {
				$all_articles_btn_text = '<i class="fa ' . $all_articles_btn_icon . '"></i> ' . $all_articles_btn_text;
			}

			$output  .= '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($catid)) . '" class="sppb-btn sppb-btn-' . $all_articles_btn_type . ' sppb-btn-' . $all_articles_btn_size . ' ' . $all_articles_btn_block . '" role="button">' . $all_articles_btn_text . '</a>';
		}

		$output .= '</div>';

		$output .= '</div>';

		return $output;

	}

	return true;

}