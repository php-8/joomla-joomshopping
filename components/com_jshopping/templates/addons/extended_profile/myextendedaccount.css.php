<?php

defined('_JEXEC') or die;
?>
<style>
.jshop_nvg {
	border:1px solid #999; 
	-webkit-border-radius: 0px 0px 5px 5px;
	border-radius: 5px; 
	padding: 10px;
	clear:both;
}

.jshop_nvg div{
	margin: 3px;
}

.eac_box {
   display: none;
   clear: both;
}
.eac_box.visible {
   display: block;
}

ul.eac_tabs {margin:0 5px!important;}

ul.eac_tabs li.current { 
	background: rgb(197,222,234); /* Old browsers */
	background: -moz-linear-gradient(top,  rgba(197,222,234,1) 0%, rgba(138,187,215,1) 31%, rgba(6,109,171,1) 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(197,222,234,1)), color-stop(31%,rgba(138,187,215,1)), color-stop(100%,rgba(6,109,171,1))); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  rgba(197,222,234,1) 0%,rgba(138,187,215,1) 31%,rgba(6,109,171,1) 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  rgba(197,222,234,1) 0%,rgba(138,187,215,1) 31%,rgba(6,109,171,1) 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  rgba(197,222,234,1) 0%,rgba(138,187,215,1) 31%,rgba(6,109,171,1) 100%); /* IE10+ */
	background: linear-gradient(to bottom,  rgba(197,222,234,1) 0%,rgba(138,187,215,1) 31%,rgba(6,109,171,1) 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#c5deea', endColorstr='#066dab',GradientType=0 ); /* IE6-9 */
	border: 1px solid #ccc /*rgb(44,83,158)*/;
	padding: 3px 10px!important;
	color: #fff!important;
	font-weight:bold;
}
ul.eac_tabs li { 
	background: rgb(222,239,255); /* Old browsers */
	background: -moz-linear-gradient(top,  rgba(222,239,255,1) 0%, rgba(152,190,222,1) 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(222,239,255,1)), color-stop(100%,rgba(152,190,222,1))); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  rgba(222,239,255,1) 0%,rgba(152,190,222,1) 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  rgba(222,239,255,1) 0%,rgba(152,190,222,1) 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  rgba(222,239,255,1) 0%,rgba(152,190,222,1) 100%); /* IE10+ */
	background: linear-gradient(to bottom,  rgba(222,239,255,1) 0%,rgba(152,190,222,1) 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#deefff', endColorstr='#98bede',GradientType=0 ); /* IE6-9 */
	border: 1px solid #ccc;
	float: left;
	list-style: none outside none;
	margin-right:2px!important;
	border-radius: 5px 5px 0 0;
	padding: 3px 10px!important;
	color: #444!important;
	font-weight:bold;
}

ul.eac_tabs li:hover { cursor: pointer;}

.jshop_nvg_edit {float:right; margin-right:20px;}
.jshop_nvg_edit a{padding: 5px 9px; background:#777; border: 1px solid #ccc; color:#fff;text-decoration:none;-webkit-border-radius: 5px;
border-radius: 5px; }
.jshop_nvg_edit a:hover{background:#950000; text-decoration:none;}
.nvg_groups_list {border:none;}
.nvg_loyalty {font-weight:bold;}

.nvg_name, .nvg_name1{
	background: #4B78A7;
	-webkit-border-radius: 5px;
	border-radius: 5px;
	font-weight: bold;
	padding: 5px 10px;
	color:#fff;
 }
.nvg_name{
	font-size: 24px;
 }

.nvg_name1{
	font-size: 16px;
 }

.acc_nvg_name {
	font-weight:bold;
	color: #02308C;
}

table.eac_groups_list{
	margin: 20px 10px 10px;
	width: 98%;
}

table.eac_groups_list tr{
	border-bottom:1px dotted #ccc;
}
table tr:hover{
	background: #E5EBEE;
	cursor: default;
}

.eac_groups_list th.eac_title {
	background: rgb(246,248,249); /* Old browsers */
	background: -moz-linear-gradient(top,  rgba(246,248,249,1) 0%, rgba(229,235,238,1) 43%, rgba(244,242,255,1) 64%, rgba(245,247,249,1) 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(246,248,249,1)), color-stop(43%,rgba(229,235,238,1)), color-stop(64%,rgba(244,242,255,1)), color-stop(100%,rgba(245,247,249,1))); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 43%,rgba(244,242,255,1) 64%,rgba(245,247,249,1) 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 43%,rgba(244,242,255,1) 64%,rgba(245,247,249,1) 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 43%,rgba(244,242,255,1) 64%,rgba(245,247,249,1) 100%); /* IE10+ */
	background: linear-gradient(to bottom,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 43%,rgba(244,242,255,1) 64%,rgba(245,247,249,1) 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f6f8f9', endColorstr='#f5f7f9',GradientType=0 ); /* IE6-9 */
	color:#111;
	border:1px solid #ccc;
	text-align:center;
	vertical-align: middle;
	margin: 20px;
	padding:3px 0;
	border-left: medium none !important; border-right: medium none !important;
}
.eac_groups_list td.eac_title{
	font-weight:bold;
	vertical-align: middle;
	color:#041670;
	padding:3px 0;
	border-left: medium none !important;
	border-right: medium none !important;
}
.eac_groups_list td.eac_discount{font-weight:bold; vertical-align: middle;text-align:center;color:#4B78A7; border-left: medium none !important; border-right: medium none !important;}

.eac_groups_list td.eac_desription{vertical-align: middle;  border-left: medium none !important; border-right: medium none !important;}

.jshop_nvg fieldset {border: 1px solid #aaa!important; border-radius:5px; padding:10px!important; margin:0 10px 20px;}

.jshop_nvg legend {
	background: rgb(246,248,249); /* Old browsers */
	background: -moz-linear-gradient(top,  rgba(246,248,249,1) 0%, rgba(229,235,238,1) 43%, rgba(244,242,255,1) 64%, rgba(245,247,249,1) 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(246,248,249,1)), color-stop(43%,rgba(229,235,238,1)), color-stop(64%,rgba(244,242,255,1)), color-stop(100%,rgba(245,247,249,1))); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 43%,rgba(244,242,255,1) 64%,rgba(245,247,249,1) 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 43%,rgba(244,242,255,1) 64%,rgba(245,247,249,1) 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 43%,rgba(244,242,255,1) 64%,rgba(245,247,249,1) 100%); /* IE10+ */
	background: linear-gradient(to bottom,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 43%,rgba(244,242,255,1) 64%,rgba(245,247,249,1) 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f6f8f9', endColorstr='#f5f7f9',GradientType=0 ); /* IE6-9 */
	color:#555;
	border: 1px solid #AAAAAA;
	font-weight: bold;
	padding: 5px 15px !important;
	-webkit-border-radius: 5px;
	border-radius: 5px;
	text-transform: uppercase;
	font-size:12px;
	width: inherit;
}

.bonusup, .bonusdown{font-size:13px!important;}
.bonusup {color:#225405!important;}
.bonusdown {color:#950000!important;}

span.aec_value {border-bottom:1px dotted #aaa; color:#4B78A7; font-size: 15px}

.bon_orders, .bon_dissys {float: left; width: 49%;}
.bon_dissys {}
.bon_bonsys {border:none;}

table.eac_coupons, table.eac_orders, table.eac_bloknot {width: 98%; margin: 20px 0px;}
table.eac_coupons tr, table.eac_coupons td, table.eac_orders tr, table.eac_orders td, table.eac_bloknot td, table.eac_bloknot tr {
	border-bottom: 1px solid #aaa;
    border-left: medium none;
    border-right: medium none;
	padding: 2px;}

table.eac_coupons td {
	text-align:center;}

table.eac_coupons th.eac_title, 
table.eac_orders th.eac_title,
table.eac_bloknot th.eac_title {
	background: rgb(246,248,249); /* Old browsers */
	background: -moz-linear-gradient(top,  rgba(246,248,249,1) 0%, rgba(229,235,238,1) 43%, rgba(244,242,255,1) 64%, rgba(245,247,249,1) 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(246,248,249,1)), color-stop(43%,rgba(229,235,238,1)), color-stop(64%,rgba(244,242,255,1)), color-stop(100%,rgba(245,247,249,1))); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 43%,rgba(244,242,255,1) 64%,rgba(245,247,249,1) 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 43%,rgba(244,242,255,1) 64%,rgba(245,247,249,1) 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 43%,rgba(244,242,255,1) 64%,rgba(245,247,249,1) 100%); /* IE10+ */
	background: linear-gradient(to bottom,  rgba(246,248,249,1) 0%,rgba(229,235,238,1) 43%,rgba(244,242,255,1) 64%,rgba(245,247,249,1) 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f6f8f9', endColorstr='#f5f7f9',GradientType=0 ); /* IE6-9 */
	color:#111;
	border:1px solid #ccc;
	text-align:center;
	vertical-align: middle;
	margin: 20px;
	padding:3px 0;
	border-left: medium none !important; 
	border-right: medium none !important;
}
.eac_coupon_acticve {color:#037005;font-weight:bold;}
.eac_coupon_used {color:#aaa;}
.eac_coupon_downtime {color:#D68B8B; text-decoration:line-through;}
.eac_coupon_future {color:#3F46C1;font-weight:bold;}

#eac_tooltip{
	position:absolute;
	border:1px solid #ccc;
	background:#777;
	padding:5px;
	display:none;
	color:#fff;
	width:300px;
}

.eac_preview {
	display: none;
}
span.deffered_pay {display:block; margin: 5px 0;}

span a.deffered_paybutton {padding:5px 12px; background: #4B78A7; color:#B0EAFC!important;-webkit-border-radius: 5px; border-radius: 5px; text-decoration:none; margin-top:5px;font-weight:bold;}

span a:hover.deffered_paybutton{color:#fff!important;}

.eac_order_product_name {
	background: url(dotitname.png)none repeat scroll 0 0 ;
	border-color: #E59C00;
	border-radius: 0 0 0 0;
	border-style: dashed none dashed solid;
	border-width: 0 0 0px 5px;
	margin: 8px 0 !important;
	padding: 3px 5px;
	}
.eac_order_product_name_sum {float:right; padding: 3px 5px;font-size:9px;}

a.eac_norder, a:hover.eac_norder {font-family: monospace; font-size:16px; font-weight:bold; text-decoration:none; border-bottom:1px dashed #888;}

table.eac_orders p {padding:0 0 0 5px!important; margin:0!important; }

p.jshop_cart_attribute {
  font-size: 11px;
  font-style: italic;
  font-weight: bold;
  padding-left: 10px;
  margin: 0 !important;
}
.order_date {font-size: 12px !important; font-style: italic; padding: 3px 0px 3px;"}
</style>