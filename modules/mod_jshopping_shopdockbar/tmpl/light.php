<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Dmitry Stashenko
* @website http://nevigen.com/
* @email support@nevigen.com
* @copyright Copyright © Nevigen.com. All rights reserved.
* @license Proprietary. Copyrighted Commercial Software
* @license agreement http://nevigen.com/license-agreement.html
**/

defined('_JEXEC') or die;
?>
<div id="strach_warper">
	<div id="fixblock">
		<div id="fixbar">
			<?php if ($mod_params->show_contacts) { ?>
			<div id="dockbar_phone" onclick="dockbar.toggle(this)"><?php echo $mod_params->department_phone_1 ?></div>
			<?php } ?>
			
			<?php if ($mod_params->show_cart) { ?>
			<div id="dockbar_cart" onclick="dockbar.toggle(this)"> 
				<span class="cart_dockbar">
					<img src="/modules/mod_jshopping_shopdockbar/images/default/cart_icon.png" width="24" height="24" style="margin-bottom: -5px;" /> 
					<span id="count_cart_product" class="<?php echo count($cart->products) > 0 ? 'emptno' : 'emptyes' ?>" >
						<?php echo $cart->count_product ?>
					</span> 
				</span> 
				<span class="cart_summa">
					<span id = "jshop_summ_product">
						<?php echo formatprice($cart->getSum(0,1)) ?>
					</span>
				</span>
			</div>
			<span class="cartcheckout">
				<a href = "<?php echo SEFLink('index.php?option=com_jshopping&controller=cart&task=view', 1) ?>"><?php echo('Оформить') ?></a>
			</span>
            <span class="cartcheckout">
				<a href = "<?php echo SEFLink('index.php?option=com_jshopping&controller=cart&task=clear2', 1) ?>"><?php echo('Очистить корзину') ?></a>
			</span>
			<?php } ?>
            <?php if ($mod_params->show_callback) { ?>
			<div id="dockbar_callback" onclick="dockbar.toggle(this)"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FIELDSETLABEL_CALLBACK') ?></div>
			<?php } ?>
			<?php if ($mod_params->show_feedback) { ?>
			<div id="dockbar_feedback" onclick="dockbar.toggle(this)"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FIELDSETLABEL_FEEDBACK') ?></div>
			<?php } ?>
			<?php if ($mod_params->show_history) { ?>
			<div id="dockbar_history" onclick="dockbar.toggle(this)"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FIELDSETLABEL_HISTORY') ?><sub class="<?php echo !count($shopdockbar_history) ? 'sub-g' : 'sub-r' ?>">(<?php echo count($shopdockbar_history) ?>)</sub></div>
			<?php } ?>
			<?php if ($mod_params->show_wishlist && $jshopConfig->enable_wishlist) { ?>
			<div id="dockbar_wishlist" onclick="dockbar.toggle(this)"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FIELDSETLABEL_WISHLIST') ?><sub class="<?php echo !count($wishlist->products) ? 'sub-g' : 'sub-r' ?>">(<?php echo count($wishlist->products) ?>)</sub></div>
			<?php } ?>
			<?php if ($mod_params->show_compare) { ?>
			<div id="dockbar_compare" onclick="dockbar.toggle(this)"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FIELDSETLABEL_COMPARE') ?><sub class="<?php echo !count($shopdockbar_compare) ? 'sub-g' : 'sub-r' ?>">(<?php echo count($shopdockbar_compare) ?>)</sub></div>
			<?php } ?>
			<?php if ($mod_params->show_login) { ?>
			<div id="dockbar_login" onclick="dockbar.toggle(this)"><?php echo $user->id ? JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FIELDSETLABEL_LOGIN') : JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FIELDSETLABEL_LOGOFF') ?></div>
			<?php } ?>
            
		</div>
		<div id="downslide_dockbar">
			<?php if ($mod_params->show_contacts) { ?>
			<div id="downslide_dockbar_phone">
				<div class="phonewrraper">
					<div class="dwnslide_left phoneblock">
						<?php if ($mod_params->office_adress) { ?>
						<div><span class="dbtitle"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_LABEL_OFFICE_ADRESS') ?>: </span><?php echo $mod_params->office_adress ?></div>
						<?php } ?>
						<?php if ($mod_params->warehouse_adress) { ?>
						<div><span class="dbtitle"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_LABEL_WAREHOUSE_ADRESS') ?>: </span><?php echo$mod_params->warehouse_adress ?></div>
						<?php } ?>
					</div>
					<div class="dwnslide_center phoneblock">
						<div><span class="dbtitle"><?php echo $mod_params->department_name_1 ?></span>  <?php echo $mod_params->department_phone_1 ?></div>
						<div><span class="dbtitle"><?php echo $mod_params->department_name_2 ?></span>  <?php echo $mod_params->department_phone_2 ?></div>
						<div><span class="dbtitle"><?php echo $mod_params->department_name_3 ?></span>  <?php echo $mod_params->department_phone_3 ?></div>
					</div>
					<div class="dwnslide_right phoneblock">
						<div class="dbtitle"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_LABEL_OPEN_HOURS') ?></div>
						<div><?php echo $mod_params->open_hours ?></div>
					</div>				
				</div>
			</div>
			<?php } ?>
            <?php if ($mod_params->show_login) { ?>
			<div id="downslide_dockbar_login">
				<?php if($user->id) { ?>
				<div class="yeslogin">
					<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="login">
						<div>
							<a href="<?php echo SEFLink('index.php?option=com_jshopping&controller=user&task=myaccount', 1) ?>"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_LOGOUT_MY_ACCOUNT') ?></a>
						</div>
						<div>
							<a href="<?php echo SEFLink('index.php?option=com_jshopping&controller=user&task=editaccount', 1) ?>"><?php echo _JSHOP_EDIT_DATA ?></a>
						</div>
						<div>
							<a href="<?php echo SEFLink('index.php?option=com_jshopping&controller=user&task=orders', 1); ?>"><?php echo _JSHOP_SHOW_ORDERS ?></a>
						</div>
						<br/>
						<div align="center">
							<input type="submit" name="Submit" class="button" value="<?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_LOGOUT_BUTTON') ?>" />
						</div>

						<input type="hidden" name="option" value="com_users" />
						<input type="hidden" name="task" value="user.logout" />
						<input type="hidden" name="return" value="<?php echo $return ?>" />
						<?php echo JHTML::_( 'form.token' ) ?>
					</form>
				</div>
				<?php } else { ?>
				<div class="nologin">
					<form action="<?php echo JRoute::_( 'index.php'); ?>" method="post" name="login" >
						<div id="form-login-username">
							<label for="modlgn_username"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_LOGIN_USERNAME') ?></label>
							<input id="modlgn_username" type="text" name="username" class="inputbox" alt="username" size="18" />
						</div>
						<div id="form-login-password">
							<label for="modlgn_passwd"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_LOGIN_PASSWORD') ?></label>
							<input id="modlgn_passwd" type="password" name="passwd" class="inputbox" size="18" alt="password" />
						</div>
						<div>
							<span>
								<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind') ?>"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_LOGIN_LOST_USERNAME') ?></a>
							</span>
							<span>
								<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset') ?>"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_LOGIN_LOST_PASSWORD') ?></a>
							</span>	
						</div>
						<div id="form-login-btns">
							<span>
							<input type="submit" name="Submit" class="button" value="<?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_LOGIN_BUTTON') ?>" /> </span>
							</span>
							<?php if (JComponentHelper::getParams('com_users')->get('allowUserRegistration')) { ?>
							<span class="nvg_registr">
								<a href="<?php echo SEFLink('index.php?option=com_jshopping&controller=user&task=register', 1); ?>"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_LOGIN_REGISTRATION') ?></a>
							</span>
							<?php } ?>

							<?php if (JPluginHelper::isEnabled('system', 'remember')) { ?>
							<div id="form-login-remember">
								<label for="modlgn_remember"><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_LOGIN_REMEMBER_ME') ?></label>
								<input id="modlgn_remember" type="checkbox" name="remember" value="yes" alt="Remember Me" />
							</div>
							<?php } ?>
						</div>

						<input type="hidden" name="option" value="com_jshopping" />
						<input type="hidden" name="controller" value="user" />
						<input type="hidden" name="task" value="loginsave" />
						<input type="hidden" name="return" value="<?php echo $return; ?>" />
						<?php echo JHTML::_( 'form.token' ) ?>
					</form>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
			<?php if ($mod_params->show_callback) { ?>
			<div id="downslide_dockbar_callback">
				<form method="post" name="callback" action="index.php">
					<div class="nvg_callback">
						<div class="dwnslide_left alright">
							<label><?php echo _JSHOP_F_NAME ?></label>
							<input type="text" name="name" value="" required="required" /><br/>
							<label><?php echo _JSHOP_TELEFON ?></label>
							<input type="text" name="phone" value="" required="required" /><br/>
							<?php if (!$user->id) { ?>
							<input type="text" name="callback_user_phone" id="callback_user_phone" value="" required="required" />
							<input type="hidden" name="callback_user_token" id="callback_user_token" value="<?php echo md5(time()) ?>" />
							<?php } ?>
							<input type="submit" value="<?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_CALLBACK_BUTTON') ?>" />
							<input type="hidden" name="option" value="com_jshopping" />
							<input type="hidden" name="controller" value="shopdockbar" />
							<input type="hidden" name="task" value="sendCallBack" />
							<input type="hidden" name="url" value="<?php echo base64_encode(JURI::current()) ?>" />
							<?php echo JHTML::_( 'form.token' ) ?>
						</div>
						<div class="dwnslide_right">
							<?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_TEXTFORCALLBACK') ?>
						</div>
					</div>
				</form>
			</div>
			<?php } ?>
			<?php if ($mod_params->show_feedback) { ?>
			<div id="downslide_dockbar_feedback">
				<form method="post" name="feedback" action="index.php">
					<div class="nvg_feedback">
						<div class="dwnslide_left alright">
							<label><?php echo _JSHOP_F_NAME ?></label>
							<input type="text" name="name" value="" required="required" /><br/>
							<label><?php echo _JSHOP_EMAIL ?></label>
							<input type="text" name="email" value="" required="required" /><br/>
							<label><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FEEDBACK_SUBJECT') ?></label>
							<input type="text" name="subject" value="" required="required" />
							<?php if (!$user->id) { ?>
							<input type="text" name="feedback_user_phone" id="feedback_user_phone" value="" required="required" />
							<input type="hidden" name="feedback_user_token" id="feedback_user_token" value="<?php echo md5(time()) ?>" />
							<?php } ?>
						</div>
						<div class="dwnslide_right">
							<textarea name="message" class="dwnslidefeed" value="" placeholder="<?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FEEDBACK_MESSAGE') ?>" required="required"></textarea><br/>
						</div>
						<div class="clear-nvg"> </div>
						<label><?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FEEDBACK_EMAIL_COPY') ?></label>
						<input type="checkbox" name="email_copy" value="1" />
						<input type="submit" value="<?php echo JText::_('MOD_JSHOPPING_SHOPDOCKBAR_FEEDBACK_BUTTON') ?>" />
						<input type="hidden" name="option" value="com_jshopping" />
						<input type="hidden" name="controller" value="shopdockbar" />
						<input type="hidden" name="task" value="sendFeedBack" />
						<input type="hidden" name="url" value="<?php echo base64_encode(JURI::current()) ?>" />
						<?php echo JHTML::_( 'form.token' ) ?>
					</div>
				</form>
			</div>
			<?php } ?>
			<?php if ($mod_params->show_history) { ?>
			<div id="downslide_dockbar_history">
				<?php include dirname(__FILE__).'/default/history.php' ?>
			</div>
			<?php } ?>
			<?php if ($mod_params->show_wishlist && $jshopConfig->enable_wishlist) { ?>
			<div id="downslide_dockbar_wishlist">
				<?php include dirname(__FILE__).'/default/wishlist.php' ?>
			</div>
			<?php } ?>
			<?php if ($mod_params->show_compare) { ?>
			<div id="downslide_dockbar_compare">
				<?php include dirname(__FILE__).'/default/compare.php' ?>
			</div>
			<?php } ?>
			<?php if ($mod_params->show_cart) { ?>
			<div id="downslide_dockbar_cart">
				<?php include dirname(__FILE__).'/default/cart.php' ?>
			</div>
			<?php } ?>
			
		</div>
	</div>
</div>