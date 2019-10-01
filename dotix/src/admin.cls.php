<?php
/**
 * Admin class
 *
 * @since 1.0
 */
namespace dotix;

defined( 'WPINC' ) || exit;

class Admin extends Instance
{
	protected static $_instance;

	/**
	 * Init admin
	 *
	 * @since  1.0
	 * @access public
	 */
	public function init()
	{
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_filter( 'plugin_action_links_dotix/dotix.php', array( $this, 'add_plugin_links' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}

	/**
	 * Admin setting page
	 *
	 * @since  1.0
	 * @access public
	 */
	public function admin_menu()
	{
		add_options_page( 'Dotix', 'Dotix', 'manage_options', 'dotix', array( $this, 'setting_page' ) );
	}

	/**
	 * admin_init
	 *
	 * @since  1.2.2
	 * @access public
	 */
	public function admin_init()
	{
		if ( get_transient( 'dotix_activation_redirect' ) ) {
			delete_transient( 'dotix_activation_redirect' );
			if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
				wp_safe_redirect( menu_page_url( 'dotix', 0 ) );
			}
		}
	}

	/**
	 * Plugin link
	 *
	 * @since  1.1
	 * @access public
	 */
	public function add_plugin_links( $links )
	{
		$links[] = '<a href="' . menu_page_url( 'dotix', 0 ) . '">' . __( 'Settings', 'dotix' ) . '</a>';

		return $links;
	}

	/**
	 * Display and save options
	 *
	 * @since  1.0
	 * @access public
	 */
	public function setting_page()
	{
		Data::get_instance()->create_tb_consume();

		if ( ! empty( $_POST ) ) {
			check_admin_referer( 'dotix' );

			// Save options
			$list = array() ;

			foreach ( Conf::get_instance()->get_options() as $id => $v ) {
				if ( $id == '_ver' ) {
					continue;
				}

				$list[ $id ] = ! empty( $_POST[ $id ] ) ? $_POST[ $id ] : false ;
			}

			foreach ( $list as $id => $v ) {
				Conf::update( $id, $v );
			}
		}

		require_once DOTIX_DIR . 'tpl/settings.tpl.php';
	}

	/**
	 * Display consume log
	 *
	 * @since  1.1
	 * @access public
	 */
	public function log()
	{
		global $wpdb;
		return $wpdb->get_results( 'SELECT * FROM ' . Data::tb_consume() . ' ORDER BY id DESC LIMIT 10' );
	}
}