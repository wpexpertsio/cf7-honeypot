<?php

/**
 * 
 * Check if CF7 is installed and activated.
 * 		Deliver a message to install CF7 if not.
 * 
 */
add_action( 'admin_init', 'honeypot4cf7_has_parent_plugin' );
function honeypot4cf7_has_parent_plugin() {
	// Get Options
	if ($honeypot4cf7_config = get_option('honeypot4cf7_config')) {
		$honeypot4cf7_config = $honeypot4cf7_config;
	} else {
		$honeypot4cf7_config = honeypot4cf7_restore_config();
	}

	if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( HONEYPOT4CF7_DEP_PLUGIN ) && empty( $honeypot4cf7_config['honeypot_cf7_req_msg_dismissed'])) {
		add_action( 'admin_notices', 'honeypot4cf7_nocf7_notice' );
	}

	// This resets dismissed notice
	if (is_plugin_active( HONEYPOT4CF7_DEP_PLUGIN ) && $honeypot4cf7_config['honeypot_cf7_req_msg_dismissed'] == 1) {
		$honeypot4cf7_config['honeypot_cf7_req_msg_dismissed'] = 0;
		update_option( 'honeypot4cf7_config', $honeypot4cf7_config );
	}
}

function honeypot4cf7_nocf7_notice() { ?>
	<div class="notice error honeypot4cf7-notice is-dismissible">
		<p>
			<?php printf(
				__('%s must be installed and activated for the CF7 Honeypot plugin to work', 'contact-form-7-honeypot'),
				'<a href="'.admin_url('plugin-install.php?tab=search&s=contact+form+7').'">Contact Form 7</a>'
			); ?>
		</p>
	</div>
	<?php
}


add_action( 'wp_ajax_honeypot4cf7_dismiss_notice', 'honeypot4cf7_dismiss_notice' );

function honeypot4cf7_dismiss_notice() {
	$honeypot4cf7_config = get_option('honeypot4cf7_config');
	$honeypot4cf7_config['honeypot_cf7_req_msg_dismissed'] = 1;
	update_option( 'honeypot4cf7_config', $honeypot4cf7_config );
}

/**
 *
 * Delete configuration in WP Options table
 * 		This cleans up on uninstall.
 * 
 */
register_uninstall_hook(HONEYPOT4CF7_PLUGIN, 'honeypot4cf7_uninstall');
function honeypot4cf7_uninstall() {
	delete_option('honeypot4cf7_config');
}


/* ***********
*
* Initialize plugin on activation
*
*********** */
function honeypot4cf7_on_activation() {
    if ( ! current_user_can( 'activate_plugins' ) )
        return;
    
    $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
    check_admin_referer( "activate-plugin_{$plugin}" );

	// Initialize option values
	return honeypot4cf7_restore_config('init');
}

register_activation_hook(HONEYPOT4CF7_PLUGIN, 'honeypot4cf7_on_activation');


add_filter( 'plugin_action_links_'.HONEYPOT4CF7_PLUGIN_BASENAME, 'honeypot4cf7_settings_link' );
function honeypot4cf7_settings_link( $links ) {
	$url = esc_url( add_query_arg(
		'page',
		'honeypot4cf7',
		get_admin_url() . 'admin.php'
	) );
	
	$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
	
	array_push($links,$settings_link);

	return $links;
}


/* ***********
*
* Restore built-in defaults, optionally overwriting existing values
*
*********** */
function honeypot4cf7_restore_config($type=false) {
	// Make sure the current user can manage options
	if (!current_user_can('manage_options')) {
		return;
	}

	$honeypot4cf7_config = get_option('honeypot4cf7_config');
	
	if (empty($honeypot4cf7_config) || 'reset' == $type) {

		$honeypot4cf7_config_defaults = array(
			'store_honeypot'					=> 0,
			'placeholder'						=> '',
			'accessibility_message'				=> '',
			'w3c_valid_autocomplete'			=> array('false'),
			'move_inline_css'					=> array('false'),
			'nomessage'							=> array('false'),
			'honeypot_count'					=> (!empty($honeypot4cf7_config['honeypot_count'])) ? $honeypot4cf7_config['honeypot_count'] : 0,
			'honeypot_install_date'				=> (!empty($honeypot4cf7_config['honeypot_install_date'])) ? $honeypot4cf7_config['honeypot_install_date'] : time(),
			'honeypot_cf7_req_msg_dismissed'	=> 0,
			'honeypot4cf7_version'				=> HONEYPOT4CF7_VERSION
		);
	
		$honeypot4cf7_config = $honeypot4cf7_config_defaults;
	}

	update_option( 'honeypot4cf7_config', $honeypot4cf7_config );

	return $honeypot4cf7_config;
}


/* ***********
*
* Setup the Options page
*
*********** */

add_action('admin_menu', 'honeypot4cf7_admin_menu');
function honeypot4cf7_admin_menu() {
	add_submenu_page('wpcf7', 'Honeypot for Conctact Form 7', 'Honeypot', 'manage_options', 'honeypot4cf7', 'honeypot4cf7_admin_page');
}

function honeypot4cf7_admin_page() {
	// Reset Values
	if (!empty($_POST['clear'])) {
		honeypot4cf7_restore_config('reset');
		echo '<div id="message" class="updated"><p>'.esc_html(__('The settings have been reset to their defaults.','contact-form-7-honeypot')).'</p></div>';
	}

	// Get Options
	$honeypot4cf7_config = $honeypot4cf7_config = get_option('honeypot4cf7_config');

	// Save Values
	if (!empty($_POST['save'])) {
		
		// Check nonce and user ability
		if ( !empty($_POST) && check_admin_referer('honeypot4cf7-submit', 'honeypot4cf7_nonce') && current_user_can('manage_options') ) {

			// Validate & Sanitize
			$honeypot4cf7_config_update = array(
				'store_honeypot' 			=> (isset($_POST['honeypot4cf7_store'])) ? $_POST['honeypot4cf7_store'] : 0,
				'placeholder' 				=> (isset($_POST['honeypot4cf7_placeholder'])) ? $_POST['honeypot4cf7_placeholder'] : '',
				'accessibility_message'		=> (isset($_POST['honeypot4cf7_accessibility_message'])) ? $_POST['honeypot4cf7_accessibility_message'] : '',
				'w3c_valid_autocomplete'	=> (isset($_POST['honeypot4cf7_w3c_valid_autocomplete'])) ? $_POST['honeypot4cf7_w3c_valid_autocomplete'] : array('false'),
				'move_inline_css'			=> (isset($_POST['honeypot4cf7_move_inline_css'])) ? $_POST['honeypot4cf7_move_inline_css'] : array('false'),
				'nomessage'					=> (isset($_POST['honeypot4cf7_nomessage'])) ? $_POST['honeypot4cf7_nomessage'] : array('false'),
			);
		
			$honeypot4cf7_config = array_replace($honeypot4cf7_config, $honeypot4cf7_config_update);

			update_option( 'honeypot4cf7_config', $honeypot4cf7_config );

			echo '<div id="message" class="updated"><p>'.esc_html(__('The changes have been saved.','contact-form-7-honeypot')).'</p></div>';
		}
	}
	?>
		
	<div class="wrap" class="honeypot4cf7-admin" id="honeypot4cf7-admin-page">
		<h1 class="honeypot4cf7-admin__title"><?php esc_html_e(__('Honeypot for Contact Form 7'),'contact-form-7-honeypot'); ?> <span><?php esc_html_e('v'.HONEYPOT4CF7_VERSION); ?></h1>
		<div class="honeypot4cf7-admin__primary">
			<div class="honeypot4cf7-admin__box">
				<form action="" method="post" id="honeypot4cf7_options_form" name="honeypot4cf7_options_form">
					<?php wp_nonce_field('honeypot4cf7-submit', 'honeypot4cf7_nonce'); ?>
					<a href="https://wordpress.org/support/plugin/contact-form-7-honeypot/" target="_blank" class="honeypot4cf7_admin__support-link">
						<span class="dashicons dashicons-editor-help"></span>
						<?php esc_html_e( __( 'Get Support', 'contact-form-7-honeypot' )); ?>
					</a>
					<h3><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e('Honeypot Settings'); ?></h3>
					<table class="form-table">
						<tbody>
							<tr valign="top">
								<th><label for="honeypot4cf7__store-honeypot"><?php esc_html_e( __( 'Store Honeypot Value', 'contact-form-7-honeypot' )); ?></label></th>
								<td>
									<input type="checkbox" name="honeypot4cf7_store" id="honeypot4cf7__store-honeypot" value="1" <?php checked($honeypot4cf7_config['store_honeypot'],1); ?>>
								</td>
							</tr>
							<tr valign="top">
								<td class="description" colspan="2"><?php printf(__( '(Recommended) By default the Honeypot field is not stored with other fields in form-saving plugins like %1$s. However, saving the field can be useful to see what spam bots are leaving behind to help you improve your spam stopping superpowers. If you\'d like to store the value of the field, simply check this box (and install %1$s).', 'contact-form-7-honeypot' ), '<a href="https://wordpress.org/plugins/flamingo/" target="_blank">Flamingo</a>'); ?></td>
							</tr>

							<tr valign="top">
								<th><label for="honeypot4cf7__placeholder"><?php esc_html_e( __( 'Global Placeholder', 'contact-form-7-honeypot' )); ?></label></th>
								<td>
									<input type="text" class="regular-text" name="honeypot4cf7_placeholder" id="honeypot4cf7__placeholder" value="<?php echo sanitize_text_field($honeypot4cf7_config['placeholder']); ?>">
								</td>
							</tr>
							<tr valign="top">
								<td class="description" colspan="2"><?php esc_html_e( __( 'If using placeholders on other fields, this can help honeypot mimic a "real" field. This can be overridden in the contact form. If you\'re unsure, leave blank.', 'contact-form-7-honeypot' ) ); ?></td>
							</tr>

							<tr valign="top">
								<th><label for="honeypot4cf7__accessibility_message"><?php esc_html_e( __( 'Accessibility Message', 'contact-form-7-honeypot' )); ?></label></th>
								<td>
									<input type="text" class="regular-text" name="honeypot4cf7_accessibility_message" id="honeypot4cf7__accessibility_message" value="<?php echo sanitize_text_field($honeypot4cf7_config['accessibility_message']); ?>">
								</td>
							</tr>
							<tr valign="top">
								<td class="description" colspan="2"><?php printf(__( 'You can customize the (hidden) accessibility message, or just leave it the default value: %1$s', 'contact-form-7-honeypot' ), '<em>'.__('Please leave this field empty.','contact-form-7-honeypot').'</em>'); ?></td>
							</tr>

							<tr valign="top">
								<th><label for="honeypot4cf7__w3c-valid-autocomplete"><?php esc_html_e( __( 'Use Standard Autocomplete Value', 'contact-form-7-honeypot' )); ?></label></th>
								<td>
									<input type="checkbox" name="honeypot4cf7_w3c_valid_autocomplete[]" id="honeypot4cf7__w3c-valid-autocomplete" value="true" <?php checked($honeypot4cf7_config['w3c_valid_autocomplete'][0],'true'); ?>>
								</td>
							</tr>
							<tr valign="top">
								<td class="description" colspan="2"><?php esc_html_e(__( 'To assure the honeypot isn\'t auto-completed by a browser, we add an atypical "autocomplete" attribute value. If you have any problems with this, you can switch it to the more standard (but less effective) "off" value. If you\'re unsure, leave this unchecked.', 'contact-form-7-honeypot' )); ?></td>
							</tr>

							<tr valign="top">
								<th><label for="honeypot4cf7__move_inline_css"><?php esc_html_e( __( 'Move Inline CSS', 'contact-form-7-honeypot' )); ?></label></th>
								<td>
									<input type="checkbox" name="honeypot4cf7_move_inline_css[]" id="honeypot4cf7__move_inline_css" value="true" <?php checked($honeypot4cf7_config['move_inline_css'][0],'true'); ?>>
								</td>
							</tr>
							<tr valign="top">
								<td class="description" colspan="2"><?php esc_html_e(__( 'By default Honeypot uses inline CSS on the honeypot field to hide it. Checking this box moves that CSS to the footer of the page. It may help confuse bots.', 'contact-form-7-honeypot' )); ?></td>
							</tr>

							<tr valign="top">
								<th><label for="honeypot4cf7__nomessage"><?php esc_html_e( __( 'Disable Accessibility Label', 'contact-form-7-honeypot' )); ?></label></th>
								<td>
									<input type="checkbox" name="honeypot4cf7_nomessage[]" id="honeypot4cf7__nomessage" value="true" <?php checked($honeypot4cf7_config['nomessage'][0],'true'); ?>>
								</td>
							</tr>
							<tr valign="top">
								<td class="description" colspan="2"><?php esc_html_e(__( 'If checked, the accessibility label will not be generated. This is not recommended, but may improve spam blocking. If you\'re unsure, leave this unchecked.', 'contact-form-7-honeypot' )); ?></td>
							</tr>
						</tbody>
					</table>
					<p class="submit">
						<input name="save" id="save" class="button button-primary" value="<?php esc_html_e( __( 'Save', 'contact-form-7-honeypot' )); ?>" type="submit" />
						<input name="clear" id="reset" class="button" value="<?php esc_html_e( __( 'Reset to Defaults', 'contact-form-7-honeypot' )); ?>" type="submit" />
						
					</p>
				</form>
			</div>
			<div class="honeypot4cf7__banner-ad honeypot4cf7__banner-ad--1a">
				<a target="_blank" href="https://shareasale.com/r.cfm?b=1713710&amp;u=2748065&amp;m=97231&amp;urllink=&amp;afftrack="><img src="<?php echo HONEYPOT4CF7_PLUGIN_DIR_URL; ?>/includes/images/banners/semrush-1_720x90.gif" border="0" alt="Tools for Any SEO Challenge" /></a>
			</div>
			<div class="honeypot4cf7__banner-ad honeypot4cf7__banner-ad--1b">
				<a target="_blank" href="https://shareasale.com/r.cfm?b=1714372&amp;u=2748065&amp;m=97231&amp;urllink=&amp;afftrack="><img src="<?php echo HONEYPOT4CF7_PLUGIN_DIR_URL; ?>/includes/images/banners/semrush-1_580x400.gif" border="0" /></a>
			</div>
			<div class="honeypot4cf7-admin__box honeypot4cf7-admin__box--count-message">
				<p>
					<span class="dashicons dashicons-chart-area"></span> <?php printf(
						__('Honeypot has stopped %1$s spam submissions since %2$s', 'contact-form-7-honeypot'),
						'<strong>'.$honeypot4cf7_config['honeypot_count'].'</strong>', 
						date(get_option('date_format'), $honeypot4cf7_config['honeypot_install_date'])
					); ?>
				</p>
			</div>
		</div>

		<div class="honeypot4cf7-admin__secondary">
			<div class="honeypot4cf7-admin__box honeypot4cf7-admin__box--coffee">
				<p class="honeypot4cf7-admin__coffee-message">
					<?php esc_html_e(__( 'Do you like Honeypot for CF7? Consider showing your support:', 'contact-form-7-honeypot' )); ?><br>
					<a href="http://www.nocean.ca/buy-us-a-coffee/" target="_blank" class="button button-primary"><strong>
						<span class="dashicons dashicons-coffee"></span> <?php esc_html_e(__('Buy Us a Coffee','contact-form-7-honeypot')); ?>
					</strong></a>
				</p>
			</div>
			<div class="honeypot4cf7-admin__box">
				<p class="honeypot4cf7__banner-ad-message">
					<?php printf(
						__( 'We use %s and find it incredibly valuable. If you choose to use them too (even for free), you are helping continued development and support of this plugin. Thank you!', 'contact-form-7-honeypot' ),
						'<a href="https://shareasale.com/r.cfm?b=1537039&u=2748065&m=97231&urllink=&afftrack=0" target="_blank">Semrush</a>'
					); ?>
				</p>
				<div class="honeypot4cf7__banner-ad honeypot4cf7__banner-ad--2">
					<a target="_blank" href="https://shareasale.com/r.cfm?b=1550765&amp;u=2748065&amp;m=97231&amp;urllink=&amp;afftrack="><img src="<?php echo HONEYPOT4CF7_PLUGIN_DIR_URL; ?>/includes/images/banners/semrush-2_300x250.png" border="0" alt="position tracking" /></a>
				</div>
			</div>

			<div class="honeypot4cf7-admin__box honeypot4cf7-admin__rate-us">
				<div class="honeypot4cf7-admin__stars"><a target="_blank" href="https://wordpress.org/support/plugin/contact-form-7-honeypot/reviews/?filter=5#new-post"><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span></a></div>
				<?php printf(__('Please rate us on %1$s. Thanks!!','contact-form-7-honeypot'),'<a target="_blank" href="https://wordpress.org/support/plugin/contact-form-7-honeypot/reviews/?filter=5#new-post">wordpress.org</a>'); ?>
			</div>
		</div>
	</div>
	
<?php
}

/* ***********
*
* Add admin page CSS
*
*********** */
function honeypot4cf7_admin_enqueues($hook) {
	wp_enqueue_script( 'honeypot4cf7-admin-js', plugins_url( 'includes/js/notice-update.js', HONEYPOT4CF7_PLUGIN ), array( 'jquery' ), HONEYPOT4CF7_VERSION, true  );
	if( strpos($hook, 'honeypot4cf7') !== false ) {
		 wp_enqueue_style( 'honeypot4cf7-admin-css', plugins_url( 'includes/css/styles.css', HONEYPOT4CF7_PLUGIN), array(), HONEYPOT4CF7_VERSION, 'all');
	}
}
add_action( 'admin_enqueue_scripts', 'honeypot4cf7_admin_enqueues' );



