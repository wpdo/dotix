<?php
/**
 * Plugin Name:       Dotix
 * Plugin URI:        https://github.com/wpdo/dotix
 * Description:       Ticket/credit system for WooCommerce
 * Version:           1.0
 * Author:            WPDO
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl.html
 * Text Domain:       dotix
 *
 * Copyright (C) 2019 WPDO
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.

 */
defined( 'WPINC' ) || exit ;

if ( defined( 'DOTIX_V' ) ) {
	return ;
}

define( 'DOTIX_V', '1.0' ) ;

! defined( 'DOTIX_TAG' ) && define( 'DOTIX_TAG', 'dotix_credit' ) ;
! defined( 'DOTIX_DIR' ) && define( 'DOTIX_DIR', dirname( __FILE__ ) . '/' ) ;// Full absolute path '/usr/local/***/wp-content/plugins/dotix/' or MU

require_once DOTIX_DIR . 'conf.cls.php' ;
require_once DOTIX_DIR . 'gui.cls.php' ;
require_once DOTIX_DIR . 'order.cls.php' ;
require_once DOTIX_DIR . 'product.cls.php' ;
require_once DOTIX_DIR . 'rest.cls.php' ;

$__conf = Dotix_Conf::get_instance() ;
$__gui = Dotix_GUI::get_instance() ;
$__order = Dotix_Order::get_instance() ;
$__product = Dotix_Product::get_instance() ;
$__rest = Dotix_REST::get_instance() ;

// Backend product related
add_action( 'woocommerce_product_options_general_product_data', array( $__product, 'field' ) ) ;

add_action( 'woocommerce_process_product_meta', array( $__product, 'field_save' ) ) ;

add_filter( 'manage_edit-product_columns', array( $__product, 'column_title' ), 15 ) ;

add_action( 'manage_product_posts_custom_column', array( $__product, 'column' ), 10, 2 ) ;

// Backend order related
add_filter( 'manage_edit-shop_order_columns', array( $__order, 'column_title' ) ) ;

add_action( 'manage_shop_order_posts_custom_column', array( $__order, 'column' ) ) ;

// Frontend product
add_action( 'woocommerce_product_meta_start', array( $__product, 'field_frontend' ), 10 ) ;

// Frontend order
add_action( 'woocommerce_order_item_meta_start', array( $__order, 'field_frontend' ), 10, 2 ) ;

add_action( 'woocommerce_order_details_after_order_table', array( $__order, 'tix_frontend' ), 10 ) ;

// Frontend order auto complete paid orders
add_action( 'woocommerce_thankyou', array( $__order, 'maybe_auto_complete' ), 20, 1 ) ;

// Frontend order adding hook
add_action( 'woocommerce_checkout_create_order', array( $__order, 'fillup' ), 10 ) ;

// REST order hooks
add_action( 'rest_api_init', array( $__rest, 'api_init' ) ) ;






