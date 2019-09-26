<div class="joomshopping_tags">
<?php
foreach($product as $key => $value){
	echo "<div class='jshopping_tag'>";
	$cat_id = $value->category_id;
	$pr_id = $value->product_id; 
	$url = SEFLink("index.php?option=com_jshopping&controller=product&task=view&category_id=$cat_id&product_id=$pr_id",1);
	echo "<a href='$url'>".$value->name."</a>";
	echo "</div> ";
}
?>
</div>