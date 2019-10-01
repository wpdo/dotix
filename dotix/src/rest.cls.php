<?php
/**
 * REST related class
 *
 * @since 1.0
 */
namespace dotix;

defined( 'WPINC' ) || exit ;

class REST extends Instance
{
	protected static $_instance;

	/**
	 * Init
	 */
	public function init()
	{
		// REST order hooks
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) ) ;
	}

	/**
	 * Register REST hooks
	 *
	 * @since  1.0
	 * @access public
	 */
	public function rest_api_init()
	{
		$__order = Order::get_instance() ;
		$__vendor = Vendor::get_instance() ;

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

}
