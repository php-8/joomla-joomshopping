<?php defined('_JEXEC') or die;

JLoader::import('joomla.filesystem.file');
JHtml::_('behavior.framework', true);
JHtml::_('bootstrap.framework');
JHtml::_('stylesheet', 'ie7only.css', array('version' => 'auto', 'relative' => true, 'conditional' => 'IE 7'));

// Check for a custom CSS file
JHtml::_('stylesheet', 'user.css', array('version' => 'auto', 'relative' => true));
JHtml::_('bootstrap.framework');

// Add template scripts
JHtml::_('script', 'templates/' . $this->template . '/javascript/md_stylechanger.js', array('version' => 'auto'));
JHtml::_('script', 'templates/' . $this->template . '/javascript/hide.js', array('version' => 'auto'));
JHtml::_('script', 'templates/' . $this->template . '/javascript/respond.src.js', array('version' => 'auto'));
JHtml::_('script', 'templates/' . $this->template . '/javascript/template.js', array('version' => 'auto'));

// Check for a custom js file
JHtml::_('script', 'templates/' . $this->template . '/javascript/user.js', array('version' => 'auto'));
require __DIR__ . '/jsstrings.php';

// Add html5 shiv
JHtml::_('script', 'jui/html5.js', array('version' => 'auto', 'relative' => true, 'conditional' => 'lt IE 9'));
$doc = JFactory::getDocument();
$doc->addStyleSheet('templates/clear_j3/css/slick.css');
$doc->addStyleSheet('templates/clear_j3/css/style.css');
$doc->addStyleSheet('templates/clear_j3/css/slideout.css');
$doc->addStyleSheet('templates/clear_j3/css/phone.css');
?>
<!DOCTYPE html>
<html xmlns="https://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
    <link rel="icon" href="http://printervoronezh.ru/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="http://printervoronezh.ru/favicon.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="yandex-verification" content="9a387fc9a22be6d3" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,300italic,700&subset=latin,cyrillic" rel="stylesheet" type="text/css">
    <jdoc:include type="head" />
    <script type="text/javascript">
    </script>
<style type="text/css">
.hide-on-mobile { display: inline; }
/* Smartphone Portrait and Landscape */
@media only screen
and (min-device-width : 320px)
and (max-device-width : 480px){ .hide-on-mobile { display: none; }}
</style>
</head>
<body>
    <nav id="menu" class="menu">
    <jdoc:include type="modules" name="user3" />
    <jdoc:include type="modules" name="left" style="rounded" />
    </nav>
    <div class="panel-header fixed-header">
        <button class="btn-hamburger js-slideout-toggle"><span class="c-hamburger"><span></span></span></button>
        <div class="info">
            <div class="login-head"><a href="/registratsiya.html"></a></div>
            <div class="cart-head">
                <jdoc:include type="modules" name="cart-head" />
            </div>
        </div>
    </div>
    <main id="panel" class="panel">
        <div class="fix-sep"></div>
        <div id="page">

        <div id="header" class="wrap">
                <div class="wrap">
                    <div class="header-inner">
                    <div id="menutop" class="clear">
                        <jdoc:include type="modules" name="user3" />
                        </div>
                        <div class="phone">
                            <a href="tel:+74732122367"><h5>+7(473)212-23-67</h5></a><span><strong>Адрес:</strong> г. Воронеж Ленинский пр-кт д. 16</span><span><strong>E-mail:</strong> <a href="mailto:info@printervoronezh.ru" style="font-size: 15px;">info@printervoronezh.ru</a></span>
                        </div>
                        <div class="cart-head">
                        <div class="login-head">
                        <a href="/index.php?option=com_users&view=login"></a></div>
                        <jdoc:include type="modules" name="cart-head" />
                        </div>
                    </div>
                </div>
            </div>   
<div id="content" class="wrap">
    <div class="left">
        <jdoc:include type="modules" name="left" style="rounded" />
    </div>
    <div class="middle">
        <jdoc:include type="message" />
        <jdoc:include type="component" />
        <?php if ($this->countModules('user1')) : ?>
        <jdoc:include type="modules" name="user1" style="rounded" />
        <?php endif; ?>

        <?php if ($this->countModules('content-bottom')) : ?>
        <?php if ($_REQUEST['task'] != 'view') { ?>
        <jdoc:include type="modules" name="content-bottom" style="rounded" />
        <?php } ?>
        <?php endif; ?>
    </div>
</div>
</div>








<div class="footerbg">

<jdoc:include type="modules" name="footertext" />
       </div>
   </main>
<div class="hide-on-mobile">
<jdoc:include type="modules" name="debug" />
</div>
<br><br><br>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-146140737-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-146140737-1');
</script>
<meta name="yandex-verification" content="9a387fc9a22be6d3" />

</body>
<script src="/templates/clear_j3/js/jquery.matchHeight.js"></script>
<script type='text/javascript' src='/templates/clear_j3/js/slideout.js'></script>
<script>
   jQuery(function($) {
       var slideout = new Slideout({
           'panel': document.getElementById('panel'),
           'menu': document.getElementById('menu'),
           'touch': false
       });

       document.querySelector('button').addEventListener('click', function() {
           slideout.toggle();
       });

       var fixed = document.querySelector('.fixed-header');
       slideout.on('translate', function(translated) {
           fixed.style.transform = 'translateX(' + translated + 'px)';
       });

       slideout.on('beforeopen', function() {
           fixed.style.transition = 'transform 300ms ease';
           fixed.style.transform = 'translateX(256px)';
       });

       slideout.on('beforeclose', function() {
           fixed.style.transition = 'transform 300ms ease';
           fixed.style.transform = 'translateX(0px)';
       });

       slideout.on('open', function() {
           fixed.style.transition = '';
       });

       slideout.on('close', function() {
           fixed.style.transition = '';
       });
   });

</script>

</html>