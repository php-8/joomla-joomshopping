<?php                       
/**
* @version      4.9.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<style type="text/css">
.shipping_descr{display: none;}

.jshop {
	margin-bottom: 10px;
}
.jshop input[type=radio] {
	display: none;
}
.jshop label {
	display: inline-block;
	cursor: pointer;
	position: relative;
	padding-left: 25px;
	margin-right: 0;
	line-height: 18px;
	user-select: none;
}
.jshop label:before {
	content: "";
	display: inline-block;
	width: 17px;
	height: 18px;
	position: absolute;
	left: 0;
	bottom: 1px;
	background: url(http://printervoronezh.ru/images/radio-1.png) 0 0 no-repeat;
}
 
/* Checked */
.jshop input[type=radio]:checked + label:before {
	background: url(http://printervoronezh.ru/images/radio-2.png) 0 0 no-repeat;
}
 
/* Hover */
.jshop label:hover:before {
	filter: brightness(120%);
}
 
/* Disabled */
.jshop input[type=radio]:disabled + label:before {
	filter: grayscale(100%);
}
</style>
<div id="comjshop">
    <?php print $this->checkout_navigator?>
    <?php print $this->small_cart?>

    <h1>Способ оплаты</h1>
<hr>
    <script type="text/javascript">
    var payment_type_check = {};
    <?php foreach($this->payment_methods as  $payment){?>
        payment_type_check['<?php print $payment->payment_class;?>'] = '<?php print $payment->existentcheckform;?>';
    <?php }?>
    </script>

    <div class="jshop checkout_payment_block">
        <form id = "payment_form" name = "payment_form" action = "<?php print $this->action ?>" method = "post" autocomplete="off" enctype="multipart/form-data">
            <?php print $this->_tmp_ext_html_payment_start?>
            <div id="table_payments">
                <?php 
                $payment_class = "";
                foreach($this->payment_methods as  $payment){
                    if ($this->active_payment==$payment->payment_id) $payment_class = $payment->payment_class;
                    ?>                    
                    <div class="name">
                        <input type = "radio" name = "payment_method" id = "payment_method_<?php print $payment->payment_id ?>" onclick = "showPaymentForm('<?php print $payment->payment_class ?>')" value = "<?php print $payment->payment_class ?>" <?php if ($this->active_payment==$payment->payment_id){?>checked<?php } ?> />
                        <label for = "payment_method_<?php print $payment->payment_id ?>"><?php
                            if ($payment->image){
                                ?><span class="payment_image"><img src="<?php print $payment->image?>" alt="<?php print htmlspecialchars($payment->name)?>" /></span><?php
                            }
                            ?><b><?php print $payment->name;?></b> 
                            <?php if ($payment->price_add_text!=''){?>
                                <span class="payment_price">(<?php print $payment->price_add_text?>)</span>
                            <?php }?>
                        </label>
                    </div>                    
                    <div class="paymform" id="tr_payment_<?php print $payment->payment_class ?>" <?php if ($this->active_payment != $payment->payment_id){?>style = "display:none"<?php } ?>>
                        <div class = "jshop_payment_method">
                            <?php print $payment->payment_description?>
                            <?php print $payment->form?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        
            <?php print $this->_tmp_ext_html_payment_end?>
            <input type = "button" id = "payment_submit" class = "btn btn-primary button" name = "payment_submit" value = "<?php print _JSHOP_NEXT ?>" onclick="checkPaymentForm();" />
        </form>
    </div>
</div>

<?php if ($payment_class){ ?>
<script type="text/javascript">
    showPaymentForm('<?php print $payment_class;?>');
</script>
<?php } ?>