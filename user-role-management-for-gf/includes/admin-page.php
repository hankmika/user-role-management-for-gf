<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin page registration.
 */
function gfrm_register_menu() {
    if ( ! gfrm_user_can_manage_caps() ) {
        return;
    }

    add_menu_page(
        __( 'GF Role Manager', 'gfrm' ),
        __( 'GF Role Manager', 'gfrm' ),
        gfrm_manage_capability(),
        'gfrm-role-manager',
        'gfrm_render_admin_page',
        'dashicons-shield-alt',
        59
    );
}
add_action( 'admin_menu', 'gfrm_register_menu' );

/**
 * Handle form submission.
 */
function gfrm_handle_save() {
    if ( ! gfrm_user_can_manage_caps() ) {
        wp_die( esc_html__( 'You do not have permission to manage these settings.', 'gfrm' ) );
    }

    check_admin_referer( 'gfrm_save_caps' );

    $caps  = gfrm_caps();
    $roles = gfrm_get_assignable_roles();

    // Whitelist role/cap keys before processing.
    $submitted = [];
    $raw_caps  = isset( $_POST['gfrm_role_caps'] ) ? wp_unslash( $_POST['gfrm_role_caps'] ) : [];
    if ( is_array( $raw_caps ) ) {
        foreach ( $raw_caps as $role_key => $caps_payload ) {
            if ( ! isset( $roles[ $role_key ] ) || ! is_array( $caps_payload ) ) {
                continue;
            }
            foreach ( $caps_payload as $cap_key => $value ) {
                if ( isset( $caps[ $cap_key ] ) ) {
                    $submitted[ $role_key ][ $cap_key ] = $value;
                }
            }
        }
    }

    $store     = [];

    foreach ( $roles as $role_key => $data ) {
        $role_obj = get_role( $role_key );
        if ( ! $role_obj || ! $role_obj->has_cap( 'read' ) ) {
            continue;
        }

        $role_caps = [];
        foreach ( $caps as $cap => $label ) {
            $enabled            = ! empty( $submitted[ $role_key ][ $cap ] );
            $role_caps[ $cap ]  = $enabled ? 1 : 0;

            if ( $enabled ) {
                $role_obj->add_cap( $cap );
            } else {
                $role_obj->remove_cap( $cap );
            }
        }

        $store[ $role_key ] = $role_caps;
    }

    update_option( 'gfrm_role_caps', $store );

    wp_safe_redirect(
        add_query_arg(
            [ 'page' => 'gfrm-role-manager', 'updated' => '1' ],
            admin_url( 'admin.php' )
        )
    );
    exit;
}
add_action( 'admin_post_gfrm_save_caps', 'gfrm_handle_save' );

/**
 * Render admin management UI (delegates to view).
 */
function gfrm_render_admin_page() {
    if ( ! gfrm_user_can_manage_caps() ) {
        wp_die( esc_html__( 'You do not have permission to view this page.', 'gfrm' ) );
    }

    $caps  = gfrm_caps();
    $roles = gfrm_get_assignable_roles();
    $saved = get_option( 'gfrm_role_caps', [] );
    $saved = is_array( $saved ) ? $saved : [];

    include GFRM_PLUGIN_DIR . 'views/admin-page.php';
}
