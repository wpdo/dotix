<?php
/**
 * Product related class
 *
 * @since 1.0
 */
defined( 'WPINC' ) || exit ;

class Dotix_Product
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
	 * init
	 */
	public function init()
	{
		// Backend product related
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'field' ) ) ;

		add_action( 'woocommerce_process_product_meta', array( $this, 'field_save' ) ) ;

		add_filter( 'manage_edit-product_columns', array( $this, 'column_title' ), 15 ) ;

		add_action( 'manage_product_posts_custom_column', array( $this, 'column' ), 10, 2 ) ;

		// Frontend product
		add_action( 'woocommerce_product_meta_start', array( $this, 'field_frontend' ), 10 ) ;

	}

	/**
	 * Hook to product edit page
	 *
	 * @since  1.0
	 */
	public function field()
	{
		$args = array(
			'id' => DOTIX_TAG,
			'label' => __( 'Containing credits', 'dotix' ),
			'class' => 'tix-custom-field',
			'desc_tip' => true,
			'description' => __( 'Enter the credits to be refilled to the order if users finished payment.', 'dotix' ),
		) ;
		woocommerce_wp_text_input( $args ) ;
	}

	/**
	* Saves the custom field data to product meta data
	*
	* @since  1.0
	*/
	public function field_save( $post_id )
	{
		$product = wc_get_product( $post_id ) ;
		$v = isset( $_POST[ DOTIX_TAG ] ) ? (int) $_POST[ DOTIX_TAG ] : 0 ;
		$product->update_meta_data( DOTIX_TAG, $v ) ;
		$product->save();
	}

	/**
	 * Display column in Product list
	 *
	 * @since  1.0
	 */
	public function column_title( $columns )
	{
		//add column
		$arr = array( DOTIX_TAG => __( 'Credits', 'dotix' ) ) ;

		$first_arr = array_splice( $columns, 0, 6 ) ;
		$columns = array_merge( $first_arr, $arr, $columns ) ;

		return $columns ;
	}

	/**
	 * Display column data in Product list
	 *
	 * @since  1.0
	 */
	public function column( $column, $postid )
	{
		if ( $column == DOTIX_TAG ) {
			echo "<div style='text-align:center;'>" . get_post_meta( $postid, DOTIX_TAG, true ) . '</div>' ;
		}
	}

	/**
	 * Display credits containing in product detail page
	 *
	 * @since  1.0
	 */
	public function field_frontend()
	{
		global $product ;

		$credit = $product->get_meta( DOTIX_TAG ) ;

		if ( ! $credit ) {
			return ;
		}

		echo "<span>Credits containing</span>: <span style='font-weight:bold; color:purple;'>$credit</span>" ;
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
