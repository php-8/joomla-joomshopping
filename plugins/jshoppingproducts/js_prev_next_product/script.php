<?php
// No direct access
defined( '_JEXEC' ) or die;

class plgJshoppingProductsJs_Prev_Next_ProductInstallerScript
{

	function postflight( $type, $parent )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery( true );
		$query->update( '#__extensions' )->set( 'enabled=1' )->where( 'type=' . $db->q( 'plugin' ) )->where( 'element=' . $db->q( 'js_prev_next_product' ) );
		$db->setQuery( $query )->execute();

	}
}