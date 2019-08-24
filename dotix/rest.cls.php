<?php
/**
 * REST related class
 *
 * @since 1.0
 */
defined( 'WPINC' ) || exit ;

class Dotix_REST
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

	/**
	 * Init
	 */
	public function init()
	{
		// REST order hooks
		add_action( 'rest_api_init', array( $this, 'api_init' ) ) ;
	}

	/**
	 * Register REST hooks
	 *
	 * @since  1.0
	 * @access public
	 */
	public function api_init()
	{
		$__order = Dotix_Order::get_instance() ;
		$__vendor = Dotix_Vendor::get_instance() ;

		register_rest_route( 'dotix/v1', '/vendor/(?P<id>\d+)/(?P<hash>\w+)', array(
			'methods' => 'GET',
			'callback' => array( $__vendor, 'rest_vendor_get' ),
		) );

		register_rest_route( 'dotix/v1', '/order/(?P<hash>\w+)', array(
			'methods' => 'GET',
			'callback' => array( $__order, 'rest_tix_get' ),
		) );

		register_rest_route( 'dotix/v1', '/order/(?P<hash>\w+)', array(
			'methods' => 'POST',
			'callback' => array( $__order, 'rest_tix_consume' ),
		) );

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
