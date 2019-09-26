<?php
/**
* @package Joomla
* @subpackage JoomShopping
* @author Garry
* @website https://joom-shopping.com
* @email info@joom-shopping.com
* @copyright Copyright © All rights reserved.
* @license GNU GPL v3
* @license agreement https://joom-shopping.com/polzovatelskoe-soglashenie
**/

defined('_JEXEC') or die;
?>
<script type="text/javascript">
var payment_type_check = {};
<?php foreach($this->payment_methods as $payment){ ?>
    payment_type_check['<?php echo $payment->payment_class ?>'] = '<?php echo $payment->existentcheckform ?>';
<?php } ?>
</script>

<div class="jshop">
<?php echo $this->_tmp_ext_html_payment_start ?>
<table id = "table_payments" cellspacing="0" cellpadding="0">
  <?php 
		$payment_class = '';
  foreach($this->payment_methods as  $payment){
		if ($this->active_payment == $payment->payment_id) {
			$payment_class = $payment->payment_class;
		}
  ?>
  <tr>
    <td style = "padding-top:5px; padding-bottom:5px">
	<label for = "payment_method_<?php echo $payment->payment_id ?>">
					<input type="radio" name="payment_method" id="payment_method_<?php echo $payment->payment_id ?>" onclick="oneStepCheckout.showPaymentForm('<?php echo $payment->payment_class ?>', 1)" value="<?php echo $payment->payment_class ?>" <?php if ($this->active_payment==$payment->payment_id){ ?>checked<?php } ?> />
					<?php if ($payment->image) { ?>
					<span class="payment_image"><img src="<?php echo $payment->image ?>" alt="<?php echo htmlspecialchars($payment->name) ?>" /></span>
					<?php } ?>
					<b><?php echo $payment->name ?></b>
        <?php if ($payment->price_add_text!=''){ ?>
            (<?php echo $payment->price_add_text ?>)
        <?php }?>
      </label>
    </td>
  </tr>
		<tr id="tr_payment_<?php echo $payment->payment_class ?>" <?php if ($this->addonParams->payment_params && $this->active_payment != $payment->payment_id){ ?>style="display:none"<?php } ?>>
    <td class = "jshop_payment_method">
        <?php echo $payment->payment_description ?>
        <?php echo $payment->form ?>
    </td>
  </tr>
  <?php } ?>
</table>
<br />
<?php echo $this->_tmp_ext_html_payment_end ?>
</div>

<?php if ($payment_class){ ?>
<script type="text/javascript">
    oneStepCheckout.showPaymentForm('<?php echo $payment_class ?>', 0);
</script>
<?php } ?>