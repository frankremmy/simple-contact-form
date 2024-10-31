<?php

// Create a settings page in the admin dashboard
function scf_add_settings_page() {
	add_options_page(
		__('Simple Contact Form Settings', 'simple-contact-form'),
		__('SCF Settings', 'simple-contact-form'),
		'manage_options',
		'scf-settings',
		'scf_render_settings_page'
	);
}
add_action('admin_menu', 'scf_add_settings_page');


// Render the settings page content
function scf_render_settings_page() {
    if (! current_user_can('manage_options')) {
        wp_die(__('You do not have permission to access this page.', 'simple-contact-form'));
    }

    // For Reset button
	if (isset($_POST['scf_reset_settings']) && check_admin_referer('scf_reset_settings_nonce')) {
		scf_reset_plugin_settings();
		add_settings_error('scf_messages', 'scf_message', __('Settings reset to default values.', 'simple-contact-form'), 'updated');
	}

	settings_errors('scf_messages');
	?>
    <div class="wrap">
        <h1><?php _e('Simple Contact Form Settings', 'simple-contact-form'); ?></h1>
        <form method="post" action="options.php">
			<?php
			settings_fields('scf_settings_group');
			do_settings_sections('scf-settings');
			submit_button();
			?>
        </form>
        <form method="post" action="">
		    <?php wp_nonce_field('scf_reset_settings_nonce'); ?>
            <input type="submit" name="scf_reset_settings" class="button button-secondary" value="<?php _e('Reset to Defaults', 'simple-contact-form'); ?>">
        </form>
    </div>
	<?php
}

function scf_register_email_settings() {
	// Register recipient email field
	register_setting('scf_settings_group', 'scf_recipient_email');
	// Register email subject field
	register_setting('scf_settings_group', 'scf_email_subject');
	// Register email body field
	register_setting('scf_settings_group', 'scf_email_body');

	add_settings_section(
		'scf_email_settings_section',
		__('Email Notification Settings', 'simple-contact-form'),
		'scf_email_settings_section_callback',
		'scf-settings'
	);

	add_settings_field(
		'scf_recipient_email',
		__('Recipient Email', 'simple-contact-form'),
		'scf_recipient_email_callback',
		'scf-settings',
		'scf_email_settings_section'
	);

	add_settings_field(
		'scf_email_subject',
		__('Email Subject', 'simple-contact-form'),
		'scf_email_subject_callback',
		'scf-settings',
		'scf_email_settings_section'
	);

	add_settings_field(
		'scf_email_body',
		__('Email Body Template', 'simple-contact-form'),
		'scf_email_body_callback',
		'scf-settings',
		'scf_email_settings_section'
	);
}
add_action('admin_init', 'scf_register_email_settings');

// Section callback
function scf_email_settings_section_callback() {
	echo '<p>' . __('Customize the email notifications sent when a form is submitted.', 'simple-contact-form') . '</p>';
}

// Callback for recipient email
function scf_recipient_email_callback() {
	$email = get_option('scf_recipient_email', get_option('admin_email'));
	echo '<input type="email" name="scf_recipient_email" value="' . esc_attr($email) . '" />';
}

// Callback for email subject
function scf_email_subject_callback() {
	$subject = get_option('scf_email_subject', 'New Contact Form Submission');
	echo '<input type="text" name="scf_email_subject" value="' . esc_attr($subject) . '" />';
}

// Callback for email body
function scf_email_body_callback() {
	$body = get_option('scf_email_body', "You have received a new message:\n\nName: {name}\nEmail: {email}\nMessage:\n{message}\n");
	echo '<textarea name="scf_email_body" rows="5" cols="50">' . esc_textarea($body) . '</textarea>';
}

// Define the reset function
function scf_reset_plugin_settings() {
	delete_option('scf_recipient_email');
	delete_option('scf_email_subject');
	delete_option('scf_email_body');
}