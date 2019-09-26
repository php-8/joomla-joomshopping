<?php
/**
 * mod_vertical_menu - Vertical Menu
 *
 * @author    Balint Polgarfi
 * @copyright 2014-2019 Offlajn.com
 * @license   https://gnu.org/licenses/gpl-2.0.html
 * @link      https://offlajn.com
 */
?><?php
/*-------------------------------------------------------------------------
# plg_offlajnparams - Offlajn Params
# -------------------------------------------------------------------------
# @ author    Balint Polgarfi
# @ copyright Copyright (C) 2016 Offlajn.com  All Rights Reserved.
# @ license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @ website   http://www.offlajn.com
-------------------------------------------------------------------------*/
?><?php
defined( '_JEXEC' ) or die( 'Restricted access' );

$version = JRequest::getString('v', '', 'GET');
$hash = JRequest::getString('hash', '', 'POST');
$post = JRequest::get('POST');
$hash = isset($post['hash']) ? '&hash='.$post['hash'].'&u='.JURI::root() : '';

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'http://offlajn.com/index2.php?option=com_offlajn_update_info&format=raw&v='.$version.$hash
));
$resp = curl_exec($curl);
curl_close($curl);

echo $resp;
exit;