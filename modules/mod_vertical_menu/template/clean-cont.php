<?php
/**
 * mod_vertical_menu - Vertical Menu
 *
 * @author    Balint Polgarfi
 * @copyright 2014-2019 Offlajn.com
 * @license   https://gnu.org/licenses/gpl-2.0.html
 * @link      https://offlajn.com
 */

 defined('_JEXEC') or die('Restricted access'); ?>
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
  <?php if ($module->showtitle): ?>
  <h3 class="sm-head">
    <span class="sm-back sm-arrow" title="<?php echo JText::_('JPREV') ?>"></span>
    <span class="sm-title"><?php echo $module->title ?></span><?php if ($params->get('forcestyle', 1)) $module->title='' ?>
  </h3>
  <?php endif; ?>
  <?php if ($filter[0] && strpos($params->get('position'), 'overlay') === false): ?>
  <div class="sm-filter-cont">
    <input class="sm-filter" type="text" placeholder="<?php echo JText::_($filter[3] ? $filter[3] : 'JSEARCH_FILTER_LABEL') ?>" />
		<div class="sm-reset"></div>
  </div>
  <?php endif; ?>
  <div class="sm-levels">
    <?php $menu->render($tmpl) ?>
  </div>
</nav>