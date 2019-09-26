<?php

defined('_JEXEC') or die;
?>
<div class="nvg_callback">
<strong><?php echo _JSHOP_F_NAME ?>:</strong> <?php echo $this->name ?><br />
<strong><?php echo _JSHOP_EMAIL ?>:</strong> <?php echo $this->email ?><br />
<strong><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FEEDBACK_MESSAGE') ?>:</strong> <?php echo $this->message ?><br />
<strong><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FEEDBACK_URL') ?>:</strong> <a href="<?php echo $this->url ?>"><?php echo $this->url ?></a><br />
</div>