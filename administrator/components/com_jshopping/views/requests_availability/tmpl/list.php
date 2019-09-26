<?php
JSFactory::loadExtLanguageFile('addon_inform_availability_product');
$count = count ($this->reviews);
$i = 0;
	if(class_exists('JHtmlSidebar') && count(JHtmlSidebar::getEntries()))
		$sidebar = JHtmlSidebar::render();
	$classMain = '';
	if($sidebar):
		$classMain = ' class="span10"'; ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $sidebar; ?>
		</div>
<?php endif; ?>

<div id="j-main-container"<?php echo $classMain;?>>
<?php displaySubmenuOptions(); ?>

<form action = "index.php?option=com_jshopping&controller=requests_availability" method = "post" name="adminForm" id="adminForm">
    <table width = "100%">
        <tr>
            <td width = "95%" align = "right">
                <?php echo _IAP_EMAIL_SENT;?>:
            </td>             
            <td>
                <?php echo $this->sent_email_select;?>
            </td>            
            <td>
                <?php echo $this->categories;?>
            </td>
            <td>
                <?php echo $this->products_select;?>  
            </td>
            <td>
                <input type = "text" name = "text_search" value = "<?php echo $this->text_search;?>" />
            </td>
            <td>
                <input type = "submit" class = "button" value = "<?php echo _JSHOP_SEARCH;?>" />
            </td>
        </tr>
    </table>

    <table class = "adminlist table table-striped">
    <thead> 
        <tr>
            <th class = "title" width  = "10">
                #
            </th>
            <th width = "20">
                <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>
            <th width = "200" align = "left">
                <?php echo JHTML::_('grid.sort', _JSHOP_NAME_PRODUCT, 'product_name', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
			<th>
                <?php echo JHTML::_('grid.sort', _JSHOP_ATTRIBUTES, 'product_attr', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
            <?php if (isset($this->addonParams['show_product_code']) && $this->addonParams['show_product_code'] == 1) : ?>
                <th>
                    <?php echo JHTML::_('grid.sort', _JSHOP_EAN_PRODUCT, 'pr.product_ean', $this->filter_order_Dir, $this->filter_order); ?>
                </th>
            <?php endif; ?>
            <th>
                <?php echo JHTML::_('grid.sort', _JSHOP_USER, 'pr_rew.user', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
			 <th>
                <?php echo JHTML::_('grid.sort', _JSHOP_PRODUCT_AVAILE_NAME, 'pr_rew.name', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
            <th>
                <?php echo JHTML::_('grid.sort', _JSHOP_EMAIL, 'pr_rew.email', $this->filter_order_Dir, $this->filter_order); ?>
            </th> 
            <th>
                <?php echo JHTML::_('grid.sort', _JSHOP_DATE, 'pr_rew.date', $this->filter_order_Dir, $this->filter_order); ?> 
            </th>
            <th>
                <?php echo JHTML::_('grid.sort', 'IP', 'pr_rew.ip', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
            <th width="50">
                <?php echo _IAP_EMAIL_SENT; ?>
            </th>            
            <th width="50">
                <?php echo _JSHOP_DELETE; ?>
            </th>
            <th width = "40">
                <?php echo JHTML::_('grid.sort', _JSHOP_ID, 'pr_rew.id', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
      </tr>
    </thead> 
    <?php
     foreach ($this->reviews as $row){
     ?>
      <tr class = "row<?php echo $i % 2;?>">
       <td>
         <?php echo $this->pagination->getRowOffset($i);?>             
       </td>
       <td>
		 <?php echo JHtml::_('grid.id', $i, $row->id);?>
       </td>
       <td>
           <a href="index.php?option=com_jshopping&controller=products&task=edit&product_id=<?php print $row->product_id?>"><?php echo $row->product_name;?></a>
       </td>
	   <td>
			<?php if ($row->product_attr>0 && trim($row->attributes)==''){
				print _JSHOP_ERROR_LOAD_ATTRIBUTE;
			}?>
			<?php echo $row->attributes;?>
       </td>
       <?php if (isset($this->addonParams['show_product_code']) && $this->addonParams['show_product_code'] == 1) : ?>
            <td>
                <?php if (isset($row->product_attr_id) && $row->product_attr_id > 0 && isset($row->ean)) : ?>
                    <?php echo $row->ean;?>
                <?php else : ?>
                    <?php echo $row->product_ean;?>
                <?php endif; ?>
            </td> 
       <?php endif; ?>
       <td>
         <?php echo $row->user;?>
       </td> 
	    <td>
         <?php echo $row->name;?>
       </td> 
       <td>
         <?php echo $row->email;?>
       </td>  
       <td>
         <?php echo date("d.m.Y H:i:s",  strtotime($row->dateadd));?>
       </td>
       <td>
         <?php echo $row->ip;?>
       </td>
       <td>
         <?php if($row->email_send) { ?><img src='components/com_jshopping/images/icon-16-allow.png'> <?php }else{?><img src='components/com_jshopping/images/disabled.png'><?php }?>
       </td>       
       <td align="center">
        <a href='index.php?option=com_jshopping&controller=requests_availability&task=remove&cid[]=<?php print $row->id?>' onclick="return confirm('<?php print _JSHOP_DELETE?>')"><img src='components/com_jshopping/images/publish_r.png'></a>
       </td>
       <td align="center">
        <?php print $row->id;?>
       </td>
      </tr>
     <?php
     $i++;
     }
     ?>
     <tfoot>
     <tr>
        <td colspan="13"><?php echo $this->pagination->getListFooter();?></td>
     </tr>
     </tfoot>   
     </table>
          
    <input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
    <input type = "hidden" name = "task" value = "" />
    <input type = "hidden" name = "hidemainmenu" value = "0" />
    <input type = "hidden" name = "boxchecked" value = "0" />
</form>
</div>