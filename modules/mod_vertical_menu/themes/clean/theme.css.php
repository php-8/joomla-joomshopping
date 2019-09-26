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

.<?php echo $mod?> .sm-level > dl {
	padding: <?php echo (int)$params->get('menuitemmargin') ?>px;
}
.<?php echo $mod?> .sm-level .sm-level > dl {
	padding-left: 0;
	padding-right: 0;
}

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
	box-shadow: inset -1px 0 0 0 rgba(0, 0, 0, 0.21);
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
<?php endif ?>
#<?php echo $mod?> .sm-filter-cont {
	position: relative;
	padding: <?php echo (int)$params->get('menuitemmargin') ?>px;
	padding-bottom: 0;
}
#<?php echo $mod?> input.sm-filter[type=text] {
	<?php $fonts->printFont('level1font', 'Text') ?>
	width: 100%;
	height: auto;
	padding: <?php echo "{$p[0]}px {$p[1]}px {$p[2]}px ".($p[3]+33) ?>px;
	<?php $filter = OfflajnParser::parse($params->get('filtercolor')) ?>
	<?php $font = $params->get('level1font') ?>
	background: <?php echo $filter[0] ?> url(<?php echo $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__).'/images/filter.png', $font['Text']['color'], '2bb197')?>) <?php echo $p[3] ?>px center no-repeat;
	background-size: 26px;
	border: 0;
	box-shadow: inset 0 0 0 1px <?php echo $filter[1] ?>;
	margin: 0;
	border-radius: 0;
	box-sizing: border-box;
}
/* filter placeholder */
#<?php echo $mod?> input.sm-filter[type=text]::-webkit-input-placeholder {
	opacity: 1;
	color: <?php echo $font['Text']['color'] ?>;
}
#<?php echo $mod?> input.sm-filter[type=text]::-moz-placeholder {
	opacity: 1;
	color: <?php echo $font['Text']['color'] ?>;
}
#<?php echo $mod?> input.sm-filter[type=text]:-ms-input-placeholder {
	opacity: 1;
	color: <?php echo $font['Text']['color'] ?>;
}
#<?php echo $mod?> input.sm-filter[type=text]:focus::-webkit-input-placeholder {opacity: 0.75}
#<?php echo $mod?> input.sm-filter[type=text]:focus::-moz-placeholder {opacity: 0.75}
#<?php echo $mod?> input.sm-filter[type=text]:focus:-ms-input-placeholder {opacity: 0.75}

#<?php echo $mod?> .sm-reset {
	cursor: pointer;
	position: absolute;
	margin-right: <?php echo (int)$params->get('menuitemmargin') ?>px;
	margin-top: <?php echo (int)$params->get('menuitemmargin')/2 ?>px;
	right: <?php echo $p[1] ?>px;
	top: 0;
	width: 20px;
	height: 100%;
	background: transparent no-repeat center center;
	<?php if ( preg_match('~\.(png|gif|jpe?g)$~i', $params->get('reseticon')) ): ?>
	<?php 	$reseticon = preg_replace('~.*(/modules/)~', '$1', $params->get('reseticon')) ?>
	background-image: url(<?php echo $this->cacheUrl.$helper->NewColorizeImage($reseticon, $font['Text']['color'], '548722') ?>);
	<?php else: ?>
	display: none;
	<?php endif ?>
	background-size: 20px 20px;
	-webkit-transition: all 300ms;
	transition: all 300ms;
}
#<?php echo $mod?> input[value=""].sm-filter ~ .sm-reset {
	opacity: 0;
	-webkit-transform: scale3d(0, 0, 1);
	-ms-transform: scale(0, 0);
	transform: scale3d(0, 0, 1);
}
<?php endif ?>

#<?php echo $mod?> .sm-levels {
	height: 100%;
}

<?php $resize = OfflajnParser::parse($this->params->get('resizeicon')) ?>

.<?php echo $clear ?> .sm-icon {
	display: table-cell;
	width: <?php echo $resize[1][0]+10 ?>px;
	text-align: center;
	vertical-align: top;
	padding: 0 10px 0 0;
}

.<?php echo $clear ?> .sm-icon img {
	margin: 0;
	border-radius: <?php echo OfflajnParser::parseUnit($this->params->get('iconborderradius'), ' ') ?>;
}

.<?php echo $clear ?> .inner{
	display: table-cell;
	vertical-align: middle;
}

.<?php echo $clear ?> dt {
	display: table;
	table-layout: fixed;
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

<?php $titleb = OfflajnParser::parse($params->get('titleborder')) ?>

#<?php echo $mod?> h3.sm-head {
	background: <?php echo $params->get('titlebg') ?>;
	<?php $titlefont = $params->get($position[0] == 'overlay' ? 'otitlefont' : 'titlefont') ?>
	height: <?php echo (int)$titlefont['Text']['lineheight'] ?>px;
	padding: 0;
	margin: 0;
	border: 0;
	border-top: 1px solid <?php echo $titleb[0] ?>;
	border-bottom: 1px solid <?php echo $titleb[1] ?>;
	text-align: center;
}

#<?php echo $mod?> h3.sm-head span {
	<?php $fonts->printFont($position[0] == 'overlay' ? 'otitlefont' : 'titlefont', 'Text') ?>
}

.<?php echo $clear ?> .link {
	position: relative;
	display: block;
	padding-right: <?php echo $params->get('displaynumprod') > 0 ? 25 : 0 ?>px;
}

/* Productnum */
<?php if ($params->get('displaynumprod') > 0): ?>
.<?php echo $clear ?> .productnum {
	position: absolute;
	right: 0;
	top: 50%;
	font-size: 1em;
	-webkit-transform: translateY(-50%);
	-ms-transform: translate(0, -50%);
	transform: translateY(-50%);
	display: block;
	width: 1.4em;
	height: 1.4em;
	line-height: 1.4em !important;
	text-align: center !important;
	border-radius: 50%;
	box-shadow: 0 0 0 1px rgba(255,255,255,0.12); /* !!! */
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
	width: <?php echo (int)$params->get('menuitemmargin')+$p[3]+26 ?>px;
	height: 100%;
	background: url(<?php echo $this->cacheUrl.$helper->NewColorizeImage(dirname(__FILE__).'/images/back.png', $titlefont['Text']['color'], '2bb197')?>) right center no-repeat;
	background-size: 27px;
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
.<?php echo $clear ?> dl.level<?php echo $i?> .productnum {
	background-color: <?php echo $params->get("level{$i}countbg", 'rgba(0,0,0,0.22)') ?>
}
<?php endif ?>

.<?php echo $clear ?> dl a,
.<?php echo $clear ?> dl a:link,
.<?php echo $clear ?> dl.level<?php echo $i?> a,
.<?php echo $clear ?> dl.level<?php echo $i?> a:link {
	<?php $fonts->printFont($textfont, 'Text') ?>
}

<?php if ($opened): ?>
.<?php echo $clear ?> dl dt.opened.parent a,
.<?php echo $clear ?> dl.level<?php echo $i?> dt.opened.parent a,
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
.<?php echo $clear ?> dl dt.opened.parent .desc,
.<?php echo $clear ?> dl.level<?php echo $i?> dt.opened.parent .desc,
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

<?php $padding = OfflajnParser::parse($params->get("level{$i}padding", '7|*|12|*|7|*|12|*|px')) ?>
.<?php echo $clear ?> dl dt,
.<?php echo $clear ?> dl.level<?php echo $i ?> dt {
	<?php $border = OfflajnParser::parse($params->get("level{$i}border"), 'ffffff00|*|00000005') ?>
	border-top: 1px solid <?php echo $border[0] ?>;
	border-bottom: 1px solid <?php echo $border[1] ?>;
	padding: <?php echo "{$padding[0]}px {$padding[1]}px {$padding[2]}px {$padding[3]}px" ?>;
}

/* Plus */
<?php
$plus = OfflajnParser::parseColorizedImage($this->params->get('level'.$i.'plus'));
$pseudo = $plus[1] == 'left' ? 'before' : 'after';
$invers = $plus[1] != 'left' ? 'before' : 'after'; ?>
.<?php echo $clear ?> dl dt:<?php echo $pseudo ?>,
.<?php echo $clear ?> dl.level<?php echo $i ?> > dt:<?php echo $pseudo ?> {
	content: "";
	display: table-cell;
	width: 20px;
	-webkit-transition: -webkit-transform <?php echo (int)$params->get('duration') ?>ms;
	transition: transform <?php echo (int)$params->get('duration') ?>ms;
}
.<?php echo $clear ?> dl dt:<?php echo $invers ?>,
.<?php echo $clear ?> dl.level<?php echo $i ?> > dt:<?php echo $invers ?> {
	display: none;
}
.<?php echo $clear ?> dl .parent:<?php echo $pseudo ?>,
.<?php echo $clear ?> dl.level<?php echo $i ?> > .parent:<?php echo $pseudo ?> {
<?php if ($plus[0]): ?>
	background-image: url('<?php echo $plus[0] ?>');
	background-size: 40px 20px;
	background-repeat: no-repeat;
	background-position: left center;
<?php else: ?>
	display: none;
<?php endif ?>
}
.<?php echo $clear ?> dl .inner,
.<?php echo $clear ?> dl.level<?php echo $i ?> .inner {
	padding-left: <?php echo $pseudo == 'before' ? $p[3] : 0 ?>px;
}

<?php $alphaColors = OfflajnParser::parse($this->params->get('level'.$i.'bg')) ?>

.<?php echo $clear ?> dl dt.active,
.<?php echo $clear ?> dl.level<?php echo $i ?> dt.active{
	background-color: <?php echo $alphaColors[1] ?>;
}

<?php if ($opened): ?>
.<?php echo $clear ?> dl dt.opened.parent,
.<?php echo $clear ?> dl.level<?php echo $i ?> dt.opened.parent,
<?php endif ?>
.<?php echo $clear ?> dl dt.hover,
.<?php echo $clear ?> dl dt:hover,
.<?php echo $clear ?> dl.level<?php echo $i ?> dt.hover,
.<?php echo $clear ?> dl.level<?php echo $i ?> dt:hover {
	background-color: <?php echo $alphaColors[0] ?>;
	/* !!!
	box-shadow: inset 0 0 0 1px rgba(255,255,255,0.12);*/
}

<?php
++$i;
} while($i <= $definedLevel);
?>

/* Plus */
.<?php echo $clear ?> dl .parent.hover:before,
.<?php echo $clear ?> dl .parent.hover:after,
.<?php echo $clear ?> dl .parent:hover:before,
.<?php echo $clear ?> dl .parent:hover:after{
	background-position: right center;
}
.<?php echo $clear ?>.sm-tree dl .opened:before,
.<?php echo $clear ?>.sm-tree dl .opened:after {
	background-position: right center;
	-webkit-transform: rotateZ(90deg);
	-ms-transform: rotate(90deg);
	transform: rotateZ(90deg);
}

<?php if ( preg_match('/tree|expand|accordion/', $params->get('navtype')) ): ?>
/* default higher level values for tree/expanded menu */
<?php $font = $params->get($position[0] == 'overlay' ? 'level'.($i-1).'ofont' : 'level'.($i-1).'font') ?>
<?php switch ($i): ?>
<?php case 2: ?>
	.<?php echo $clear ?> dl.level2 dt {
		padding-left: <?php echo $p[3]+15 ?>px;
	}
	<?php if ($opened): ?>
	.<?php echo $clear ?> dl.level2 dt.opened.parent a,
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
	.<?php echo $clear ?> dl.level3 dt.opened.parent a,
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