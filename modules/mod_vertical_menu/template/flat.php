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

if($item->fib){
  $this->stack[] = $item->parent;
  $this->level = count($this->stack);
}
if($this->up){
  while($this->level > $item->level){ ?>
    </dl></div></dd><?php
    array_pop($this->stack);
    $this->level = count($this->stack);
  }
  $this->up = false;
}

$classes = array('level'.$this->level, 'off-nav-'.$item->id, $item->p ? "parent" : "notparent");
if($item->opened) $classes[] = 'opened';
if($item->active) $classes[] = 'active';
if(isset($this->openedlevels[$this->level]) && $item->p) $classes[] = 'opened forceopened';
if($item->fib) $classes[] = 'first';
if($item->lib) $classes[] = 'last';
$classes = implode(' ', $classes);

if($item->fib): ?>
<div class="sm-level level<?php echo $this->level ?>"><dl class="level<?php echo $this->level ?>">
<?php endif; ?>
<?php if (isset($item->modpos)): ?>
 <?php $parId = !empty($item->parent_id) ? $item->parent_id : $item->parent->id; ?>
  <?php $modpos = $item->level == 1 ? $this->_params->get('bottom_module') : $this->_params->get('custom_module', 'sm-').$parId ?>
  <?php if ($this->_params->get('show_modulepos')): ?>
    <dt class="sm-modpos <?php echo $modpos ?>">
      <div class="sm-postag">module-position</div>
      <div class="sm-posname"><?php echo $modpos ?></div>
    </dt><dd></dd>
  <?php endif ?>
  <?php if (count($modules = JModuleHelper::getModules($modpos))): // custom MODULEPOS ?>
    <dt class="sm-mod <?php echo $modpos ?>">
    <?php foreach ($modules as $m): ?>
      <?php echo JModuleHelper::renderModule($m) ?>
    <?php endforeach ?>
    </dt><dd></dd>
  <?php endif ?>
<?php else: ?>
  <dt class="<?php echo $classes ?>">
    <?php if (!empty($item->image)): ?>
    <div class="sm-icon">
      <?php $resize = OfflajnParser::parse($this->_params->get('resizeicon')) ?>
      <?php if ($resize[0]): ?>
        <?php $imgpath = JPATH_SITE.'/'.str_replace(JURI::root(), '', $item->image); ?>
        <img src="<?php echo $this->imageCache->generateImage($imgpath, $resize[2][0], $resize[3][0], $resize[4]) ?>" alt="" ondragstart="return false" />
      <?php else: ?>
        <img src="<?php echo $item->image ?>" alt="" ondragstart="return false" />
      <?php endif ?>
    </div>
    <?php endif ?>
    <div class="inner">
      <div class="link"><a data-text="<?php echo $item->title ?>" <?php empty($item->anchorAttr) or print $item->anchorAttr ?>><?php echo $item->title.(!empty($item->badge) ? $item->badge : '') ?></a><?php empty($item->number) or print $item->number ?></div>
      <?php if (!empty($item->description)): ?>
      <p class="desc"><?php echo $item->description ?></p>
      <?php endif ?>
    </div>
  </dt>
  <dd class="<?php echo $classes ?>">
    <?php if($item->p): $this->renderItem();
    else: ?>
  </dd>
  <?php endif; ?>
<?php endif ?>
<?php
if($item->lib):
  $this->up = true; ?>
  <?php if($item->level == 1): ?>
</dl></div>
  <?php endif; ?>
<?php endif; ?>