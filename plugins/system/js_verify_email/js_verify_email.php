<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

class plgSystemjs_verify_email extends JPlugin {

    function onBeforeCompileHead() {
        if (JFactory::getApplication()->isAdmin()) {
            return;
        }
        if (!(JRequest::getVar('option') == 'com_jshopping' && ((JRequest::getVar('controller') == 'user' && JRequest::getVar('task') == 'register') || (JRequest::getVar('controller') == 'quickcheckout')))) {
            return;
        }
        if (!file_exists(JPATH_SITE . '/components/com_jshopping/jshopping.php')) {
            return true;
        }

        $doc = JFactory::getDocument();
        $script = "
            jQuery(document).ready(function() {
                  jQuery('#email').focus(function(){
                    if(jQuery('#emailerror')){
                      jQuery('#emailerror').remove();
                      jQuery('#email').css('background-color','transparent');
                     }
                  });
                  jQuery('#u_name').focus(function(){
                    if(jQuery('#u_nameerror').length>0){
                      jQuery('#u_nameerror').remove();
                      jQuery('#u_name').css('background-color','transparent');
                     }
                  });
				  jQuery('#email').blur(VerifyJSEmail());
				  jQuery('#u_name').blur(VerifyJSLogin());
             });
		function VerifyJSLogin(){
			var u_name=jQuery('#u_name').val();
			if(u_name.length==0){
				return;
			}
			
			jQuery.ajax( {
								url: '/?action=VerifyJSLogin&login='+u_name,
								type: 'post',
								data: u_name,
								success: function(response){
									if(response  != 'no'){
										jQuery('#u_name').after('<span class=\'wrap_ttip\' id=\'u_nameerror\'>Логин занят</span>').css('color','red').show(400);
                                        jQuery('#u_name').css('background-color','lightred');
									}else{
                                        jQuery('#u_name').after('<span class=\'wrap_ttip\' id=\'u_nameerror\'>Логин свободен</span>').show(400);
                                                                              
										jQuery('#u_name').css('background-color','lightGreen');
										
									}
								}
							} );
		}
		
		function VerifyJSEmail(){
			function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((\"[\w-\s]+\")|([\w-]+(?:\.[\w-]+)*)|(\"[\w-\s]+\")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
    }
			var email=jQuery('#email').val();
			if(email.length==0 ){
				return;
			}
			if(isValidEmailAddress(email)===false){
				jQuery('#email').after('<span class=\'wrap_ttip\' id=\'emailerror\'>это не Email</span>').show(400);
                                                                              
										jQuery('#email').css('background-color','red');
				return;
			}
			jQuery.ajax( {
								url: '/?action=VerifyJSEmail&email='+email,
								type: 'post',
								data: email,
								success: function(response){
									if(response  != 'no'){
										jQuery('#email').after('<span class=\'wrap_ttip\' id=\'emailerror\'>Email занят</span>').show(400);
                                                                              
										jQuery('#email').css('background-color','red');
									}else{
                                        jQuery('#email').after('<span class=\'wrap_ttip\' id=\'emailerror\'>Email свободен</span>').show(400);
                                                                              
										jQuery('#email').css('background-color','lightgreen');
									}
								}
							} );
		}
		
		
		";
        $doc->addScriptDeclaration($script);
    }
    function onAfterRender() {
        if (JRequest::getVar('option') == 'com_jshopping' && ((JRequest::getVar('controller') == 'user' && JRequest::getVar('task') == 'register') || (JRequest::getVar('controller') == 'quickcheckout'))) {
            $buffer = JResponse::getBody();
            $buffer = str_replace('name = "email"', 'name = "email" onblur="VerifyJSEmail()"', $buffer);
            $buffer = str_replace('name = "u_name"', 'name = "u_name" onblur="VerifyJSLogin()"', $buffer);
            JResponse::setBody($buffer);
            return true;
        }
    }
    function onAfterInitialise() { 
        // ini_set("display_errors", "1");
        // ini_set("display_startup_errors", "1");
        // ini_set('error_reporting', E_ALL);
        if (JFactory::getApplication()->isAdmin()) {
            return true;
        }
        if (!file_exists(JPATH_SITE . '/components/com_jshopping/jshopping.php')) {
            return true;
        }
        $input = JFactory::getApplication()->input;
        $db = JFactory::getDbo();
        if ($input->getCmd('action', '') === 'VerifyJSLogin') {
            $query = 'SELECT count(*) FROM #__users WHERE username = "' . trim(JRequest::getVar('login', '')) . '"';
            $db->setQuery($query);
            $result = $db->loadResult();
             if ($result == 0) print('no');
            else print('yes');
            exit;
        }
        if ($input->getCmd('action', '') === 'VerifyJSEmail') {
            $query = 'SELECT count(*) as kol FROM #__users WHERE email = "' . trim(JRequest::getVar('email', '')) . '"';
            $result = $db->setQuery($query)->loadResult();
            if ($result == 0) print('no');
            else print('yes');
            exit;
        }
    }

}
