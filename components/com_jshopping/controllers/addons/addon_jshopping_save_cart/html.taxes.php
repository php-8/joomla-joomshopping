<?php if(count($this->taxes)): ?>
	<table class="taxes">
		<?php foreach($this->taxes as $percent=>$value): ?>
			<tr>
				<td class="name">
					<?php echo displayTotalCartTaxName(); ?>
					<?php echo JSFactory::getConfig()->hide_tax ? '' : formattax($percent).'%'; ?>
				</td>
				<td class="value">
					<?php echo formatprice($value); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>