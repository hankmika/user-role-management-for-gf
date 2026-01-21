<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Gravity Forms capabilities we manage.
 *
 * @return array<string,string>
 */
function gfrm_caps() {
    return [
        'gravityforms_view_entries'   => __( 'View entries', 'gfrm' ),
        'gravityforms_edit_entries'   => __( 'Edit entries', 'gfrm' ),
        'gravityforms_delete_entries' => __( 'Delete entries', 'gfrm' ),
        'gravityforms_export_entries' => __( 'Export entries', 'gfrm' ),
        'gravityforms_create_form'    => __( 'Create forms', 'gfrm' ),
        'gravityforms_edit_forms'     => __( 'Edit forms', 'gfrm' ),
        'gravityforms_delete_forms'   => __( 'Delete forms', 'gfrm' ),
        'gravityforms_view_settings'  => __( 'View settings', 'gfrm' ),
    ];
}

/**
 * Capability required to manage assignments.
 */
function gfrm_manage_capability() {
    return defined( 'GFRM_MANAGE_CAP' ) ? GFRM_MANAGE_CAP : 'gfrm_manage_caps';
}

/**
 * Determine whether the current user can manage assignments.
 */
function gfrm_user_can_manage_caps() {
    return current_user_can( gfrm_manage_capability() );
}

/**
 * Ensure administrators have the management capability.
 */
function gfrm_grant_manage_cap_to_admins() {
    $admin_role = get_role( 'administrator' );

    if ( $admin_role && ! $admin_role->has_cap( gfrm_manage_capability() ) ) {
        $admin_role->add_cap( gfrm_manage_capability() );
    }
}
add_action( 'admin_init', 'gfrm_grant_manage_cap_to_admins' );

/**
 * Return roles eligible for Gravity Forms permissions.
 * Excludes administrators and any role without wp-admin access (no "read").
 *
 * @return array<string,array> role key => role data
 */
function gfrm_get_assignable_roles() {
    global $wp_roles;

    if ( ! isset( $wp_roles ) ) {
        $wp_roles = new WP_Roles();
    }

    $roles = $wp_roles->roles;

    return array_filter(
        $roles,
        function( $data, $key ) {
            if ( $key === 'administrator' ) {
                return false;
            }

            return ! empty( $data['capabilities']['read'] );
        },
        ARRAY_FILTER_USE_BOTH
    );
}

/**
 * Apply saved capability assignments to roles.
 */
function gfrm_apply_saved_caps() {
    $saved = get_option( 'gfrm_role_caps', [] );
    $caps  = gfrm_caps();

    if ( empty( $saved ) || ! is_array( $saved ) ) {
        return;
    }

    foreach ( $saved as $role_key => $role_caps ) {
        if ( ! is_array( $role_caps ) ) {
            continue;
        }

        $role = get_role( $role_key );
        if ( ! $role || $role_key === 'administrator' || ! $role->has_cap( 'read' ) ) {
            continue;
        }

        foreach ( $caps as $cap => $label ) {
            if ( ! empty( $role_caps[ $cap ] ) ) {
                $role->add_cap( $cap );
            } else {
                $role->remove_cap( $cap );
            }
        }
    }
}
add_action( 'admin_init', 'gfrm_apply_saved_caps' );
