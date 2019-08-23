<?php
/**
 * Vendor related class
 *
 * @since 1.0
 */
defined( 'WPINC' ) || exit ;

class Dotix_Vendor
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
	 * Hooks init
	 */
	public function init()
	{
		// QRCode in frontend order table
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'qrcode_in_order_detail' ), 30 ) ;
		// QRCode in backend order edit page
		add_action( 'add_meta_boxes_shop_order', array( $this, 'qrcode_in_order_edit' ) ) ;
		// QR code short page
		add_action( 'init', array( $this, 'qrcode_page' ) );

		// Resiter vendor post type
		add_action( 'init', array( $this, 'custom_post_type' ), 0 ) ;
		// Add custom filed hash to vendor
		add_action( 'add_meta_boxes_vendor', array( $this, 'qrcode_in_vendor_edit' ) );
		// Save hash in vendor
		add_action( 'save_post_vendor', array( $this, 'hash_save' ) );

		// Register Vendor Tix tax
		add_action( 'init', array( $this, 'vendor_dotix_taxonomies' ), 0 ) ;

		// Vendor -> Tix -> Dotix meta add
		add_action( 'vendor_dotix_add_form_fields', array( $this, 'dotix_amount_meta_add' ), 10, 2 ) ;
		// Vendor -> Tix -> Dotix meta add submit
		add_action( 'created_vendor_dotix', array( $this, 'dotix_amount_meta_post' ), 10, 2 ) ;
		// Vendor -> Tix -> Dotix meta edit
		add_action( 'vendor_dotix_edit_form_fields', array( $this, 'dotix_amount_meta_edit' ), 10, 2 ) ;
		// Vendor -> Tix -> Dotix meta edit submit
		add_action( 'edited_vendor_dotix', array( $this, 'dotix_amount_meta_update' ), 10, 2 ) ;
		// Vendor -> Tix -> Dotix meta in list column
		add_filter( 'manage_edit-vendor_dotix_columns', array( $this, 'dotix_amount_column_title' ) ) ;
		// Vendor -> Tix -> Dotix in list
		add_filter( 'manage_vendor_dotix_custom_column', array( $this, 'dotix_amount_column' ), 10, 3 ) ;
		// Vendor -> Tix -> Dotix in list sortable
		add_filter( 'manage_edit-vendor_dotix_sortable_columns', array( $this, 'dotix_amount_column_sortable' ) ) ;
	}

	/**
	 * Show QR code in order
	 */
	public function qrcode_in_order_detail( $order )
	{
		$this->qrcode( $order->get_order_key() ) ;
	}

	/**
	 * Show QR code page
	 */
	public function qrcode_page()
	{
		if ( empty( $_GET[ 'qrtix' ] ) ) {
			return ;
		}

		$order_id = wc_get_order_id_by_order_key( $_GET[ 'qrtix' ] ) ;

		if ( ! $order_id ) {
			return ;
		}

		$order = wc_get_order( $order_id ) ;

		$status = $order->get_status() ;
		$bal = $order->get_meta( DOTIX_TAG ) ;

		$color = $status == 'completed' ? 'success' : 'warning' ;

		require DOTIX_DIR . 'tpl/tix_shortpage.tpl.php' ;
		exit ;
	}

	/**
	 * QRCode in backend order edit
	 */
	public function qrcode_in_order_edit( $post )
	{
		if ( ! get_post_meta( $post->ID, DOTIX_TAG, true ) ) {
			return ;
		}

		add_meta_box( 'dotix-meta-box', _( 'Barcode' ), array( $this, 'qrcode_in_order_edit_metabox' ), 'shop_order', 'side' );
	}

	/**
	 * QRCode in backend order edit metabox
	 */
	public function qrcode_in_order_edit_metabox()
	{
		global $post;

		$order = wc_get_order( $post->ID );

		$this->qrcode( $order->get_order_key(), 5 );

		echo '<br /> <h2>' . $order->get_meta( DOTIX_TAG ) . ' credits left</h2>';
	}

	/**
	 * QRCode Parse
	 */
	public function qrcode( $order_key, $size = 20 )
	{
		echo do_shortcode( '[qrcode size="' . $size . '"]' . home_url( '/' ) . '?qrtix=' . $order_key . '[/qrcode]' );
	}

	/**
	 * QRCode for vendor data
	 */
	public function vendor_qrcode( $vendor_data, $size = 20 )
	{
		echo do_shortcode( '[qrcode size="' . $size . '"]' . json_encode( $vendor_data ) . '[/qrcode]' );
	}

	/**
	 * QRCode in backend order edit
	 */
	public function qrcode_in_vendor_edit( $post )
	{
		add_meta_box( 'dotix-meta-box', __( 'Dotix Info' ), array( $this, 'qrcode_in_vendor_edit_metabox' ), 'vendor', 'side' );
	}

	/**
	 * QRCode in backend vendor edit metabox
	 */
	public function qrcode_in_vendor_edit_metabox()
	{
		global $post;

		$auth_key = get_post_meta( $post->ID, 'dotix_hash', true );

		require DOTIX_DIR . 'tpl/vendor_dotix.tpl.php';

		$vendor_data = array(
			'home'		=> home_url( '/' ),
			'app_id'	=> $post->ID,
			'auth_key'	=> $auth_key,
		);
		$this->vendor_qrcode( $vendor_data, 5 );

	}

	/**
	 * Vendor Hash save
	 */
	public function hash_save( $post_id )
	{
		if ( empty( $_POST[ 'dotix_hash' ] ) ) {
			$hash = md5( time() );
		}
		else {
			$hash = preg_replace( '|\W|', '', $_POST[ 'dotix_hash' ] );
		}

		$hash = substr( $hash, 0, 12 );

		update_post_meta( $post_id, 'dotix_hash', $hash );
	}


	/**
	 * Vendor register
	 */
	public function custom_post_type()
	{
		$labels = array(
			'name'                => __( 'DotixApp' ),
			'singular_name'       => __( 'App'),
			'menu_name'           => __( 'DotixApp'),
			'parent_item_colon'   => __( 'Parent App'),
			'all_items'           => __( 'All Apps'),
			'view_item'           => __( 'View App'),
			'add_new_item'        => __( 'Add New App'),
			'add_new'             => __( 'Add New'),
			'edit_item'           => __( 'Edit App'),
			'update_item'         => __( 'Update App'),
			'search_items'        => __( 'Search App'),
			'not_found'           => __( 'Not Found'),
			'not_found_in_trash'  => __( 'Not found in Trash')
		);

		$args = array(
			'label'               => __( 'DotixApp'),
			'description'         => __( 'Dotix connection list'),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields'),
			'public'              => true,
			'hierarchical'        => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'has_archive'         => true,
			'can_export'          => true,
			'exclude_from_search' => false,
			'yarpp_support'       => true,
			'taxonomies' 	      => array('post_tag'),
			'publicly_queryable'  => true,
			'capability_type'     => 'page'
		);

		register_post_type( 'vendor', $args );
	}

	/**
	 * Regsiter Vendor -> Tix taxonomy
	 */
	public function vendor_dotix_taxonomies() {
		// Add new `Ticket Type` taxonomy to Vendor
		register_taxonomy(
			'vendor_dotix',
			'vendor',
			array(
				// Hierarchical taxonomy (like categories)
				'hierarchical' => true,
				// This array of options controls the labels displayed in the WordPress Admin UI
				'labels' => array(
				'name' => __( 'Dotix Set', 'dotix' ),
				'singular_name' => __( 'Dotix Set', 'dotix' ),
				'search_items' =>  __( 'Search Dotix Set' ),
				'all_items' => __( 'All Dotix Set' ),
				'parent_item' => __( 'Parent Dotix Set' ),
				'parent_item_colon' => __( 'Parent Dotix Set:' ),
				'edit_item' => __( 'Edit Dotix Set' ),
				'update_item' => __( 'Update Dotix Set' ),
				'add_new_item' => __( 'Add New Dotix Set' ),
				'new_item_name' => __( 'New Dotix Set Name' ),
				'menu_name' => __( 'Dotix Set' ),
			),
			// Control the slugs used for this taxonomy
			'rewrite' => array(
				'slug' => 'Dotix Set', // This controls the base slug that will display before each term
				'with_front' => true, // Don't display the category base before "/Vendors/"
				'hierarchical' => true // This will allow URL's like "/Vendors/boston/cambridge/"
			),
		));
	}

	/**
	 * Vendor Tix Dotix add
	 */
	public function dotix_amount_meta_add( $taxonomy ) {
		require DOTIX_DIR . 'tpl/vendor_dotix_ammount_add.tpl.php' ;
	}

	/**
	 * Vendor Tix Dotix add submit
	 */
	public function dotix_amount_meta_post( $term_id, $tt_id ){
		$val = $_POST[ 'dotix' ] == 'max' ? 'max' : (int) $_POST[ 'dotix' ] ;
		add_term_meta( $term_id, 'dotix', $val, true ) ;
	}

	/**
	 * Vendor Tix Dotix edit
	 */
	public function dotix_amount_meta_edit( $term, $taxonomy )
	{
		$curr = get_term_meta( $term->term_id, 'dotix', true ) ;

		require DOTIX_DIR . 'tpl/vendor_dotix_ammount_edit.tpl.php' ;
	}

	/**
	 * Vendor Tix Dotix edit submit
	 */
	public function dotix_amount_meta_update( $term_id, $tt_id )
	{
		$val = $_POST[ 'dotix' ] == 'max' ? 'max' : (int) $_POST[ 'dotix' ] ;
		update_term_meta( $term_id, 'dotix', $val, true ) ;
	}

	/**
	 * Show Dotix column in Vendor Tix taxonomy list
	 */
	public function dotix_amount_column_title( $columns )
	{
		$columns[ 'dotix' ] = __( 'Dotix Amount', 'dotix' ) ;
		return $columns ;
	}

	/**
	 * Vendor Tix list column Dotix
	 */
	public function dotix_amount_column( $content, $column_name, $term_id )
	{
		if ( $column_name == 'dotix' ) {

			$term_id = absint( $term_id ) ;
			$dotix = get_term_meta( $term_id, 'dotix', true ) ;

			if( ! empty( $dotix ) ) {
				$content .= esc_html( $dotix ) ;
			}
		}

		return $content ;
	}

	/**
	 * Vendor Tix list Dotix sortable
	 */
	public function dotix_amount_column_sortable( $sortable )
	{
		$sortable[ 'dotix' ] = 'dotix' ;
		return $sortable ;
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
