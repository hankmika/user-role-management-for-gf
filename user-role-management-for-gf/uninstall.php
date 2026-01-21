<?php
/**
 * Cleanup on uninstall.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

require_once __DIR__ . '/includes/helpers.php';

// Remove stored settings.
delete_option( 'gfrm_role_caps' );
delete_site_option( 'gfrm_role_caps' );

// Remove granted capabilities from non-administrator roles.
$caps  = gfrm_caps();
$roles = gfrm_get_assignable_roles();

foreach ( $roles as $role_key => $data ) {
    $role = get_role( $role_key );
    if ( ! $role ) {
        continue;
    }

    foreach ( $caps as $cap => $label ) {
        $role->remove_cap( $cap );
    }

    $role->remove_cap( gfrm_manage_capability() );
}

// Administrators are excluded from gfrm_get_assignable_roles but we still grant them the manage cap. Keep GF caps intact.
$admin_role = get_role( 'administrator' );
if ( $admin_role ) {
    $admin_role->remove_cap( gfrm_manage_capability() );
}
