<?php 
$document = JFactory::getDocument();
$document->addStyleSheet(Juri::base() . "modules/mod_joomshopping_categories_menu/css/mat_joomshopping_menu_style.css",'text/css',"screen");

if($load_jquery == 1){JHtml::script(Juri::base() . 'modules/mod_joomshopping_categories_menu/js/jquery-3.2.1.min.js');}
JHtml::script(Juri::base() . 'modules/mod_joomshopping_categories_menu/js/mat_joomshopping_menu.js');

?>
<div class = "<?php print $class; ?>">
	<nav>
		<div id="mat_categories_menu" class="mat_categories_menu">
			<ul>
				<?php 
				$level = 0;
				$active_category = true;
				$category = JTable::getInstance('category', 'jshop');        
				$b = $category->getAllCategories($publish = 1, $access = 1, $listType = 'name');
				foreach ($b as $valdal){
					$category_id = $valdal->category_id;
					$category = JTable::getInstance('category', 'jshop');        
					$category->load($category_id);
					$categories_id = $category->getTreeParentCategories();
					if ($category_id && $valdal->category_parent_id <= 0 ){
						if (isset($categories_id[$level])){
							$cat = JTable::getInstance('category', 'jshop');  
							$cat->load($category_id);
							$cats = $cat->getSisterCategories($order, $ordering);
							foreach ($cats as $key=>$value){
								$value->level = $level;
								if (in_array($value->category_id, $categories_id)){
								
									// 1 Проверяем совпадает ли ID категории в запросе с текущей ID категорией, если да то назначаем active, если нет то no_active
									if($request_id == $value->category_id){$active_category = "active";} else{$active_category = "no_active";};
									
									$product_count = NULL;
									if($display_count == 1){
										foreach($countproducts as $key => $prod_count){
											if($value->category_id == $key){$product_count = "(" . $prod_count . ")";}
										}
									}
									print $value->product_link = '<li><a href = "' . $value->category_link . '" class="' . $active_category . '" >' . $value->name . '<span class="mat_product_count">' . $product_count . '</span> </a><div class="mat_but_hide"></div>';
									unset($active_category);
									unset($product_count);
									// Children Start
									if ($value->category_id == $category_id){
										$cat = JTable::getInstance('category', 'jshop');        
										$cat->load($category_id);
										$cat->category_id = $category_id; 
										$childs = $cat->getChildCategories($order, $ordering);
										if(!empty($childs)){
										?><ul class="submenu"><?php
										foreach ($childs as $key2=>$value2){
										
										// 2 Проверяем совпадает ли ID категории в запросе с текущей ID категорией, если да то назначаем active, если нет то no_active					
										if($request_id == $value2->category_id){$active_category = "active";} else{$active_category = "no_active";};
										// Проверяем есть ли подкотегории чтобы назначить кнопку плюс
										if (!empty($cat->getSubCategories($value2->category_id))){$mat_but_hide = "mat_but_hide";} else {$mat_but_hide = NULL;}
										
										$product_count = NULL;
										if($display_count == 1){
											foreach($countproducts as $key => $prod_count){
												if($value2->category_id == $key){$product_count = "(" . $prod_count . ")";}
											}
										}	
											print $value2->product_link = '<li><a href = "' . $value2->category_link . '" class="' . $active_category . '" >' . $value2->name . '<span class="mat_product_count">' . $product_count . '</span> </a><div class="'. $mat_but_hide .'"></div>';
											
											unset($active_category);
											unset($product_count);
											
											// Children Two Start
											
											 if (!empty($cat->getSubCategories($value2->category_id)) && $cat->getSubCategories($value2->category_id, $order, $ordering, $publish=1)){
												$cat = JTable::getInstance('category', 'jshop');        
												$cat->load($category_id);
												$cat->category_id = $category_id; 
												$childs = $cat->getSubCategories($value2->category_id, $order, $ordering);
												
												?><ul class="submenu"><?php
												foreach ($childs as $key3=>$value3){
													
													// 3 Проверяем совпадает ли ID категории в запросе с текущей ID категорией, если да то назначаем active, если нет то no_active
													if($request_id == $value3->category_id){$active_category = "active";} else{$active_category = "no_active";};
													
													// Проверяем есть ли подкотегории чтобы назначить кнопку плюс
													if (!empty($cat->getSubCategories($value3->category_id))){$mat_but_hide = "mat_but_hide";} else {$mat_but_hide = NULL;}
													
													$product_count = NULL;
													if($display_count == 1){
														foreach($countproducts as $key => $prod_count){
															if($value3->category_id == $key){$product_count = "(" . $prod_count . ")";}
														}
													}
													print $value3->product_link = '<li><a href = "' . $value3->category_link . '" class="' . $active_category . '">' . $value3->name . '<span class="mat_product_count">' . $product_count . '</span> </a><div class="'. $mat_but_hide .'"></div>';
													
													unset($active_category);
													unset($product_count);
													
													// Children Three Start
													if (!empty($cat->getSubCategories($value3->category_id)) && $cat->getSubCategories($value3->category_id, $order, $ordering, $publish=1)){
														$cat = JTable::getInstance('category', 'jshop');        
														$cat->load($category_id);
														$cat->category_id = $category_id; 
														$childs = $cat->getSubCategories($value3->category_id, $order, $ordering);
														
														?><ul class="submenu"><?php
														foreach ($childs as $key4=>$value4){
															
															// 3 Проверяем совпадает ли ID категории в запросе с текущей ID категорией, если да то назначаем active, если нет то no_active
															if($request_id == $value4->category_id){$active_category = "active";} else{$active_category = "no_active";};
															
															// Проверяем есть ли подкотегории чтобы назначить кнопку плюс
															if (!empty($cat->getSubCategories($value4->category_id))){$mat_but_hide = "mat_but_hide";} else {$mat_but_hide = NULL;}
															
															$product_count = true;
															if($display_count == 1){
																foreach($countproducts as $key => $prod_count){
																	if($value4->category_id == $key){$product_count = "(" . $prod_count . ")";}
																}
															}
															print $value4->product_link = '<li><a href = "' . $value4->category_link . '" class="' . $active_category . '" >' . $value4->name . '<span class="mat_product_count">' . $product_count . '</span> </a><div class="'. $mat_but_hide .'"></div>';
															unset($product_count);
														}
														?></ul><?php 
													}
													?></li><?php
												}
												?></ul><?php 
											}
											?></li><?php
										 }
									?></ul><?php
										}
									}
						} //else {
							//foreach($countproducts as $key => $prod_count){
							//	if($value->category_id == $key){$product_count = "(" . $prod_count . ")";}
							//}
							//print $value->product_link = '<li><a href = "' . $value->category_link . '">' . $value->name . '<span class="mat_product_count">' . $product_count . '</span></a></li>';
							//unset($product_count);
						//}
								?></li><?php
							}
						}
					} else {
						$cat = JTable::getInstance('category', 'jshop');
						$cat->category_parent_id = 0;
						$cats = $cat->getSisterCategories($order, $ordering);
						foreach($cats as $key=>$value){
							$cats[$key]->level = 0;
						}
					}
				//End Foreach
				}
				?>
			</ul>
		</div>
	</nav>
</div>