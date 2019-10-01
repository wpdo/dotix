<?php
/**
 * Data structure class
 *
 * @since 1.0
 */
namespace dotix;

defined( 'WPINC' ) || exit;

class Data extends Instance
{
	protected static $_instance;

	const TB_CONSUME = 'dotix_consume' ; // Consume log

	private $_charset_collate ;
	private $_tb_consume ;

	/**
	 * Init
	 *
	 * @since  1.0
	 * @access protected
	 */
	protected function __construct()
	{
		global $wpdb ;

		$this->_charset_collate = $wpdb->get_charset_collate() ;

		$this->_tb_consume = self::tb_consume() ;
	}

	/**
	 * Get table consume
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function tb_consume()
	{
		global $wpdb ;
		return $wpdb->prefix . self::TB_CONSUME ;
	}

	/**
	 * Check if table existed or not
	 *
	 * @since  1.0
	 * @access public
	 */
	public static function tb_consume_exist()
	{
		global $wpdb ;

		$instance = self::get_instance() ;

		return $wpdb->get_var( "SHOW TABLES LIKE '$instance->_tb_consume'" ) ;
	}

	/**
	 * Create table consume
	 *
	 * @since  1.0
	 * @access public
	 */
	public function create_tb_consume()
	{
		if ( defined( __NAMESPACE__ . '_DID_' . __FUNCTION__ ) ) {
			return;
		}
		define( __NAMESPACE__ . '_DID_' . __FUNCTION__, true );

		global $wpdb;

		// Check if table exists first
		if ( self::tb_consume_exist() ) {
			return;
		}

		$sql = sprintf(
			'CREATE TABLE IF NOT EXISTS `%1$s` (' . $this->_tb_structure( 'consume' ) . ') %2$s;',
			$this->_tb_consume,
			$this->_charset_collate
		);

		$res = $wpdb->query( $sql );
	}

	/**
	 * Get data structure of one table
	 *
	 * @since  1.0
	 * @access private
	 */
	private function _tb_structure( $tb )
	{
		return File::read( DOTIX_DIR . 'src/data_structure/' . $tb . '.sql' ) ;
	}

	/**
	 * Drop generated tables
	 *
	 * @since  1.1
	 * @access public
	 */
	public function del_tables()
	{
		global $wpdb ;

		if ( self::tb_consume_exist() ) {

			$q = "DROP TABLE IF EXISTS $this->_tb_consume" ;
			$wpdb->query( $q ) ;
		}

	}


}