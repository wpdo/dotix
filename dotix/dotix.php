<?php
/**
 * Plugin Name:       Dotix
 * Plugin URI:        https://wordpress.org/support/plugin/dotix/
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

require_once DOTIX_DIR . 'core.cls.php' ;
require_once DOTIX_DIR . 'conf.cls.php' ;
require_once DOTIX_DIR . 'gui.cls.php' ;
require_once DOTIX_DIR . 'order.cls.php' ;
require_once DOTIX_DIR . 'product.cls.php' ;
require_once DOTIX_DIR . 'rest.cls.php' ;
require_once DOTIX_DIR . 'vendor.cls.php' ;

Dotix::get_instance()->init() ;





