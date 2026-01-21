# User Role Management for Gravity Forms

A WordPress plugin that adds and Admin-only UI to grant specific Gravity Forms permissions (view, edit, create, export, etc.) to existing roles that already have wp-admin access, while keeping administrators’ access intact and blocking non-admin users from managing these settings.

## Why this plugin exists

Gravity Forms ships with granular capabilities, but assigning them typically requires:
- General role editors (Members, User Role Editor, etc.) that expose every capability and make it easy to break roles.
- Code snippets in `functions.php` to add/remove `gravityforms_*` caps.
- Giving broad caps like `manage_options` or full Administrator to let someone manage forms.

This plugin narrows the problem: it only exposes Gravity Forms capabilities, hides roles without wp-admin access, and keeps the configuration behind a single custom capability so only trusted admins can change it.

## How it works

- Adds a management capability `gfrm_manage_caps` and grants it to Administrators.
- Registers an admin page under “GF Role Manager” for users with `gfrm_manage_caps`.
- Lets you toggle the following caps per role: `gravityforms_view_entries`, `gravityforms_edit_entries`, `gravityforms_delete_entries`, `gravityforms_export_entries`, `gravityforms_create_form`, `gravityforms_edit_forms`, `gravityforms_delete_forms`, `gravityforms_view_settings`.
- Saves choices to the `gfrm_role_caps` option and applies them to roles when settings are saved (also on activation).
- Prevents non-admin roles (no `read`/wp-admin access) from appearing to avoid granting caps where they cannot be used.

## Installation

1) Upload the plugin folder to `/wp-content/plugins/` or install via ZIP.  
2) Activate the plugin. Activation ensures Administrators get `gfrm_manage_caps` and applies any saved settings.  
3) In wp-admin, visit **GF Role Manager** (visible only to users with `gfrm_manage_caps`) to assign capabilities to eligible roles.  
4) Deactivation leaves Gravity Forms caps in place; uninstall removes the stored option and the custom manage capability.

## Technical notes

- Management gate: `gfrm_manage_caps` (override by defining `GFRM_MANAGE_CAP` before plugin load).
- Settings storage: option `gfrm_role_caps` keyed by role and capability. Sanitized before saving.
- Security: admin-post handler uses capability checks plus a nonce (`gfrm_save_caps`). Menu visibility aligns with the same capability.
- Cleanup: `uninstall.php` removes stored settings and plugin-added capabilities.
