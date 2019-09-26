<?php
class DPD_service
{
	public $arMSG = array();		// ������-��������� ('str' => �����_���������, 'type' => ���_��������� (�� �������: 0 - ������)
	
	private $IS_ACTIVE 	= 1;		// ���� ���������� ������� (0 - ��������, 1 - �������)
	private $IS_TEST 	= 0;		// ���� ������������ (0 - ������, 1 - ����)
	private $SOAP_CLIENT;			// SOAP-������
	
	private $MY_NUMBER 	= '1017002137';	// �������� �� ����!!! - ���������� ����� � ������� DPD (����� �������� � DPD) 
	private $MY_KEY		= '563A5677269C5A3FF82AA099A4585B2A44600CE7'; // �������� �� ����!!! - ���������� ���� ��� �����������

	private $arDPD_HOST = array(
					0 => 'ws.dpd.ru/services/',					// ������� ����
					1 => 'appl.dpd.ru:8080/services/',			// �������� ����
			);

	private $arSERVICE = array(									// �������: �������� => �����
					'getCitiesCashPay'	=> 'geography2',			// ��������� DPD (������ ��������)
					'getTerminalsSelfDelivery' => 'geography2', 	// ������ ���������� DPD (TODO)
					'getServiceCost'	=> 'calculator2',		// ������ ���������
					'createOrder'		=> 'order2',			// ������� ����� �� �������� (TODO)
					'getOrderStatus'	=> 'order2',			// �������� ������ �������� ������ (TODO)
			);
	
	

	/**
	* �����������
	*
	* @access public
	* @return void
	*/
	public function __construct()
	{
		$this->IS_TEST = $this->IS_TEST ? 1 : 0;
	}
	
	
	/**
	* ������ ������� ��������
	* 
	* @access public
	* @return 
	*/
	public function getCityList()
	{
        $obj = $this->_getDpdData('getCitiesCashPay');

		$res = $this->_parceObj2Arr($obj->return);
		
		return $res;
	}
	
	
	/**
	* ����������� ��������� ��������
	* 
	* @access public
	* @param array	$arData		// ������ ������� ����������*
	* @return 
	*/
	public function getServiceCost($arData)
	{
	   //echo "in function";
		// ����
		if($arData['delivery']['cityName'])
		{
			$arData['delivery']['cityName'] = iconv('windows-1251','utf-8',$arData['delivery']['cityName']);
		}
		
		// ������
		$arData['pickup'] = array(
							'cityId'	=> 49175217,
							'cityName'	=> iconv('windows-1251','utf-8','������������'),
							//'regionCode'	=> '66',
							//'countryCode'	=> 'RU',
		);
		
		// ��� ������ � ����������
		$arData['selfPickup'] 	= true; 	// �������� �� ���������
		$arData['selfDelivery'] = true;		// �������� �� ���������
		
        
        
		// ������ �������� - ���� �������� ������� � ����� ���� "request"
		$obj = $this->_getDpdData('getServiceCost', $arData, 1);

		// ������� $obj --> $arr
		$res = $this->_parceObj2Arr($obj->return);
	
        
		return $res;
	}


// PRIVATE ------------------------

	/**
	* ������� � ��������������� ��������
	*
	* @access private
	* @param string	$method_name 	������������� ����� ������� (��. ���� �������� ������ $this->arSERVICE)
	* @return bool					��������� ������������� (���� ������������� - �������� �������� $this->SOAP_CLIENT, ����� $this->arMSG)
	*/
	private function _connect2Dpd($method_name)
	{
	 
		if(!$this->IS_ACTIVE) {return false;}
		
		if(!$service = $this->arSERVICE[$method_name])
		{
			$this->arMSG['str']  = '� ��������� ������ ��� ������� "'.$method_name.'"';
            
			return false;
		}
		$host = $this->arDPD_HOST[$this->IS_TEST].$service.'?WSDL';
//        echo $host."<br/>";
		
       
		try 
		{
			// Soap-����������� � �������
			$this->SOAP_CLIENT = new SoapClient('http://'.$host);
			if(!$this->SOAP_CLIENT) throw new Exception('������'); 
            
		} catch (Exception $ex) {
		  
			$this->arMSG['str'] = '�� ������� ������������ � �������� DPD '.$service;
//            echo "Error<br/>";
			return false;
		}
        
//        echo "Done 1<br/>";
        

		return true;
	}

	/**
	* ������ ������ � ������ �������
	* 
	* @access private
	* @param string		$method_name	�������� ������ Dpd-������� (��. $arSERVICE) 
	* @param array		$arData			������ ����������, ������������ � �����
	* @param integer 	$is_request		���� �������� ������� � ���� 'request'
	* @return XZ_obj					������, ���������� �� �������
	*/
	private function _getDpdData($method_name, $arData=array(), $is_request=0)
	{
//	   echo "---Enter in _getDpdData<br>";
		if(!$this->_connect2Dpd($method_name)) {echo "Fuck!";return false;}

		// �������� ������� ��� ��������������
        
        if($method_name!="getCitiesCashPay"){
        
		$arData['auth'] = array(
			'clientNumber'	=> $this->MY_NUMBER,
			'clientKey'		=> $this->MY_KEY,
		);
        }else{
            $auth= array(
    			'clientNumber'	=> $this->MY_NUMBER,
    			'clientKey'		=> $this->MY_KEY,
		      );
            $arData['request']['auth']=$auth;
        }
        
		
		// �������� ������� � ���� 'request'
//        echo "---Enter data<br>";
  //      echo "<pre>";
   //     print_r($arData);
   //     echo "</pre>";
        
		if($is_request) $arRequest['request'] = $arData;
		else $arRequest = $arData;
//		  echo "1<br/>";
        
        	$obj = $this->SOAP_CLIENT->$method_name($arRequest);
//            echo "2<br/>";
		try 
		{
			//eval("\$obj = \$this->SOAP_CLIENT->\$method_name(\$arRequest);");
		
			
			if(!$obj) {throw new Exception('������');
//            echo "---Error 1<br>";
            }
		} catch (Exception $ex) {
//		      echo "---Error 2<br>";
			$this->arMSG['str'] = '�� ������� ������� ����� '.$method_name.' / '.$ex;
		}
		
//       echo "All done";
//       echo "<pre>";
 //      print_r($obj);
//       echo "</pre>";
        
		return $obj ? $obj : false;
	}
	
	/**
	* ������ ������� � ������ (��������)
	* 
	* @access private
	* @param object		$obj		������
	* @param integer	$isUTF		���� ������������� ��������������� ����� �� UTF � WIN (0|1), ��-������� "1" - ����������
	* @param array		$arr		���������� c�������� ������ ��� ����������� ��������
	* @return array
	*/
	private function _parceObj2Arr($obj,$isUTF=1,$arr=array())
	{
		$isUTF = $isUTF ? 1 : 0;
		
		if(is_object($obj) || is_array($obj) )
		{
			$arr = array();
			for(reset($obj); list($k, $v) = each($obj);)
			{
				if($k === "GLOBALS") continue;
				$arr[$k] = $this->_parceObj2Arr($v, $isUTF, $arr);
			}
			return $arr;
		}
		elseif(gettype($obj) == 'boolean') 
		{
			return $obj ? 'true' : 'false';	
		}
		else
		{
			// ������� �����: utf-8 --> windows-1251
			if($isUTF && gettype($obj)=='string') $obj = iconv('utf-8','windows-1251',$obj);
			return $obj;
		}
	}
}

?>
 
