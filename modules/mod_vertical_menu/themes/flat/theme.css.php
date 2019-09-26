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
?>

#<?php echo $mod?> {
	margin: <?php echo OfflajnParser::parseUnit($params->get('margin'), ' ') ?>;
}
.sm-container > #<?php echo $mod?> {
	margin: 0;
	width: <?php echo $position[2][0] ?>px;
}

<?php if ($position[0] == 'overlay'): ?>
.sm-overlay-win #<?php echo $mod?> .sm-levels {
	background-color: transparent;
}
.sm-overlay-win #<?php echo $mod?> .sm-level.level1 {
	background-image: none;
}
.sm-overlay-<?php echo $module->id ?> .sm-overlay-win {
	<?php $g = explode('-', $params->get("level1gradient")) ?>
	background: <?php echo $g[1] ?>;
	background: -webkit-linear-gradient(top, <?php echo $g[1] ?>, <?php echo $g[2] ?>);
	background: linear-gradient(top, <?php echo $g[1] ?>, <?php echo $g[2] ?>);
}

<?php else: ?>
.<?php echo $clear ?> > .sm-level,
#<?php echo $mod?> {
	box-shadow: 0 0px 0px -0px rgba(0, 0, 0, 0.0), 0 0px 0px rgba(0, 0, 0, 0.0);
	border-radius: <?php echo OfflajnParser::parseUnit($params->get('borderradius'), ' ') ?>;
}

#<?php echo $mod?> .sm-head {
	<?php $g = explode('-', $this->params->get("titlegradient")) ?>
	background: <?php echo $g[1] ?>;
	background: -webkit-linear-gradient(top, <?php echo $g[1] ?>, <?php echo $g[2] ?>);
	background: linear-gradient(top, <?php echo $g[1] ?>, <?php echo $g[2] ?>);
}

/* custom module positions */
#<?php echo $mod?> .sm-logo,
#<?php echo $mod?> .<?php echo $this->params->get('top_module') ?> {
	<?php $grad = explode('-', $this->params->get("level1gradient")) ?>
	background: <?php echo $grad[1] ?>;
}

#<?php echo $mod?> .sm-levels {
	background: <?php $params->get('menubg', '#f6f6f6') ?>;
}

#<?php echo $mod?> input.sm-filter[type=text] {
	<?php $fonts->printFont('level1font', 'Text') ?>
	width: 100%;
	height: auto;
	<?php $filter = OfflajnParser::parse($params->get('filtercolor')) ?>
	background: <?php echo $filter[0] ?>;
	border: 0;
	border-bottom: 1px solid <?php echo $filter[1] ?>;
	margin: 0;
	border-radius: 0;
	padding: <?php echo OfflajnParser::parseUnit($params->get('level1padding'), ' ') ?>;
	box-sizing: border-box;
}
#<?php echo $mod?> .sm-filter-cont {
	position: relative;
	overflow: hidden;
}
<?php $font = $params->get('level1font') ?>
#<?php echo $mod?> .sm-search,
#<?php echo $mod?> .sm-reset {
	cursor: pointer;
	pointer-events: none;
	position: absolute;
	right: <?php echo $p[1] ?>px;
	top: 0;
	width: 20px;
	height: 100%;
	background: transparent no-repeat center center;
	<?php if ( preg_match('~\.(png|gif|jpe?g)$~i', $params->get('filtericon')) ): ?>
	<?php   $filtericon = preg_replace('~.*(/modules/)~', '$1', $params->get('filtericon')) ?>
	background-image: url(<?php echo $this->cacheUrl.$helper->NewColorizeImage($filtericon, $font['Text']['color'], '548722') ?>);
	display: block;
	<?php else: ?>
	display: none;
	<?php endif ?>
	background-size: 20px 20px;
	opacity: 0;
	-webkit-transform: translateX(200%);
	-ms-transform: translate(200%, 0);
	transform: translateX(200%);
	-webkit-transition: all 300ms;
	transition: all 300ms;
}
#<?php echo $mod?> .sm-reset {
	pointer-events: all;
	<?php if ( preg_match('~\.(png|gif|jpe?g)$~i', $params->get('reseticon')) ): ?>
	<?php   $reseticon = preg_replace('~.*(/modules/)~', '$1', $params->get('reseticon')) ?>
	background-image: url(<?php echo $this->cacheUrl.$helper->NewColorizeImage($reseticon, $font['Text']['color'], '548722') ?>);
	display: block;
	<?php else: ?>
	display: none;
	<?php endif ?>
	opacity: 1;
	-webkit-transform: none;
	-ms-transform: none;
	transform: none;
	-webkit-transition-delay: 300ms;
	transition-delay: 300ms;
}
#<?php echo $mod?> input[value=""].sm-filter ~ .sm-search {
	opacity: 1;
	-webkit-transform: none;
	-ms-transform: none;
	transform: none;
	-webkit-transition-delay: 300ms;
	transition-delay: 300ms;
}
#<?php echo $mod?> input[value=""].sm-filter ~ .sm-reset {
	opacity: 0;
	-webkit-transform: scale3d(0, 0, 1);
	-ms-transform: scale(0, 0);
	transform: scale3d(0, 0, 1);
	-webkit-transition-delay: 0ms;
	transition-delay: 0ms;
}
<?php endif ?>

#<?php echo $mod?> .sm-levels {
	height: 100%;
}

<?php $resize = OfflajnParser::parse($this->params->get('resizeicon')) ?>

.<?php echo $clear ?> .sm-icon {
	display: table-cell;
	width: <?php echo (isset($resize[1][0]) ? $resize[1][0] : 0)+10 ?>px;
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

#<?php echo $mod?> h3.sm-head {
	<?php $titlefont = $params->get($position[0] == 'overlay' ? 'otitlefont' : 'titlefont') ?>
	height: <?php echo (int)$titlefont['Text']['lineheight'] ?>px;
	padding: 0;
	margin: 0;
	border: 0;
	text-align: center;
}

#<?php echo $mod?> h3.sm-head .sm-title:first-child {
	position: static;
	max-width: 100% !important;
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
	-webkit-transform: translateY(-50%);
	-ms-transform: translate(0, -50%);
	transform: translateY(-50%);
	margin-top: -1px;
}
.<?php echo $clear ?> .productnum.one{
	padding-left: 9px;
	padding-right: 9px;
}
.<?php echo $clear ?> .productnum.more{
	padding-left: 7px;
	padding-right: 7px;
}
<?php endif ?>

#<?php echo $mod?> .sm-title:first-child {
	max-width: 78%;
}

<?php $p = OfflajnParser::parse($this->params->get("level1padding")) ?>

#<?php echo $mod?> .sm-title,
#<?php echo $mod?> .sm-back {
	position: absolute;
	left: 0;
	max-width: 66%;
	padding: 0 <?php echo $p[3] ?>px;
	white-space: nowrap;
	text-overflow: ellipsis;
	overflow: hidden;
}
#<?php echo $mod?> .sm-back {
	cursor: pointer;
	max-width: 45%;
	padding: 0 0 0 <?php echo $p[3] ?>px;
	-webkit-transform-origin: <?php echo $p[3] ?>px 50%;
	transform-origin: <?php echo $p[3] ?>px 50%;
}

.<?php echo $clear ?> .sm-level{
	height: 100%;
	/* background: transparent; */
	box-shadow: 0 0 0px 0px rgba(0, 0, 0, 0.2), 0 0 0px 0 rgba(0, 0, 0, 0.0);
}

/*** Level specific iteration ***/
<?php $i=1; do {
$p = OfflajnParser::parse($this->params->get("level{$i}padding"));
$textfont = $position[0] == 'overlay' ? "level{$i}ofont" : "level{$i}font";
$descfont = $position[0] == 'overlay' ? "level{$i}odescfont" : "level{$i}descfont"; ?>

.<?php echo $clear ?> dt,
.<?php echo $clear ?> dl.level<?php echo $i?> dt {
	padding: <?php echo "{$p[0]}px {$p[1]}px {$p[2]}px {$p[3]}px" ?>;
}

<?php if ($params->get('displaynumprod') > 0): ?>
.<?php echo $clear ?> dl .productnum,
.<?php echo $clear ?> dl.level<?php echo $i?> .productnum {
	<?php $g = explode('-', $params->get("level{$i}countgrad")) ?>
	background: <?php echo $g[1] ?>;
	background: -webkit-linear-gradient(top, <?php echo $g[1] ?>, <?php echo $g[2] ?>);
	background: linear-gradient(top, <?php echo $g[1] ?>, <?php echo $g[2] ?>);
}
<?php endif ?>

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

.<?php echo $clear ?> dl dt.active.parent a,
.<?php echo $clear ?> dl dt.active a,
.<?php echo $clear ?> dl.level<?php echo $i?> dt.active.parent a,
.<?php echo $clear ?> dl.level<?php echo $i?> dt.active a{
	<?php $fonts->printFont($textfont, 'Active', true) ?>
}

.<?php echo $clear ?> dl .desc,
.<?php echo $clear ?> dl.level<?php echo $i?> .desc,
.<?php echo $clear ?> dl .productnum,
.<?php echo $clear ?> dl.level<?php echo $i?> .productnum {
	<?php $fonts->printFont($descfont, 'Text') ?>
}

<?php if ($opened): ?>
.<?php echo $clear ?> dl dt.opened.parent .desc,
.<?php echo $clear ?> dl.level<?php echo $i?> dt.opened.parent .desc,
<?php endif ?>
.<?php echo $clear ?> dl dt.hover .desc,
.<?php echo $clear ?> dl dt:hover .desc,
.<?php echo $clear ?> dl.level<?php echo $i?> dt.hover .desc,
.<?php echo $clear ?> dl.level<?php echo $i?> dt:hover .desc{
	<?php $fonts->printFont($descfont, 'Hover', true) ?>
}

.<?php echo $clear ?> dl dt.active .desc,
.<?php echo $clear ?> dl.level<?php echo $i?> dt.active .desc{
	<?php $fonts->printFont($descfont, 'Active', true) ?>
}

.<?php echo $clear ?> .sm-level,
.<?php echo $clear ?> div.level<?php echo $i ?> {
	<?php $g = explode('-', $params->get("level{$i}gradient")) ?>
	background: <?php echo $g[1] ?>;
	background: -webkit-linear-gradient(top, <?php echo $g[1] ?>, <?php echo $g[2] ?>);
	background: linear-gradient(top, <?php echo $g[1] ?>, <?php echo $g[2] ?>);
}

/* product number */

.<?php echo $clear ?> dl .productnum,
.<?php echo $clear ?> dl.level<?php echo $i?> .productnum {
	color: <?php echo $titlefont['Text']['color'] ?>;
	border-radius: 9px;
	font-style: normal;
}

.<?php echo $clear ?> dl dt,
.<?php echo $clear ?> dl.level<?php echo $i ?> dt {
	<?php $border = OfflajnParser::parse($params->get("level{$i}border")) ?>
	border-top: 1px solid <?php echo $border[0] ?>;
	border-bottom: 1px solid <?php echo $border[1] ?>;
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
	padding-left: <?php echo $pseudo == 'before' ? 4 : 0 ?>px;
	padding-right: <?php echo $pseudo == 'after' ? 4 : 0 ?>px;
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
}

<?php
} while (++$i <= $definedLevel);
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