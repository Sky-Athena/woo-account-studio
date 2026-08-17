<?php
defined( 'ABSPATH' ) || exit;

/**
 * Small, bundled language packs for the customer hub.
 *
 * The plugin still follows normal WordPress gettext conventions.  These maps
 * provide reliable interface-language overrides when a store owner chooses a
 * language inside Account Studio, independently of the site's global locale.
 */
final class WCAS_Translation_Packs {
	private static $cache = array();

	public static function supported() {
		return array( 'ar', 'en', 'tr', 'fr', 'es', 'de', 'it' );
	}

	public static function labels() {
		return array(
			'auto' => __( 'Follow site language', 'woo-account-studio' ),
			'ar'   => __( 'Arabic', 'woo-account-studio' ),
			'en'   => __( 'English', 'woo-account-studio' ),
			'tr'   => __( 'Turkish', 'woo-account-studio' ),
			'fr'   => __( 'French', 'woo-account-studio' ),
			'es'   => __( 'Spanish', 'woo-account-studio' ),
			'de'   => __( 'German', 'woo-account-studio' ),
			'it'   => __( 'Italian', 'woo-account-studio' ),
		);
	}

	public static function from_locale( $locale ) {
		$locale = strtolower( str_replace( '_', '-', (string) $locale ) );
		foreach ( self::supported() as $language ) {
			if ( 0 === strpos( $locale, $language ) ) return $language;
		}
		return 'en';
	}

	public static function map( $language ) {
		if ( ! in_array( $language, self::supported(), true ) || in_array( $language, array( 'ar', 'en' ), true ) ) return array();
		if ( isset( self::$cache[ $language ] ) ) return self::$cache[ $language ];
		$file = WCAS_PATH . 'languages/' . $language . '.php';
		$map  = is_readable( $file ) ? require $file : array();
		self::$cache[ $language ] = is_array( $map ) ? $map : array();
		return self::$cache[ $language ];
	}
}
