<?php
if (!defined('_JEXEC')) {	
	define('_JEXEC', 1 );
	define('JPATH_BASE', dirname(__FILE__) );
	define('DS', DIRECTORY_SEPARATOR );
	require_once(JPATH_BASE . DS . 'includes' . DS . 'defines.php');
	require_once(JPATH_BASE . DS.'includes' . DS . 'framework.php');	
	require_once(JPATH_BASE . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'factory.php');	
	require_once(JPATH_LIBRARIES . DS . 'import.legacy.php');
	require_once(JPATH_SITE . DS . 'components' . DS . 'com_jshopping' . DS . 'lib' . DS . 'factory.php');
	require_once(JPATH_SITE . DS . 'components' . DS . 'com_jshopping' . DS . 'lib' . DS . 'functions.php');
	$app = JFactory::getApplication('site')->initialise();
}

$db = JFactory::getDBO();

$ct = 's1y5k_jshopping_categories';
$pt = 's1y5k_jshopping_products';
$pc = 's1y5k_jshopping_products_to_categories';
$man = 's1y5k_jshopping_manufacturers';

$param_host = 'https://';			// Выбор http или https
$param_url = 'o-printere.ru';
$param_name = 'O-PRINTERE.RU';		//не более 20 символов
$param_company = 'O-PRINTERE.RU';	//Полное наименование компании, владеющей магазином.
$param_currency_default = 'RUB';	// Валюта поумолчанию
$param_currency_rate = 'CBRF';		//rate = Постоянное число — внутренний курс, который вы используете.
									//CBRF — курс по Центральному банку РФ. NBU — курс по Национальному банку Украины. NBK — курс по Национальному банку Казахстана.
									//СВ — курс по банку той страны, к которой относится магазин по своему региону, указанному в личном кабинете.

$param_bid = 10; // bid="80" cbid="90" fee="325"
$param_cbid = 0;
$param_fee = 0;
$param_cpa = 1; // Участие в программе «Заказ на Маркете»: 0 - не учавствовать; 1 - учавствовать
$param_old_price = 0; // Показывать старую цену: 0 - нет; 1 - да
$param_ean_code = 1; // Артикул: 0 - Артикул; 1 - Код производителя
$param_delivery = 'true';
$param_pickup = 'false';
$param_store = 'false'; //https://yandex.ru/support/partnermarket/delivery.html

$query = "
	SELECT `value_id`, `name_ru-RU` AS name_value
	FROM `s1y5k_jshopping_attr_values`
";
$db->setQuery($query);
$attr_values_result = $db->loadObjectList();
$attr_values = array();
foreach($attr_values_result as $key => $value) {
	$attr_values[$value->value_id] = $value->name_value;
}

$array_replacer = array(
	'"' => '&quot;',
	'&' => '&amp;',
	'>' => '&gt;',
	'<' => '&lt;',
	'\'' => '&apos;'
);

$array_colors = array('бежевый','белый','бирюзовый','бордовый','голубой','желтый','зеленый','золотистый','коричневый','красный','оливковый','оранжевый','разноцветный','розовый',
'рыжий','салатовый','светло-розовый','серебристый','серый','синий','сиреневый','темно-зеленый','темно-коричневый','темно-серый','темно-синий','фиолетовый','хаки','черный','ярко-розовый');
$array_sizes = array('3XS', '2XS', 'XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL', '7XL');

$array_replacer_colors = array(
	'Black' => 'черный',
	'Cyan' => 'голубой',
	'Magenta' => 'ярко-розовый',
	'Yellow' => 'желтый',
	'Light Cyan' => 'бирюзовый',
	'Light Magenta' => 'светло-розовый',
	'Matte Black' => 'черный',
	'Blue' => 'голубой',
	'Red' => 'красный',
	'Green' => 'зеленый',
	'Orange' => 'оранжевый',
	'Light Black' => 'темно-коричневый',
	'Light Light Black' => 'темно-серый',
	'Photo Black' => 'белый',
	'Gloss Optimizer' => 'белый',
	'Натуральный' => 'белый',
	'Белый' => 'белый',
	'Прозрачный' => 'белый',
	'Gray' => 'серый',
	'Комплект' => 'разноцветный'
);

$yml_content = '';
$yml_content .= "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
$yml_content .= "<!DOCTYPE yml_catalog SYSTEM \"shops.dtd\">\n";
$yml_content .= "<yml_catalog date=\"" . date("Y-m-d H:i") . "\">\n";
$yml_content .= "<shop>\n";
$yml_content .= "<name>" . $param_name . "</name>\n";
$yml_content .= "<company>" . $param_company . "</company>\n";
$yml_content .= "<url>" . $param_url . "</url>\n";

$yml_content .= "<currencies>\n";
	$yml_content .= "\t<currency id=\"" . $param_currency_default . "\" rate=\"1\"/>\n";
	$query = "
		SELECT *
		FROM `s1y5k_jshopping_currencies`
		WHERE `currency_publish` = 1 AND `currency_code_iso` != '" . $param_currency_default . "'
	";
	$query = "
		SELECT *
		FROM `s1y5k_jshopping_currencies`
		WHERE `currency_publish` = 1 AND `currency_code_iso` != '" . $param_currency_default . "'
	";
	$db->setQuery($query);
	$currencies = $db->loadObjectList();
	if (count($currencies)) {
		foreach($currencies as $id) {
			$curr_fix = $id->currency_value;
		}
		unset($id);
	}
	/*if (count($currencies)) {
		foreach($currencies as $id) {
			$yml_content .= "\t<currency id=\"" . $id->currency_code_iso . "\" rate=\"" . (($param_currency_rate != 1)?($param_currency_rate):$id->currency_value) . "\"/>\n";
		}
		unset($id);
	}*/
$yml_content .= "</currencies>\n";

$yml_content .= "<categories>\n";
	$query = "
		SELECT
			`category_id`,
			`name_ru-RU` AS cat_name,
			`category_parent_id`
		FROM `s1y5k_jshopping_categories`
		WHERE `category_publish` = 1
		ORDER BY `category_parent_id`, `category_id`
	";
	
	$db->setQuery($query);
	$categories = $db->loadObjectList();
	foreach($categories as $id) {
		$yml_content .= "\t<category id=\"" . $id->category_id . "\"" . (($id->category_parent_id != 0)?(" parentId=\"" . $id->category_parent_id . "\""):"") . ">" . trim($id->cat_name) . "</category>\n";
	}
	unset($id);
$yml_content .= "</categories>\n";

/*$yml_content .= "<delivery-options>\n";
	$yml_content .= "\t<option cost=\"500\" days=\"5\"/>\n";
$yml_content .= "</delivery-options>\n";*/

$yml_content .= "<cpa>" . $param_cpa . "</cpa>\n";

$exclude_product_ids = array(13039, 13117);
$yml_content .= "<offers>\n";
$query = "
	SELECT
		$pt.`product_id`,
		$pt.`product_url`,
		$pt.`product_ean`,
		$pt.`manufacturer_code`,
		$pt.`unlimited`,
		$pt.`product_quantity`,
		$pt.`product_price`,
		$pt.`min_price`,
		$pt.`product_manufacturer_id`,
		$pt.`name_ru-RU` AS name,
		$pt.`product_weight`,
		$pt.`extra_field_12`,
		$pt.`image`,
		$pt.`short_description_ru-RU` AS description,
		$pt.`currency_id`,
		$ct.`alias_ru-RU` AS cat_url,
		$ct.`category_id`,
		$man.`name_ru-RU` AS man_name,
		cur.`currency_code_iso`
	FROM $pt
	LEFT JOIN $pc ON $pt.`product_id` = $pc.`product_id`
	LEFT JOIN $ct ON $pc.`category_id` = $ct.`category_id`
	LEFT JOIN $man ON $pt.`product_manufacturer_id` = $man.`manufacturer_id`
	LEFT JOIN `s1y5k_jshopping_currencies` cur ON $pt.`currency_id` = cur.`currency_id`
	WHERE $pt.`product_publish` = 1 &&
		$pt.`product_id` NOT IN (" . implode(',', $exclude_product_ids) . ") &&
		$pt.`product_price` > 0 &&
		$ct.`category_publish` = 1 &&
		$pt.`product_weight` > 0
	GROUP BY $pt.`product_id`
	ORDER BY $pt.`product_id`
";

$db->setQuery($query);
$result = $db->loadObjectList();
foreach($result as $id) {
	$link = SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id=' . $id->category_id . '&product_id=' . $id->product_id, 1);
	if($id->product_quantity > 0) {
		$available = 'true';
	} else {
		$available = 'false';
	}
	
	if(!empty($id->image) && ($id->unlimited == 1 || $id->product_quantity > 0)) {
		$product_price = $id->product_price;
		$query = "
			SELECT ar.*, ap.`image`
			FROM `#__jshopping_products_attr` AS ar
			LEFT JOIN `#__jshopping_products` ap ON ar.`ext_attribute_product_id` = ap.`product_id`
			WHERE ar.`product_id` = $id->product_id
		";
		$db->setQuery($query);
		$attributes = $db->loadObjectList();
		if (count($attributes)) {
			foreach($attributes as $attr_id) {
				if ($attr_id->price > 0 && $product_price > $attr_id->price) {
					$product_price = $attr_id->price;
				}
			}
		}		
			$yml_content .= "\t<offer id=\"" . $id->product_id . "\" available=\"" . $available . "\"" . (($param_bid != 0)?(" bid=\"" . $param_bid . "\""):"") . (($param_cbid != 0)?(" bid=\"" . $param_cbid . "\""):"") . (($param_fee != 0)?(" bid=\"" . $param_fee . "\""):"") . ">\n";
				$yml_content .= "\t\t<url>" . $param_host . $param_url . $link . "</url>\n";
				//$yml_content .= "\t\t<price>" . $id->product_price . "</price>\n";
				//$yml_content .= "\t\t<price>" . number_format($id->product_price * $curr_fix, 0, '.', '') . "</price>\n";
				$yml_content .= "\t\t<price>" . number_format($product_price * $curr_fix, 0, '.', '') . "</price>\n";
				//$yml_content .= "\t\t<price>" . number_format($id->product_price, 0, '.', '') . "</price>\n";
				if ($param_old_price == 1 && $id->product_old_price > 0) {
					$yml_content .= "\t\t<oldprice>" . $id->product_old_price . "</oldprice>\n";
				}
				$yml_content .= "\t\t<currencyId>RUB</currencyId>\n";
				$yml_content .= "\t\t<categoryId>" . $id->category_id . "</categoryId>\n";
				
				
					$yml_content .= "\t\t<picture>" . $param_host . $param_url . "/components/com_jshopping/files/img_products/" . $id->image . "</picture>\n";
				
				
				$yml_content .= "\t\t<store>true</store>\n";
				$yml_content .= "\t\t<pickup>true</pickup>\n";
				$yml_content .= "\t\t<delivery>true</delivery>\n";
				$yml_content .= "\t\t<local_delivery_cost>250</local_delivery_cost>\n";
				/*$yml_content .= "\t\t<store>" . $param_store . "</store>\n";
				$yml_content .= "\t\t<pickup>" . $param_pickup . "</pickup>\n";
				$yml_content .= "\t\t<delivery>" . $param_delivery . "</delivery>\n";
				
				$yml_content .= "\t\t<delivery-options>\n";
					$yml_content .= "\t\t\t<option cost=\"500\" days=\"5\"/>\n"; // Переделать
				$yml_content .= "\t\t</delivery-options>\n";*/
				
				$yml_content .= "\t\t<name>" . strip_tags(strtr($id->name, $array_replacer)) . "</name>\n";
				
				if(!empty($id->man_name)) {
					$yml_content .= "\t\t<vendor>" . strip_tags(strtr($id->man_name, $array_replacer)) . "</vendor>\n";
				}
				
				$yml_content .= "\t\t<vendorCode>" . (($param_ean_code == 0)?($id->product_ean):$id->manufacturer_code) . "</vendorCode>\n";
				
				$yml_content .= "\t\t<description>" . strip_tags(strtr($id->description, $array_replacer)) . "</description>\n";
				
				$yml_content .= "\t\t<param name=\"Вес\" unit=\"кг\">" . $id->product_weight  . "</param>\n";
				
				$yml_content .= "\t\t<sales_notes>Наличные, б/н расчет, Avangard.</sales_notes>\n";
				$yml_content .= "\t\t<cpa>" . $param_cpa . "</cpa>\n";
			$yml_content .= "\t</offer>\n";
	}
}
unset($id);
$yml_content .= "</offers>\n";
$yml_content .= "</shop>\n";
$yml_content .= "</yml_catalog>";

$handler = fopen(__DIR__ . '/export_yandex.yml', "w");
fwrite($handler, $yml_content);
fclose($handler);
?>