TRUNCATE TABLE `js_jshopping_categories`;
INSERT INTO `js_jshopping_categories` (`category_id`,`category_image`,`category_parent_id`,`category_publish`,`category_ordertype`,`category_template`,`ordering`,`category_add_date`,`products_page`,`products_row`,`access`,`name_en-GB`,`alias_en-GB`,`short_description_en-GB`,`description_en-GB`,`meta_title_en-GB`,`meta_description_en-GB`,`meta_keyword_en-GB`,`name_ru-RU`,`alias_ru-RU`,`short_description_ru-RU`,`description_ru-RU`,`meta_title_ru-RU`,`meta_description_ru-RU`,`meta_keyword_ru-RU`) VALUES
('1','cat8.jpg','0','1','1','','2','2017-08-02 11:20:03','12','3','1','','','','','','','','Крупнобытовая техника','1-krupnobytovaya-tehnika','','','','',''),
('2','cat9.jpg','1','1','1','','1','2017-08-02 11:20:03','12','3','1','','','','','','','','Холодильники','2-holodilniki','','','','',''),
('3','','2','1','1','','0','2018-04-15 12:11:29','12','3','1','','','','','','','','Acer','3-acer','','','','',''),
('4','','2','1','1','','0','2018-04-15 12:11:29','12','3','1','','','','','','','','Asus','4-asus','','','','',''),
('5','','1','1','1','','0','2018-04-15 12:11:29','12','3','1','','','','','','','','Комплектующие для ПК','5-komplektuushie-dlya-pk','','','','',''),
('6','','5','1','1','','0','2018-04-15 12:11:29','12','3','1','','','','','','','','Модули памяти','6-moduli-pamyati','','','','',''),
('7','','5','1','1','','0','2018-04-15 12:11:29','12','3','1','','','','','','','','Звуковые карты','7-zvukovye-karty','','','','',''),
('8','','0','1','1','','0','2018-04-15 12:11:29','12','3','1','','','','','','','','Крупнобытовая техника','8-krupnobytovaya-tehnika','','','','',''),
('9','','8','1','1','','0','2018-04-15 12:11:29','12','3','1','','','','','','','','Холодильники','9-holodilniki','','','','',''),
('10','','8','1','1','','0','2018-04-15 12:11:29','12','3','1','','','','','','','','Кухонные плиты','10-kuhonnye-plity','','','','',''),
('11','','2','1','1','','1','2018-04-15 12:11:56','12','3','1','','','','','','','','56785','11-56785','','','','',''),
('12','','2','1','1','','3','2018-04-15 12:11:56','12','3','1','','','','','','','','48686','12-48686','','','','','');

TRUNCATE TABLE `js_jshopping_products`;
INSERT INTO `js_jshopping_products` (`product_id`,`parent_id`,`product_ean`,`manufacturer_code`,`product_quantity`,`unlimited`,`product_availability`,`product_date_added`,`date_modify`,`product_publish`,`product_tax_id`,`currency_id`,`product_template`,`product_url`,`product_old_price`,`product_buy_price`,`product_price`,`min_price`,`different_prices`,`product_weight`,`image`,`product_manufacturer_id`,`product_is_add_price`,`add_price_unit_id`,`average_rating`,`reviews_count`,`delivery_times_id`,`hits`,`weight_volume_units`,`basic_price_unit_id`,`label_id`,`vendor_id`,`access`,`name_en-GB`,`alias_en-GB`,`short_description_en-GB`,`description_en-GB`,`meta_title_en-GB`,`meta_description_en-GB`,`meta_keyword_en-GB`,`name_ru-RU`,`alias_ru-RU`,`short_description_ru-RU`,`description_ru-RU`,`meta_title_ru-RU`,`meta_description_ru-RU`,`meta_keyword_ru-RU`,`extra_field_1`,`extra_field_2`,`extra_field_3`) VALUES
('1','0','','','10.00','1','','2017-08-02 11:20:03','2018-04-15 12:11:29','1','0','2','default','','0.0000','0.0000','150.000000','250.00','1','0.0000','1_112213_1.jpg','0','0','3','0.00','0','0','0','1.0000','2','0','0','1','','','','','','','','Acer G11195HQVB','acer-g11195hqvb','Краткое описание8','','','','','','0','0'),
('3','0','','','10.00','0','','2018-04-15 12:11:30','0000-00-00 00:00:00','1','0','2','default','','0.0000','0.0000','50.000000','0.00','0','0.0000','3_779_1.jpg','0','0','0','0.00','0','0','0','0.0000','0','0','0','1','','','','','','','','1Gb 400MHz TakeMS BD1024TEC600A bulk','1gb-400mhz-takems-bd1024tec600a-bulk','','','','','','','',''),
('2','0','','','10.00','1','','2017-08-02 11:20:03','2018-04-15 12:11:30','1','0','2','default','','0.0000','0.0000','220.000000','160.00','1','0.0000','2_19473_1.jpg','0','0','0','0.00','0','0','0','0.0000','0','0','0','1','','','','','','','','Asus ML239H','asus-ml239h','Краткое описание9','','','','','','',''),
('4','0','','','10.00','0','','2018-04-15 12:11:30','0000-00-00 00:00:00','1','0','2','default','','0.0000','0.0000','48.000000','0.00','0','0.0000','4_19253_1.jpg','0','0','0','0.00','0','0','0','0.0000','0','0','0','1','','','','','','','','1GB 800 Hynix Original l SO DIMM','1gb-800-hynix-original-l-so-dimm','','','','','','','',''),
('5','0','','','10.00','0','','2018-04-15 12:11:30','0000-00-00 00:00:00','1','0','2','default','','0.0000','0.0000','80.000000','0.00','0','0.0000','5_19322_1.jpg','0','0','0','0.00','0','0','0','0.0000','0','0','0','1','','','','','','','','Asus XONAR D1/A (PCI, support 7.1)','asus-xonar-d1-a-pci-support-71','','','','','','','',''),
('6','0','','','10.00','0','','2018-04-15 12:11:30','0000-00-00 00:00:00','1','0','2','default','','0.0000','0.0000','70.000000','0.00','0','0.0000','6_19256_1.jpg','0','0','0','0.00','0','0','0','0.0000','0','0','0','1','','','','','','','','Asus XONAR DG (PCI, Dolby Headphone 5.1)','asus-xonar-dg-pci-dolby-headphone-51','','','','','','','',''),
('7','0','','','10.00','0','','2018-04-15 12:11:30','0000-00-00 00:00:00','1','0','2','default','','0.0000','0.0000','75.000000','0.00','0','0.0000','7_783_1.jpg','0','0','0','0.00','0','0','0','0.0000','0','0','0','1','','','','','','','','Asus Xonar DS A','asus-xonar-ds-a','','','','','','','',''),
('8','0','','','10.00','0','','2018-04-15 12:11:30','0000-00-00 00:00:00','1','0','2','default','','0.0000','0.0000','250.000000','0.00','0','0.0000','8_56785_1.jpg','0','0','0','0.00','0','0','0','0.0000','0','0','0','1','','','','','','','','Ardo COO2210SHC','ardo-coo2210shc','','','','','','','',''),
('9','0','','','10.00','0','','2018-04-15 12:11:30','0000-00-00 00:00:00','1','0','2','default','','0.0000','0.0000','200.000000','0.00','0','0.0000','9_48686_1.jpg','0','0','0','0.00','0','0','0','0.0000','0','0','0','1','','','','','','','','Beko DSA 25000','beko-dsa-25000','','','','','','','',''),
('10','0','','','10.00','0','','2018-04-15 12:11:30','0000-00-00 00:00:00','1','0','2','default','','0.0000','0.0000','150.000000','0.00','0','0.0000','10_785_1.jpg','0','0','0','0.00','0','0','0','0.0000','0','0','0','1','','','','','','','','Ardo A 564 VG6 Inox','ardo-a-564-vg6-inox','','','','','','','',''),
('11','0','','','10.00','0','','2018-04-15 12:11:30','0000-00-00 00:00:00','1','0','2','default','','0.0000','0.0000','200.000000','0.00','0','0.0000','11_18967_1.jpg','0','0','0','0.00','0','0','0','0.0000','0','0','0','1','','','','','','','','Beko CE 51011','beko-ce-51011','','','','','','','',''),
('12','0','','','10.00','0','','2018-04-15 12:11:30','0000-00-00 00:00:00','1','0','2','default','','0.0000','0.0000','170.000000','0.00','0','0.0000','12_888_1.jpg','0','0','0','0.00','0','0','0','0.0000','0','0','0','1','','','','','','','','Ardo PL998CREAM','ardo-pl998cream','','','','','','','',''),
('13','0','56785','','1.00','1','','2018-04-15 12:11:56','2018-04-15 12:11:56','1','0','2','default','','0.0000','0.0000','250.000000','0.00','0','0.0000','','0','0','0','0.00','0','0','0','0.0000','0','0','0','1','','','','','','','','Ardo COO2210SHC','13-ardo-coo2210shc','Краткое описание8','Полное описание8','','','','','',''),
('14','0','48686','','1.00','1','','2018-04-15 12:11:56','2018-04-15 12:11:56','1','0','2','default','','0.0000','0.0000','200.000000','0.00','0','0.0000','','0','0','0','0.00','0','0','0','0.0000','0','0','0','1','','','','','','','','Beko DSA 25000','14-beko-dsa-25000','Краткое описание9','Полное описание9','','','','','','');

TRUNCATE TABLE `js_jshopping_products_attr`;
INSERT INTO `js_jshopping_products_attr` (`product_attr_id`,`product_id`,`buy_price`,`price`,`old_price`,`count`,`ean`,`manufacturer_code`,`weight`,`weight_volume_units`,`ext_attribute_product_id`,`attr_6`,`attr_1`,`attr_2`,`attr_3`,`attr_4`) VALUES
('18','1','0.00','300.0000','320.0000','3.0000','A56785','13','22.0000','0.0000','0','','0','0','24','22'),
('17','1','0.00','300.0000','320.0000','3.0000','A56785','12','22.0000','0.0000','0','','0','0','24','21'),
('16','1','0.00','300.0000','320.0000','3.0000','A56785','11','22.0000','0.0000','0','','0','0','23','22'),
('15','1','0.00','300.0000','320.0000','3.0000','A56785','10','22.0000','0.0000','0','','0','0','23','21'),
('14','1','0.00','280.0000','300.0000','2.0000','56785','9','20.0000','0.0000','0','','0','0','20','22'),
('13','1','0.00','250.0000','270.0000','5.0000','56785','8','20.0000','0.0000','0','','0','0','20','21'),
('24','2','200.00','250.0000','280.0000','5.0000','A48686','','18.0000','0.0000','0','','0','0','23','22'),
('23','2','180.00','220.0000','240.0000','1.0000','48686','','18.0000','0.0000','0','','0','0','23','21'),
('22','2','180.00','200.0000','220.0000','3.0000','48686','','20.0000','0.0000','0','','0','0','24','22'),
('21','2','180.00','200.0000','220.0000','3.0000','48686','','20.0000','0.0000','0','','0','0','24','21'),
('20','2','180.00','200.0000','220.0000','3.0000','48686','','20.0000','0.0000','0','','0','0','20','22'),
('19','2','180.00','200.0000','220.0000','3.0000','48686','','20.0000','0.0000','0','','0','0','20','21');

TRUNCATE TABLE `js_jshopping_products_attr2`;
INSERT INTO `js_jshopping_products_attr2` (`id`,`product_id`,`attr_id`,`attr_value_id`,`price_mod`,`addprice`) VALUES
('30','1','2','15','+','0.0000'),
('29','1','2','16','+','20.0000'),
('28','1','2','17','=','280.0000'),
('27','1','1','18','+','20.0000'),
('26','1','1','19','+','0.0000'),
('23','2','2','17','=','160.0000'),
('25','2','1','19','+','0.0000'),
('22','2','2','16','-','20.0000'),
('24','2','1','18','=','240.0000'),
('21','2','2','15','+','0.0000');

TRUNCATE TABLE `js_jshopping_products_images`;
INSERT INTO `js_jshopping_products_images` (`image_id`,`product_id`,`image_name`,`name`,`ordering`) VALUES
('5','1','1_112213_1.jpg','','1'),
('6','2','2_19473_1.jpg','','1'),
('7','3','3_779_1.jpg','','1'),
('8','4','4_19253_1.jpg','','1'),
('9','5','5_19322_1.jpg','','1'),
('10','6','6_19256_1.jpg','','1'),
('11','7','7_783_1.jpg','','1'),
('12','8','8_56785_1.jpg','','1'),
('13','9','9_48686_1.jpg','','1'),
('14','10','10_785_1.jpg','','1'),
('15','11','11_18967_1.jpg','','1'),
('16','12','12_888_1.jpg','','1');

TRUNCATE TABLE `js_jshopping_products_prices`;
TRUNCATE TABLE `js_jshopping_products_relations`;
TRUNCATE TABLE `js_jshopping_products_to_categories`;
INSERT INTO `js_jshopping_products_to_categories` (`product_id`,`category_id`,`product_ordering`) VALUES
('1','3','1'),
('2','4','2'),
('3','6','3'),
('4','6','4'),
('5','7','5'),
('6','7','6'),
('7','7','7'),
('8','9','8'),
('9','9','9'),
('10','10','10'),
('11','10','11'),
('12','10','12'),
('13','2','1'),
('14','11','1');

TRUNCATE TABLE `js_jshopping_products_free_attr`;
TRUNCATE TABLE `js_jshopping_products_files`;
TRUNCATE TABLE `js_jshopping_manufacturers`;
INSERT INTO `js_jshopping_manufacturers` (`manufacturer_id`,`manufacturer_url`,`manufacturer_logo`,`manufacturer_publish`,`products_page`,`products_row`,`ordering`,`name_en-GB`,`alias_en-GB`,`short_description_en-GB`,`description_en-GB`,`meta_title_en-GB`,`meta_description_en-GB`,`meta_keyword_en-GB`,`name_ru-RU`,`alias_ru-RU`,`short_description_ru-RU`,`description_ru-RU`,`meta_title_ru-RU`,`meta_description_ru-RU`,`meta_keyword_ru-RU`) VALUES
('1','','','1','12','3','0','','','','','','','','SONEX','sonex','','','','',''),
('2','','','1','12','3','0','','','','','','','','ODEON LIGHT','odeon-light','','','','',''),
('3','','','1','12','3','0','','','','','','','','NOVOTECH','novotech','','','','',''),
('4','','','1','12','3','0','','','','','','','','NOVOTECH ПРОМО','novotech-promo','','','','',''),
('5','','','1','12','3','0','','','','','','','','LUMION','lumion','','','','',''),
('6','','','1','12','3','0','','','','','','','','НP','np','','','','','');

TRUNCATE TABLE `js_jshopping_attr`;
INSERT INTO `js_jshopping_attr` (`attr_id`,`attr_ordering`,`attr_type`,`independent`,`allcats`,`cats`,`group`,`name_en-GB`,`description_en-GB`,`name_ru-RU`,`description_ru-RU`) VALUES
('1','1','1','1','1','a:0:{}','0','No frost','','No frost',''),
('2','2','1','1','1','a:0:{}','0','Color','','Цвет',''),
('3','3','1','0','1','a:0:{}','0','Class','','Класс',''),
('4','4','1','0','1','a:0:{}','0','frost type','','Морозильная камера','');

TRUNCATE TABLE `js_jshopping_attr_values`;
INSERT INTO `js_jshopping_attr_values` (`value_id`,`attr_id`,`value_ordering`,`image`,`name_en-GB`,`name_ru-RU`) VALUES
('1','2','1','','','Белый'),
('2','2','2','','','Красный'),
('3','2','3','','','Синий'),
('4','1','1','','','Есть'),
('5','1','2','','','Нет'),
('6','3','1','','','C'),
('7','4','1','','','Нижняя'),
('8','4','2','','','Верхняя'),
('9','3','2','','','A'),
('10','3','3','','','B');

