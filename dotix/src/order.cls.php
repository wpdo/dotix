<?php
/**
 * Order related class
 *
 * @since 1.0
 */
namespace dotix;

defined( 'WPINC' ) || exit ;

class Order extends Instance
{
	protected static $_instance ;

	/**
	 * Init
	 */
	public function init()
	{
 		// Backend order related
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'column_title' ) ) ;

		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'column' ) ) ;

		// Frontend order
		add_action( 'woocommerce_order_item_meta_start', array( $this, 'field_frontend' ), 10, 2 ) ;

		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'tix_frontend' ), 10 ) ;

		// Frontend order auto complete paid orders
		add_action( 'woocommerce_thankyou', array( $this, 'maybe_auto_complete' ), 20, 1 ) ;

		// Frontend order adding hook
		add_action( 'woocommerce_checkout_create_order', array( $this, 'fillup' ), 10 ) ;

	}

	/**
	 * Display containing tix in an order list
	 *
	 * @since  1.0
	 */
	public function field_frontend( $item_id, $item )
	{
		$product = $item->get_product() ;
		$credit = $product->get_meta( DOTIX_TAG ) ;
		if ( ! $credit ) {
			return ;
		}

		$quantity = $item->get_quantity() ;

		$credits = $quantity * $credit ;

		echo '<p class="dotix dotix-containing">' . sprintf( __( '%s containing', 'dotix' ), ucfirst( Conf::val( 'credit_title' ) ) ) . ':' . $credits . '</p>';

	}

	/**
	 * Display total credits in an order
	 *
	 * @since  1.0
	 */
	public function tix_frontend( $order )
	{
		$tixleft = $order->get_meta( DOTIX_TAG ) ;
		if ( ! $tixleft ) {
			return ;
		}

		echo "<h2 class='woocommerce-order-details__title dotix dotix-order-remaining_title'>" . sprintf( __( '%s remaining', 'dotix' ), ucfirst( Conf::val( 'credit_title' ) ) ) . "</h2>
			<style>
				.dotix-remaining_num {font-size: 2em; color: purple; font-weight: bold;background-color:#f8f8f8;padding: 2px 20px;margin-left:40px;}
			</style>
			<span class='dotix dotix-remaining_num'>$tixleft</span>
		" ;

		$status = $order->get_status() ;

		if ( $status != 'completed' ) {
			echo '<div class="woocommerce-error">' . sprintf( __( '%s not available yet due to order status', 'dotix' ), ucfirst( Conf::val( 'credit_title' ) ) ) . ': ' . $status . '</div>' ;
		}
	}

	/**
	 * Display order column title in background order list
	 *
	 * @since  1.0
	 */
	public function column_title( $columns )
	{
		//add column
		$arr = array(
			DOTIX_TAG . '_total' => sprintf( __( '%s Total', 'dotix' ), ucfirst( Conf::val( 'credit_title' ) ) ),
			DOTIX_TAG => sprintf( __( '%s Left', 'dotix' ), ucfirst( Conf::val( 'credit_title' ) ) ),
		) ;
		$first_arr = array_splice( $columns, 0, 4 ) ;
		$columns = array_merge( $first_arr, $arr, $columns ) ;

		return $columns ;
	}

	/**
	 * Display order column in background order list
	 *
	 * @since  1.0
	 */
	public function column( $column )
	{
		global $post ;
		$order = wc_get_order( $post->ID ) ;

		if ( $column == DOTIX_TAG ) {
			echo $order->get_meta( DOTIX_TAG ) ;
			return ;
		}

		if ( $column == DOTIX_TAG . '_total' ) {
			echo $this->cal_total( $order ) ;
			return ;
		}

	}

	/**
	 * Hook to fill credits into the related order
	 *
	 * @since  1.0
	 */
	public function fillup( $order )
	{
		if ( $order->get_meta( DOTIX_TAG ) ) {
			return ;
		}

		$credits = $this->cal_total( $order ) ;

		if ( ! $credits ) {
			return ;
		}

		$order->add_meta_data( DOTIX_TAG, $credits, true ) ;
	}

	/**
	 * Calculate original total credit based on order items
	 */
	public function cal_total( $order )
	{
		$credits = 0 ;

		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product() ;
			if ( ! $product ) {
				continue;
			}
			$credit = $product->get_meta( DOTIX_TAG ) ;
			if ( ! $credit ) {
				continue ;
			}

			$quantity = $item->get_quantity() ;

			$credits += $quantity * $credit ;
		}

		return $credits ?: '' ;
	}

	/**
	 * Check order remaining tix
	 *
	 * @since  1.0
	 */
	public function rest_tix_get( $data )
	{
		$order = $this->_rest_validate_order( $data ) ;
		if ( ! $order ) {
			return new WP_Error( 'wrong_hash', 'Failed to validate hash key', array( 'status' => 422 ) ) ;
		}

		return array(
			'order_id'	=> $order->get_id(),
			'status'	=> $order->get_status(),
			'balance' => $order->get_meta( DOTIX_TAG ),
		) ;
	}

	/**
	 * Consume tix in an order
	 *
	 * @since  1.0
	 */
	public function rest_tix_consume( $data )
	{
		global $wpdb;

		$order = $this->_rest_validate_order( $data ) ;
		if ( ! $order ) {
			return new WP_Error( 'wrong_hash', 'Failed to validate hash key', array( 'status' => 422 ) ) ;
		}

		if ( $order->get_status() != 'completed' ) {
			return new WP_Error( 'wong_status', 'The order is not under completed status.', array( 'status' => 409 ) ) ;
		}

		if ( empty( $_POST[ 'num' ] ) || empty( $_POST[ 'app_id' ] ) || empty( $_POST[ 'app_key' ] ) ) {
			// @see https://softwareengineering.stackexchange.com/questions/341732/should-http-status-codes-be-used-to-represent-business-logic-errors-on-a-server
			// The 409 (Conflict) status code indicates that the request could not be completed due to a conflict with the current state of the target resource.
			return new WP_Error( 'lack_of_param', 'Please spedify the num/app_id/app_key.', array( 'status' => 409 ) ) ;
		}

		// Validate vendor info
		if ( is_wp_error( $err = Vendor::get_instance()->validate( $_POST[ 'app_id' ], $_POST[ 'app_key' ] ) ) ) {
			return $err ;
		}

		$bal = $order->get_meta( DOTIX_TAG ) ;

		if ( ! $bal ) {
			return new WP_Error( 'lack_of_bal', 'Not enough credits.', array( 'status' => 409 ) ) ;
		}

		// Todo: check if allow this tix type

		$num = $_POST[ 'num' ] == 'max' ? $bal : (int) $_POST[ 'num' ] ;

		if ( ! $num ) {
			return new WP_Error( 'lack_of_param', 'Please spedify the credits to consume.', array( 'status' => 409 ) ) ;
		}

		if ( $num > $bal ) {
			return new WP_Error( 'lack_of_bal', 'Not enough credits.', array( 'status' => 409 ) ) ;
		}

		$new_bal = $bal - $num ;
		$order->update_meta_data( DOTIX_TAG, $new_bal ) ;
		$order->save() ;

		$order_id = $order->get_id();

		// Log
		$q = 'INSERT INTO ' . Data::tb_consume() . ' SET order_id = %d, app_id = %d, num_consumed = %d, num_left = %d, dateline = %d' ;
		$wpdb->query( $wpdb->prepare( $q, array( $order_id, $_POST[ 'app_id' ], $num, $new_bal, time() ) ) );

		return array(
			'order_id'	=> $order_id,
			'status'	=> $order->get_status(),
			'consumed'	=> $num,
			'balance'	=> $new_bal,
		) ;
	}

	/**
	 * Auto complete order when the order doesn't include other non-tix products
	 *
	 * @since  1.0
	 */
	public function maybe_auto_complete( $order_id )
	{
		$order = wc_get_order( $order_id ) ;

		// Check if contains non-tix product
		foreach ( $order->get_items() as $item ) {
			$product = $item->get_product() ;
			$credit = $product->get_meta( DOTIX_TAG ) ;
			if ( ! $credit ) {
				return ;
			}
		}

		// No updated status for orders delivered with Bank wire, Cash on delivery and Cheque payment methods.
		// @see https://stackoverflow.com/questions/35686707/woocommerce-auto-complete-paid-orders
		if ( in_array( $order->get_payment_method(), array( 'bacs', 'cod', 'cheque', '' ) ) ) {
			return ;
		}

		if ( $order->has_status( 'processing' ) ) {
			$order->update_status( 'completed' ) ;
		}
	}

	/**
	 * Validate REST id and key to return related order
	 *
	 * @since  1.0
	 */
	private function _rest_validate_order( $data )
	{
		$order_id = wc_get_order_id_by_order_key( $data[ 'hash' ] ) ;
		if ( ! $order_id ) {
			return false ;
		}

		$order = wc_get_order( $order_id ) ;

		return $order ;
	}

}
