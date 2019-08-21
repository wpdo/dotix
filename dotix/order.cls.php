<?php
/**
 * Order related class
 *
 * @since 1.0
 */
defined( 'WPINC' ) || exit ;

class Dotix_Order
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

		echo "<p>Credits containing : $credits</p>";

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

		echo "<h2 class='woocommerce-order-details__title'>" . __( 'Credits remaining', 'dotix' ) . "</h2>
			<span style='font-size: 2em; color: purple; font-weight: bold;background-color:#f8f8f8;padding: 2px 20px;margin-left:40px;'>$tixleft</span>
		" ;

		$status = $order->get_status() ;

		if ( $status != 'completed' ) {
			echo "<div class='woocommerce-error'>Credits not available yet due to order status: $status</div>" ;
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
			DOTIX_TAG . '_total' => __( 'Credits Total', 'dotix' ),
			DOTIX_TAG => __( 'Credits Left', 'dotix' ),
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
		$order = $this->_rest_validate_order( $data ) ;
		if ( ! $order ) {
			return new WP_Error( 'wrong_hash', 'Failed to validate hash key', array( 'status' => 422 ) ) ;
		}

		if ( $order->status != 'completed' ) {
			return new WP_Error( 'wong_status', 'The order is not under completed status.', array( 'status' => 409 ) ) ;
		}

		if ( empty( $_POST[ 'num' ] ) ) {
			// @see https://softwareengineering.stackexchange.com/questions/341732/should-http-status-codes-be-used-to-represent-business-logic-errors-on-a-server
			// The 409 (Conflict) status code indicates that the request could not be completed due to a conflict with the current state of the target resource.
			return new WP_Error( 'lack_of_param', 'Please spedify the credits to consume.', array( 'status' => 409 ) ) ;
		}

		$bal = $order->get_meta( DOTIX_TAG ) ;

		if ( ! $bal ) {
			return new WP_Error( 'lack_of_bal', 'Not enough credits.', array( 'status' => 409 ) ) ;
		}

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

		return array(
			'order_id'	=> $order->get_id(),
			'status'	=> $order->get_status(),
			'consume'	=> $num,
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
