<?php
/**
 * Auto registration
 *
 * @since      	1.0
 */
defined( 'WPINC' ) || exit;

if ( ! function_exists( '_dotix_autoload' ) ) {
	function _dotix_autoload( $cls )
	{
		if ( strpos( $cls, 'dotix' ) !== 0 ) {
			return;
		}

		$file = explode( '\\', $cls );
		array_shift( $file );
		$file = implode( '/', $file );
		$file = str_replace( '_', '-', strtolower( $file ) );

		if ( strpos( $file, 'lib/' ) === 0 || strpos( $file, 'cli/' ) === 0 || strpos( $file, 'thirdparty/' ) === 0 ) {
			$file = DOTIX_DIR . $file . '.cls.php';
		}
		else {
			$file = DOTIX_DIR . 'src/' . $file . '.cls.php';
		}

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}

spl_autoload_register( '_dotix_autoload' );

