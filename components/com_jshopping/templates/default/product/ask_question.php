<?php

defined('_JEXEC') or die;
?>
<div class="jshop productask">
<?php
if ($this->send) {
	echo _JSHOP_PRODUCT_ASK_QUESTION_THANX;
} else if ($this->admin_mail) {
	print _JSHOP_PRODUCT.': <a href="'.$this->product->href.'">'.$this->product->name.'</a><br/>';
	print _JSHOP_PRODUCT_ASK_QUESTION_USER_NAME.': '.$this->user_name.'<br/>';
	print _JSHOP_PRODUCT_ASK_QUESTION_USER_EMAIL.': '.$this->user_email.'<br/>';
	print _JSHOP_PRODUCT_ASK_QUESTION_TEXT.':<br/>';
	print $this->user_question;
} else {
?>
	<h1><?php echo _JSHOP_PRODUCT_ASK_QUESTION_LINK; ?></h1>
	<table class="jshop">
		<tr>
			<td class="image_middle">
				<?php
				if(count($this->images)) {
					$image = $this->images[0];
				?>
				<img src = "<?php echo $this->image_product_path?>/<?php echo $image->image_name;?>" alt="<?php echo htmlspecialchars($image->_title)?>" title="<?php echo htmlspecialchars($image->_title)?>" />
				<?php } else { ?>
				<img src = "<?php echo $this->image_product_path?>/<?php echo $this->noimage?>" alt = "<?php echo htmlspecialchars($this->product->name)?>" />
				<?php } ?>
			</td>
			<td>
				<h2><?php echo $this->product->name?></h2>
				<div class="jshop_prod_description">
					<?php echo $this->product->short_description; ?>
				</div>        
			</td>
		</tr>
	</table>
	<form action="<?php echo $this->action?>" name="ask_question" method="post" onsubmit="return validateReviewForm(this.name)">
		<input type="hidden" name="product_id" id="product_id" value="<?php echo $this->product->product_id?>" />
		<input type="hidden" name="category_id" id="category_id" value="<?php echo $this->category_id?>" />
		<table id="jshop_ask_question">
			<tr>
				<td>
					<?php echo _JSHOP_PRODUCT_ASK_QUESTION_USER_NAME?>
				</td>
				<td>
					<input type="text" name="user_name" id="review_user_name" class="inputbox" value="<?php echo $this->user->username?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _JSHOP_PRODUCT_ASK_QUESTION_USER_EMAIL?>
				</td>
				<td>
					<input type="text" name="user_email" id="review_user_email" class="inputbox" value="<?php echo $this->user->email?>" />
				</td>
			</tr>
			<tr>
				<td>
					<?php echo _JSHOP_PRODUCT_ASK_QUESTION_TEXT?>
				</td>
				<td>
					<textarea name="user_question" id="review_review" rows="4" cols="40" class="inputbox"></textarea>
				</td>
			</tr>
			<?php if (isset($this->_tmp_product_ask_question_before_submit)) echo $this->_tmp_product_ask_question_before_submit;?>
			<tr>
				<td></td>
				<td>
					<input type="submit" class="button validate" value="<?php echo _JSHOP_PRODUCT_ASK_QUESTION_SUBMIT?>" />
				</td>
			</tr>
		</table>
	</form>
<?php
}
?>
</div>