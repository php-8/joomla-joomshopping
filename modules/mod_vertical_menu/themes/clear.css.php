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

$root = rtrim(JURI::root(true), '/');
for ($x = 1; $params->get('level'.$x) && $x < 20; $x++) $definedLevel = $x;

$fonts = new OfflajnMiniFontHelper($params);
echo $fonts->parseFonts();

$mod = $clear = $module->instanceid;
$open = 'sm-open-'.$module->id;
$full = 'sm-full-'.$module->id;
$overlay = 'sm-overlay-'.$module->id;
$GLOBALS['googlefontsloaded'] = array();

$visibility = OfflajnParser::parse($params->get('visibility'), '1|*|1|*|1|*|1|*|0|*|0||px|*|10000||px');
?>

<?php if (!$visibility[4]): // Basic Visibility ?>
	<?php if (!$visibility[0]): // Phones ?>
@media screen and (max-width: 767px) {
	#off-menu_<?php echo $module->id ?>, .sm-btn-<?php echo $module->id ?> {
		display: none !important;
	}
}
	<?php endif ?>
	<?php if (!$visibility[1]): // Tablets ?>
@media screen and (min-width: 768px) and (max-width: 991px) {
	#off-menu_<?php echo $module->id ?>, .sm-btn-<?php echo $module->id ?> {
		display: none !important;
	}
}
	<?php endif ?>
	<?php if (!$visibility[2]): // Desktops  ?>
@media screen and (min-width: 992px) and (max-width: 1199px) {
	#off-menu_<?php echo $module->id ?>, .sm-btn-<?php echo $module->id ?> {
		display: none !important;
	}
}
	<?php endif ?>
	<?php if (!$visibility[3]): // Wide Screens ?>
@media screen and (min-width: 1200px) {
	#off-menu_<?php echo $module->id ?>, .sm-btn-<?php echo $module->id ?> {
		display: none !important;
	}
}
	<?php endif ?>
<?php else: // Advanced Visibility ?>
@media screen and (max-width: <?php echo $visibility[5][0] ?>px) {
	#off-menu_<?php echo $module->id ?>, .sm-btn-<?php echo $module->id ?> {
		display: none !important;
	}
}
@media screen and (min-width: <?php echo $visibility[6][0] ?>px) {
	#off-menu_<?php echo $module->id ?>, .sm-btn-<?php echo $module->id ?> {
		display: none !important;
	}
}
<?php endif ?>

<?php if ($params->get('badge')): ?>
.<?php echo $clear ?> .sm-square-badge,
.<?php echo $clear ?> .sm-round-badge {
	display: inline-block;
	position: absolute;
	margin: 0 0.5em;
	padding: 0 0.5em;
	white-space: nowrap;
}
.<?php echo $clear ?> .sm-square-badge {
	background: <?php echo OfflajnParser::parse($params->get('squarebadge')) ?>;
	<?php $fonts->printFont('squarefont', 'Text') ?>
}
.<?php echo $clear ?> .sm-round-badge {
	background: <?php echo OfflajnParser::parse($params->get('roundbadge')) ?>;
	<?php $fonts->printFont('roundfont', 'Text') ?>
	border-radius: <?php echo OfflajnParser::parseUnit($params->get('badgeradius'), ' ') ?>
}
<?php endif ?>

.<?php echo $clear ?> .sm-logo {
	text-align: center;
}
.<?php echo $clear ?> .sm-logo img {
	max-width: 100%;
}

/* custom module positions */
.<?php echo $clear ?> dt.sm-mod,
.<?php echo $clear ?> dt.sm-mod:hover{
	padding: 0 !important;
	cursor: default !important;
	background-color: inherit !important;
}

.<?php echo $clear ?> .sm-modpos {
	font-family: Menlo,Monaco,Consolas,"Courier New",monospace;
	padding: 9px !important;
	cursor: default !important;
	background-color: inherit !important;
}
.<?php echo $clear ?> .sm-postag {
	display: inline-block;
	background: #4ed7c2;
	color: #fff;
	border-radius: 4px 4px 0 0;
	padding: 0 7px;
	line-height: 20px;
	font-size: 12px;
}
.<?php echo $clear ?> .sm-posname {
	text-align: center;
	border: 1px solid #e3e3e3;
	background: #f5f5f5;
	color: #434343;
	font-size: 16px;
	font-weight: normal;
	line-height: 50px;
}

.<?php echo $clear ?> dt.sm-modpos:after, .<?php echo $clear ?> dt.sm-mod:after,
.<?php echo $clear ?> dt.sm-modpos:before, .<?php echo $clear ?> dt.sm-mod:before{
	display: none !important;
}
.<?php echo $clear ?> dt.sm-back-item a,
.<?php echo $clear ?> dt.parent:after, .<?php echo $clear ?> dt.parent:before {
	cursor: pointer;
}

.<?php echo $clear ?> .sm-level > dl > dt {
	overflow-x: hidden !important;
	max-width: 100vw;
}

.sm-scroll .sm-level dt {
	pointer-events: none !important;
}

<?php $drop = OfflajnParser::parse($params->get('drop')) ?>
.<?php echo $mod ?> > div.sm-level {
	width: <?php echo $drop[0][0] ?>px;
	height: auto;
	max-height: 100vh;
}

html.<?php echo $full ?>,
html.<?php echo $full ?> body {
	padding: 0 !important;
	border: 0 !important;
}
html.<?php echo $full ?>:not(.sm-reduce-width),
html.<?php echo $full ?>:not(.sm-reduce-width) body {
	overflow: hidden !important;
}
html.<?php echo $full ?>.sm-reduce-width body {
	position: static !important;
}

.no-trans {
	-webkit-transition: none !important;
	transition: none !important;
}

.<?php echo $full ?> .sm-pusher {
	outline: 1px solid transparent;
	z-index: 99;
	position: relative;
	height: 100%;
	overflow: hidden !important;
	-webkit-transition: -webkit-transform 0.5s ease 0s;
	transition: transform 0.5s ease 0s;
}

.sm-pusher:after {
	content: "";
	height: 100%;
	left: 0;
	position: fixed;
	top: 0;
	width: 100%;
	visibility: hidden;
	background: #000;
	z-index: 10000;
	opacity: 0;
	-webkit-transition: opacity 0.5s ease 0s;
	transition: opacity 0.5s ease 0s;
}
.sm-content {
	max-width: 100vw;
}
.<?php echo $full ?> .sm-pusher:after {
	visibility: visible;
}
.<?php echo $open ?> .sm-pusher:after {
	opacity: 0.2;
}

.<?php echo $full ?> .sm-content-inner,
.<?php echo $full ?> .sm-content,
.<?php echo $full ?> .sm-pusher {
	box-sizing: border-box;
}
.<?php echo $full ?> .sm-content {
	overflow-y: auto !important;
	width: 100vw;
	height: 100%;
}

<?php
$position = OfflajnParser::parse($params->get('position'));
$barpos = $position[0] == 'rightbar' || $position[0] == 'overlay' ? 'right' : 'left';
$spin = $position[0] == 'rightbar' ? -1 : 1;
?>
@media (max-width: 767px) {
	.sm-menu > .menu-icon-cont {
		margin-right: 0 !important;
	}
}
@media (min-width: 768px) {
	.<?php echo $full ?>.sm-reduce-width .sm-content {
		-webkit-transition: max-width 0.5s;
		transition: max-width 0.5s;
		float: <?php echo $spin < 0 ? 'left' : 'right' ?>;
	}
	.sm-reduce-width .<?php echo $open ?> .sm-content {
		max-width: calc(100% - <?php echo $position[2][0] ?>px);
	}
	.sm-reduce-width .<?php echo $open ?> .sm-pusher:after {
		display: none;
	}
}

.<?php echo $full ?> .sm-effect-14 .sm-content,
.<?php echo $full ?> .sm-effect-12 .sm-content,
.<?php echo $full ?> .sm-effect-11 .sm-content,
.<?php echo $full ?> .sm-effect-9 .sm-content,
.<?php echo $full ?> .sm-effect-6 .sm-content {
	overflow-y: auto;
}

.<?php echo $full ?> .sm-content-inner {
	min-height: 100vh;
	position: relative;
}

#<?php echo $mod ?> {
	-webkit-transition: -webkit-transform 0.5s;
	transition: transform 0.5s;
}

<?php if ($position[0] != 'module'): ?>
#<?php echo $mod ?> {display: none}
<?php endif ?>

.sm-container > #<?php echo $mod ?>:not(.sm-popup) {
	position: fixed;
	z-index: 100;
	max-width: 80vw;
	height: 100%;
	top: 0;
	<?php echo $barpos ?>: 0;
	display: none;
	visibility: hidden;
}
.<?php echo $full ?> #<?php echo $mod ?>:not(.sm-popup) {
	display: block;
	visibility: visible;
}

.<?php echo $full ?> .sm-overlay-win #<?php echo $mod ?> {
	display: inline-block;
	vertical-align: middle;
}

.sm-container > #<?php echo $mod ?>:after { /* ??? */
	display: none;
	position: absolute;
	top: 0;
	right: 0;
	width: 100%;
	height: 100%;
	background: #000;
	content: '';
	opacity: 0.2;
	-webkit-transition: opacity 0.5s;
	transition: opacity 0.5s;
}

.<?php echo $open ?> > #<?php echo $mod ?>:after {
	width: 0;
	opacity: 0;
	-webkit-transition: opacity 0.5s, width 0 0.5s;
	transition: opacity 0.5s, width 0 0.5s;
}

<?php if (preg_match('/module|leftbar|rightbar/', $position[0])): ?>
.sm-effect-5 #<?php echo $mod ?>:after,
.sm-effect-7 #<?php echo $mod ?>:after,
.sm-effect-10 #<?php echo $mod ?>:after,
.sm-effect-13 #<?php echo $mod ?>:after,
.sm-effect-14 #<?php echo $mod ?>:after {
	display: block;
}

.sm-effect-14.<?php echo $open ?> .sm-pusher,
.sm-effect-13.<?php echo $open ?> .sm-pusher,
.sm-effect-10.<?php echo $open ?> .sm-pusher,
.sm-effect-8.<?php echo $open ?> .sm-pusher,
.sm-effect-7.<?php echo $open ?> .sm-pusher,
.sm-effect-5.<?php echo $open ?> .sm-pusher,
.sm-effect-4.<?php echo $open ?> .sm-pusher,
.sm-effect-3.<?php echo $open ?> .sm-pusher,
.sm-effect-2.<?php echo $open ?> .sm-pusher {
	-webkit-transform: translate3d(<?php echo $spin * $position[2][0] ?>px, 0, 0);
	-ms-transform: translate(<?php echo $spin * $position[2][0] ?>px, 0);
	transform: translate3d(<?php echo $spin * $position[2][0] ?>px, 0, 0);
}

.sm-effect-6.<?php echo $open ?> .sm-pusher {
	-webkit-transform: translate3d(<?php echo $spin * $position[2][0] ?>px, 0, 0) perspective(1500px) rotateY(<?php echo $spin * -15 ?>deg);
	-ms-transform: translate(<?php echo $spin * $position[2][0] ?>px, 0);
	transform: translate3d(<?php echo $spin * $position[2][0] ?>px, 0, 0) perspective(1500px) rotateY(<?php echo $spin * -15 ?>deg);
}

@media screen and (max-width: <?php echo $position[2][0]/0.8 ?>px) {
	.sm-effect-14.<?php echo $open ?> .sm-pusher,
	.sm-effect-13.<?php echo $open ?> .sm-pusher,
	.sm-effect-10.<?php echo $open ?> .sm-pusher,
	.sm-effect-8.<?php echo $open ?> .sm-pusher,
	.sm-effect-7.<?php echo $open ?> .sm-pusher,
	.sm-effect-5.<?php echo $open ?> .sm-pusher,
	.sm-effect-4.<?php echo $open ?> .sm-pusher,
	.sm-effect-3.<?php echo $open ?> .sm-pusher,
	.sm-effect-2.<?php echo $open ?> .sm-pusher {
		-webkit-transform: translate3d(<?php echo $spin * 80 ?>vw, 0, 0);
		-ms-transform: translate(<?php echo $spin * 80 ?>vw, 0);
		transform: translate3d(<?php echo $spin * 80 ?>vw, 0, 0);
	}
	.sm-effect-6.<?php echo $open ?> .sm-pusher {
		-webkit-transform: translate3d(<?php echo $spin * 80 ?>vw, 0, 0) perspective(1500px) rotateY(<?php echo $spin * -15 ?>deg);
		-ms-transform: translate(<?php echo $spin * 80 ?>vw, 0);
		transform: translate3d(<?php echo $spin * 80 ?>vw, 0, 0) perspective(1500px) rotateY(<?php echo $spin * -15 ?>deg);
	}
}

.sm-container.<?php echo $open ?> > #<?php echo $mod ?> {
	-webkit-transform: none;
	-ms-transform: none;
	transform: none;
}

.sm-effect-7 > #<?php echo $mod ?>,
.sm-effect-8 > #<?php echo $mod ?>,
.<?php echo $full ?> .sm-effect-12 .sm-pusher {
	<?php $originX = $spin > 0 ? 100 : 0 ?>
	-webkit-transform-origin: <?php echo $originX ?>% 50%;
	transform-origin: <?php echo $originX ?>% 50%;
}

.sm-effect-1 > #<?php echo $mod ?>,
.sm-effect-3 > #<?php echo $mod ?>,
.sm-effect-6 > #<?php echo $mod ?>,
.sm-effect-9 > #<?php echo $mod ?>,
.sm-effect-11 > #<?php echo $mod ?>,
.sm-effect-12 > #<?php echo $mod ?>{
	visibility: visible;
	-webkit-transform: translate3d(<?php echo $spin * -100 ?>%, 0, 0);
	-ms-transform: translate(<?php echo $spin * -100 ?>%, 0);
	transform: translate3d(<?php echo $spin * -100 ?>%, 0, 0);
}

/* Effect 2: Reveal */
.<?php echo $full ?> .sm-effect-14 > #<?php echo $mod ?>,
.<?php echo $full ?> .sm-effect-13 > #<?php echo $mod ?>,
.<?php echo $full ?> .sm-effect-10 > #<?php echo $mod ?>,
.<?php echo $full ?> .sm-effect-5 > #<?php echo $mod ?>,
.<?php echo $full ?> .sm-effect-4 > #<?php echo $mod ?>,
.<?php echo $full ?> .sm-effect-2 > #<?php echo $mod ?> {
	z-index: 1;
}

/* Effect 4: Slide along */
.sm-effect-4 > #<?php echo $mod ?> {
	-webkit-transform: translate3d(<?php echo $spin * -50 ?>%, 0, 0);
	-ms-transform: translate(<?php echo $spin * -50 ?>%, 0);
	transform: translate3d(<?php echo $spin * -50 ?>%, 0, 0);
}

/* Effect 5: Reverse slide out */
.sm-effect-5 > #<?php echo $mod ?> {
	-webkit-transform: translate3d(<?php echo $spin * 50 ?>%, 0, 0);
	-ms-transform: translate(<?php echo $spin * 50 ?>%, 0);
	transform: translate3d(<?php echo $spin * 50 ?>%, 0, 0);
}

/* Effect 7: 3D rotate in */
.sm-effect-7 > #<?php echo $mod ?> {
	-webkit-transform: translate3d(<?php echo $spin * -100 ?>%, 0, 0) perspective(1500px) rotateY(<?php echo $spin * -90 ?>deg);
	-ms-transform: translate(<?php echo $spin * -100 ?>%, 0);
	transform: translate3d(<?php echo $spin * -100 ?>%, 0, 0) perspective(1500px) rotateY(<?php echo $spin * -90 ?>deg);
}

/* Effect 8: 3D rotate out */
.sm-effect-8 > #<?php echo $mod ?> {
	-webkit-transform: translate3d(<?php echo $spin * -100 ?>%, 0, 0) perspective(1500px) rotateY(<?php echo $spin * 90 ?>deg);
	-ms-transform: translate(<?php echo $spin * -100 ?>%, 0);
	transform: translate3d(<?php echo $spin * -100 ?>%, 0, 0) perspective(1500px) rotateY(<?php echo $spin * 90 ?>deg);
}

.sm-effect-7.<?php echo $open ?> > #<?php echo $mod ?>,
.sm-effect-8.<?php echo $open ?> > #<?php echo $mod ?> {
	-webkit-transform: perspective(1500px);
	transform: perspective(1500px);
}

/* Effect 9: Scale down pusher */
.sm-effect-9.<?php echo $open ?> .sm-pusher {
	-webkit-transform: scale3d(0.85, 0.85, 1);
	-ms-transform: scale(0.85, 0.85);
	transform: scale3d(0.85, 0.85, 1);
}
.sm-effect-9 > #<?php echo $mod ?>,
.sm-effect-11 > #<?php echo $mod ?>,
.sm-effect-12 > #<?php echo $mod ?> {
	opacity: 1;
}

/* Effect 10: Scale up */
.sm-effect-10 > #<?php echo $mod ?> {
	opacity: 1;
	-webkit-transform: scale3d(0.85, 0.85, 1);
	-ms-transform: scale(0.85, 0.85);
	transform: scale3d(0.85, 0.85, 1);
}

/* Effect 11: Scale and rotate pusher */
.sm-effect-11.<?php echo $open ?> .sm-pusher {
	-webkit-transform: perspective(1500px) translate3d(<?php echo $spin * 100 ?>px, 0, -600px) rotateY(<?php echo $spin * -20 ?>deg);
	-ms-transform: translate(<?php echo $spin * 100 ?>px, 0) scale(0.85, 0.85);
	transform: perspective(1500px) translate3d(<?php echo $spin * 100 ?>px, 0, -600px) rotateY(<?php echo $spin * -20 ?>deg);
}

/* Effect 12: Open door */
.sm-effect-12.<?php echo $open ?> .sm-pusher {
	-webkit-transform: perspective(1500px) rotateY(<?php echo $spin * -10 ?>deg);
	transform: perspective(1500px) rotateY(<?php echo $spin * -10 ?>deg);
}

/* Effect 13: Fall down */
.sm-effect-13 > #<?php echo $mod ?> {
	opacity: 1;
	-webkit-transform: translate3d(0, -100%, 0);
	-ms-transform: translate(0, -100%);
	transform: translate3d(0, -100%, 0);
}
.sm-effect-13.<?php echo $open ?> > #<?php echo $mod ?> {
	-webkit-transition-delay: 0.1s;
	transition-delay: 0.1s;
}

/* Effect 14: Delayed 3D rotate */
.sm-effect-14 > #<?php echo $mod ?> {
	-webkit-transform: perspective(1500px) rotateY(<?php echo $spin * 90 ?>deg);
	transform: perspective(1500px) rotateY(<?php echo $spin * 90 ?>deg);
	<?php $originX = $spin > 0 ? 0 : 100 ?>
	-webkit-transform-origin: <?php echo $originX ?>% 50%;
	transform-origin: <?php echo $originX ?>% 50%;
}
.sm-effect-14.<?php echo $open ?> > #<?php echo $mod ?> {
	-webkit-transition-duration: 550ms;
	transition-duration: 550ms;
	-webkit-transition-delay: 0.1s;
	transition-delay: 0.1s;
}
<?php endif ?>

<?php if ($position[0] == 'overlay'): ?>
/* OVERLAY */

.<?php echo $overlay ?>,
.<?php echo $full ?> .sm-pusher {
	-webkit-transition: -webkit-transform 500ms, opacity 500ms;
	transition: transform 500ms, opacity 500ms;
}

<?php $opened = $params->get('opened') && preg_match('/tree|accordion/', $params->get('navtype')) ?>

#<?php echo $mod ?> .link a {
	position: relative;
}
#<?php echo $mod ?> dt .link a:before,
#<?php echo $mod ?> dt .link a:after {
	position: absolute;
	opacity: 0;
	-webkit-transition: -webkit-transform 0.3s, opacity 0.3s;
	transition: transform 0.3s, opacity 0.3s;
}
<?php switch ((int)$params->get('text_anim', 1)): ?>
<?php case 1: ?> /* square brackets effect */
#<?php echo $mod ?> dt .link a:before {
	content: "[";
	left: 0px;
}
#<?php echo $mod ?> dt .link a:after {
	content: "]";
	right: 0px;
}
<?php if ($opened): ?>#<?php echo $mod ?> dt.opened .link a:before,<?php endif ?>
#<?php echo $mod ?> dt:hover .link a:before {
	opacity: 1;
	-webkit-transform: translateX(-1em);
	-ms-transform: translate(-1em, 0);
	transform: translateX(-1em);
}
<?php if ($opened): ?>#<?php echo $mod ?> dt.opened .link a:after,<?php endif ?>
#<?php echo $mod ?> dt:hover .link a:after {
	opacity: 1;
	-webkit-transform: translateX(1em);
	-ms-transform: translate(1em, 0);
	transform: translateX(1em);
}
<?php break ?>
<?php case 2: ?> /* underline 1 effect */
#<?php echo $mod ?> dt .link a:before {
	content: "";
	left: 0;
	bottom: 0;
	width: 100%;
	height: 0.1em;
	min-height: 1px;
	<?php $f = $params->get('level1ofont') ?>
	background: <?php echo isset($f['Hover']['color']) ? $f['Hover']['color'] : $f['Text']['color'] ?>;
	opacity: 1;
	-webkit-transform: scale3d(0, 1, 1);
	-ms-transform: scale(0, 1);
	transform: scale3d(0, 1, 1);
}
<?php if ($opened): ?>#<?php echo $mod ?> dt.opened .link a:before,<?php endif ?>
#<?php echo $mod ?> dt:hover .link a:before {
	-webkit-transform: none;
	-ms-transform: none;
	transform: none;
}
<?php break ?>
<?php case 3: ?> /* underline 2 effect */
#<?php echo $mod ?> dt .link a:before {
	content: "";
	left: 0;
	bottom: 0;
	width: 100%;
	height: 0.1em;
	min-height: 1px;
	<?php $f = $params->get('level1ofont') ?>
	background: <?php echo isset($f['Hover']['color']) ? $f['Hover']['color'] : $f['Text']['color'] ?>;
	-webkit-transform: translateY(200%);
	-ms-transform: translate(0, 200%);
	transform: translateY(200%);
}
<?php if ($opened): ?>#<?php echo $mod ?> dt.opened .link a:before,<?php endif ?>
#<?php echo $mod ?> dt:hover .link a:before {
	opacity: 1;
	-webkit-transform: none;
	-ms-transform: none;
	transform: none;
}
<?php break ?>
<?php case 4: ?> /* swipe up effect */
#<?php echo $mod ?> dt .link {overflow: hidden}
#<?php echo $mod ?> dt .link a {
	-webkit-transition: text-shadow 0.3s;
	transition: text-shadow 0.3s;
	color: transparent !important;
	<?php $f = $params->get('level1ofont') ?>
	text-shadow:
		0px 0px 0px <?php echo $f['Text']['color'] ?>,
		0px 1.5em 0px <?php echo isset($f['Hover']['color']) ? $f['Hover']['color'] : $f['Text']['color'] ?>;
}
<?php if ($opened): ?>#<?php echo $mod ?> dt.opened .link a,<?php endif ?>
#<?php echo $mod ?> dt:hover .link a {
	text-shadow:
		0px -1.5em 0px <?php echo $f['Text']['color'] ?>,
		0px 0px 0px <?php echo isset($f['Hover']['color']) ? $f['Hover']['color'] : $f['Text']['color'] ?>;
}
.sm-mobile #<?php echo $mod ?> dt .link a {
	-webkit-transition: none;
	transition: none;
}
<?php break ?>
<?php case 5: ?> /* push down effect */
#<?php echo $mod ?> dt .link a {
	<?php $f = $params->get('level1ofont') ?>
	color: <?php echo isset($f['Hover']['color']) ? $f['Hover']['color'] : $f['Text']['color'] ?>;
}
#<?php echo $mod ?> dt .link a:before {
	content: attr(data-text);
	position: absolute;
	width: 100%;
	height: 100%;
	text-shadow: none;
	opacity: 1;
	color: <?php echo $f['Text']['color'] ?>;
}
<?php if ($opened): ?>#<?php echo $mod ?> dt.opened .link a:before,<?php endif ?>
#<?php echo $mod ?> dt:hover .link a:before {
	-webkit-transform: scale3d(0.9, 0.9, 1);
	-ms-transform: scale(0.9);
	transform: scale3d(0.9, 0.9, 1);
	opacity: 0;
}
<?php break ?>
<?php case 6: ?> /* fill in effect */
#<?php echo $mod ?> dt .link a {
	<?php $f = $params->get('level1ofont') ?>
	color: <?php echo $f['Text']['color'] ?>;
}
#<?php echo $mod ?> dt .link a:before {
	content: attr(data-text);
	position: absolute;
	overflow: hidden;
	max-width: 0%;
	width: 100%;
	height: 100%;
	white-space: nowrap;
	text-shadow: none;
	opacity: 1;
	color: <?php echo isset($f['Hover']['color']) ? $f['Hover']['color'] : $f['Text']['color'] ?>;
	-webkit-transition: max-width 0.35s;
	transition: max-width 0.35s;
}
<?php if ($opened): ?>#<?php echo $mod ?> dt.opened .link a:before,<?php endif ?>
#<?php echo $mod ?> dt:hover .link a:before {
	max-width: 100%;
}
<?php endswitch ?>

/* slide-down */
.sm-effect-1 .<?php echo $overlay ?> {
	-webkit-transform: translateY(-100%);
	-ms-transform: translate(0, -100%);
	transform: translateY(-100%);
}
/* scale */
.sm-effect-2 .<?php echo $overlay ?> {
	-webkit-transition-duration: 300ms, 300ms;
	transition-duration: 300ms, 300ms;
	-webkit-transform: scale3d(0.9, 0.9, 1);
	-ms-transform: scale(0.9, 0.9);
	transform: scale3d(0.9, 0.9, 1);
	opacity: 0;
}
/* genie */
.sm-effect-3 .<?php echo $overlay ?> {
	-webkit-transform: translateY(60%) scale(0);
	-ms-transform: translate(0, 60%) scale(0);
	transform: translateY(60%) scale(0);
	opacity: 0;
}
/* content scale */
.sm-effect-4 .<?php echo $overlay ?> {
	-webkit-transform: translateY(100%);
	-ms-transform: translate(0, 100%);
	transform: translateY(100%);
}
.sm-effect-4.<?php echo $open ?> .sm-pusher {
	-webkit-transform: scale3d(0.85, 0.85, 1);
	-ms-transform: scale(0.85, 0.85);
	transform: scale3d(0.85, 0.85, 1);
}
/* content scale */
.sm-effect-5 .<?php echo $overlay ?>,
.<?php echo $full ?> .sm-effect-5 .sm-pusher {
	-webkit-transition-duration: 700ms;
	transition-duration: 700ms;
	-webkit-transform-origin: 0% 100%;
	transform-origin: 0% 100%;
}
.sm-effect-5 .<?php echo $overlay ?> {
	-webkit-transform: rotateZ(-90deg);
	-ms-transform: rotate(-90deg);
	transform: rotateZ(-90deg);
}
.sm-effect-5.<?php echo $open ?> .sm-pusher {
	-webkit-transform: rotateZ(90deg);
	-ms-transform: rotate(90deg);
	transform: rotateZ(90deg);
}
/* Flip top */
.sm-effect-6 .<?php echo $overlay ?> {
	-webkit-transform: perspective(1500px) rotateX(-120deg);
	transform: perspective(1500px) rotateX(-120deg);
	-webkit-transform-origin: 50% 0%;
	transform-origin: 50% 0%;
	-webkit-transition-duration: 800ms, 800ms;
	transition-duration: 800ms, 800ms;
}
.sm-effect-6 .<?php echo $overlay.' #'.$mod ?> {
	-webkit-transition: -webkit-transform 800ms;
	transition: transform 800ms;
	-webkit-transform: translateY(33%);
	transform: translateY(33%);
}
.<?php echo $full ?> .sm-effect-6 .sm-pusher {
	-webkit-transition-duration: 700ms, 700ms;
	transition-duration: 700ms, 700ms;
	-webkit-transform-origin: 50% 100%;
	transform-origin: 50% 100%;
}
.sm-effect-6.<?php echo $open ?> .sm-pusher {
	-webkit-transform: perspective(1500px) rotateX(120deg);
	transform: perspective(1500px) rotateX(120deg);
}
.sm-effect-6.<?php echo $open ?> .<?php echo $overlay.' #'.$mod ?> {
	-webkit-transform: none;
	transform: none;
}
/* Flip left */
.sm-effect-7 .<?php echo $overlay ?> {
	-webkit-transform: perspective(1500px) rotateY(120deg);
	transform: perspective(1500px) rotateY(120deg);
	-webkit-transform-origin: 0% 50%;
	transform-origin: 0% 50%;
	-webkit-transition-duration: 700ms, 700ms;
	transition-duration: 700ms, 700ms;
}
.<?php echo $full ?> .sm-effect-7 .sm-pusher {
	-webkit-transform-origin: 100% 50%;
	transform-origin: 100% 50%;
	-webkit-transition-duration: 700ms, 700ms;
	transition-duration: 700ms, 700ms;
}
.sm-effect-7.<?php echo $open ?> .sm-pusher {
	-webkit-transform: perspective(1500px) rotateY(-120deg);
	transform: perspective(1500px) rotateY(-120deg);
}
/* Flip on Y */
.sm-effect-9 .<?php echo $overlay ?>,
.<?php echo $full ?> .sm-effect-9 .sm-pusher,
.sm-effect-10 .<?php echo $overlay ?>,
.<?php echo $full ?> .sm-effect-10 .sm-pusher {
	-webkit-backface-visibility: hidden;
	backface-visibility: hidden;
	-webkit-transition-duration: 750ms, 750ms;
	transition-duration: 750ms, 750ms;
}
.sm-effect-9 .<?php echo $overlay ?> {
	-webkit-transform: perspective(1500px) scale(0.6) rotateY(180deg);
	transform: perspective(1500px) scale(0.6) rotateY(180deg);
	opacity: 0;
}
.sm-effect-9.<?php echo $open ?> .sm-pusher {
	-webkit-transform: perspective(1500px) scale(0.6) rotateY(180deg);
	transform: perspective(1500px) scale(0.6) rotateY(180deg);
	opacity: 0;
}
/* Flip on X */
.sm-effect-10 .<?php echo $overlay ?> {
	-webkit-transform: perspective(1500px) scale(0.6) rotateX(180deg);
	transform: perspective(1500px) scale(0.6) rotateX(180deg);
	opacity: 0;
}
.sm-effect-10.<?php echo $open ?> .sm-pusher {
	-webkit-transform: perspective(1500px) scale(0.6) rotateX(180deg);
	transform: perspective(1500px) scale(0.6) rotateX(180deg);
	opacity: 0;
}

/* zoom */
.sm-effect-11 .<?php echo $overlay ?> {
	-webkit-transform: scale3d(1.2, 1.2, 1);
	-ms-transform: scale(1.2, 1.2);
	transform: scale3d(1.2, 1.2, 1);
	opacity: 0;
}
.sm-effect-11.<?php echo $open ?> .sm-pusher {
	-webkit-transform: scale3d(0.6, 0.6, 1);
	-ms-transform: scale(0.6, 0.6);
	transform: scale3d(0.6, 0.6, 1);
}
/* Flip bottom */
.sm-effect-12 .<?php echo $overlay ?>,
.<?php echo $full ?> .sm-effect-12 .sm-pusher {
	-webkit-transform-origin: 50% 100%;
	transform-origin: 50% 100%;
	-webkit-transition-duration: 600ms, 600ms;
	transition-duration: 600ms, 600ms;
}
.sm-effect-12 .<?php echo $overlay ?> {
	-webkit-transform: perspective(1500px) rotateX(90deg);
	transform: perspective(1500px) rotateX(-90deg);
	opacity: 0;
}
.sm-effect-12.<?php echo $open ?> .sm-pusher {
	-webkit-transform: perspective(1500px) rotateX(90deg);
	transform: perspective(1500px) rotateX(90deg);
}

/* content scale and slide right  */
.sm-effect-13 .<?php echo $overlay ?>,
.<?php echo $full ?> .sm-effect-13 .sm-pusher {
	-webkit-transform-origin: 70% 50%;
	transform-origin: 70% 50%;
	-webkit-transition-duration: 750ms, 750ms;
	transition-duration: 750ms, 750ms;
}
.sm-effect-13 .<?php echo $overlay.' #'.$mod ?> {
	-webkit-transform: translateX(-33%);
	transform: translateX(-33%);
	-webkit-transition: -webkit-transform 750ms 150ms;
	transition: transform 750ms 150ms;
}
.sm-effect-13 .<?php echo $overlay ?> {
	-webkit-transform: translateX(-100%);
	-ms-transform: translate(-100%,0);
	transform: translateX(-100%);
}

.sm-effect-13.<?php echo $open ?> .sm-pusher {
	-webkit-transform: scale3d(0.65, 0.65, 1);
	-ms-transform: scale(0.65, 0.65);
	transform: scale3d(0.65, 0.65, 1);
}
.sm-effect-13.<?php echo $open ?> .<?php echo $overlay.' #'.$mod ?> {
	-webkit-transform: none;
	transform: none;
}

.sm-effect-14 .<?php echo $overlay ?> {
	-webkit-transition-duration: 400ms, 300ms;
	transition-duration: 400ms;
	opacity: 0;
}

.<?php echo $overlay ?> {
	position: absolute;
	display: none;
	overflow: hidden;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	z-index: 99;
}
<?php $m = OfflajnParser::parse($params->get('omargin')) ?>
.<?php echo $overlay ?> .sm-overlay-win {
	position: relative;
	width: calc(100% - <?php echo $m[1]+$m[3].$m[4] ?>);
	height: calc(100% - <?php echo $m[0]+$m[2].$m[4] ?>);
	top: <?php echo $m[0].$m[4] ?>;
	left: <?php echo $m[3].$m[4] ?>;
	text-align: center;
	overflow: hidden;
}

@media screen and (max-width: 768px) {
	.<?php echo $overlay ?> .sm-overlay-win {
		width: 100vw;
		height: 100vh;
		top: 0;
		left: 0;
	}
}

.sm-overlay-win:after {
	content: '';
	display: inline-block;
	vertical-align: middle;
	height: 100%;
	font-size: 0;
}

.<?php echo $overlay ?> #<?php echo $mod ?> {
	background: none;
	<?php $over = OfflajnParser::parse($params->get('overlay')) ?>
	width: <?php echo $over[0].$over[1] ?>;
	max-width: 100%;
	max-height: 100%;
	margin: 0 auto;
	border-radius: 0px;
}

.<?php echo $full ?> .<?php echo $overlay ?> {
	display: block;
}
.<?php echo $overlay ?> .sm-level,
.<?php echo $overlay ?> .sm-level > dl {
	max-height: inherit;
}

.sm-container.<?php echo $open ?> .<?php echo $overlay ?> {
	-webkit-transform: none;
	-ms-transform: none;
	transform: none;
	opacity: 1;
}

.<?php echo $overlay ?> .menu-icon-cont {
	position: absolute;
	top: 0;
	right: 0;
	-webkit-transform: none;
	-ms-transform: none;
	transform: none;
}

<?php endif ?>

/* Perfect Scrollbar */
.ps-container .ps-scrollbar-y-rail {
	position: absolute; /* please don't change 'position' */
	right: 3px; /* there must be 'right' for ps-scrollbar-y-rail */
	width: 8px;
	z-index: 1;
	border-radius: 4px;
	opacity: 0;
	-webkit-transition: background-color .2s linear, opacity .2s linear;
	transition: background-color .2s linear, opacity .2s linear;
}

.ps-container:hover .ps-scrollbar-y-rail,
.ps-container.hover .ps-scrollbar-y-rail {
	opacity: 0.6;
}

.ps-container .ps-scrollbar-y-rail:hover,
.ps-container .ps-scrollbar-y-rail.hover {
	background-color: #eee;
	opacity: 0.9;
}

.ps-container .ps-scrollbar-y-rail.in-scrolling {
	background-color: #eee;
	opacity: 0.9;
}

.ps-container .ps-scrollbar-y {
	position: absolute; /* please don't change 'position' */
	right: 0; /* there must be 'right' for ps-scrollbar-y */
	width: 8px;
	background-color: #aaa;
	border-radius: 4px;
	-webkit-transition: background-color.2s linear;
	transition: background-color .2s linear;
}

.ps-container .ps-scrollbar-y-rail:hover .ps-scrollbar-y,
.ps-container .ps-scrollbar-y-rail.hover .ps-scrollbar-y {
	background-color: #999;
}

.ps-container.ie .ps-scrollbar-y,
.ps-container.ie:hover .ps-scrollbar-y,
.ps-container.ie.hover .ps-scrollbar-y {
	visibility: visible;
}


/* ========= Menu Icon Base ======== */
<?php $icon = OfflajnParser::parse($params->get('sidebar_icon'), '#eeeeee|*|rgba(0, 0, 0, 0.5)|*|50||px|*|0||px|*|0||px|*|0||px|*|0.08em|*|1') ?>

.sm-btn-<?php echo $module->id ?> {
	background: <?php echo $icon[1] ?>;
	<?php $transl = ($position[0] == 'rightbar' ? -$icon[5][0] : $icon[3][0])."px, {$icon[4][0]}px" ?>
	-webkit-transform: translate(<?php echo $transl ?>);
	-ms-transform: translate(<?php echo $transl ?>);
	transform: translate(<?php echo $transl ?>);
}
.menu-icon-cont {
	cursor: pointer;
	display: inline-block;
	font-size: 0;
	line-height: 0;
	-webkit-transition: -webkit-transform 300ms, opacity 300ms;
	transition: transform 300ms, opacity 300ms;
	z-index: 9999;
}

.sm-hide .menu-icon-cont {
	-webkit-transition: -webkit-transform 500ms;
	transition: transform 500ms;
}

.sm-parent .menu-icon-cont {
	position: relative;
}

.sm-parent .menu-icon-cont,
.menu-icon-cont.sm-close {
	-webkit-transform: none;
	-ms-transform: none;
	transform: none;
}
/* border-radius fix */
.sm-parent > .<?php echo $clear?> {
	overflow: hidden;
}

body > .sm-btn-<?php echo $module->id ?>,
.sm-content-inner > .sm-btn-<?php echo $module->id ?>,
.<?php echo $clear?> .sm-btn-<?php echo $module->id ?> {
	position: fixed;
	top: 0;
	<?php echo $barpos ?>: 0;
}
.<?php echo $open ?> > .sm-btn-<?php echo $module->id ?>,
.<?php echo $open ?> .sm-content-inner > .sm-btn-<?php echo $module->id ?> {
	z-index: 99999;
}
.<?php echo $clear?> .sm-btn-<?php echo $module->id ?> {
	position: absolute;
	<?php echo $barpos ?>: 100%;
	top: 0;
	z-index: -1;
}
.sm-btn-<?php echo $module->id ?> .menu-icon3 {
	font-size: <?php echo $icon[2][0] ?>px;
}
.menu-icon-cont .menu-icon3 {
	display: inline-block;
	position: relative;
	height: .6em;
	margin: .2em;
	user-select: none;
	width: .6em;
}
.sm-btn-<?php echo $module->id ?> .menu-icon3 span {
	background: <?php echo $icon[0] ?>;
	height: <?php echo $icon[6] ?>;
	border-radius: <?php echo !$icon[7] ? 0 : (int)$icon[6]/2 ?>em;
}
.menu-icon-cont .menu-icon3 span {
	backface-visibility: hidden;
	position: absolute;
	-webkit-transition: all 0.2s ease-in-out;
	transition: all 0.2s ease-in-out;
	width: 100%;
}

/* ============ menu-icon3 ============ */
.menu-icon-cont .menu-icon3 span {
	left: 0;
}
.menu-icon3 span:nth-child(1) {
	top: .1em;
}
.menu-icon3 span:nth-child(2) {
	top: .26em;
}
.menu-icon3 span:nth-child(3) {
	top: .42em;
}

.sm-close .menu-icon3 span:nth-child(1) {
	-webkit-transform: rotateZ(45deg) translate3d(.11em, .11em, 0);
	-ms-transform: rotate(45deg) translate(.11em, .11em);
	transform: rotateZ(45deg) translate3d(.11em, .11em, 0);
}
.sm-close .menu-icon3 span:nth-child(2) {
	opacity: 0;
}
.sm-close .menu-icon3 span:nth-child(3) {
	-webkit-transform: rotateZ(-45deg) translate3d(.11em, -.11em, 0);
	-ms-transform: rotate(-45deg) translate(.11em, -.11em);
	transform: rotateZ(-45deg) translate3d(.11em, -.11em, 0);
}
.sm-hide .menu-icon3 span:nth-child(1) {
	width: 50%;
	-webkit-transform: translate3d(.16em, -0.08em, 0) rotateZ(45deg) translate3d(.11em, .11em, 0);
	-ms-transform: translate(.16em, -0.08em) rotate(45deg) translate(.11em, .11em);
	transform: translate3d(.16em, -0.08em, 0) rotateZ(45deg) translate3d(.11em, .11em, 0);
}
.sm-hide .menu-icon3 span:nth-child(3) {
	width: 50%;
	-webkit-transform: translate3d(.16em, 0.08em, 0) rotateZ(-45deg) translate3d(.11em, -.11em, 0);
	-ms-transform: translate(.16em, 0.08em) rotate(-45deg) translate(.11em, -.11em);
	transform: translate3d(.16em, 0.08em, 0) rotateZ(-45deg) translate3d(.11em, -.11em, 0);
}
<?php $titlefont = $params->get($position[0] == 'overlay' ? 'otitlefont' : 'titlefont') ?>
<?php $bt = OfflajnParser::parse($params->get('burgertitle'), '0|*|MENU|*|20||px') ?>
<?php if ($bt[0]): ?>
<?php if ($bt[4] == 'vertical'): ?>
.sm-btn-<?php echo $module->id ?>:before {
	content: "<?php echo preg_replace('/(.)/u', '$1\A ', $bt[1]) ?>";
	position: absolute;
	top: 100%;
	left: 0;
	width: 100%;
	margin: 0;
	padding: 0 0 <?php echo (int)$icon[2][0]/5 ?>px;
	line-height: <?php echo $bt[2][0] ?>px;
	background: <?php echo $icon[1] ?>;
	color: <?php echo $icon[0] ?>;
	font-size: <?php echo $bt[2][0] ?>px;
	font-family: <?php echo "'{$titlefont['Text']['family']}', ".preg_replace('/\|\|\d$/', '', $titlefont['Text']['afont']) ?>;
	text-align: center;
	white-space: pre;
}
<?php elseif ($bt[4] == 'horizontal'): ?>
.sm-btn-<?php echo $module->id ?>:before {
	content: "<?php echo $bt[1] ?>";
	position: absolute;
	top: 0;
	<?php if ($position[0] == 'rightbar' || $position[0] == 'overlay'): ?>
	right: 100%;
	padding: 0 0 0 <?php echo (int)$icon[2][0]/5 ?>px;
	<?php else: ?>
	left: 100%;
	padding: 0 <?php echo (int)$icon[2][0]/5 ?>px 0 0;
	<?php endif ?>
	margin: 0;
	background: <?php echo $icon[1] ?>;
	color: <?php echo $icon[0] ?>;
	line-height: <?php echo $icon[2][0] ?>px;
	font-size: <?php echo $bt[2][0] ?>px;
	font-family: <?php echo "'{$titlefont['Text']['family']}', ".preg_replace('/\|\|\d$/', '', $titlefont['Text']['afont']) ?>;
	max-height: 100%;
	white-space: nowrap;
}
<?php elseif ($bt[4] == 'rotated'): ?>
.sm-btn-<?php echo $module->id ?>:before {
	content: "<?php echo $bt[1] ?>";
	position: absolute;
	top: 100%;
	left: 0;
	margin: 0;
	padding: 0 <?php echo (int)$icon[2][0]/5 ?>px 0 0;
	background: <?php echo $icon[1] ?>;
	color: <?php echo $icon[0] ?>;
	line-height: <?php echo $icon[2][0] ?>px;
	font-size: <?php echo $bt[2][0] ?>px;
	font-family: <?php echo "'{$titlefont['Text']['family']}', ".preg_replace('/\|\|\d$/', '', $titlefont['Text']['afont']) ?>;
	max-height: 100%;
	white-space: nowrap;
	-webkit-transform: rotateZ(90deg) translateX(-<?php echo $icon[2][0] ?>px);
	transform: rotateZ(90deg) translateX(-<?php echo $icon[2][0] ?>px);
	-webkit-transform-origin: 0 100% 0;
	transform-origin: 0 100% 0;
}
<?php elseif ($bt[4] == 'small'): ?>
.sm-btn-<?php echo $module->id ?>:before {
	content: "<?php echo $bt[1] ?>";
	position: absolute;
	top: 100%;
	left: 0;
	width: 100%;
	margin: 0;
	padding: 0 0 <?php echo (int)$icon[2][0]/5 ?>px;
	line-height: <?php echo $bt[3][0] ?>px;
	background: <?php echo $icon[1] ?>;
	color: <?php echo $icon[0] ?>;
	font-size: <?php echo $bt[3][0] ?>px;
	font-family: <?php echo "'{$titlefont['Text']['family']}', ".preg_replace('/\|\|\d$/', '', $titlefont['Text']['afont']) ?>;
	text-align: center;
	overflow: hidden;
}
<?php endif ?>
.menu-icon-cont .menu-icon-cont:before {
	display: none;
}
<?php endif ?>
<?php if ($params->get('hideburger', 0) > 0): ?>
@media (min-width: <?php echo $position[1][0] ?>px) {
	.sm-popup-burger > .menu-icon3,
	.sm-btn-<?php echo $module->id ?>:not(.sm-popup-burger) { display: none !important; }
}
<?php endif ?>

/* sliding menu */

.<?php echo $clear?> .sm-background {
	display: none;
}

.<?php echo $clear?> .sm-levels {
	position: relative;
	overflow: hidden;
	-webkit-tap-highlight-color: transparent;
	-moz-tap-highlight-color: transparent;
	tap-highlight-color: transparent;
}

.<?php echo $clear?> .sm-levels.sm-swipe:after {
	content: "";
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	z-index: 100;
}

<?php
	$pos = OfflajnParser::parse($params->get('rtlmode'));
	if (!$pos) $pos = "right"; else $pos = "left";
?>

.<?php echo $clear?> a,
.<?php echo $clear?> a:link,
.<?php echo $clear?> a:visited,
.<?php echo $clear?> a:hover {
	text-decoration: none;
}

.<?php echo $clear?> .sm-head {
	cursor: default;
	position: relative;
	overflow: hidden;
}

.<?php echo $clear?> :not(input) {
	-webkit-touch-callout: none;
	-webkit-user-select: none;
	-khtml-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
}

.<?php echo $clear?> div,
.<?php echo $clear?> dl,
.<?php echo $clear?> dt,
.<?php echo $clear?> dd,
.<?php echo $clear?> span,
.<?php echo $clear?> a,
.<?php echo $clear?> p,
.<?php echo $clear?> img,
.<?php echo $clear?> h3{
	width: auto;
	padding: 0;
	margin: 0;
	border: 0;
	float: none;
	clear: none;
	line-height: normal;
	position: static;
	list-style: none;
	box-sizing: border-box;
}
.<?php echo $clear?> a:active,
.<?php echo $clear?> a:focus {
	outline: 0;
	-webkit-tap-highlight-color: transparent;
}

.<?php echo $clear?> .sm-filter::-ms-clear {
	display: none;
}

.<?php echo $clear?> .sm-level {
	top: 0;
	position: absolute;
	width: 100%;
	overflow: hidden;
}
#<?php echo $mod?> .sm-level {
	-webkit-backface-visibility: hidden;
	backface-visibility: hidden;
}

.<?php echo $clear?> dl .sm-level,
.<?php echo $clear?> .sm-level.level1 {
	position: static;
}

.<?php echo $clear?> dl {
	position: relative;
	overflow: hidden;
	<?php if ($maxheight = (int)$params->get('maxheight', 0)): ?>
	max-height: <?php echo $maxheight ?>px;
	<?php endif ?>
}

.sm-mobile .<?php echo $clear?> dl {
	overflow: auto;
	-webkit-overflow-scrolling: touch;
}
.sm-mobile .<?php echo $clear?> dd dl {
	overflow: hidden;
}

.sm-container > .sm-menu dl {
	max-height: 100%;
}

.<?php echo $clear ?> dt {
	-webkit-transition: background 300ms;
	transition: background 300ms;
}

.<?php echo $clear ?> .link a,
.<?php echo $clear ?> .sm-arrow,
.<?php echo $clear ?> .desc {
	-webkit-transition: color 300ms;
	transition: color 300ms;
}

.<?php echo $clear?> dd {
	display: block;
	margin: 0;
	border: 0;
	overflow:hidden;
}

.noscript .<?php echo $clear?> dd,
.sm-tree.<?php echo $clear?> dd,
.<?php echo $clear?> .sm-result .sm-arrow {
	display: none;
}

.sm-tree.<?php echo $clear?> dd.opened {
	display: block;
	margin: 0;
	border: 0;
}

<?php if ($params->get('fontawesome', 0)) : ?>
.sm-menu .fa::before {
	display: inline-block;
	width: 1.8em;
	font: normal normal normal 14px/1 FontAwesome;
	font-size: inherit;
	text-rendering: auto;
	-webkit-font-smoothing: antialiased;
	-moz-osx-font-smoothing: grayscale
}
<?php endif ?>
