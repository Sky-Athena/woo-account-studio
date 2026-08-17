<?php
/**
 * Uninstall routine — removes every option the plugin created.
 *
 * @package WooAccountStudio
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

$wcas_option = 'wcas_settings';

if ( is_multisite() ) {
	$wcas_sites = get_sites( array( 'fields' => 'ids', 'number' => 0 ) );

	foreach ( $wcas_sites as $wcas_site_id ) {
		switch_to_blog( $wcas_site_id );
		delete_option( $wcas_option );
		restore_current_blog();
	}
} else {
	delete_option( $wcas_option );
}
