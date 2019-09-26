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

if (!isset($GLOBALS['sm-sym'])) $GLOBALS['sm-sym'] = array();
$lvl = 0;
$sym = array();
while ($plus = $params->get('level'.(++$lvl).'plus')) {
  $svg = __DIR__.'/../themes/rounded/images/arrows/'.basename(OfflajnParser::parse($plus)[0]);
  if (file_exists($svg) && empty($GLOBALS['sm-sym'][$svg])) {
    $GLOBALS['sm-sym'][$svg] = true;
    $xml = JFactory::getXML($svg)->asFormattedXML(true);
    $sym[$svg] = '<symbol id="sym-'.basename($svg, '.svg').'"' . substr($xml, 4, -4) . 'symbol>';
  }
}
empty($sym) or JFactory::getDocument()->addCustomTag('<svg height="0" style="position:absolute">'.implode('', $sym).'</svg>');
?>
<nav id="<?php echo $module->instanceid ?>" class="<?php echo $module->instanceid ?> sm-menu <?php echo $params->get('moduleclass_sfx', ''); ?>">
  <?php if (preg_match('~\.(jpe?g|png|gif|bmp|svg)$~i', $params->get('logo'))): ?>
  <div class="sm-logo" style="position:relative">
    <img src="<?php echo rtrim(JURI::root(true), '/').preg_replace('~^.*?(/images/)~', '$1', $params->get('logo')) ?>" alt="logo" />
  </div>
  <?php endif ?>
  <?php if ($params->get('show_modulepos')): ?>
    <div class="sm-modpos <?php echo $params->get('top_module') ?>" style="position:relative">
      <div class="sm-postag">module-position</div>
      <div class="sm-posname"><?php echo $params->get('top_module') ?></div>
    </div>
  <?php endif ?>
  <?php if (count($modules = JModuleHelper::getModules($params->get('top_module')))): // TOP MODULEPOS ?>
    <div class="<?php echo $params->get('top_module') ?>" style="position:relative">
    <?php foreach ($modules as $m): ?>
      <?php echo JModuleHelper::renderModule($m) ?>
    <?php endforeach ?>
    </div>
  <?php endif ?>
  <?php $x = OfflajnParser::parse($params->get('xicon'), '0|*|#666666|*|right') ?>
  <?php if ($x[0]): ?>
    <div class="sm-x" style="color:<?php echo $x[1] ?>; <?php echo $x[2] ?>:10px;"></div>
  <?php endif ?>
  <?php if ($module->showtitle): ?>
  <h3 class="sm-head">
    <?php
      $icon = __DIR__.'/../themes/rounded/images/back/'.basename($params->get('backicon', 'go-back.svg'));
      if (file_exists($icon)) {
        $svg = JFactory::getXML($icon);
        $svg->addAttribute('class', 'sm-back sm-arrow');
        $svg->addAttribute('title', JText::_('JPREV'));
        echo $svg->asFormattedXML(true);
      }
    ?>
    <span class="sm-title"><?php echo $module->title ?></span><?php if ($params->get('forcestyle', 1)) $module->title='' ?>
  </h3>
  <?php endif; ?>
  <?php if ($filter[0] && strpos($params->get('position'), 'overlay') === false): ?>
  <div class="sm-filter-cont">
    <?php
      $icon = __DIR__.'/../themes/rounded/images/filter/'.basename($params->get('filtericon', 'search.svg'));
      if (file_exists($icon)) {
        $svg = JFactory::getXML($icon);
        $svg->addAttribute('class', 'sm-filter-icon');
        echo $svg->asFormattedXML(true);
      }
    ?>
    <input class="sm-filter" type="text" placeholder="<?php echo JText::_($filter[3] ? $filter[3] : 'JSEARCH_FILTER_LABEL') ?>" />
    <?php
      $icon = __DIR__.'/../themes/rounded/images/reset/'.basename($params->get('reseticon', 'close.svg'));
      if (file_exists($icon)) {
        $svg = JFactory::getXML($icon);
        $svg->addAttribute('class', 'sm-reset');
        echo $svg->asFormattedXML(true);
      }
    ?>
  </div>
  <?php endif; ?>
  <div class="sm-levels">
    <?php $menu->render($tmpl) ?>
  </div>
</nav>