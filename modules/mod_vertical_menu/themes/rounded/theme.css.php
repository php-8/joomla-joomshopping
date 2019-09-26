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

$position = OfflajnParser::parse($params->get('position'));
$opened = $params->get('opened') && preg_match('/tree|accordion/', $params->get('navtype'));
$p = OfflajnParser::parse($params->get('level1padding'));
$bg = OfflajnParser::parse($params->get('bg'));
$bs = OfflajnParser::parse($params->get('boxshadow'));
?>

#<?php echo $mod?> {
	position: relative;
	margin: <?php echo OfflajnParser::parseUnit($params->get('margin'), ' ') ?>;
}
.sm-container > #<?php echo $mod?> {
	position: absolute;
	margin: 0;
	width: <?php echo $position[2][0] ?>px;
}

.<?php echo $mod?> .sm-levels > .sm-level > dl,
.<?php echo $mod?> > .sm-level dl {
	padding: <?php echo (int)$params->get('menupadding') ?>px;
}
nav#<?php echo $mod?> h3.sm-head {
	margin-top: <?php echo (int)$params->get('menupadding') ?>px;
}
#<?php echo $mod?> .sm-head ~ .sm-levels > .sm-level > dl {
	padding-top: 0;
}
.<?php echo $mod?> > .sm-level {
	margin-top: -<?php echo (int)$params->get('menupadding') ?>px;
	border-radius: <?php echo OfflajnParser::parseUnit($params->get('borderradius'), ' ') ?>;
	box-shadow: <?php echo implode(' ', array($bs[0][0].$bs[0][1], $bs[1][0].$bs[1][1], $bs[2][0].$bs[2][1], $bs[3][0].$bs[3][1], $bs[4])) ?>;
}

<?php if ($params->get('xicon', '0')[0]): ?>
.sm-parent > .<?php echo $mod?> .sm-x { display: none; }
.<?php echo $mod?> .sm-x {
	position: absolute;
	top: 10px;
	width: 14px;
	height: 14px;
	z-index: 100;
	cursor: pointer;
}
.<?php echo $mod?> .sm-x:before,
.<?php echo $mod?> .sm-x:after {
	content: '';
	position: absolute;
	width: 11px;
	height: 0px;
	border: 2px solid;
	border-radius: 2px;
	box-sizing: content-box;
}
.<?php echo $mod?> .sm-x:before {
	left: 3px;
	transform-origin: 0 0;
	transform: rotate(45deg);
}
.<?php echo $mod?> .sm-x:after {
	right: 3px;
	transform-origin: 100% 0;
	transform: rotate(-45deg);
}
<?php endif ?>

<?php if ($position[0] == 'overlay'): ?>
.<?php echo $overlay ?> .sm-overlay-win:before {
	content: "";
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: <?php echo $bg[0] ?>;
}
.<?php echo $overlay ?> #<?php echo $mod?> .sm-head {border: 0}
	<?php if ( preg_match('~\.(jpe?g|png|gif|bmp|svg)$~i', $params->get('bgimg')) ): ?>
	.<?php echo $overlay ?> .sm-overlay-win {
		background-image: url('<?php echo $root.preg_replace('~.*(/modules/)~', '$1', $params->get('bgimg')) ?>');
		background-size: cover;
		background-position: center;
	}
	<?php endif ?>
<?php else: ?>
.<?php echo $mod?> > .sm-level:before,
#<?php echo $mod?>:before {
	content: "";
	position: absolute;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
	background: <?php echo $bg[0] ?>;
}
#<?php echo $mod?> .sm-logo img {
	border-right: 1px solid transparent;
}
	<?php if ( preg_match('~\.(jpe?g|png|gif|bmp|svg)$~i', $params->get('bgimg')) ): ?>
.<?php echo $mod?> > .sm-level,
#<?php echo $mod?> {
	background-image: url('<?php echo $root.preg_replace('~.*(/modules/)~', '$1', $params->get('bgimg')) ?>');
	background-size: cover;
	background-position: 0 0;
}
	<?php endif ?><?php if ($params->get('filter') > 0): ?>
#<?php echo $mod?> .sm-filter-cont {
	position: relative;
	padding: <?php echo (int)$params->get('menupadding') ?>px;
	padding-bottom: calc(<?php echo (int)$params->get('menupadding') ?>px / 2);
}
#<?php echo $mod?> .sm-head ~ .sm-filter-cont {
	padding-top: 0;
}
#<?php echo $mod?> .sm-head ~ .sm-filter-cont .sm-filter-icon,
#<?php echo $mod?> .sm-head ~ .sm-filter-cont .sm-reset {
	top: calc(<?php echo (int)$params->get('menupadding') ?>px / -4);
}
.<?php echo $mod?> .sm-level > dl.level1 {
	padding-top: calc(<?php echo (int)$params->get('menupadding') ?>px / 2);
}
#<?php echo $mod?> input.sm-filter[type=text] {
	<?php
	$fonts->printFont('level1font', 'Text');
	$m = OfflajnParser::parse($params->get('level1margin'), '7|*|12|*|7|*|12|*|px');
	?>
	width: calc(100% - <?php echo $m[1] ?>px - <?php echo $m[3] ?>px);
	height: auto;
	<?php $font = $params->get('level1font') ?>
	padding: <?php echo "{$p[0]}px {$p[1]}px {$p[2]}px ".($p[3]+(float)$font['Text']['size']*1.8) ?>px;
	<?php $filter = OfflajnParser::parse($params->get('filtercolor')) ?>
	background: <?php echo $filter[0] ?>;
	color: <?php echo $filter[1] ?>;
	border: 0;
	margin: <?php echo OfflajnParser::parseUnit($params->get('level1margin', '7|*|12|*|7|*|12|*|px'), ' ') ?>;
	border-radius: <?php echo OfflajnParser::parseUnit($params->get('level1br', '4|*|4|*|4|*|4|*|px'), ' ') ?>;
	box-sizing: border-box;
	box-shadow: none;
}
#<?php echo $mod?> input.sm-filter[type=text]:focus {
	box-shadow: none;
}
/* filter placeholder */
#<?php echo $mod?> input.sm-filter[type=text]::-webkit-input-placeholder {
	opacity: 1;
	color: <?php echo $filter[1] ?>;
}
#<?php echo $mod?> input.sm-filter[type=text]::-moz-placeholder {
	opacity: 1;
	color: <?php echo $filter[1] ?>;
}
#<?php echo $mod?> input.sm-filter[type=text]:-ms-input-placeholder {
	opacity: 1;
	color: <?php echo $filter[1] ?>;
}
#<?php echo $mod?> input.sm-filter[type=text]:focus::-webkit-input-placeholder { opacity: 0.75 }
#<?php echo $mod?> input.sm-filter[type=text]:focus::-moz-placeholder { opacity: 0.75 }
#<?php echo $mod?> input.sm-filter[type=text]:focus:-ms-input-placeholder { opacity: 0.75 }

#<?php echo $mod?> .sm-filter-icon,
#<?php echo $mod?> .sm-reset {
	position: absolute;
	top: calc(<?php echo (int)$params->get('menupadding') ?>px / 4);
	height: 100%;
	width: <?php echo (float)$font['Text']['size'] ?>px;
	color: <?php echo $filter[1] ?>;
}
#<?php echo $mod?> .sm-filter-icon {
	left: <?php echo (int)$params->get('menupadding')+$m[3] ?>px;
	margin-left: <?php echo $p[3] ?>px;
	pointer-events: none;
}
#<?php echo $mod?> .sm-reset {
	right: <?php echo (int)$params->get('menupadding')+$m[1] ?>px;
	margin-right: <?php echo $p[1] ?>px;
	cursor: pointer;
	-webkit-transition: all 300ms;
	transition: all 300ms;
}
#<?php echo $mod?> input[value=""].sm-filter ~ .sm-reset {
	opacity: 0;
	-webkit-transform: scale(0);
	-ms-transform: scale(0);
	transform: scale(0);
}
<?php endif ?><?php endif ?>

#<?php echo $mod?> .sm-levels {
	height: 100%;
}

<?php $resize = OfflajnParser::parse($params->get('resizeicon')) ?>

.<?php echo $clear ?> .sm-icon {
	display: table-cell;
	width: <?php echo $resize[1][0]+10 ?>px;
	text-align: center;
	vertical-align: top;
	padding: 0 10px 0 0;
}

.<?php echo $clear ?> .sm-icon img {
	margin: 0;
	border-radius: <?php echo OfflajnParser::parseUnit($params->get('iconborderradius'), ' ') ?>;
}

.<?php echo $clear ?> .inner{
	display: table-cell;
	vertical-align: middle;
}

.<?php echo $clear ?> dt {
	display: table;
	table-layout: fixed;
	background-clip: padding-box;
	width: 100%;
	<?php if (!$params->get('parenthref')): ?>
	cursor: pointer;
	<?php endif ?>
}

<?php if ($params->get('parenthref')): ?>
.<?php echo $clear ?> dt .desc,
.<?php echo $clear ?> dt .link {
	cursor: default;
}
<?php else: ?>
.<?php echo $clear ?> dt.parent a {
	pointer-events: none;
}
<?php endif ?>

#<?php echo $mod?> h3.sm-head {
	background: <?php echo $params->get('titlebg') ?>;
	<?php $titlefont = $params->get($position[0] == 'overlay' ? 'otitlefont' : 'titlefont') ?>
	height: <?php echo (int)$titlefont['Text']['lineheight'] ?>px;
	padding: 0;
	margin: 0;
	border: 0;
<?php if ($params->get('filter', '1')[0] || $params->get('navtype') == 'slider'): ?>
	text-align: center;
}
<?php else: ?>
}
#<?php echo $mod?> .sm-title { display: block; }
<?php endif ?>

#<?php echo $mod?> h3.sm-head span {
	<?php $fonts->printFont($position[0] == 'overlay' ? 'otitlefont' : 'titlefont', 'Text') ?>
}

.<?php echo $clear ?> .link {
	position: relative;
	display: block;
	padding-right: <?php echo $params->get('displaynumprod') > 0 ? 35 : 0 ?>px;
}

/* Productnum */
<?php if ($params->get('displaynumprod') > 0): ?>
.<?php echo $clear ?> .productnum {
	position: absolute;
	right: 5px;
	top: 50%;
	-webkit-transform: translateY(-50%);
	-ms-transform: translate(0, -50%);
	transform: translateY(-50%);
	display: block;
	padding: 0 0.8em;
	height: 1.5em;
	line-height: 1.5em !important;
	border-radius: .8em;
	box-shadow: 0 0 0 1px rgba(255,255,255,0.12); /* !!! */
}
.<?php echo $clear ?> .productnum.more {
	padding: 0 0.6em;
}
<?php endif ?>

#<?php echo $mod?> .sm-title,
#<?php echo $mod?> .sm-back {
	position: absolute;
	left: 0;
	padding: 0 <?php echo $p[3] ?>px;
	white-space: nowrap;
	text-overflow: ellipsis;
	overflow: hidden;
	max-width: 80%;
}
#<?php echo $mod?> .sm-title:nth-child(2) {
	max-width: 100%;
	position: static;
}

#<?php echo $mod?> .sm-back {
	cursor: pointer;
	padding: 0 0 0 <?php echo $p[3] ?>px;
	-webkit-transform-origin: <?php echo $p[3] ?>px 50%;
	transform-origin: <?php echo $p[3] ?>px 50%;
}
#<?php echo $mod?> .sm-back.sm-arrow {
	display: none;
	opacity: 0;
	width: calc(<?php echo (float)$titlefont['Text']['size'] ?>px * 0.8);
	height: 100%;
	color: <?php echo $titlefont['Text']['color'] ?>;
	box-sizing: content-box;
}

.<?php echo $clear ?> .ps-scrollbar-y-rail {
	right: 0 !important;
}

.<?php echo $clear ?> .sm-level{
	height: 100%;
}

#<?php echo $mod?> {
	border-radius: <?php echo OfflajnParser::parseUnit($params->get('borderradius'), ' ') ?>;
}
.menu-icon-cont > #<?php echo $mod?> {
	overflow: hidden;
	box-shadow: <?php echo implode(' ', array($bs[0][0].$bs[0][1], $bs[1][0].$bs[1][1], $bs[2][0].$bs[2][1], $bs[3][0].$bs[3][1], $bs[4])) ?>;
}

.<?php echo $clear ?> dl dt > .sm-arrow {
	display: table-cell;
	vertical-align: middle;
	width: 1em;
}
.<?php echo $clear ?> .sm-level dl dt svg {
	display: block;
	height: 1em;
	width: 1em;
}
.<?php echo $clear ?>.sm-tree dt svg,
.<?php echo $clear ?>.sm-tree dt use + use {
	-webkit-transition: -webkit-transform <?php echo (int)$params->get('duration') ?>ms;
	transition: transform <?php echo (int)$params->get('duration') ?>ms;
}
.<?php echo $clear ?>.sm-tree .opened svg {
	-webkit-transform: rotateZ(90deg);
	-ms-transform: rotate(90deg);
	transform: rotateZ(90deg);
}
.<?php echo $clear ?>.sm-tree dt use + use {
	transform-origin: 50% 50%;
	transform: rotateZ(90deg);
}
.<?php echo $clear ?>.sm-tree .opened use + use {
	transform: rotateZ(180deg);
}

/*** Level specific iteration ***/
<?php $i=1; do {

$textfont = $position[0] == 'overlay' ? "level{$i}ofont" : "level{$i}font";
$descfont = $position[0] == 'overlay' ? "level{$i}odescfont" : "level{$i}descfont"; ?>

<?php $f = $params->get($textfont) ?>
.<?php echo $clear ?> dt .link,
.<?php echo $clear ?> dt.level<?php echo $i?> .link {
	text-align: <?php echo $f['Text']['align'] ?>;
}
<?php if (isset($f['Hover']['align'])): ?>
.<?php echo $clear ?> dt:hover .link,
.<?php echo $clear ?> dt.level<?php echo $i?>:hover .link {
	text-align: <?php echo $f['Hover']['align'] ?>;
}
<?php endif ?>
<?php if (isset($f['Active']['align'])): ?>
.<?php echo $clear ?> dt.active .link,
.<?php echo $clear ?> dt.active.level<?php echo $i?> .link {
	text-align: <?php echo $f['Active']['align'] ?>;
}
<?php endif ?>

<?php if ($params->get('displaynumprod') > 0): ?>
.<?php echo $clear ?> dl .productnum,
.<?php echo $clear ?> dl.level<?php echo $i?> .productnum { <?php
	$count = OfflajnParser::parse($params->get("level{$i}count"), '#666666|*|#ffffff') ?>
	background-color: <?php echo $count[0] ?>;
	color: <?php echo $count[1] ?>;
	font-family: <?php echo ($f['Text']['family'] ? "'{$f['Text']['family']}', " : '') . rtrim($f['Text']['afont'], '|01') ?>;
	font-size: calc(<?php echo (float)$f['Text']['size'] ?>px * 0.7);
	line-height: <?php echo (float)$f['Text']['size'] ?>px !important;
}
<?php endif ?>

.<?php echo $clear ?> dl a,
.<?php echo $clear ?> dl a:link,
.<?php echo $clear ?> dl.level<?php echo $i?> a,
.<?php echo $clear ?> dl.level<?php echo $i?> a:link {
	<?php $fonts->printFont($textfont, 'Text') ?>
}

<?php if ($opened): ?>
.<?php echo $clear ?> dl dt.opened a,
.<?php echo $clear ?> dl.level<?php echo $i?> dt.opened a,
<?php endif ?>
.<?php echo $clear ?> dl dt.hover a,
.<?php echo $clear ?> dl dt:hover a,
.<?php echo $clear ?> dl.level<?php echo $i?> dt.hover a,
.<?php echo $clear ?> dl.level<?php echo $i?> dt:hover a{
	<?php $fonts->printFont($textfont, 'Hover', true) ?>
}

.<?php echo $clear ?> dl dt.active a,
.<?php echo $clear ?> dl.level<?php echo $i?> dt.active a{
	<?php $fonts->printFont($textfont, 'Active', true) ?>
}

.<?php echo $clear ?> dl .desc,
.<?php echo $clear ?> dl.level<?php echo $i?> .desc {
	<?php $fonts->printFont($descfont, 'Text') ?>
}

<?php if ($opened): ?>
.<?php echo $clear ?> dl dt.opened .desc,
.<?php echo $clear ?> dl.level<?php echo $i?> dt.opened .desc,
<?php endif ?>
.<?php echo $clear ?> dl dt.hover .desc,
.<?php echo $clear ?> dl dt:hover .desc,
.<?php echo $clear ?> dl.level<?php echo $i?> dt.hover .desc,
.<?php echo $clear ?> dl.level<?php echo $i?> dt:hover .desc {
	<?php $fonts->printFont($descfont, 'Hover', true) ?>
}

.<?php echo $clear ?> dl dt.active .desc,
.<?php echo $clear ?> dl.level<?php echo $i?> dt.active .desc{
	<?php $fonts->printFont($descfont, 'Active', true) ?>
}
<?php
$m = OfflajnParser::parse($params->get("level{$i}margin", '7|*|12|*|7|*|12|*|px'));
$br = OfflajnParser::parse($params->get("level{$i}br", '4|*|4|*|4|*|4|*|px'));
$padding = OfflajnParser::parse($params->get("level{$i}padding", '7|*|12|*|7|*|12|*|px'));
?>
.<?php echo $clear ?> dl dt,
.<?php echo $clear ?> dl.level<?php echo $i ?> dt {
	padding: <?php echo "{$padding[0]}px {$padding[1]}px {$padding[2]}px {$padding[3]}px" ?>;
	border-top: <?php echo $m[0] ?>px solid transparent;
	border-right: <?php echo $m[1] ?>px solid transparent;
	border-bottom: <?php echo $m[2] ?>px solid transparent;
	border-left: <?php echo $m[3] ?>px solid transparent;
	border-top-left-radius: <?php echo ($m[3]+$br[0]).'px '.($m[0]+$br[0]).'px' ?>;
	border-top-right-radius: <?php echo ($m[1]+$br[1]).'px '.($m[0]+$br[1]).'px' ?>;
	border-bottom-right-radius: <?php echo ($m[1]+$br[2]).'px '.($m[2]+$br[2]).'px' ?>;
	border-bottom-left-radius: <?php echo ($m[3]+$br[3]).'px '.($m[2]+$br[3]).'px' ?>;
}

/* Plus */
<?php $plus = OfflajnParser::parse($params->get('level'.$i.'plus')) ?>
.<?php echo $clear ?> dt > .sm-arrow,
.<?php echo $clear ?> dt.level<?php echo $i ?> > .sm-arrow {
	font-size: <?php echo $plus[2][0] ?>px;
	color: <?php echo $plus[3] ?>;
}
.<?php echo $clear ?> dt.opened > .sm-arrow,
.<?php echo $clear ?> dt.hover > .sm-arrow,
.<?php echo $clear ?> dt:hover > .sm-arrow,
.<?php echo $clear ?> dt.level<?php echo $i ?>.opened > .sm-arrow,
.<?php echo $clear ?> dt.level<?php echo $i ?>.hover > .sm-arrow,
.<?php echo $clear ?> dt.level<?php echo $i ?>:hover > .sm-arrow {
	color: <?php echo $plus[5] ?>;
}
.<?php echo $clear ?> dt.active > .sm-arrow,
.<?php echo $clear ?> dt.level<?php echo $i ?>.active > .sm-arrow {
	color: <?php echo $plus[4] ?>;
}
.<?php echo $clear ?> dt .inner,
.<?php echo $clear ?> dt.level<?php echo $i ?> .inner {
	padding-left: <?php echo $plus[1] == 'left' ? $p[3] : 0 ?>px;
}

<?php $alphaColors = OfflajnParser::parse($params->get('level'.$i.'bg')) ?>

.<?php echo $clear ?> dl dt,
.<?php echo $clear ?> dl.level<?php echo $i ?> dt{
	background-color: <?php echo $alphaColors[0] ?>;
}

<?php if ($opened): ?>
.<?php echo $clear ?> dl dt.opened,
.<?php echo $clear ?> dl.level<?php echo $i ?> dt.opened,
<?php endif ?>
.<?php echo $clear ?> dl dt.hover,
.<?php echo $clear ?> dl dt:hover,
.<?php echo $clear ?> dl.level<?php echo $i ?> dt.hover,
.<?php echo $clear ?> dl.level<?php echo $i ?> dt:hover {
	background-color: <?php echo $alphaColors[2] ?>;
}

.<?php echo $clear ?> dl dt.active,
.<?php echo $clear ?> dl.level<?php echo $i ?> dt.active{
	background-color: <?php echo $alphaColors[1] ?>;
}

<?php
++$i;
} while($i <= $definedLevel);
?>

<?php if ( preg_match('/tree|expand|accordion/', $params->get('navtype')) ): ?>
/* default higher level values for tree/expanded menu */
<?php $font = $params->get($position[0] == 'overlay' ? 'level'.($i-1).'ofont' : 'level'.($i-1).'font') ?>
<?php switch ($i): ?>
<?php case 2: ?>
	.<?php echo $clear ?> dl.level2 dt {
		padding-left: <?php echo $p[3]+15 ?>px;
	}
	<?php if ($opened): ?>
	.<?php echo $clear ?> dl.level2 dt.opened a,
	<?php endif ?>
	.<?php echo $clear ?> dl.level2 dt.active a,
	.<?php echo $clear ?> dl.level2 dt:hover a,
	.<?php echo $clear ?> dl.level2 a,
	.<?php echo $clear ?> dl.level2 a:link {
		font-size: <?php echo (float)$font['Text']['size']-2 ?>px;
	}
<?php case 3: ?>
	.<?php echo $clear ?> dl.level3 dt {
		padding-left: <?php echo $p[3]+30 ?>px;
	}
	<?php if ($opened): ?>
	.<?php echo $clear ?> dl.level3 dt.opened a,
	<?php endif ?>
	.<?php echo $clear ?> dl.level3 dt.active a,
	.<?php echo $clear ?> dl.level3 dt:hover a,
	.<?php echo $clear ?> dl.level3 a,
	.<?php echo $clear ?> dl.level3 a:link {
		font-size: <?php echo (float)$font['Text']['size']-3 ?>px;
	}
<?php case 4: ?>
	.<?php echo $clear ?> dl.level4 dt {
		padding-left: <?php echo $p[3]+45 ?>px;
	}
<?php case 5: ?>
	.<?php echo $clear ?> dl.level5 dt {
		padding-left: <?php echo $p[3]+60 ?>px;
	}
<?php endswitch ?>
<?php endif ?>