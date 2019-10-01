<?php
/**
 * Core class
 *
 * @since 1.0
 */
namespace dotix;

defined( 'WPINC' ) || exit;

class Core extends Instance
{
	protected static $_instance;

	const VER = DOTIX_V;

	/**
	 * Init
	 *
	 * @since  1.0
	 * @access protected
	 */
	protected function __construct()
	{
		Conf::get_instance()->init();

		if ( is_admin() ) {
			Admin::get_instance()->init();
		}

		Order::get_instance()->init();

		Product::get_instance()->init();

		Vendor::get_instance()->init();

		REST::get_instance()->init();

		Util::get_instance()->init();

		register_activation_hook( DOTIX_DIR . 'dotix.php', __NAMESPACE__ . '\Util::activate' );
		register_deactivation_hook( DOTIX_DIR . 'dotix.php', __NAMESPACE__ . '\Util::deactivate' ) ;
		register_uninstall_hook( DOTIX_DIR . 'dotix.php', __NAMESPACE__ . '\Util::uninstall' ) ;
	}
}
