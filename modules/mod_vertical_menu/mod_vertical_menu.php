<?php
/**
 * mod_vertical_menu - Vertical Menu
 *
 * @author    Balint Polgarfi
 * @copyright 2014-2019 Offlajn.com
 * @license   https://gnu.org/licenses/gpl-2.0.html
 * @link      https://offlajn.com
 */

defined('_JEXEC') or die('Restricted access');

$revision = '4.0.270';
$er = error_reporting();
if ($er & E_STRICT || $er & E_DEPRECATED)
	error_reporting($er & ~E_STRICT & ~E_DEPRECATED);

$get = JRequest::get('GET');
$get['x'] = 0;
$get['stack'] = array();

global $MiniImageHelper, $bgHelper;

if (version_compare(JVERSION, '3.0.0', 'l') && !function_exists('offlajn_jimport')){
	function offlajn_jimport($key, $base = null){
		return jimport($key);
	}
}

call_user_func(function_exists('offlajn_jimport') ? 'offlajn_jimport' : 'offlajnjimport', 'joomla.html.parameter');

@ini_set('memory_limit','260M');
@ini_set('xdebug.max_nesting_level', 300);

if (!extension_loaded('gd') || !function_exists('gd_info')) {
	echo "Vertical Menu needs the <a href='http://php.net/manual/en/book.image.php'>GD module</a> enabled in your PHP runtime
	environment. Please consult with your System Administrator and he will
	enable it!";
	return;
}

require_once(dirname(__FILE__).'/helpers/functions.php');
JFactory::getLanguage()->load('com_finder');

$module->navClassPrefix = 'off-nav-';
$module->instanceid = 'off-menu_'.$module->id;

// init params
$params->loadArray(o_flat_array($params->toArray()));
$params->set('parentlink', 1);
$params->set('moduleshowtitle', 1);
$params->set('gradientheight', '400||px');

$position = OfflajnParser::parse($params->get('position'));
if ($params->get('forcestyle', 1)
|| ($params->get('openbutton') == 'corner' && $position[0] != 'module'))
	$attribs['style'] = 'none';

// Loading the right class for the menu type
$type = preg_replace("/[^A-Za-z0-9]/", '', $params->get('menutype'));
if($type == '' or !file_exists(dirname(__FILE__).'/types/'.$type.'/menu.php')){
	echo JText::_('Menu type not exists!');
	return;
}

require_once(dirname(__FILE__).'/types/'.$type.'/menu.php');

$class = 'Offlajn'.ucfirst($type).'Menu2';
if(!class_exists($class)) return;
$menu = new $class($module, $params);
$menu->generateItems();

// Loading the template file for the theme
$templateDir = dirname(__FILE__).'/template/';

$theme = $params->get('theme', 'default');
$filter = OfflajnParser::parse($params->get('filter'), '1|*|500||ms|*|3|*|');
$tmpl = $templateDir.$theme.'.php';

if(!file_exists($tmpl)){
	$tmpl = $templateDir.'default.php';
	if(!file_exists($tmpl)){
		echo JText::_('Template file missing. Please reinstall the module.');
		return;
	}
}

// Loading the template container file for the theme
$containerTmpl = $templateDir.$theme.'-cont.php';

if(!file_exists($containerTmpl)){
	$containerTmpl = $templateDir.'default-cont.php';
	if(!file_exists($containerTmpl)){
		echo JText::_('Template file missing. Please reinstall the module.');
		return;
	}
}
?>
<div class="noscript">
	<?php include($containerTmpl); // Render the menu ?>
</div>
<?php
require_once(dirname(__FILE__).'/classes/cache.class.php');
$cache = new OfflajnMenuThemeCache2('default', $module, $params);

$document = JFactory::getDocument();

// Build the CSS
$cache->addCss(dirname(__FILE__).'/themes/clear.css.php');
$cache->addCss(dirname(__FILE__).'/themes/'.$theme.'/theme.css.php');
$cache->addStyle($params->get('custom_css'));

// Load image helper
require_once(dirname(__FILE__).'/classes/ImageHelper.php');

// Set up enviroment variables for the cache generation
$module->url = JUri::root(true).'/modules/'.$module->module.'/';
$oh7 = new OfflajnHelper7($cache->cachePath, $cache->cacheUrl);
$cache->addCssEnvVars('module', $module);
$cache->addCssEnvVars('helper', $oh7);

$MiniImageHelper = new OfflajnMiniImageHelper($cache->cachePath, $cache->cacheUrl);

// Add cached contents to the document
$cacheFiles = $cache->generateCache();
$document->addStyleSheet($cacheFiles[0]);
$document->addStyleDeclaration('
.noscript div#'.$module->instanceid.' dl.level1 dl{
	position: static;
}
.noscript div#'.$module->instanceid.' dl.level1 dd.parent{
	height: auto !important;
	display: block;
	visibility: visible;
}
');
if ($params->get('fontawesome', 0)) {
	$document->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
}

$root = JURI::root(true);
if ($root != '/') $root.= '/';

// Add scripts
if (version_compare(JVERSION, '3.0.0', 'l')) {
	if ($params->get('jquery', 1)) {
		$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js');
		$document->addScript($root.'media/offlajn/jquery.noconflict.js');
	}
} else JHtml::_('jquery.framework');

$document->addScript('https://cdnjs.cloudflare.com/ajax/libs/gsap/1.18.2/TweenMax.min.js');

$trans_in = OfflajnParser::parse($params->get('transition_in'), 'Quad.easeOut|*|50% 50% 0|*|100|*|%|*|0|*|100||%|*|0||°|*|0||°|*|0||°|*|0||°|*|0||°|*|100||%|*|100||%');
$trans_out = OfflajnParser::parse($params->get('transition_out'), 'Quad.easeOut|*|50% 50% 0|*|-100|*|%|*|0|*|100||%|*|0||°|*|0||°|*|0||°|*|0||°|*|0||°|*|100||%|*|100||%');
$drop_dur = OfflajnParser::parse($params->get('dropdur'), '300||ms|*|300||ms');
$drop_in = OfflajnParser::parse($params->get('drop_in'), 'Quad.easeOut|*|50% 50% 0|*|-30|*|px|*|0||px|*|0||%|*|0||°|*|0||°|*|0||°|*|0||°|*|0||°|*|100||%|*|100||%');
$drop_out = OfflajnParser::parse($params->get('drop_out'), 'Quad.easeOut|*|50% 50% 0|*|20|*|px|*|0||px|*|0||%|*|0||°|*|0||°|*|0||°|*|0||°|*|0||°|*|100||%|*|100||%');
$anim_mi = OfflajnParser::parse($params->get('animatemenuitem'), '0|*|500||ms|*|40||ms');
$trans_mi = OfflajnParser::parse($params->get('menuitemtrans'), 'Quad.easeOut|*|50% 50% 0|*|40|*|%|*|0||px|*|0||%|*|0||°|*|0||°|*|0||°|*|0||°|*|0||°|*|100||%|*|100||%');
$back = OfflajnParser::parse($params->get('back'), '0|*|Back');
$bg = OfflajnParser::parse($params->get('bg'));
$icon = OfflajnParser::parse($params->get('sidebar_icon'), '#eeeeee|*|rgba(0, 0, 0, 0.53)|*|50||px|*|0||px|*|0||px');
$drop = OfflajnParser::parse($params->get('drop'), '250||px|*|0||px|*|0|*|mouseenter');
$visibility = OfflajnParser::parse($params->get('visibility'), '1|*|1|*|1|*|1|*|0|*|0||px|*|10000||px');
$autoopen = OfflajnParser::parse($params->get('autoopen'), '0|*|1');

//get link for the logo image
$logoLink = OfflajnParser::parse($params->get('logolink'));
$logoUrl = "";
if (!empty($logoLink[0]) && $logoLink[0] != "custom") {
  $menu = JFactory::getApplication()->getMenu();
  $item = $menu->getItem($logoLink[0]);
  if ($item) $logoUrl = JRoute::_($item->link.(strpos($item->link, '?')?'&':'?').'Itemid='.$logoLink[0], false);
} elseif (!empty($logoLink[1]) && $logoLink[0] == "custom") {
  $logoUrl = $logoLink[1];
}

$instance = "
document[(_el=document.addEventListener)?'addEventListener':'attachEvent'](_el?'DOMContentLoaded':'onreadystatechange',function(){
	if (!_el && document.readyState != 'complete') return;
	(window.jq183||jQuery)('.noscript').removeClass('noscript');
	window.sm{$module->id} = new VerticalSlideMenu({
		id: $module->id,
		visibility: ".json_encode($visibility).",
		parentHref: {$params->get('parenthref', 0)},
		theme: '{$params->get('theme', 'flat')}',
		result: '".addcslashes(JText::_('COM_FINDER_DEFAULT_PAGE_TITLE'), "'")."',
		noResult: '".addcslashes(JText::_('COM_FINDER_SEARCH_NO_RESULTS_HEADING'), "'")."',
		backItem: '".($back[0] ? addcslashes($back[1], "'") : "")."',
		filterDelay: {$filter[1][0]},
		filterMinChar: {$filter[2]},
		navtype: '{$params->get('navtype', 'slide')}',
		sidebar: ".(preg_match('/leftbar|rightbar/', $position[0]) ? ($position[0][0] == 'r' ? -1 : 1) : 0).",
		popup: ".($position[0] == 'popup' ? 1 : 0).",
		overlay: ".($position[0] == 'overlay' ? 1 : 0).",
		sidebarUnder: {$position[1][0]},
		width: {$position[2][0]},
		menuIconCorner: ".($params->get('openbutton') == 'corner' ? 1 : 0).",
		menuIconX: ".($position[0] == 'rightbar' ? -$icon[5][0] : $icon[3][0]).",
		menuIconY: {$icon[4][0]},
		hidePopupUnder: ".(int)$params->get('hidepopupunder', 0).",
		siteBg: '{$params->get('sitebg')}',
		effect: ".$params->get($position[0] == 'overlay' ? 'overlay_anim' : 'sidebar_anim', 1).",
    dur: ".(int)$params->get('duration', 450)."/1000,
		perspective: ".(int)$params->get('perspective', 0).",
		inEase: '{$trans_in[0]}'.split('.').reverse().join(''),
		inOrigin: '{$trans_in[1]}',
		inX: {$trans_in[2]},
		inUnitX: '{$trans_in[3]}',
    logoUrl: '{$logoUrl}',
		inCSS: {
			y: {$trans_in[4][0]},
			opacity: {$trans_in[5][0]}/100,
			rotationX: {$trans_in[6][0]},
			rotationY: {$trans_in[7][0]},
			rotationZ: {$trans_in[8][0]},
			skewX: {$trans_in[9][0]},
			skewY: {$trans_in[10][0]},
			scaleX: {$trans_in[11][0]}/100,
			scaleY: {$trans_in[12][0]}/100
		},
		outEase: '{$trans_out[0]}'.split('.').reverse().join(''),
		outOrigin: '{$trans_out[1]}',
		outX: {$trans_out[2]},
		outUnitX: '{$trans_out[3]}',
		outCSS: {
			y: {$trans_out[4][0]},
			opacity: {$trans_out[5][0]}/100,
			rotationX: {$trans_out[6][0]},
			rotationY: {$trans_out[7][0]},
			rotationZ: {$trans_out[8][0]},
			skewX: {$trans_out[9][0]},
			skewY: {$trans_out[10][0]},
			scaleX: {$trans_out[11][0]}/100,
			scaleY: {$trans_out[12][0]}/100
		},
		anim: {
			perspective: ".(int)$params->get('droppersp', 0).",
			inDur: {$drop_dur[0][0]}/1000,
			inEase: '{$drop_in[0]}'.split('.').reverse().join(''),
			inOrigin: '{$drop_in[1]}',
			inX: {$drop_in[2]},
			inUnitX: '{$drop_in[3]}',
			inCSS: {
				y: {$drop_in[4][0]},
				opacity: {$drop_in[5][0]}/100,
				rotationX: {$drop_in[6][0]},
				rotationY: {$drop_in[7][0]},
				rotationZ: {$drop_in[8][0]},
				skewX: {$drop_in[9][0]},
				skewY: {$drop_in[10][0]},
				scaleX: {$drop_in[11][0]}/100,
				scaleY: {$drop_in[12][0]}/100
			},
			outDur: {$drop_dur[1][0]}/1000,
			outEase: '{$drop_out[0]}'.split('.').reverse().join(''),
			outOrigin: '{$drop_out[1]}',
			outX: {$drop_out[2]},
			outUnitX: '{$drop_out[3]}',
			outCSS: {
				y: {$drop_out[4][0]},
				opacity: {$drop_out[5][0]}/100,
				rotationX: {$drop_out[6][0]},
				rotationY: {$drop_out[7][0]},
				rotationZ: {$drop_out[8][0]},
				skewX: {$drop_out[9][0]},
				skewY: {$drop_out[10][0]},
				scaleX: {$drop_out[11][0]}/100,
				scaleY: {$drop_out[12][0]}/100
			}
		},
		miAnim: {$anim_mi[0]},
		miDur: {$anim_mi[1][0]}/1000,
		miShift: {$anim_mi[2][0]}/1000,
		miEase: '{$trans_mi[0]}'.split('.').reverse().join(''),
		miX: {$trans_mi[2]},
		miUnitX: '{$trans_mi[3]}',
		miCSS: {
			transformPerspective: 600,
			transformOrigin: '{$trans_mi[1]}',
			y: {$trans_mi[4][0]},
			opacity: {$trans_mi[5][0]}/100,
			rotationX: {$trans_mi[6][0]},
			rotationY: {$trans_mi[7][0]},
			rotationZ: {$trans_mi[8][0]},
			skewX: {$trans_mi[9][0]},
			skewY: {$trans_mi[10][0]},
			scaleX: {$trans_mi[11][0]}/100,
			scaleY: {$trans_mi[12][0]}/100
		},
		iconAnim: {$params->get('menu_images', 0)} && {$params->get('iconanim', 0)},
		bgX: ".(!empty($bg[1]) ? $bg[2][0] : 0).",
		dropwidth: {$drop[0][0]},
		dropspace: {$drop[1][0]},
		dropFullHeight: {$drop[2]},
		dropEvent: '{$drop[3]}',
		opened: {$params->get('opened', 1)},
		autoOpen: {$autoopen[0]},
		autoOpenAnim: {$autoopen[1]},
		hideBurger: {$params->get('hideburger', 0)}
	});
});\n";

$custom_js = !$params->get('custom_js') ? '' : "
document[(_el=document.addEventListener)?'addEventListener':'attachEvent'](_el?'DOMContentLoaded':'onreadystatechange',function(){
	if (!_el && document.readyState != 'complete') return;
	var $ = window.jq183||jQuery, sm = sm{$module->id};
	{$params->get('custom_js')}
});\n";

$ignoreJS = $params->get('ignorejs', 1) ? ' data-cfasync="false"' : '';
$v = $params->get('nojscache', 0) || !isset($revision) ? '_='.time() : 'v='.$revision;
$document->addScript($root."modules/{$module->module}/js/perfect-scrollbar.js?$v");
$document->addScript($root."modules/{$module->module}/js/{$module->module}.js?$v");
$document->addCustomTag("<script$ignoreJS>$instance</script>");
if ($custom_js) $document->addCustomTag("<script$ignoreJS>$custom_js</script>");
