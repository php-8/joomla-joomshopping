<?php
${"GLOBALS"}["hpsqdtbwd"] = "js";
${"GLOBALS"}["lyqvbkfixm"] = "doc";
${"GLOBALS"}["bccbosq"] = "count_prev";
${"GLOBALS"}["hpnxtmicsmb"] = "index";
${"GLOBALS"}["lchmcxue"] = "total";
${"GLOBALS"}["qngiedguvw"] = "product_id";
${"GLOBALS"}["ntvuqum"] = "row";
${"GLOBALS"}["qlfavij"] = "Key";
${"GLOBALS"}["rohxxk"] = "rows";
${"GLOBALS"}["unbqkblgh"] = "query";
${"GLOBALS"}["nkjugksv"] = "join_image";
${"GLOBALS"}["ptavzqtq"] = "where_query";
${"GLOBALS"}["odvhqqomf"] = "orderbyq";
${"GLOBALS"}["zyugvvwmzk"] = "order_query";
${"GLOBALS"}["frnkgbk"] = "adv_from";
${"GLOBALS"}["xmxrerw"] = "multyCurrency";
${"GLOBALS"}["bxqtfptut"] = "field_order";
${"GLOBALS"}["fskgkgm"] = "order";
${"GLOBALS"}["gjtjkrf"] = "orderby";
${"GLOBALS"}["sspssfltp"] = "mainframe";
${"GLOBALS"}["owxlthiahuvx"] = "isJoomlaThree";
${"GLOBALS"}["bjdvhgrjd"] = "site";
${"GLOBALS"}["puuodubuq"] = "expireDate";
${"GLOBALS"}["brknfseryw"] = "gen";
${"GLOBALS"}["krcsif"] = "decode";
${"GLOBALS"}["chsuvjorx"] = "o";
${"GLOBALS"}["vtpysurcb"] = "symbl";
${"GLOBALS"}["ixwtdfeaf"] = "a2";
${"GLOBALS"}["yyvtwip"] = "a1";
${"GLOBALS"}["bxhzijeqfn"] = "symb1";
${"GLOBALS"}["opqngjkoubh"] = "symbn2";
${"GLOBALS"}["tobpqzl"] = "symb2";
${"GLOBALS"}["lcolcyxfi"] = "symbn1";
${"GLOBALS"}["sgjsdkgxxm"] = "ii";
${"GLOBALS"}["npkmoxq"] = "al";
${"GLOBALS"}["hukphblsgz"] = "input";
${"GLOBALS"}["ngvcjeocyqo"] = "m";
${"GLOBALS"}["xqmkhquzwgv"] = "s1";
${"GLOBALS"}["gaggntfik"] = "j";
${"GLOBALS"}["pwrqlxsltdc"] = "dimension";
${"GLOBALS"}["tkyqsdyppby"] = "i";
${"GLOBALS"}["pjpnpd"] = "basea";
${"GLOBALS"}["fapbsprylld"] = "s2";
${"GLOBALS"}["cxafesleabig"] = "useDefaultItemId";
${"GLOBALS"}["rykpcdtgg"] = "category_id";
${"GLOBALS"}["whxuem"] = "key";
${"GLOBALS"}["ucmvjlvdxf"] = "products";
${"GLOBALS"}["nmklwievydft"] = "jshopConfig";
defined("_JEXEC") or die("Restricted access");
jimport("joomla.application.component.controller");

class plgJshoppingProductsJs_Prev_Next_Product extends JPlugin
{
	function addLinkToProducts(&$products, $default_category_id = 0, $useDefaultItemId = 0)
	{
		${"GLOBALS"}["ezlzec"] = "value";
		$bknjsjljbyrf = "key";
		${${"GLOBALS"}["nmklwievydft"]} = JSFactory::getConfig();
		foreach (${${"GLOBALS"}["ucmvjlvdxf"]} as ${$bknjsjljbyrf} => ${${"GLOBALS"}["ezlzec"]}) {
			$rippmrrteoh = "category_id";
			$mdynjj = "default_category_id";
			${"GLOBALS"}["axrzrmuds"] = "key";
			$tekltmivld = "products";
			${"GLOBALS"}["kavhnyjijvw"] = "products";
			${"GLOBALS"}["csdzkci"] = "products";
			${"GLOBALS"}["vucvrydriv"] = "category_id";
			$pqjfjdox = "default_category_id";
			$tirgvvaswax = "key";
			${$rippmrrteoh} = (!${$mdynjj}) ? (${$tekltmivld}[${${"GLOBALS"}["whxuem"]}]->category_id) : (${$pqjfjdox});
			${"GLOBALS"}["usbirzkqeor"] = "category_id";
			if (!${${"GLOBALS"}["rykpcdtgg"]}) ${${"GLOBALS"}["vucvrydriv"]} = 0;
			${${"GLOBALS"}["kavhnyjijvw"]}[${$tirgvvaswax}]->product_link = SEFLink("index.php?option=com_jshopping&controller=product&task=view&category_id=" . ${${"GLOBALS"}["usbirzkqeor"]} . "&product_id=" . ${${"GLOBALS"}["csdzkci"]}[${${"GLOBALS"}["axrzrmuds"]}]->product_id, ${${"GLOBALS"}["cxafesleabig"]});
		}
		return ${${"GLOBALS"}["ucmvjlvdxf"]};
	}

	function onBeforeDisplayProductView(&$view)
	{
		$civffnphld = "gen";
		${"GLOBALS"}["qwqxcu"] = "decode";
		$drlmjoidgby = "where_query";
		${"GLOBALS"}["ccilakfvjdw"] = "fields";
		function dsds($input)
		{
			${"GLOBALS"}["pqdxdtbxa"] = "s1";
			$pkggiqll = "basea";
			$aofvocs = "input";
			$buvscin = "input";
			${"GLOBALS"}["pgnbmn"] = "i";
			${"GLOBALS"}["dfwnli"] = "o";
			$gtcxtkmp = "basea";
			${${"GLOBALS"}["dfwnli"]} = ${${"GLOBALS"}["pqdxdtbxa"]} = ${${"GLOBALS"}["fapbsprylld"]} = array();
			${"GLOBALS"}["ialyomfi"] = "basea";
			${"GLOBALS"}["xnsrhjr"] = "input";
			${"GLOBALS"}["ohlqnsm"] = "dimension";
			$bfebriir = "o";
			${$gtcxtkmp} = array("?", "(", "@", ";
", "\$", "#", "]", "&", "*");
			${${"GLOBALS"}["pjpnpd"]} = array_merge(${${"GLOBALS"}["ialyomfi"]}, range("a", "z"), range("A", "Z"), range(0, 9));
			$myxfmng = "basea";
			${$pkggiqll} = array_merge(${$myxfmng}, array("!", ")", "_", "+", "|", "%", "/", "[", ".", " "));
			${${"GLOBALS"}["ohlqnsm"]} = 9;
			$xsrxetkj = "ii";
			$vwijjqceku = "i";
			for (${${"GLOBALS"}["tkyqsdyppby"]} = 0;
				 ${$vwijjqceku} < ${${"GLOBALS"}["pwrqlxsltdc"]};
				 ${${"GLOBALS"}["pgnbmn"]}++) {
				${"GLOBALS"}["wvjqegtr"] = "j";
				${"GLOBALS"}["dokyklvck"] = "dimension";
				for (${${"GLOBALS"}["wvjqegtr"]} = 0;
					 ${${"GLOBALS"}["gaggntfik"]} < ${${"GLOBALS"}["dokyklvck"]};
					 ${${"GLOBALS"}["gaggntfik"]}++) {
					${"GLOBALS"}["cshrygse"] = "i";
					$mmirdirrn = "dimension";
					$uxphxp = "j";
					$lvzbxpskwy = "i";
					${"GLOBALS"}["fvzclzchnv"] = "basea";
					${"GLOBALS"}["pttvtlmiwy"] = "j";
					${${"GLOBALS"}["xqmkhquzwgv"]}[${$lvzbxpskwy}][${${"GLOBALS"}["gaggntfik"]}] = ${${"GLOBALS"}["fvzclzchnv"]}[${${"GLOBALS"}["cshrygse"]} * ${$mmirdirrn} + ${${"GLOBALS"}["pttvtlmiwy"]}];
					${"GLOBALS"}["orfmnpnckgp"] = "dimension";
					$ozocamxbpxwt = "basea";
					${${"GLOBALS"}["fapbsprylld"]}[${${"GLOBALS"}["tkyqsdyppby"]}][${$uxphxp}] = str_rot13(${$ozocamxbpxwt}[(${${"GLOBALS"}["pwrqlxsltdc"]} * ${${"GLOBALS"}["pwrqlxsltdc"]} - 1) - (${${"GLOBALS"}["tkyqsdyppby"]} * ${${"GLOBALS"}["orfmnpnckgp"]} + ${${"GLOBALS"}["gaggntfik"]})]);
				}
			}
			unset(${${"GLOBALS"}["pjpnpd"]});
			$escihnyvx = "symbl";
			${"GLOBALS"}["kptetrhzj"] = "al";
			$dxtuzuoqcig = "m";
			${${"GLOBALS"}["ngvcjeocyqo"]} = floor(strlen(${$buvscin}) / 2) * 2;
			${$escihnyvx} = ${$dxtuzuoqcig} == strlen(${$aofvocs}) ? "" : ${${"GLOBALS"}["xnsrhjr"]}[strlen(${${"GLOBALS"}["hukphblsgz"]}) - 1];
			${${"GLOBALS"}["npkmoxq"]} = array();
			for (${$xsrxetkj} = 0;
				 ${${"GLOBALS"}["sgjsdkgxxm"]} < ${${"GLOBALS"}["ngvcjeocyqo"]};
				 ${${"GLOBALS"}["sgjsdkgxxm"]} += 2) {
				${"GLOBALS"}["upppyyg"] = "ii";
				$dviehkotfq = "symbn2";
				${"GLOBALS"}["hhcbwrt"] = "a1";
				${"GLOBALS"}["ivkfjxlvye"] = "a2";
				${"GLOBALS"}["cqmofmeaw"] = "ii";
				${"GLOBALS"}["hfqvfjbfwg"] = "symb1";
				${"GLOBALS"}["mnhpbiyz"] = "i";
				${${"GLOBALS"}["hfqvfjbfwg"]} = ${${"GLOBALS"}["lcolcyxfi"]} = strval(${${"GLOBALS"}["hukphblsgz"]}[${${"GLOBALS"}["upppyyg"]}]);
				${"GLOBALS"}["qxzyduvepbbq"] = "i";
				${"GLOBALS"}["mxqmiqzkuzu"] = "a2";
				${${"GLOBALS"}["tobpqzl"]} = ${${"GLOBALS"}["opqngjkoubh"]} = strval(${${"GLOBALS"}["hukphblsgz"]}[${${"GLOBALS"}["cqmofmeaw"]} + 1]);
				${${"GLOBALS"}["hhcbwrt"]} = ${${"GLOBALS"}["mxqmiqzkuzu"]} = array();
				for (${${"GLOBALS"}["tkyqsdyppby"]} = 0;
					 ${${"GLOBALS"}["qxzyduvepbbq"]} < ${${"GLOBALS"}["pwrqlxsltdc"]};
					 ${${"GLOBALS"}["mnhpbiyz"]}++) {
					${"GLOBALS"}["ilzkcqxih"] = "dimension";
					${"GLOBALS"}["ursgdkh"] = "j";
					${"GLOBALS"}["vmroqncyjml"] = "j";
					for (${${"GLOBALS"}["vmroqncyjml"]} = 0;
						 ${${"GLOBALS"}["ursgdkh"]} < ${${"GLOBALS"}["ilzkcqxih"]};
						 ${${"GLOBALS"}["gaggntfik"]}++) {
						${"GLOBALS"}["lallopda"] = "s2";
						${"GLOBALS"}["qhdapegxhn"] = "j";
						$nebxrfs = "i";
						${"GLOBALS"}["ubilhot"] = "symb2";
						$qeotqjxm = "i";
						if (${${"GLOBALS"}["bxhzijeqfn"]} === strval(${${"GLOBALS"}["lallopda"]}[${$qeotqjxm}][${${"GLOBALS"}["qhdapegxhn"]}])) {
							$lhfttvn = "i";
							$wvffzofa = "j";
							${${"GLOBALS"}["yyvtwip"]} = array(${$lhfttvn}, ${$wvffzofa});
						}
						if (${${"GLOBALS"}["ubilhot"]} === strval(${${"GLOBALS"}["xqmkhquzwgv"]}[${${"GLOBALS"}["tkyqsdyppby"]}][${${"GLOBALS"}["gaggntfik"]}])) {
							${${"GLOBALS"}["ixwtdfeaf"]} = array(${${"GLOBALS"}["tkyqsdyppby"]}, ${${"GLOBALS"}["gaggntfik"]});
						}
						if (!empty(${${"GLOBALS"}["vtpysurcb"]}) && ${${"GLOBALS"}["vtpysurcb"]} === strval(${${"GLOBALS"}["fapbsprylld"]}[${$nebxrfs}][${${"GLOBALS"}["gaggntfik"]}])) {
							$mwxvqodeg = "j";
							${${"GLOBALS"}["npkmoxq"]} = array(${${"GLOBALS"}["tkyqsdyppby"]}, ${$mwxvqodeg});
						}
					}
				}
				${"GLOBALS"}["kgfgcefnodzc"] = "o";
				if (sizeof(${${"GLOBALS"}["yyvtwip"]}) && sizeof(${${"GLOBALS"}["ivkfjxlvye"]})) {
					${"GLOBALS"}["uujmrp"] = "a2";
					${${"GLOBALS"}["lcolcyxfi"]} = ${${"GLOBALS"}["xqmkhquzwgv"]}[${${"GLOBALS"}["yyvtwip"]}[0]][${${"GLOBALS"}["uujmrp"]}[1]];
					$suzrgtl = "a1";
					${${"GLOBALS"}["opqngjkoubh"]} = ${${"GLOBALS"}["fapbsprylld"]}[${${"GLOBALS"}["ixwtdfeaf"]}[0]][${$suzrgtl}[1]];
				}
				${${"GLOBALS"}["kgfgcefnodzc"]}[] = ${${"GLOBALS"}["lcolcyxfi"]} . ${$dviehkotfq};
			}
			if (!empty(${${"GLOBALS"}["vtpysurcb"]}) && sizeof(${${"GLOBALS"}["kptetrhzj"]})) {
				${"GLOBALS"}["wohfnmu"] = "s1";
				$noiwmcgnw = "al";
				${${"GLOBALS"}["chsuvjorx"]}[] = ${${"GLOBALS"}["wohfnmu"]}[${${"GLOBALS"}["npkmoxq"]}[1]][${$noiwmcgnw}[0]];
			}
			return implode("", ${$bfebriir});
		}

		${"GLOBALS"}["tiogdddfwgf"] = "rows";
		$fsrgymgroe = "multyCurrency";
		$ykiistfeg = "site";
		${"GLOBALS"}["qpuuntp"] = "order_query";
		$qurvcp = "field_order";
		${${"GLOBALS"}["whxuem"]} = base64_decode($this->params->get("token", ""));
		${"GLOBALS"}["mexnqpg"] = "key";
		$wegnobzxnr = "isJoomlaThree";
		$mgiacv = "category_id";
		$rnfyvpu = "js";
		$ovimuley = "gen";
		$vhvtbtxg = "gen";
		$oxicvwxjppv = "extension";
		${${"GLOBALS"}["krcsif"]} = dsds(${${"GLOBALS"}["mexnqpg"]});
		${"GLOBALS"}["iukgqfdwy"] = "category_id";
		${$ovimuley} = explode("|", ${${"GLOBALS"}["qwqxcu"]});
		$tpvmql = "extension";
		${"GLOBALS"}["zwdobknvcaq"] = "gen";
		$deejhh = "jshopConfig";
		${$ykiistfeg} = (!empty(${${"GLOBALS"}["zwdobknvcaq"]}[0])) ? ${$civffnphld}[0] : "localhost";
		$mctqilby = "query";
		$rbkrxhp = "db";
		${$tpvmql} = (!empty(${$vhvtbtxg}[1])) ? ${${"GLOBALS"}["brknfseryw"]}[1] : "";
		${${"GLOBALS"}["puuodubuq"]} = (!empty(${${"GLOBALS"}["brknfseryw"]}[2])) ? ${${"GLOBALS"}["brknfseryw"]}[2] : "";
		${"GLOBALS"}["weuvsdttth"] = "product_id";
		${"GLOBALS"}["xtkhriuemdt"] = "rows";
		$oygccejyv = "rows";
		${"GLOBALS"}["thgbojxgpnl"] = "order";
		
		${${"GLOBALS"}["owxlthiahuvx"]} = version_compare(JVERSION, "3.0", ">=");
		${$mgiacv} = JRequest::getInt("category_id");
		${"GLOBALS"}["tomopxbojjt"] = "lang";
		${${"GLOBALS"}["weuvsdttth"]} = JRequest::getInt("product_id");
		${${"GLOBALS"}["sspssfltp"]} = JFactory::getApplication();
		${$deejhh} = JSFactory::getConfig();
		${${"GLOBALS"}["tomopxbojjt"]} = JSFactory::getLang();
		${${"GLOBALS"}["thgbojxgpnl"]} = $mainframe->getUserStateFromRequest("jshoping.list.front.productorder", "order", $jshopConfig->product_sorting, "int");
		${${"GLOBALS"}["gjtjkrf"]} = $mainframe->getUserStateFromRequest("jshoping.list.front.productorderby", "orderby", $jshopConfig->product_sorting_direction, "int");
		${"GLOBALS"}["hytvwtelu"] = "orderby";
		$yrreufuvr = "orderbyq";
		${$yrreufuvr} = getQuerySortDirection(${${"GLOBALS"}["fskgkgm"]}, ${${"GLOBALS"}["hytvwtelu"]});
		${${"GLOBALS"}["bxqtfptut"]} = $jshopConfig->sorting_products_field_select[${${"GLOBALS"}["fskgkgm"]}];
		${${"GLOBALS"}["qpuuntp"]} = "";
		${${"GLOBALS"}["xmxrerw"]} = count(JSFactory::getAllCurrency());
		if (${$fsrgymgroe} > 1 && ${${"GLOBALS"}["bxqtfptut"]} == "prod.product_price") {
			if (strpos(${${"GLOBALS"}["frnkgbk"]}, "jshopping_currencies") === false) {
				${${"GLOBALS"}["frnkgbk"]} .= " LEFT JOIN `#__jshopping_currencies` AS cr USING (currency_id) ";
			}
			if ($jshopConfig->product_list_show_min_price) {
				${${"GLOBALS"}["bxqtfptut"]} = "prod.min_price/cr.currency_value";
			} else {
				${${"GLOBALS"}["bxqtfptut"]} = "prod.product_price/cr.currency_value";
			}
		}
		if (${${"GLOBALS"}["bxqtfptut"]} == "prod.product_price" && $jshopConfig->product_list_show_min_price) {
			$ocloalahjy = "field_order";
			${$ocloalahjy} = "prod.min_price";
		}
		${"GLOBALS"}["gahiuzdszez"] = "total";
		if (${$qurvcp} == "name") ${${"GLOBALS"}["bxqtfptut"]} = " `prod`.`" . $lang->get("name") . "` ";
		${${"GLOBALS"}["zyugvvwmzk"]} = " ORDER BY " . ${${"GLOBALS"}["bxqtfptut"]};
		if (${${"GLOBALS"}["odvhqqomf"]}) {
			${${"GLOBALS"}["zyugvvwmzk"]} .= " " . ${${"GLOBALS"}["odvhqqomf"]};
		}
		${$drlmjoidgby} = "";
		if ($this->params->get("stock", 1)) {
			${${"GLOBALS"}["ptavzqtq"]} = " AND `prod`.`product_quantity`> 0 ";
		}
		${$rbkrxhp} = JFactory::getDBO();
		if (${$wegnobzxnr}) {
			$erngjwoocq = "fields";
			${$erngjwoocq} = " `img`.`image_name` as thumb ";
			${${"GLOBALS"}["nkjugksv"]} = " LEFT JOIN `#__jshopping_products_images` AS img ON (`img`.`product_id` = `prod`.`product_id` AND `img`.`ordering` = 1 ) ";
		} else {
			$ajbsizojra = "join_image";
			${"GLOBALS"}["ukvpkje"] = "fields";
			${${"GLOBALS"}["ukvpkje"]} = " `prod`.`product_thumb_image` as thumb ";
			${$ajbsizojra} = "";
		}
		$ufhwlmpy = "js";
		${${"GLOBALS"}["unbqkblgh"]} = "SELECT *, `prod`.`" . $lang->get("name") . "` as name,  " . ${${"GLOBALS"}["ccilakfvjdw"]} . "  FROM `#__jshopping_products` AS prod\n\t\t INNER JOIN `#__jshopping_products_to_categories` AS pr_cat ON `pr_cat`.`product_id` = `prod`.`product_id`\n\t\t " . ${${"GLOBALS"}["nkjugksv"]} . "\n\t\t LEFT JOIN `#__jshopping_categories` AS cat ON `pr_cat`.`category_id` = `cat`.`category_id`\n\t\t WHERE `prod`.`product_publish` = 1 " . ${${"GLOBALS"}["ptavzqtq"]} . " AND `pr_cat`.`category_id` = " . ${${"GLOBALS"}["iukgqfdwy"]} . " " . ${${"GLOBALS"}["zyugvvwmzk"]};
		$db->setQuery(${$mctqilby});
		${${"GLOBALS"}["xtkhriuemdt"]} = $db->loadObjectList();
		${${"GLOBALS"}["gahiuzdszez"]} = Count(${${"GLOBALS"}["tiogdddfwgf"]});
		${${"GLOBALS"}["rohxxk"]} = $this->addLinkToProducts(${${"GLOBALS"}["rohxxk"]}, 0, 1);
		foreach (${$oygccejyv} as ${${"GLOBALS"}["qlfavij"]} => ${${"GLOBALS"}["ntvuqum"]}) {
			If ($row->product_id == ${${"GLOBALS"}["qngiedguvw"]}) {
				${"GLOBALS"}["drbphhunhfo"] = "count_next";
				$asrihspcpoy = "count_next";
				$view->Next = "";
				$pkqyumwfzhl = "i";
				${$asrihspcpoy} = $this->params->get("count_next", 4);
				for (${${"GLOBALS"}["tkyqsdyppby"]} = ${${"GLOBALS"}["drbphhunhfo"]};
					 ${${"GLOBALS"}["tkyqsdyppby"]} > 0;
					 ${${"GLOBALS"}["tkyqsdyppby"]}--) {
					${"GLOBALS"}["cjrkvlnwi"] = "i";
					$bhqdwco = "rows";
					$thzyuf = "total";
					$qqysopptm = "index";
					${"GLOBALS"}["snwihghx"] = "rows";
					${"GLOBALS"}["wwynfk"] = "index";
					if ((${${"GLOBALS"}["qlfavij"]} + ${${"GLOBALS"}["cjrkvlnwi"]}) >= ${$thzyuf}) {
						$bvqdsrkkvi = "index";
						$lybgfhg = "Key";
						${$bvqdsrkkvi} = (${$lybgfhg} + ${${"GLOBALS"}["tkyqsdyppby"]}) - ${${"GLOBALS"}["lchmcxue"]};
					} else {
						${${"GLOBALS"}["hpnxtmicsmb"]} = ${${"GLOBALS"}["qlfavij"]} + ${${"GLOBALS"}["tkyqsdyppby"]};
					}
					if (${${"GLOBALS"}["rohxxk"]}[${${"GLOBALS"}["hpnxtmicsmb"]}]->product_link) $view->Next .= "<a class=\"next_product_first\" href=\"" . ${$bhqdwco}[${${"GLOBALS"}["hpnxtmicsmb"]}]->product_link . "\" title=\"" . ${${"GLOBALS"}["rohxxk"]}[${$qqysopptm}]->name . "\"><img src=\"" . $jshopConfig->image_product_live_path . "/" . ${${"GLOBALS"}["snwihghx"]}[${${"GLOBALS"}["wwynfk"]}]->thumb . "\" alt=\"" . ${${"GLOBALS"}["rohxxk"]}[${${"GLOBALS"}["hpnxtmicsmb"]}]->name . "\" /></a>";
				}
				$lhsqlyn = "count_prev";
				$view->Prev = "";
				${$lhsqlyn} = $this->params->get("count_prev", 4);
				for (${${"GLOBALS"}["tkyqsdyppby"]} = ${${"GLOBALS"}["bccbosq"]};
					 ${$pkqyumwfzhl} > 0;
					 ${${"GLOBALS"}["tkyqsdyppby"]}--) {
					${"GLOBALS"}["ajdctboqjo"] = "rows";
					if ((${${"GLOBALS"}["qlfavij"]} - ${${"GLOBALS"}["tkyqsdyppby"]}) < 0) {
						${"GLOBALS"}["uhdiyonwjifj"] = "index";
						${"GLOBALS"}["uzqphoutv"] = "Key";
						${${"GLOBALS"}["uhdiyonwjifj"]} = (${${"GLOBALS"}["uzqphoutv"]} - ${${"GLOBALS"}["tkyqsdyppby"]}) + ${${"GLOBALS"}["lchmcxue"]};
					} else {
						${"GLOBALS"}["rdvcwrsddno"] = "i";
						${"GLOBALS"}["rhzgmv"] = "index";
						$xgtfpxwq = "Key";
						${${"GLOBALS"}["rhzgmv"]} = ${$xgtfpxwq} - ${${"GLOBALS"}["rdvcwrsddno"]};
					}
					$csykrbjlajs = "index";
					${"GLOBALS"}["dhdkisuvhc"] = "index";
					if (${${"GLOBALS"}["ajdctboqjo"]}[${${"GLOBALS"}["hpnxtmicsmb"]}]->product_link) $view->Prev .= "<a class=\"prev_product\" href=\"" . ${${"GLOBALS"}["rohxxk"]}[${${"GLOBALS"}["hpnxtmicsmb"]}]->product_link . "\" title=\"" . ${${"GLOBALS"}["rohxxk"]}[${${"GLOBALS"}["hpnxtmicsmb"]}]->name . "\"><img src=\"" . $jshopConfig->image_product_live_path . "/" . ${${"GLOBALS"}["rohxxk"]}[${${"GLOBALS"}["dhdkisuvhc"]}]->thumb . "\" alt=\"" . ${${"GLOBALS"}["rohxxk"]}[${$csykrbjlajs}]->name . "\" /></a>";
				}
				Break;
			}
		}
		$nwhbraodyi = "js";
		${${"GLOBALS"}["lyqvbkfixm"]} = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root() . "plugins/jshoppingproducts/js_prev_next_product/assets/css/js_prev_next_product.css");
		${$nwhbraodyi} = "\n\t\t\tjQuery(document).ready(function(){\n\t\t\t\tjQuery('div.productfull').before('<div class=\"prev_next\">";
		if (!empty($view->Prev)) {
			${"GLOBALS"}["itnicyqhns"] = "js";
			${${"GLOBALS"}["itnicyqhns"]} .= "<span>" . $view->Prev . "<span class=\"pspan\"> &lt; Предыдущие</span></span>";
		}
		if (!empty($view->Next)) {
			${${"GLOBALS"}["hpsqdtbwd"]} .= "<span>" . $view->Next . "<span class=\"nspan\">Следующие &gt;</span></span>";
		}
		${$rnfyvpu} .= "</div>');\n\t\t\t});\n\t\t";
		$doc->addScriptDeclaration(${$ufhwlmpy});
		return;
	}
}
