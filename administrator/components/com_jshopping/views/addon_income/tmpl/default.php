<?php
	defined('_JEXEC') or die();
    
    $today = $this->today;           
    $week = $this->week;
    $month = $this->month;
    $year = $this->year;
	$from_date = $this->from_date;
	$to_date   = $this->to_date;
	
	if(class_exists('JHtmlSidebar') && count(JHtmlSidebar::getEntries()))
		$sidebar = JHtmlSidebar::render();
	$classMain = '';
	if($sidebar): $classMain = ' class="span10 jshop_edit"';
?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $sidebar; ?>
	</div>
<?php endif; ?>

<div id="j-main-container"<?php echo $classMain;?>>
<?php displaySubmenuOptions();?>

<form class="orders-form" name="adminForm" id="adminForm" method="post" action="index.php?option=com_jshopping&controller=addon_income">
	<div id="j-main-container">
	 <table style='width:100%'>
	  <tr>
	   <td>	
		<div id="filter-bar">
			<table style='float:left;'>
				<tr>
					<td>
						<?php print _DATE_FROM;?>:
					</td>
					<td>
						<?php print JHTML::_('calendar', $from_date, 'from_date', 'from_date', '%Y-%m-%d %H:%M:%S'); ?>
					</td>
					<td>
						&nbsp;
					</td>
					<td>
						<?php print _DATE_TO;?>
					</td>
					<td>
						<?php print JHTML::_('calendar', $to_date, 'to_date', 'to_date', '%Y-%m-%d %H:%M:%S'); ?>
					</td>
					
					<td>
						<button class="btn tip hasTooltip" type="submit" title="">
							<?php print _BUTTON_SEARCH;?>
						</button>
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;
					</td>
				</tr>
			</table>
		</div>
	   </td>
	  </tr>
	  <tr>
		<td>
<?php if ( version_compare(JVERSION, '3.0.0', '>=') ) { ?>
    <ul class="nav nav-tabs">    
        <li class="active"><a href="#tab_general" data-toggle="tab"><?php echo _GENERAL;?></a></li>
        <li><a href="#tab_orders" data-toggle="tab"><?php echo _ORDERS;?></a></li>
        <li><a href="#tab_products" data-toggle="tab"><?php echo _PRODUCTS;?></a></li>
    </ul>
<?php } else {
    jimport('joomla.html.pane');
    $pane = JPane::getInstance('tabs'); 
    echo $pane->startPane( 'pane' );
}?>


<?php if ( version_compare(JVERSION, '3.0.0', '>=') ) { ?>
<div id="editdata-document" class="tab-content">
    <div id="tab_general" class="tab-pane active">
<?php } else {
    echo $pane->startPanel( _GENERAL, 'general' ); 
}?>
    
<table class = "adminlist" >
    <thead>
        <tr>
            <th width = "140" align = "left" >
                &nbsp;
            </th>
            <th width = "115" >
              <?php echo _JSHOP_THIS_DAY;?>
            </th>
            <th width = "115" >
                <?php echo _JSHOP_THIS_WEEK;?>
            </th>
            <th width = "115" >
                <?php echo _JSHOP_THIS_MONTH;?>
            </th>
            <th width = "115" >
                <?php echo _JSHOP_THIS_YEAR;?>
            </th>
        </tr>
    </thead> 
    <tr>
        <td align = "left">
            <b><?php echo _JSHOP_PRICE;?></b>
        </td>
        <td style="text-align:right;">
            <?php echo formatprice( $today['total_sum']); ?>
        </td>   
        <td style="text-align:right;">
            <?php  echo formatprice( $week['total_sum']) ; ?>   
        </td>
        <td style="text-align:right;">
         <?php  echo formatprice( $month['total_sum']); ?>  
        </td>
        <td style="text-align:right;">
            <?php  echo formatprice( $year['total_sum']); ?>
        </td>  
    </tr>
    <tr>
        <td align = "left">
            <b><?php echo _JSHOP_PRODUCT_BUY_PRICE;?></b>
        </td>
        <td style="text-align:right;">
            <div><?php echo formatprice( $today['buy_total_sum']); ?></div>
        </td>   
        <td style="text-align:right;">
            <div><?php echo formatprice( $week['buy_total_sum']); ?></div>
        </td>
        <td style="text-align:right;">
            <div><?php echo formatprice( $month['buy_total_sum']); ?></div>
        </td>
        <td style="text-align:right;">
            <div><?php echo formatprice( $year['buy_total_sum']); ?></div>
        </td>  
    </tr>
    <tr align = "left">
        <td  align = "left">
            <b><?php echo _INCOME;?></b>
        </td>
        <td style="text-align:right;">
            <div><?php echo formatprice( $today['total_sum']-$today['buy_total_sum']); ?></div>
        </td>   
        <td style="text-align:right;">
            <div><?php echo formatprice( $week['total_sum']-$week['buy_total_sum']); ?></div>
        </td>
        <td style="text-align:right;">
            <div><?php echo formatprice( $month['total_sum']-$month['buy_total_sum']); ?></div>
        </td>
        <td style="text-align:right;">
            <div><?php echo formatprice( $year['total_sum']-$year['buy_total_sum']); ?></div>
        </td>  
    </tr>
    </table>

<?php if ( version_compare(JVERSION, '3.0.0', '>=') ) { ?>
    </div>
    <div id="tab_orders" class="tab-pane">
<?php } else {
    echo $pane->endPanel();
    echo $pane->startPanel(_ORDERS, 'orders');
}?>


    <table class = "adminlist">
    <thead>
        <tr>
            <th width = "11" >
                <?php echo _ID;?>
            </th>
            <th width = "55" >
                <?php echo _ORDER_NUMBER;?>
            </th>
            <th width = "115" >
                <?php echo _DATE;?>
            </th>
            <th width = "115" >
                <?php echo _JSHOP_PRICE;?>
            </th>
            <th width = "115" >
                <?php echo _JSHOP_PRODUCT_BUY_PRICE;?>
            </th>
            <th width = "115" >
                <?php echo _INCOME;?>
            </th>
        </tr>
    </thead> 
    <?php if ( count($this->income_orders) ) foreach ($this->income_orders as $op) { ?>
    <tr>
        <td style="text-align:left;">
            <?php echo $op['order_id']; ?>
        </td>
        <td style="text-align:left;">
            <a target="_blank" href = "index.php?option=com_jshopping&controller=orders&task=show&order_id=<?php echo $op['order_id']; ?>">
                <?php echo $op['order_number']; ?>
            </a>
        </td>
        <td style="text-align:right;">
            <?php echo $op['order_date']; ?>
        </td>
        <td style="text-align:right;">
            <?php echo formatprice( $op['total_sum']); ?>
        </td> 
        <td style="text-align:right;">
            <?php  echo formatprice( $op['buy_total_sum']) ; ?>   
        </td>
        <td style="text-align:right;">
         <?php echo formatprice( $op['total_sum'] - $op['buy_total_sum'] ); ?>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td colspan="6" style='padding-top:25px;'>
            <?php echo $this->IOpageNav->getListFooter();?>
        </td>
    </tr>
    </table>
    
    <input type = "hidden" name = "task" value = "" />
    <input type = "hidden" name = "boxchecked" value = "0" />
    <input type = "hidden" name = "tab" value = "orders" />
    </form>

<?php if ( version_compare(JVERSION, '3.0.0', '>=') ) { ?>
    </div>
    <div id="tab_products" class="tab-pane">
<?php } else {
    echo $pane->endPanel();
    echo $pane->startPanel(_PRODUCTS, 'products');
}?>
        
<form class="products-form" name="none" id="none" method="post" action="index.php?option=com_jshopping&controller=addon_income">
    <table class = "adminlist">
    <thead>
        <tr>
            <th width = "11" >
                <?php echo _ID;?>
            </th>
            <th width = "55" >
                <?php echo _PRODUCT_NAME;?>
            </th>
            <th width = "115" >
                <?php echo _DATE;?>
            </th>
            <th width = "115" >
                <?php echo _SINGLE_PRICE;?>
            </th>
            <th width = "11" >
                <?php echo _PRODUCT_QUANTITY;?>
            </th>
            <th width = "115" >
                <?php echo _JSHOP_PRICE;?>
            </th>
            <th width = "115" >
                <?php echo _JSHOP_PRODUCT_BUY_PRICE;?>
            </th>
            <th width = "115" >
                <?php echo _INCOME;?>
            </th>
        </tr>
    </thead> 
    <?php  if ( count($this->income_products) ) foreach ($this->income_products as $oi) { ?>
    <tr>
        <td  align = "left">
            <?php echo $oi['order_item_id']; ?>
        </td>
        <td style="text-align:left;">
            <a target="_blank" href="index.php?option=com_jshopping&controller=products&task=edit&product_id=<?php echo $oi['product_id'];?>">
                <?php echo $oi['product_name']; ?>
            </a>
        </td>
        <td style="text-align:right;">
            <?php echo $oi['order_date']; ?>
        </td>
        <td style="text-align:right;">
            <?php echo formatprice( $oi['product_item_price']); ?>
        </td>
        <td style="text-align:right;">
            <?php echo (int) $oi['product_quantity']; ?>
        </td>
        <td style="text-align:right;">
            <?php echo formatprice( $oi['total_price']); ?>
        </td>
        <td style="text-align:right;">
            <?php  echo formatprice( $oi['total_buyprice']) ; ?>   
        </td>
        <td style="text-align:right;">
         <?php echo formatprice( $oi['total_income']); ?>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td colspan="8" align="center" style='padding-top:25px;'>
            <?php
                echo $this->IPpageNav->getListFooter();
//                echo $this->IPpageNav->getPagesLinks();
            ?>
        </td>
    </tr>
    </table>

    <input type = "hidden" name = "task" value = "" />
    <input type = "hidden" name = "boxchecked" value = "0" />
    <input type = "hidden" name = "tab" value = "products" />
    </form>
        
<?php if ( version_compare(JVERSION, '3.0.0', '>=') ) { ?>
    </div>
</div>
<?php } else {
    echo $pane->endPanel();
    echo $pane->endPane();
}?>     

		</td>
	   </tr>
	  </table>
	</div>
<script type="text/javascript" language="javascript">
    jQuery(document).ready(function()
    {
        var tabactive = "<?php echo $this->tabactive; ?>";
        
        checkTabActive(tabactive);
    });
</script>