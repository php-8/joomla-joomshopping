<div class="manufactuter_list">

<div class="manufactuter_list_inner">
<?php
  foreach($list as $curr){
      $class = "jshop_menu_level_0";
	  if ($curr->manufacturer_id == $manufacturer_id) $class = $class."_a";      
      ?>
      <div class = "manuf">
        <div class="manuf_inner">
            <a href = "<?php print $curr->link?>">
                <?php if ($show_image && $curr->manufacturer_logo){?>
                    <img align = "absmiddle" src = "<?php print $jshopConfig->image_manufs_live_path."/".$curr->manufacturer_logo?>" alt = "<?php print $curr->name?>" />
                <?php } ?>
                <?php /*<div class="manuf-title"><?php print $curr->name?></div>*/ ?>
            </a>
        </div>
      </div>
<?php } ?>
</div>

</div>