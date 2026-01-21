<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap">
    <h1><?php esc_html_e( 'Gravity Forms Role Access', 'gfrm' ); ?></h1>
    <?php if ( isset( $_GET['updated'] ) ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e( 'Permissions updated.', 'gfrm' ); ?></p>
        </div>
    <?php endif; ?>
    <p><?php esc_html_e( 'Only Administrators and Super Admins can manage these permissions. Administrator access to Gravity Forms cannot be restricted.', 'gfrm' ); ?></p>
    <p><?php esc_html_e( 'Roles without wp-admin access are hidden to prevent granting capabilities where they cannot be used.', 'gfrm' ); ?></p>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( 'gfrm_save_caps' ); ?>
        <input type="hidden" name="action" value="gfrm_save_caps" />

        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Role', 'gfrm' ); ?></th>
                    <?php foreach ( $caps as $cap => $label ) : ?>
                        <th><?php echo esc_html( $label ); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $roles as $role_key => $role_data ) :
                    $role_obj = get_role( $role_key );
                    if ( ! $role_obj ) {
                        continue;
                    }
                    ?>
                    <tr>
                        <td><strong><?php echo esc_html( $role_data['name'] ); ?></strong></td>
                        <?php foreach ( $caps as $cap => $label ) :
                            $checked = isset( $saved[ $role_key ][ $cap ] )
                                ? (bool) $saved[ $role_key ][ $cap ]
                                : $role_obj->has_cap( $cap );
                            ?>
                            <td>
                                <label class="screen-reader-text" for="<?php echo esc_attr( $role_key . '_' . $cap ); ?>">
                                    <?php echo esc_html( $label ); ?>
                                </label>
                                <input type="checkbox"
                                    id="<?php echo esc_attr( $role_key . '_' . $cap ); ?>"
                                    name="gfrm_role_caps[<?php echo esc_attr( $role_key ); ?>][<?php echo esc_attr( $cap ); ?>]"
                                    value="1"
                                    <?php checked( $checked ); ?> />
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php submit_button( __( 'Save Permissions', 'gfrm' ) ); ?>
    </form>
</div>
