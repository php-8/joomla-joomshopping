<?php 
/**
* @version      4.10.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class = "jshop pagelogin col-sm-offset-4" id="comjshop">    
    <?php print $this->checkout_navigator?>
    
    <?php if ($this->config->shop_user_guest && $this->show_pay_without_reg) : ?>
        <span class = "text_pay_without_reg"><?php print _JSHOP_ORDER_WITHOUT_REGISTER_CLICK?> <a href="<?php print SEFLink('index.php?option=com_jshopping&controller=checkout&task=step2',1,0, $this->config->use_ssl);?>"><?php print _JSHOP_HERE?></a></span>
    <?php endif; ?>
    
    <?php echo $this->tmpl_login_html_1?>
    <div class = "row-fluid">
        <div class = "span6 login_block">
			<?php echo $this->tmpl_login_html_2?>
            
            <form method="post" action="<?php print SEFLink('index.php?option=com_jshopping&controller=user&task=loginsave', 1,0, $this->config->use_ssl)?>" name="jlogin" class="">
                <div class="control-group">

                    <div class="controls">
                        <input type="text" id="jlusername" name="username" value="" class="inputbox" placeholder="<?php print _JSHOP_USERNAME ?>"/>
                    </div>
                </div>
                
                <div class="control-group rowpasword">
                    
                    <div class="controls">
                        <input type="password" id="jlpassword" name="passwd" value="" class="inputbox" placeholder="<?php print _JSHOP_PASSWORT ?>" />
                    </div>
                </div>
                
                <div class="control-group checkbox rowremember">
                    <div class="controls">
						<label>
                        <input type="checkbox" name="remember" id="remember_me" value="yes" />
                        <?php print _JSHOP_REMEMBER_ME ?>
						</label>
                    </div>
                </div>
                
                <div class="control-group rowbutton">
                    <div class="controls">
                        <input type="submit" class="btn btn-default" value="<?php print _JSHOP_LOGIN ?>" />
                    </div>
                </div>
                
                <div class="control-group rowlostpassword">
                    <span class="controls">
                        <a href = "<?php print $this->href_lost_pass ?>"><?php print _JSHOP_LOST_PASSWORD ?></a>
                    </span>
					<span class="controls">
                        <a href = "<?php print $this->href_register ?>"><?php print _JSHOP_HAVE_NOT_ACCOUNT ?></a>
                    </span>
                </div>
                
                <input type = "hidden" name = "return" value = "<?php print $this->return ?>" />
                <?php echo JHtml::_('form.token');?>
				<?php echo $this->tmpl_login_html_3?>
            </form>   
        </div>
       
    </div>
	<?php echo $this->tmpl_login_html_6?>
</div>    