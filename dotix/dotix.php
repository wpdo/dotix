<?php
/**
 * Plugin Name:       Dotix
 * Description:       Ticket/credit system for WooCommerce
 * Version:           1.2.3
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

define( 'DOTIX_V', '1.2.3' ) ;

! defined( 'DOTIX_TAG' ) && define( 'DOTIX_TAG', 'dotix_credit' ) ;
! defined( 'DOTIX_DIR' ) && define( 'DOTIX_DIR', dirname( __FILE__ ) . '/' ) ;// Full absolute path '/usr/local/***/wp-content/plugins/dotix/' or MU
! defined( 'DOTIX_PLUGIN_URL' ) && define( 'DOTIX_PLUGIN_URL', plugin_dir_url( __FILE__ ) ) ;// Full URL path '//example.com/wp-content/plugins/dotix/'

require_once DOTIX_DIR . 'autoload.php';

\dotix\Core::get_instance();
