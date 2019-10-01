<?php
/**
 * Product related class
 *
 * @since 1.0
 */
namespace dotix;

defined( 'WPINC' ) || exit;

class Product extends Instance
{
	protected static $_instance ;

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
			'label' => sprintf( __( 'Containing %s', 'dotix' ), ucfirst( Conf::val( 'credit_title' ) ) ),
			'class' => 'tix-custom-field',
			'desc_tip' => true,
			'description' => __( 'Enter the credits to be refilled to the order if the user finish payment.', 'dotix' ),
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
		$arr = array( DOTIX_TAG => ucfirst( Conf::val( 'credit_title' ) ) ) ;

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

		echo '<span class="product_meta--credits dotix-product">' . sprintf( __( '%s containing', 'dotix' ), ucfirst( Conf::val( 'credit_title' ) ) ) . ': <strong>' . $credit . '</strong></span>' ;
	}

}
