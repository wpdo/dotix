<?php
/**
 * Core class
 *
 * @since 1.0
 */
defined( 'WPINC' ) || exit ;

class Dotix
{
	private static $_instance ;

	/**
	 * Init
	 *
	 * @since  1.0
	 * @access private
	 */
	private function __construct()
	{
	}

	public function init()
	{
		$__conf = Dotix_Conf::get_instance() ;
		$__gui = Dotix_GUI::get_instance() ;
		Dotix_Order::get_instance()->init() ;
		Dotix_Product::get_instance()->init() ;
		Dotix_REST::get_instance()->init() ;

		// Vendor init
		Dotix_Vendor::get_instance()->init() ;
	}

	/**
	 * Get the current instance object.
	 *
	 * @since 1.0
	 * @access public
	 */
	public static function get_instance()
	{
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self() ;
		}

		return self::$_instance ;
	}

}
