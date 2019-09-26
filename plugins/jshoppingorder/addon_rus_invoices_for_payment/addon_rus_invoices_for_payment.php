<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgJshoppingorderAddon_rus_invoices_for_payment extends JPlugin {
	
	function __construct($subject, $config){
		parent::__construct($subject, $config);
	}
	
	function get_seting(){
		JSFactory::loadExtLanguageFile('addon_rus_invoices_for_payment');
		$addon = JTable::getInstance("addon", "jshop");
		$addon -> loadAlias('rus_invoices_for_payment');
		return (object)$addon -> getParams();
	}
	
	function onBeforeCreatePdfOrderEnd($order, &$pdf, $name_pdf){
		
		$addon_config = self::get_seting();
		$jshopConfig = JSFactory::getConfig();
		$vendorinfo = $order->getVendorInfo();
		
		$pdf = new JorderPDF();
		$pdf->_vendorinfo = $vendorinfo;
		$pdf->SetFont('freesans','',10);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetMargins(0,0,0);
		self::addNewPage($pdf);
		
		$pdf->SetXY(14,35);
		$pdf->setfontsize(8);
		$pdf->MultiCell(181,1,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_FELLOW_THE_FORM,0,"C");
		$pdf->Rect(14, 40, 181, 34 );
		$pdf->text(16,45,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_INN.' '.$vendorinfo->benef_swift);
		$pdf->line(14,48,120,48);
		$pdf->line(65,40,65,48);
		$pdf->text(67,45,$vendorinfo->benef_iban);
		$pdf->line(120,40,120,74);
		$pdf->text(16,52,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_RECIPIENT);
		$pdf->text(16,58,$vendorinfo->benef_payee);//$vendorinfo->shop_name
		$pdf->text(122,58,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_MF_NUM);
		$pdf->line(135,40,135,74);
		$pdf->text(137,58,$vendorinfo->benef_conto);//_JSHOP_ADDON_RUS_INVOICES_PAYMENT_KPP.' '.$vendorinfo->benef_payee
		$pdf->line(14,60,195,60);
		$pdf->text(16,65,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_PAYEES_BANK);
		$pdf->text(16,71,''.$vendorinfo->benef_bank_info);
		$pdf->text(122,65,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_BIK);
		$pdf->line(120,67,135,67);
		$pdf->text(122,71,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_MF_NUM);
		$pdf->text(137,65,$vendorinfo->benef_bic);
		if($addon_config->account_correspondent){$pdf->text(137,71,$addon_config->account_correspondent);}//_JSHOP_ADDON_RUS_INVOICES_PAYMENT_INN.' '.$vendorinfo->benef_conto
		// --------- ----------------
		
		$pdf->SetXY(14,80);
		$pdf->SetFont('freesans','',12);
		$pdf->MultiCell(181,1,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_ACCOUNT_NUMBER.$order->order_number._JSHOP_ADDON_RUS_INVOICES_PAYMENT_OT.self::rdate($order->order_date)._JSHOP_ADDON_RUS_INVOICES_PAYMENT_PR_YEAR,0,"C");
		$pdf->SetFont('freesans','',8);
		$adress=array();
		if($order->firma_name)$adress[]=$order->firma_name;
		if($order->firma_code)$adress[]=_JSHOP_ADDON_RUS_INVOICES_PAYMENT_INN." ".$order->firma_code;
		if($order->city)$adress[] = _JSHOP_ADDON_RUS_INVOICES_PAYMENT_PRE_CITY.$order->city;
		if($order->street)$adress[] = $order->street;
		if($order->home)$adress[] = $order->home;
		$pdf->text(13,93,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_PAYER.implode(", ",$adress));
		$adress=array();
		if($order->d_firma_name)$adress[]=$order->firma_name;
		if($order->firma_code)$adress[]=_JSHOP_ADDON_RUS_INVOICES_PAYMENT_INN." ".$order->firma_code;
		if($order->d_city)$adress[] = _JSHOP_ADDON_RUS_INVOICES_PAYMENT_PRE_CITY.$order->city;
		if($order->d_street)$adress[] = $order->street;
		if($order->d_home)$adress[] = $order->home;
		$pdf->text(13,98,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_CUSTOMER.implode(", ",$adress));
		
		$y = 103;
		$pdf->Rect(14,$y,181,10);
		$pdf->line(22,$y,22,$y+10);
		$pdf->line(102,$y,102,$y+10);
		$pdf->line(122,$y,122,$y+10);
		$pdf->line(140,$y,140,$y+10);
		$pdf->line(165,$y,165,$y+10);
		$pdf->SetFont('freesans','',8);
		$pdf->SetXY(14,$y+3);
		$pdf->MultiCell(8, 1,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_NUMBBER, 0, 'C');
		$pdf->SetXY(22,$y+4);
		$pdf->MultiCell(70, 1,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_NAME_OF_THE_PRODUCT, 0, 'C');
		$pdf->SetXY(102,$y+3);
		$pdf->MultiCell(20, 1,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_UNIT, 0, 'C');
		$pdf->SetXY(122,$y+1);
		$pdf->MultiCell(18, 2,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_COUNT, 0, 'C');
		$pdf->SetXY(140,$y+3);
		$pdf->MultiCell(25, 1,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_PRICE, 0, 'C');
		$pdf->SetXY(165,$y+3);
		$pdf->MultiCell(30, 1,_JSHOP_ADDON_RUS_INVOICES_PAYMENT_SUMM, 0, 'C');
		
		$y = 116;
		$y = $pdf->getY()+2.7;
		
		
		
		$num=0;
		foreach($order->products as $prod){
			$num++;
			$width_filename = 80;
			$pdf->SetFont('freesans','',8);
			$pdf->SetXY(14, $y + 1);
			$pdf->MultiCell(8, 4, $num, 0, 'C');
			$pdf->SetXY(23, $y + 1);
			$pdf->MultiCell($width_filename, 4, $prod->product_name, 0, 'L');
			
			if ($prod->manufacturer!=''){
				$pdf->SetXY(23, $pdf->getY());
				$pdf->MultiCell($width_filename, 4, _JSHOP_MANUFACTURER.": ".$prod->manufacturer, 0, 'L');
			}
			if ($prod->product_attributes!="" || $prod->product_freeattributes!="" || $prod->delivery_time || $prod->extra_fields!=''){
				if ($prod->delivery_time){
					$pdt = _JSHOP_DELIVERY_TIME.": ".$prod->delivery_time;
				}else{
					$pdt = "";
				}
				$pdf->SetXY(26, $pdf->getY());
				$pdf->SetFont('freesans','',8);
				$attribute = sprintAtributeInOrder($prod->product_attributes, "pdf");
				$attribute .= sprintFreeAtributeInOrder($prod->product_freeattributes, "pdf");
				$attribute .= sprintExtraFiledsInOrder($prod->extra_fields,"pdf");
				$attribute .= $prod->_ext_attribute;
				$attribute .= $pdt;
				$pdf->MultiCell(62, 4, $attribute, 0, 'L');
				$pdf->SetFont('freesans','',8);
			}
			//$y2 = $pdf->getY() + 1;
			$y2 = $pdf->getY();
        
			if ($jshopConfig->show_product_code_in_order && 0){
				$pdf->SetXY(85, $y + 1);
				$pdf->MultiCell(22, 4, $prod->product_ean, 0, 'L');
				$y3 = $pdf->getY() + 1;
			}else{
				$y3 = $pdf->getY();
			}
			$pdf->SetXY(102, $y + 1);
			$pdf->MultiCell(20, 1, _JSHOP_ADDON_RUS_INVOICES_PAYMENT_CHT, 0 , 'C');
		
			$pdf->SetXY(122, $y + 1);
			$pdf->MultiCell(16, 4, formatqty($prod->product_quantity).$prod->_qty_unit, 0 , 'R');
			//$y4 = $pdf->getY() + 1;
			$y4 = $pdf->getY();
        
			$pdf->SetXY(140, $y + 1);
			//$pdf->MultiCell(25, 4, formatprice($prod->product_item_price, $order->currency_code), 0 , 'L');
			$pdf->MultiCell(23, 4, $this->formatprice($prod->product_item_price), 0 , 'R');
        
			if ($prod->_ext_price){
				$pdf->SetXY(150, $pdf->getY());
				$pdf->MultiCell(40, 4, $prod->_ext_price, 0 , 'R');
			}

			if ($jshopConfig->show_tax_product_in_cart && $prod->product_tax>0){
				$pdf->SetXY(125, $pdf->getY());
				$pdf->SetFont('freesans','',6);
				$text = productTaxInfo($prod->product_tax, $order->display_price);
				$pdf->MultiCell(25, 4, $text, 0 , 'L');
			}
			//$y5 = $pdf->getY() + 1;
			$y5 = $pdf->getY();
        
			$pdf->SetFont('freesans','',8);
			$pdf->SetXY(165, $y + 1);
			//$pdf->MultiCell(40, 4, formatprice($prod->product_quantity * $prod->product_item_price, $order->currency_code), 0 , 'R');
			$pdf->MultiCell(28, 4, $this->formatprice($prod->product_quantity * $prod->product_item_price,0), 0 , 'R');
        
			if ($prod->_ext_price_total){           
			$pdf->SetXY(150, $pdf->getY());
			$pdf->MultiCell(40, 4, $prod->_ext_price_total, 0 , 'R');
			}
        
			if ($jshopConfig->show_tax_product_in_cart && $prod->product_tax>0){
				$pdf->SetXY(150, $pdf->getY());
				$pdf->SetFont('freesans','',6);
				$text = productTaxInfo($prod->product_tax, $order->display_price);
				$pdf->MultiCell(40, 4, $text, 0 , 'R');
			}
			//$y6 = $pdf->getY() + 1;
			$y6 = $pdf->getY();
        
			$yn = max($y2, $y3, $y4, $y5, $y6);
			// line 
			$pdf->Rect(14, $y, 181, $yn - $y );
			//$pdf->Rect(20, $y, 130, $yn - $y );
			
			$pdf->line(22,$y,22,$yn);
			$pdf->line(102,$y,102,$yn);
			
			if ($jshopConfig->show_product_code_in_order && 0){
				$pdf->line(85, $y, 85, $yn);
			}
			$pdf->line(122, $y, 122, $yn);
			$pdf->line(140, $y, 140, $yn);
			$pdf->line(165, $y, 165, $yn);
        
			$y = $yn; 
        
			if ($y > 260){
				self::addNewPage($pdf);
				$y = 45;
			}
		}//--- endforeach
		
		if ($y > 230){
			self::addNewPage($pdf);
			$y = 45;
		}
			
		$pdf->SetFont('freesans','',8);
		
		if (($jshopConfig->hide_tax || count($order->order_tax_list)==0) && $order->order_discount==0 && $order->order_payment==0 && $jshopConfig->without_shipping) $hide_subtotal = 1; else $hide_subtotal = 0;
		
		if (!$hide_subtotal){
			$pdf->Rect(165,$y,30,5);
			$pdf->SetXY(20,$y);
			$pdf->MultiCell(143,5,_JSHOP_SUBTOTAL,'0','R');	
			$pdf->SetXY(165,$y);	
			$pdf->MultiCell(28,5,$this->formatprice($order->order_subtotal).$order->_pdf_ext_subtotal,'0','R');
		}else{
			$y = $y - 5;
		}
	
		if ($order->order_discount > 0){
			$y = $y + 5;     
			$pdf->Rect(165,$y,30,5);
			$pdf->SetXY(20,$y);
			$pdf->MultiCell(143,5,_JSHOP_RABATT_VALUE,'0','R');
			$pdf->SetXY(165,$y);
			$pdf->MultiCell(28,5, "-".$this->formatprice($order->order_discount).$order->_pdf_ext_discount,'0','R');       
		}
	
		if (!$jshopConfig->without_shipping){
			$pdf->Rect(165,$y + 5,30,5);
			$pdf->SetXY(20,$y + 5);
			$pdf->MultiCell(143,5,_JSHOP_SHIPPING_PRICE,'0','R');
			$pdf->SetXY(165,$y + 5);
			//$pdf->MultiCell(28,5,formatprice($order->order_shipping, $order->currency_code).$order->_pdf_ext_shipping,'0','R');
			$pdf->MultiCell(28,5,$this->formatprice($order->order_shipping).$order->_pdf_ext_shipping,'0','R');
			if ($order->order_package>0 || $jshopConfig->display_null_package_price){
				$y=$y+5;
				$pdf->SetXY(20,$y + 5);
				$pdf->Rect(165,$y + 5,30,5);
				$pdf->MultiCell(143,5,_JSHOP_PACKAGE_PRICE,'0','R');
				$pdf->SetXY(165,$y + 5);
				//$pdf->MultiCell(28,5,formatprice($order->order_package, $order->currency_code).$order->_pdf_ext_shipping_package,'0','R');
				$pdf->MultiCell(28,5,$this->formatprice($order->order_package).$order->_pdf_ext_shipping_package,'0','R');
			}
		}else{
			$y = $y - 5;
		}
	
		if ($order->order_payment != 0){
			$y = $y + 5;     
			$pdf->SetXY(20,$y+5);
			$pdf->Rect(165,$y+5,30,5);
			$pdf->MultiCell(143,5, $order->payment_name,'0','R');
			$pdf->SetXY(165,$y+5);
			//$pdf->MultiCell(40,5, formatprice($order->order_payment, $order->currency_code).$order->_pdf_ext_payment, '1','R');
			$pdf->MultiCell(28,5, $this->formatprice($order->order_payment).$order->_pdf_ext_payment, '0','R');
		}
	
		$show_percent_tax = 1;
		if ($jshopConfig->hide_tax) $show_percent_tax = 0;
	
		if (!$jshopConfig->hide_tax){
			foreach($order->order_tax_list as $percent=>$value){
				$pdf->SetXY(20,$y + 10);
				$pdf->Rect(165,$y + 10,30,5);
				$text = displayTotalCartTaxName($order->display_price);
				if ($show_percent_tax) $text = $text." ".formattax($percent)."%";
				$pdf->MultiCell(143,5,$text ,'0','R');        
				$pdf->SetXY(165,$y + 10);
				//$pdf->MultiCell(40,5,formatprice($value, $order->currency_code).$order->_pdf_ext_tax[$percent],'1','R');
				$pdf->MultiCell(28,5,$this->formatprice($value).$order->_pdf_ext_tax[$percent],'0','R');
				$y = $y + 5;
			}
		}
	
		$text_total = _JSHOP_ENDTOTAL;
		if (($jshopConfig->show_tax_in_product || $jshopConfig->show_tax_product_in_cart) && (count($order->order_tax_list)>0)){
			$text_total = _JSHOP_ENDTOTAL_INKL_TAX;
		}
	
		$pdf->SetFont('freesansb','',8);
		$pdf->SetXY(20,$y + 10);
		$pdf->Rect(165,$y + 10,30, 5.1);
		$pdf->MultiCell(143, 5 , $text_total,'0','R');
	
		$pdf->SetXY(165,$y + 10);
		//$pdf->MultiCell(40,5,formatprice($order->order_total, $order->currency_code).$order->_pdf_ext_total,'1','R');
		//$pdf->MultiCell(28,5,$this->formatprice($order->order_total, $order->currency_code).$order->_pdf_ext_total,'0','R');
		$pdf->MultiCell(28,5,$this->formatprice($order->order_total).$order->_pdf_ext_total,'0','R');
		if ($jshopConfig->display_tax_id_in_pdf && $order->tax_number){
			$y = $y+5.2;
			$pdf->SetFont('freesans','',7);
			$pdf->SetXY(20,$y + 10);        
			$pdf->MultiCell(143, 4 , _JSHOP_TAX_NUMBER.": ".$order->tax_number,'0','L');
		}
	
		$y = $pdf->getY()+5; 
	
		$pdf->SetFont('freesans','',8);
		$pdf->SetXY(13,$y);
		$pdf->MultiCell(180, 1 , _JSHOP_ADDON_RUS_INVOICES_PAYMENT_TOTAL_ITEMS.$num.", "._JSHOP_ADDON_RUS_INVOICES_PAYMENT_THE_AMOUNT.$this->formatprice($order->order_total),'0','L');
		$pdf->SetFont('freesansb','',8);
		$pdf->SetXY(13,$y+5);
		$pdf->MultiCell(180, 1 , $this->mb_ucfirst($this->SumProp($this->formatprice($order->order_total),$order->currency_code)),'0','L');
    
		$y = $y + 20; 
		
		$pdf->Image($jshopConfig->path.'images/header.jpg',120,$y-5,$jshopConfig->pdf_header_width,$jshopConfig->pdf_header_height);
		$pdf->Image($jshopConfig->path.'images/footer.jpg',70,$y-14,$jshopConfig->pdf_footer_width,$jshopConfig->pdf_footer_height);

		$pdf->SetFont('freesans','',8);
		$text = _JSHOP_ADDON_RUS_INVOICES_PAYMENT_GUIDE_ENTERPRISES;
		$pdf->text(14,$y,$text);
		$t_x=strlen($text);
		$t_x2=140;
		$pdf->line($t_x+10,$y,$t_x2,$y);
		$pdf->text($t_x2+2,$y,($addon_config->guide_enterprises)?"(".$addon_config->guide_enterprises.")":'');
	
		$y = $y + 10; 
		$text = _JSHOP_ADDON_RUS_INVOICES_PAYMENT_CHIEF_ACCOUNTANT;
		$pdf->text(14,$y,$text);
		$t_x=strlen($text);
		$t_x2=140;
		$pdf->line($t_x+10,$y,$t_x2,$y);
		$pdf->text($t_x2+2,$y,($addon_config->chief_accountant)?"(".$addon_config->chief_accountant.")":'');
	
	}
	
	function addNewPage(&$pdf){
		$pdf->addPage();
		self::addTitleHead($pdf);
	}
	
	function addTitleHead(&$pdf){
		$jshopConfig = JSFactory::getConfig();
        $vendorinfo = $pdf->_vendorinfo;
		//$pdf->Image($jshopConfig->path.'images/header.jpg',1,1,$jshopConfig->pdf_header_width,$jshopConfig->pdf_header_height);
		//$pdf->Image($jshopConfig->path.'images/footer.jpg',1,265,$jshopConfig->pdf_footer_width,$jshopConfig->pdf_footer_height);
		
		$y=12;
		$x=12;
		
		if($vendorinfo->logo){
			$pdf->Image($vendorinfo->logo,$x,$y-2,25,25);
			$x+=30;
		}
		
		$pdf->SetFont('freesans','',8);
		$pdf->SetXY($x,$y);
        $pdf->SetTextColor($pdf->pdfcolors[2][0], $pdf->pdfcolors[2][1], $pdf->pdfcolors[2][2]);
		$_vendor_info = array();
		$_vendor_info[] = $vendorinfo->company_name;
		$adress = array();
		if($vendorinfo->zip)$adress[] = $vendorinfo->zip;
		//if($vendorinfo->country)$adress[] = $vendorinfo->country;
		if($vendorinfo->city)$adress[] = $vendorinfo->city;
		if($vendorinfo->adress)$adress[] = $vendorinfo->adress;
				
        $_vendor_info[] = implode(", ",$adress);
		unset($adress);
        if ($vendorinfo->phone) $_vendor_info[] = _JSHOP_CONTACT_PHONE.": ".$vendorinfo->phone;
        if ($vendorinfo->fax) $_vendor_info[] = _JSHOP_CONTACT_FAX . ": ".$vendorinfo->fax;
        if ($vendorinfo->email) $_vendor_info[] = _JSHOP_EMAIL.": ".$vendorinfo->email;
        $str_vendor_info = implode("\n",$_vendor_info);
        $pdf->MultiCell(80, 3, $str_vendor_info, 0, 'L');
        $pdf->SetTextColor($pdf->pdfcolors[0][0], $pdf->pdfcolors[0][1], $pdf->pdfcolors[0][2]);
	}
	
	
	function formatprice($price, $currency_exchange = 0) {
    $jshopConfig = JSFactory::getConfig();

    if ($currency_exchange){
        $price = $price * $jshopConfig->currency_value;
    }

    if (!$currency_code) $currency_code = $jshopConfig->currency_code;
    $price = number_format($price, $jshopConfig->decimal_count, $jshopConfig->decimal_symbol, $jshopConfig->thousand_separator);
    return $price;
	}
	
	function rdate($date){
		$MonthNames=array(
			_JSHOP_ADDON_RUS_INVOICES_PAYMENT_MONTH_JANUARY,
			_JSHOP_ADDON_RUS_INVOICES_PAYMENT_MONTH_FEBRUARY,
			_JSHOP_ADDON_RUS_INVOICES_PAYMENT_MONTH_MARCH,
			_JSHOP_ADDON_RUS_INVOICES_PAYMENT_MONTH_APRIL,
			_JSHOP_ADDON_RUS_INVOICES_PAYMENT_MONTH_MAY,
			_JSHOP_ADDON_RUS_INVOICES_PAYMENT_MONTH_JUNE,
			_JSHOP_ADDON_RUS_INVOICES_PAYMENT_MONTH_JULY,
			_JSHOP_ADDON_RUS_INVOICES_PAYMENT_MONTH_AUGUST,
			_JSHOP_ADDON_RUS_INVOICES_PAYMENT_MONTH_SEPTEMBER,
			_JSHOP_ADDON_RUS_INVOICES_PAYMENT_MONTH_OCTOBER,
			_JSHOP_ADDON_RUS_INVOICES_PAYMENT_MONTH_NOVEMBER,
			_JSHOP_ADDON_RUS_INVOICES_PAYMENT_MONTH_DECEMBER);
		$d=explode(".",$date);
		$d[1]=$MonthNames[(int)$d[1]-1];
		return implode(" ",$d);
	}
	
	// echo SumProp(2004.30, 'руб.', 'коп.');
	// SumProp(nnnn,'USD'|'RUR'|'EUR')-полный вывод со спряжением "долларов"-"центов"
	function SumProp($srcsumm,$val_rub='', $val_kop=''){
		$cifir= Array('од','дв','три','четыр','пят','шест','сем','восем','девят');
		$sotN = Array('сто','двести','триста','четыреста','пятьсот','шестьсот','семьсот','восемьсот','девятьсот');
		$milion= Array('триллион','миллиард','миллион','тысяч');
		$anDan = Array('','','','сорок','','','','','девяносто');
		$scet=4;
		$cifR='';
		$cfR='';
		$oboR= Array();
	//==========================
		$splt = explode('.',"$srcsumm");
		if(count($splt)<2) $splt = explode(',',"$srcsumm");
		$xx = $splt[0];
		$xx1 = (empty($splt[1])? '00': $splt[1]);
		$xx1 = str_pad("$xx1", 2, "0", STR_PAD_RIGHT); // 2345.1 -> 10 копеек
	//  $xx1 = round(($srcsumm-floor($srcsumm))*100);
		if ($xx>999999999999999) { $cfR=$srcsumm; return $cfR; }
		while($xx/1000>0){
			$yy=floor($xx/1000);
			$delen= round(($xx/1000-$yy)*1000);

			$sot= floor($delen/100)*100;
			$des=(floor($delen-$sot)>9? floor(($delen-$sot)/10)*10:0);
			$ed= floor($delen-$sot)-floor(($delen-$sot)/10)*10;

			$forDes=($des/10==2?'а':'');
			$forEd= ($ed==1 ? 'ин': ($ed==2?'е':'') );
			if ( floor($yy/1000)>=1000 ) { // делаю "единицы" для тысяч, миллионов...
				$ffD=($ed>4?'ь': ($ed==1 || $scet<3? ($ed<2?'ин': ($scet==3?'на': ($scet<4? ($ed==2?'а':( $ed==4?'е':'')) :'на') ) ) : ($ed==2 || $ed==4?'е':'') ) );
			}else{ // единицы для "единиц
				$ffD=($ed>4?'ь': ($ed==1 || $scet<3? ($scet<3 && $ed<2?'ин': ($scet==3?'на': ($scet<4? ($ed==2?'а':( $ed==4?'е':'')) :'ин') ) ) : ( $ed==4?'е':($ed==2?'а':'')) ) );
			}
			if($ed==2) $ffD = ($scet==3)?'е':'а'; // два рубля-миллиона-миллиарда, но две тысячи

			$forTys=($des/10==1? ($scet<3?'ов':'') : ($scet<3? ($ed==1?'': ($ed>1 && $ed<5?'а':'ов') ) : ($ed==1? 'а': ($ed>1 && $ed<5?'и':'') )) );
			$nnn = floor($sot/100)-1;
			$oprSot=(!empty($sotN[$nnn]) ? $sotN[$nnn]:'');
			$nnn = floor($des/10);
			$oprDes=(!empty($cifir[$nnn-1])? ($nnn==1?'': ($nnn==4 || $nnn==9? $anDan[$nnn-1]:($nnn==2 || $nnn==3?$cifir[$nnn-1].$forDes.'дцать':$cifir[$nnn-1].'ьдесят') ) ) :'');

			$oprEd=(!empty($cifir[$ed-1])? $cifir[$ed-1].(floor($des/10)==1?$forEd.'надцать' : $ffD ) : ($des==10?'десять':'') );
			$oprTys=(!empty($milion[$scet]) && $delen>0) ? $milion[$scet].$forTys : '';

			$cifR= (strlen($oprSot) ? ' '.$oprSot:'').
				(strlen($oprDes)>1 ? ' '.$oprDes:'').
				(strlen($oprEd)>1  ? ' '.$oprEd:'').
				(strlen($oprTys)>1 ? ' '.$oprTys:'');
			$oboR[]=$cifR;
			$xx=floor($xx/1000);
			$scet--;
			if (floor($xx)<1 ) break;
		}
		$oboR = array_reverse($oboR);
		for ($i=0; $i<count($oboR); $i++){
			$probel = strlen($cfR)>0 ? ' ':'';
			$cfR .= (($oboR[$i]!='' && $cfR!='') ? $probel:'') . $oboR[$i];
		}
		if (strlen($cfR)<3) $cfR='ноль';

		$intsrc = $splt[0];
		$kopeiki = $xx1;
		$kop2 =str_pad("$xx1", 2, "0", STR_PAD_RIGHT);

		$sum2 = str_pad("$intsrc", 2, "0", STR_PAD_LEFT);
		$sum2 = substr($sum2, strlen($sum2)-2); // 676571-> '71'
		$sum21 = substr($sum2, strlen($sum2)-2,1); // 676571-> '7'
		$sum22 = substr($sum2, strlen($sum2)-1,1); // 676571-> '1'
		$kop1  = substr($kop2,0,1);
		$kop2  = substr($kop2,1,1);
		$ar234 = array('2','3','4'); // доллар-А, рубл-Я...
	// делаю спряжения у слова рубл-ей|я|ь / доллар-ов... / евро
		if($val_rub=='RUR') {
			$val1 = 'рубл';
			$val2 = 'копейка';
			if($sum22=='1' && $sum21!='1') $val1 .= 'ь'; // 01,21...91 рубль
			elseif(in_array($sum22, $ar234) && ($sum21!='1')) $val1 .= 'я';
			else $val1 .= 'ей';

			if(in_array($kop2, $ar234) && ($kop1!='1')) $val2 = 'копейки';
			elseif($kop2=='1' && $kop1!='1') $val2 = 'копейка'; // 01,21...91 копейка
			else $val2 = 'копеек';
			$cfR .= " $val1 $kopeiki $val2";
		}elseif($val_rub=='USD') {
			$val1 = 'доллар';
			$val2 = 'цент';
			if($sum22=='1' && $sum21!='1') $val1 .= ''; // 01,21...91 доллар
			elseif(in_array($sum22, $ar234) && ($sum21!='1')) $val1 .= 'a';
			else $val1 .= 'ов';

			if($kop2=='1' && $kop1!='1') $val2 .= ''; // 01,21...91 цент
			elseif(in_array($kop2, $ar234) && ($kop1!='1')) $val2 .= 'a';
			else $val2 .= 'ов';
			$val1 .= ' США';
			$cfR .= " $val1 $kopeiki $val2";
		}elseif($val_rub=='EUR') {
			$val1 = 'евро';
			$val2 = 'цент';
			if($kop2=='1' && $kop1!='1') $val2 .= ''; // 01,21...91 цент
			elseif(in_array($kop2, $ar234) && ($kop1!='1')) $val2 .= 'a';
			else $val2 .= 'ов';
	
			$cfR .= " $val1 $kopeiki $val2";
		}else{
			$cfR .= ' '.$val_rub;
			if($val_kop!='') $cfR .= "$kopeiki $val_kop";
		}
		return trim($cfR);
	} // SumProp() end

	function mb_ucfirst($str, $encoding='utf-8'){
		$str = mb_ereg_replace('^[\ ]+', '', $str);
		$str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).mb_substr($str, 1, mb_strlen($str), $encoding);
		return $str;
	}
}
?>