<?php
// No direct access
defined( '_JEXEC' ) or die;

class plgsystemjs_verify_emailInstallerScript
{

	function postflight( $type, $parent )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery( true );
		$query->update( '#__extensions' )->set( 'enabled=1' )->where( 'type=' . $db->q( 'plugin' ) )->where( 'element=' . $db->q( 'js_verify_email' ) );
		$db->setQuery( $query )->execute();

	}
}