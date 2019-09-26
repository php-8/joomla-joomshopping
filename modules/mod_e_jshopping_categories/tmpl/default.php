
<?php $menu_view = $params->get('view');
$menu_v ="200";
if ($menu_view == accordion) {?>
<div class="acc_menu_jshopping">
	<?php echo $categories; ?>
</div>
<script type="text/javascript">
function toggleShow(child,elem) {
	elem.className = (elem.className == 'open' ? 'closed' : 'open');
	jQuery(child).slideToggle(200);
	return false;
} 
</script>
<?php }  else  {  ?>
<div class="lofmenu_jshopping">
	<?php echo $categories; ?>
</div>
<script language="javascript">
	if(jQuery('.lofmenu_jshopping .lofmenu .lofitem1') ){
		jQuery('.lofmenu_jshopping .lofmenu .lofitem1').find('ul').css({'visibility':'hidden'});
	}
	jQuery(document).ready(function(){
		jQuery('.lofmenu_jshopping .lofmenu .lofitem1 ul').each(function(){
			jQuery(this).find('li:first').addClass('loffirst');
		})
		jQuery('.lofmenu_jshopping .lofmenu li').each(function(){
			jQuery(this).mouseenter(function(){
				jQuery(this).addClass('lofactive');
				jQuery(this).find('ul').css({'visibility':'visible'});
				jQuery(this).find('ul li ul').css({'visibility':'hidden'});
			});
			jQuery(this).mouseleave(function(){
				jQuery(this).removeClass('lofactive');
				jQuery(this).find('ul').css({'visibility':'hidden'});
			});
		});
	});
</script>
<?php } ?>