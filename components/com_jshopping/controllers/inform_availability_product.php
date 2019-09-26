<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class JshoppingControllerInform_availability_product extends JControllerLegacy {
	public function showform(){
		JSFactory::loadExtLanguageFile('addon_inform_availability_product');
		$user = JFactory::getUser();
		$prod_id = JRequest::getInt("prod_id");

		$uri = JURI::getInstance();
		$liveurlhost = $uri->toString(array("scheme", 'host', 'port'));
		//$request_url = htmlspecialchars_decode($liveurlhost . SEFLink("index.php?option=com_jshopping&controller=inform_availability_product&task=InformAjax&ajax=1"), ENT_NOQUOTES);
        $request_url = JUri::base() . "index.php?option=com_jshopping&controller=inform_availability_product&task=InformAjax&ajax=1";

		$document = JFactory::getDocument();
		$document->addCustomTag('<link type="text/css" rel="stylesheet" href="' . JURI::root() . 'components/com_jshopping/css/inform_availability_product.css" />');
		$document->addScriptDeclaration("var iap_jSonRequest = null;
		function sendbase(prod_id){
            var prod_attr_id = jQuery('#product_attr_id', window.parent.document).val();
            if (!prod_attr_id) prod_attr_id = 0;            
			if (!checkEmailIAP(jQuery('#iap_email').val())){
				alert ('" . _JSHOP_REGWARN_MAIL . "');
			} else {
				var data = { 'name': jQuery('#iap_name').val(), 'email':jQuery('#iap_email').val(), 'prod_id':prod_id, 'prod_attr_id':prod_attr_id };
				if (iap_jSonRequest){
					iap_jSonRequest.abort();
				}
				iap_jSonRequest = jQuery.ajax({
					url: '" . $request_url . "',
					dataType: 'json',
					data: data,
					type: 'post',    
					success: function (json) {
						if (json.status == 'OK'){
                            jQuery('#notify_msg').show();
                            jQuery('#notify_table').hide();
							/*window.parent.SqueezeBox.close();*/
						}else{
							alert('" . _JSHOP_ERROR_SAP . "');
						}
					},
					error: function() {
						alert('" . _JSHOP_ERROR_SAP . "');
					}
				});
			}
		}

		function checkEmailIAP(myForm){
			return (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(myForm));
		}");
		?>
        <div id="notify_msg" style="display:none;">
            <?php print _JSHOP_SENT_REQUEST?>
        </div>
        
        <div id="notify_table">
		    <table class="inform_availability">
			    <tr>
				    <th>
                        <?php print _JSHOP_NOTIFY ?>
                    </th>
			    </tr>
			    <tr>
				    <td>
                        <?php print _JSHOP_INTERM_NAME ?><br/>
                        <input type = "text" name="iap_name" id="iap_name" class = "inputbox" style = "width: 95%;" value = "<?php print $user->username ?>" />
                    </td>
			    </tr>
			    <tr>
				    <td>
                        <?php print _JSHOP_EMAIL ?><br/>
                        <input type = "text" name="iap_email" id="iap_email" class = "inputbox" style = "width: 95%;" value = "<?php print $user->email ?>" />
                    </td>
			    </tr>
			    <tr>
				    <td>
                        <input type="button" class="button" value="<?php print _JSHOP_SEND ?>" onclick="sendbase('<?php print $prod_id ?>')" />
                    </td>
			    </tr>
		    </table>
        </div>
		<?php
	}

	public function InformAjax() {
		$email = JRequest::getVar("email");	
        $product_id = JRequest::getInt("prod_id");

        $product = JTable::getInstance('product', 'jshop');
        $product->load($product_id);        

        if (trim($email) == '' || !$product_id || !$product->product_publish) {
            $res['status'] = 'Error';
            echo json_encode($res);
            die;
        }
        
        $row = JTable::getInstance('addon', 'jshop');
        $row->loadAlias('inform_availability');
        $addon_params = $row->getParams();   

        if ($this->requestsAvailability($email)){
            $mailfrom = JFactory::getApplication()->getCfg('mailfrom');
            $fromname = JFactory::getApplication()->getCfg('fromname');

            if (isset($addon_params['admin_email_notify']) && $addon_params['admin_email_notify']){
                $this->sendEmailAdmin($product, $mailfrom, $fromname, $email);
            }

            if (isset($addon_params['client_email_notify']) && $addon_params['client_email_notify']){
                $this->sendEmailClient($product, $mailfrom, $fromname, $email);
            }
            
            $res['status'] = 'OK';
        } else {
            $res['status'] = 'Error';
        }

		echo json_encode($res);
		die;
	}

	public function getlink() {
		$db = JFactory::getDbo();
		$product_id = JRequest::getInt('product_id', 0);
		$product_attr_id = JRequest::getInt('product_attr_id', 0);
		$adv_link = '';
		$product = JTable::getInstance('product', 'jshop');
		$product->load($product_id);
        
		if (isset($product_attr_id) && $product_attr_id > 0) {
			$query = "SELECT * FROM `#__jshopping_products_attr` WHERE product_id = " . $db->escape($product_id) . " AND `product_attr_id` = " . $db->escape($product_attr_id) . " LIMIT 1";
			$db->setQuery($query);
			$attr = $db->loadObject();
			if (count($attr > 0)) {
				$allattribs = JSFactory::getAllAttributes(0);
				if (count($allattribs) > 0) {
					foreach ($allattribs as $key => $value) {
						$field = 'attr_' . $value->attr_id;
						if ($attr->$field != 0) {
							$adv_link .= '&attr[' . $value->attr_id . ']=' . $attr->$field;
						}
					}
				}
			}
		}
        
		$this->setRedirect(SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id=' . $product->getCategory() . '&product_id=' . $product_id . $adv_link . '&Itemid=' . getShopMainPageItemid(), 0, 1));
	}

	private function requestsAvailability($email) {
		$name = JRequest::getVar("name");
		$product_id = JRequest::getInt("prod_id", 0);
		$product_attr_id = JRequest::getInt('prod_attr_id', 0);
		$user = JFactory::getUser();
        
		if ($user->username) {
			$username = $user->username;
		} else {
			$username = "unregistered";
		}
        
		$date = date("Y-m-d H:i:s", time());
		$ip = $this->getRealIp();
		$attributes = $this->getProductAttr($product_id, $product_attr_id);
		$db = JFactory::getDBO();
        
        $query = "SELECT * FROM `#__jshopping_requests_availability_product` WHERE `product_id` = ".$product_id." AND `product_attr_id` = ".(int)$db->escape($product_attr_id)." AND `email_send` = 0 AND `email` = '".$db->escape($email)."'";
        $db->setQuery($query);
        $db->query();
        
        if ($db->getNumRows()){
            //update request date if client send repeated request
            $query = "UPDATE `#__jshopping_requests_availability_product` SET `date` = '".$db->escape($date)."' "
                    . "WHERE `product_id` = ".$product_id." AND `product_attr_id` = ".(int)$db->escape($product_attr_id)." AND `email_send` = 0 AND `email` = '".$db->escape($email)."'";
            $db->setQuery($query);
            $db->query();
            
            return 1;
        }
        
		$query = "INSERT INTO `#__jshopping_requests_availability_product` (`product_id`, `product_attr_id`, `attributes`, `user`, `name`, `email`, `date`, `ip`, `email_send`, `language`) "
                . "VALUES(".$product_id.", ".(int)$db->escape($product_attr_id).", '".$db->escape($attributes)."', '".$db->escape($username)."', '".$db->escape($name)."', '".$db->escape($email)."', '".$db->escape($date)."', '".$db->escape($ip)."', 0, '".$db->escape(JFactory::getLanguage()->getTag())."')";
		$db->setQuery($query);	
		return $db->query();
	}
    
    private function sendEmailAdmin($product, $mailfrom, $fromname, $email){
        JSFactory::loadExtLanguageFile('addon_inform_availability_product');
		$name = JRequest::getVar("name");
		$product_attr_id = JRequest::getInt('prod_attr_id', 0);
        
        $liveurlhost = JURI::getInstance()->toString( array("scheme",'host', 'port'));
        $cur_lang = JFactory::getLanguage()->getTag();
        $adv_link = '';
        if ($product_attr_id){
            $adv_link = "&product_attr_id=".$product_attr_id;
        }

        $_name = JSFactory::getLang()->get('name');
        $product_name = $product->$_name;

        $url = $liveurlhost.'/index.php?option=com_jshopping&controller=inform_availability_product&task=getlink&product_id='.$product->product_id.$adv_link.'&lang='.substr($cur_lang, 0, 2);
        $message_adm = sprintf(_IAP_ADM_MESSAGE, $product_name, $name, $email, $url);
        $mailer = JFactory::getMailer();                    
        $mailer->setSender(array($mailfrom, $fromname));
        $mailer->addRecipient(JSFactory::getConfig()->contact_email);
        $mailer->setSubject(_JSHOP_PRODUCT.' "'.$product_name.'" '._JSHOP_REQUESTS_AVAILABILITY);
        $mailer->setBody($message_adm);
        $mailer->Send();            
    }
    
    private function sendEmailClient($product, $mailfrom, $fromname, $email){
        JSFactory::loadExtLanguageFile('addon_inform_availability_product');
        $name = JRequest::getVar("name");
        
        $_name = JSFactory::getLang()->get('name');
        $product_name = $product->$_name;

        $message_client = sprintf(_IAP_CLIENT_MESSAGE, $product_name, $name);
        $mailer = JFactory::getMailer();                    
        $mailer->setSender(array($mailfrom, $fromname));
        $mailer->addRecipient($email);
        $mailer->setSubject(_JSHOP_PRODUCT.' "'.$product_name.'" '._JSHOP_REQUESTS_AVAILABILITY);
        $mailer->setBody($message_client);
        $mailer->Send();            
    }
    
	private function getRealIp() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
    
	private function getProductAttr($product_id, &$product_attr_id){
		$require = array();
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->admin_show_attributes) return '';
		
		$table = JTable::getInstance('product','jshop');
		$table->load($product_id);
		
        $allattribs = JSFactory::getAllAttributes(2);
        $dependent_attr = $allattribs['dependent'];

        if (count($dependent_attr)){
            $prodAttribVal = $table->getAttributes();

            if (count($prodAttribVal)){
                $prodAtrtib = $prodAttribVal[0];
                foreach($dependent_attr as $attrib){
                    $field = "attr_".$attrib->attr_id;
                    if ($prodAtrtib->$field) $require[] = $attrib->attr_id;
                }
            }
        }
        
        $attributeText = null;
		
        if (count($require) && $product_attr_id){
			$lang = JSFactory::getLang();
			$require_key = array();
			for ($i = 0; $i < count($require); $i++){
				$require_key[$i] = "attr_".$require[$i];
			}
            
			$db = JFactory::getDbo();
			$query = "SELECT ".implode(",", $require_key)." FROM `#__jshopping_products_attr` WHERE `product_attr_id` = ".$product_attr_id." AND `product_id` = ".$product_id;
			$db->setQuery($query);
			$attr_value = $db->loadAssoc();
			
            //check if product_attr_id exist for selected product
            if (is_array($attr_value) && count($attr_value)){
                $attr = array();
			
                for ($i = 0; $i < count($require); $i++){
                    $query = "SELECT ja.`".$lang->get("name")."` AS attr_title, jav.`".$lang->get("name")."` AS attr_value 
                                FROM `#__jshopping_attr` AS ja
                                LEFT JOIN `#__jshopping_attr_values` AS jav ON (ja.`attr_id` = jav.`attr_id`)	
                                WHERE ja.`attr_id` = ".$require[$i]." AND jav.`value_id` = ".$attr_value[$require_key[$i]];
                    $db->setQuery($query);
                    $attr[$i] = $db->loadObject();
                }

                foreach ($attr as $at){
                    $attributeText .= $at->attr_title.": ".$at->attr_value."; ";
                }
            }
		}
        
        if ($attributeText === null){
            $attributeText = '';
            $product_attr_id = 0;
        }
        
        return $attributeText;
	}
}