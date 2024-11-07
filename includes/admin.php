<?php
/**
 * Admin functions for Honeypot for Contact Form 7
 *
 * @package Honeypot for Contact Form 7
 */

defined( 'ABSPATH' ) || exit;

/**
 * 
 * Check if CF7 is installed and activated.
 * 		Deliver a message to install CF7 if not.
 * 
 */
add_action( 'admin_init', 'honeypot4cf7_init' );
function honeypot4cf7_init() {
	// Get Options
	$honeypot4cf7_config = honeypot4cf7_get_config();

	// CF7 is not enabled
	if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( HONEYPOT4CF7_DEP_PLUGIN ) && empty( $honeypot4cf7_config['honeypot_cf7_req_msg_dismissed'] ) ) {
		add_action( 'admin_notices', 'honeypot4cf7_nocf7_notice' );
	}

	// CF7 is enabled, but it is OOOOOLD. Display message and deactivate Honeypot.
	if ( is_admin() && current_user_can( 'activate_plugins' ) && is_plugin_active( HONEYPOT4CF7_DEP_PLUGIN ) && defined('WPCF7_VERSION') && version_compare(WPCF7_VERSION, '3.0', '<' ) ) {
		add_action( 'admin_notices', 'honeypot4cf7_oldcf7_notice' );
		deactivate_plugins( HONEYPOT4CF7_PLUGIN_BASENAME );
	}

	// This resets dismissed notice
	if ( is_plugin_active( HONEYPOT4CF7_DEP_PLUGIN ) && $honeypot4cf7_config['honeypot_cf7_req_msg_dismissed'] === 1 ) {
		$honeypot4cf7_config['honeypot_cf7_req_msg_dismissed'] = 0;
		update_option( 'honeypot4cf7_config', $honeypot4cf7_config );
	}
}

function honeypot4cf7_nocf7_notice() { 
	?>
	<div class="notice error honeypot4cf7-notice is-dismissible">
		<p>
			<?php 
			printf(
				/* translators: %s: Link to Contact Form 7 plugin page. */
				__('%s must be installed and activated for the CF7 Honeypot plugin to work', 'contact-form-7-honeypot'),
				'<a href="'.admin_url('plugin-install.php?tab=search&s=contact+form+7').'">'.__('Contact Form 7','contact-form-7-honeypot').'</a>'
			); 
			?>
		</p>
	</div>
	<?php
}

function honeypot4cf7_oldcf7_notice() { 
	?>
	<div class="notice error">
		<p>
			<?php 
			printf(
				/* translators: %s: Link to Contact Form 7 plugin page. */
				__('The version of %s that is installed will not work with this version of Honeypot for CF7.', 'contact-form-7-honeypot'),
				'<a href="'.admin_url('plugin-install.php?tab=search&s=contact+form+7').'">'.__('Contact Form 7','contact-form-7-honeypot').'</a>'
			); 
			?>
		</p>
	</div>
	<?php
}


add_action( 'wp_ajax_honeypot4cf7_dismiss_notice', 'honeypot4cf7_dismiss_notice' );
function honeypot4cf7_dismiss_notice() {
	$honeypot4cf7_config = honeypot4cf7_get_config();
	$honeypot4cf7_config['honeypot_cf7_req_msg_dismissed'] = 1;
	update_option( 'honeypot4cf7_config', $honeypot4cf7_config );
}

/**
 *
 * Delete configuration in WP Options table
 * 		This cleans up on uninstall.
 * 
 */
register_uninstall_hook( HONEYPOT4CF7_PLUGIN, 'honeypot4cf7_uninstall' );
function honeypot4cf7_uninstall() {
	delete_option( 'honeypot4cf7_config' );
}


/* ***********
*
* Initialize plugin on activation
*
*********** */
function honeypot4cf7_on_activation() {
	// Initialize option values
	return honeypot4cf7_get_config();
}

register_activation_hook( HONEYPOT4CF7_PLUGIN, 'honeypot4cf7_on_activation' );


add_filter( 'plugin_action_links_'.HONEYPOT4CF7_PLUGIN_BASENAME, 'honeypot4cf7_settings_link' );
function honeypot4cf7_settings_link( $links ) {
	$url = esc_url( add_query_arg(
		'page',
		'honeypot4cf7',
		get_admin_url() . 'admin.php'
	) );
	
	$settings_link = "<a href='$url'>" . __( 'Settings', 'contact-form-7-honeypot' ) . '</a>';
	
	array_push( $links, $settings_link );

	return $links;
}


/* ***********
*
* Set / Get / Restore Config
*
*********** */
function honeypot4cf7_get_config( $context = false ) {

	$honeypot4cf7_config = get_option( 'honeypot4cf7_config' );	

	$honeypot4cf7_config = array(
		'store_honeypot'					=> ( 'reset' == $context || empty( $honeypot4cf7_config['store_honeypot'] ) ) ?  0 : $honeypot4cf7_config['store_honeypot'],
		'placeholder'						=> ( 'reset' == $context || empty( $honeypot4cf7_config['placeholder'] ) ) ?  '' : $honeypot4cf7_config['placeholder'],
		'accessibility_message'				=> ( 'reset' == $context || empty( $honeypot4cf7_config['accessibility_message'] ) ) ?  '' : $honeypot4cf7_config['accessibility_message'],
		'w3c_valid_autocomplete'			=> ( 'reset' == $context || empty( $honeypot4cf7_config['w3c_valid_autocomplete'] ) ) ?  array( 'false' ) : $honeypot4cf7_config['w3c_valid_autocomplete'],
		'move_inline_css'					=> ( 'reset' == $context || empty( $honeypot4cf7_config['move_inline_css'] ) ) ?  array( 'false' ) : $honeypot4cf7_config['move_inline_css'],
		'nomessage'							=> ( 'reset' == $context || empty( $honeypot4cf7_config['nomessage'] ) ) ?  array( 'false' ) : $honeypot4cf7_config['nomessage'],
		'timecheck_enabled'					=> ( 'reset' == $context || empty( $honeypot4cf7_config['timecheck_enabled'] ) ) ?  array( 'false' ) : $honeypot4cf7_config['timecheck_enabled'],
		'timecheck_value'					=> ( 'reset' == $context || empty( $honeypot4cf7_config['timecheck_value'] ) ) ?  4 : $honeypot4cf7_config['timecheck_value'],
		'honeypot_count'					=> ( empty( $honeypot4cf7_config['honeypot_count'] ) ) ? 0 : $honeypot4cf7_config['honeypot_count'],
		'honeypot_install_date'				=> ( empty( $honeypot4cf7_config['honeypot_install_date'] ) ) ? time() : $honeypot4cf7_config['honeypot_install_date'],
		'honeypot_cf7_req_msg_dismissed'	=> ( 'reset' == $context || empty( $honeypot4cf7_config['honeypot_cf7_req_msg_dismissed'] ) ) ? 0 : $honeypot4cf7_config['honeypot_cf7_req_msg_dismissed'],
		'honeypot4cf7_version'				=> HONEYPOT4CF7_VERSION,
	);
	
	update_option( 'honeypot4cf7_config', $honeypot4cf7_config );

	return $honeypot4cf7_config;
}


/* ***********
*
* Setup the Options page
*
*********** */

add_action( 'admin_menu', 'honeypot4cf7_admin_menu' );
function honeypot4cf7_admin_menu() {
	add_submenu_page(
		'wpcf7', 
		__('Honeypot for Contact Form 7', 'contact-form-7-honeypot'),
		__('Honeypot', 'contact-form-7-honeypot'),
		'manage_options','honeypot4cf7',
		'honeypot4cf7_admin_page'
	);
}

function honeypot4cf7_admin_page() {
    $honeypot4cf7_tabs = array(
        array(
            'title' => __( 'General', 'contact-form-7-honeypot' ),
            'slug'  => 'general',
        ),
        array(
            'title' => __( 'Forms', 'contact-form-7-honeypot' ),
            'slug'  => 'all-forms',
        ),
    );

    $current_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';
	?>
		
	<div class="wrap" class="honeypot4cf7-admin" id="honeypot4cf7-admin-page">

        <h1 class="honeypot4cf7-admin__title">
			<?php esc_html_e( 'Honeypot for Contact Form 7', 'contact-form-7-honeypot' ); ?> <span><?php echo esc_html( 'v' . HONEYPOT4CF7_VERSION ); ?>
        </h1>

        <h2 class="nav-tab-wrapper">
            <?php foreach ( $honeypot4cf7_tabs as $tab ) : ?>
                <?php
                $tab_url = add_query_arg(
                    array(
                        'page' => 'honeypot4cf7',
                        'tab'  => $tab['slug'],
                    ),
                    admin_url( 'admin.php' )
                );
                $active = ( $current_tab === $tab['slug'] ) ? ' nav-tab-active' : '';
                ?>
                <a href="<?php echo esc_attr( $tab_url ); ?>" class="nav-tab <?php echo esc_attr( $active ); ?>"><?php echo esc_html( $tab['title'] ); ?></a>
            <?php endforeach; ?>
        </h2>

        <?php do_action( 'honeypot4cf7_tab_' . $current_tab ); ?>

        <?php echo honeypot4cf7_side_content(); ?>

	</div>
	
<?php
}

add_action( 'honeypot4cf7_tab_general', 'honeypot4cf7_general_tab_content' );
function honeypot4cf7_general_tab_content() {
	// Reset Values
	if ( ! empty( $_POST['clear'] ) && check_admin_referer( 'honeypot4cf7-submit', 'honeypot4cf7_nonce' ) && current_user_can( 'manage_options' ) ) {
		$honeypot4cf7_config = honeypot4cf7_get_config( 'reset' );
		echo '<div id="message" class="updated"><p>'.esc_html( __('The settings have been reset to their defaults.', 'contact-form-7-honeypot' ) ).'</p></div>';

	} elseif ( ! empty( $_POST['save'] ) && check_admin_referer( 'honeypot4cf7-submit', 'honeypot4cf7_nonce' ) && current_user_can( 'manage_options' ) ) {
		$honeypot4cf7_config = honeypot4cf7_get_config();

		// Validate & Sanitize
		$honeypot4cf7_config_update = array(
			'store_honeypot' 			=> ( isset( $_POST['honeypot4cf7_store'] ) ) ? $_POST['honeypot4cf7_store'] : 0,
			'placeholder' 				=> ( isset( $_POST['honeypot4cf7_placeholder'] ) ) ? $_POST['honeypot4cf7_placeholder'] : '',
			'accessibility_message'		=> ( isset( $_POST['honeypot4cf7_accessibility_message'] ) ) ? $_POST['honeypot4cf7_accessibility_message'] : '',
			'w3c_valid_autocomplete'	=> ( isset( $_POST['honeypot4cf7_w3c_valid_autocomplete'] ) ) ? $_POST['honeypot4cf7_w3c_valid_autocomplete'] : array( 'false' ),
			'move_inline_css'			=> ( isset( $_POST['honeypot4cf7_move_inline_css'] ) ) ? $_POST['honeypot4cf7_move_inline_css'] : array( 'false' ),
			'nomessage'					=> ( isset( $_POST['honeypot4cf7_nomessage'] ) ) ? $_POST['honeypot4cf7_nomessage'] : array( 'false' ),
			'timecheck_enabled'			=> ( isset( $_POST['honeypot4cf7_timecheck_enabled'] ) ) ? $_POST['honeypot4cf7_timecheck_enabled'] : array( 'false' ),
			'timecheck_value'			=> ( isset( $_POST['honeypot4cf7_timecheck_value'] ) ) ? $_POST['honeypot4cf7_timecheck_value'] : 0,
		);

		$honeypot4cf7_config = array_replace( $honeypot4cf7_config, $honeypot4cf7_config_update );

		update_option( 'honeypot4cf7_config', $honeypot4cf7_config );

		echo '<div id="message" class="updated"><p>' . esc_html( __( 'The changes have been saved.', 'contact-form-7-honeypot' ) ).'</p></div>';
	} else {
		$honeypot4cf7_config = honeypot4cf7_get_config();
	}
    ?>
    <div class="honeypot4cf7-admin__primary">
        <div class="honeypot4cf7-admin__box">
            <form action="" method="post" id="honeypot4cf7_options_form" name="honeypot4cf7_options_form">
				<?php wp_nonce_field( 'honeypot4cf7-submit', 'honeypot4cf7_nonce' ); ?>
                <a href="https://wordpress.org/support/plugin/contact-form-7-honeypot/" target="_blank" class="honeypot4cf7_admin__support-link">
                    <span class="dashicons dashicons-editor-help"></span>
					<?php esc_html_e( 'Get Support', 'contact-form-7-honeypot' ); ?>
                </a>
                <h3><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e( 'Honeypot Settings', 'contact-form-7-honeypot' ); ?></h3>
                <p class="honeypot4cf7-admin__introduction"><?php esc_html_e( 'Below are global settings for the Honeypot plugin. Many of these settings can be overridden when inserting the Honeypot field shortcode when creating your CF7 contact form.', 'contact-form-7-honeypot' ); ?></p>
                <table class="form-table">
                    <tbody>
                    <tr valign="top">
                        <th><label for="honeypot4cf7__store-honeypot"><?php esc_html_e( 'Store Honeypot Value', 'contact-form-7-honeypot' ); ?></label></th>
                        <td>
                            <input type="checkbox" name="honeypot4cf7_store" id="honeypot4cf7__store-honeypot" value="1" <?php checked( $honeypot4cf7_config['store_honeypot'], 1 ); ?>>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td class="description" colspan="2">
							<?php
							printf(
							/* translators: 1: Link to Flamingo plugin page */
								__( '(Recommended) By default the Honeypot field is not stored with other fields in form-saving plugins like %1$s. However, saving the field can be useful to see what spam bots are leaving behind to help you improve your spam stopping superpowers. If you\'d like to store the value of the field, simply check this box (and install %1$s).', 'contact-form-7-honeypot' ),
								'<a href="https://wordpress.org/plugins/flamingo/" target="_blank">' . __( 'Flamingo', 'contact-form-7-honeypot' ) . '</a>'
							);
							?>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th><label for="honeypot4cf7__placeholder"><?php esc_html_e( 'Global Placeholder', 'contact-form-7-honeypot' ); ?></label></th>
                        <td>
                            <input type="text" class="regular-text" name="honeypot4cf7_placeholder" id="honeypot4cf7__placeholder" value="<?php echo sanitize_text_field( $honeypot4cf7_config['placeholder'] ); ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <td class="description" colspan="2"><?php esc_html_e( 'If using placeholders on other fields, this can help honeypot mimic a "real" field. This can be overridden in the contact form. If you\'re unsure, leave blank.', 'contact-form-7-honeypot' ); ?></td>
                    </tr>

                    <tr valign="top">
                        <th><label for="honeypot4cf7__accessibility_message"><?php esc_html_e( 'Accessibility Message', 'contact-form-7-honeypot' ); ?></label></th>
                        <td>
                            <input type="text" class="regular-text" name="honeypot4cf7_accessibility_message" id="honeypot4cf7__accessibility_message" value="<?php echo sanitize_text_field( $honeypot4cf7_config['accessibility_message'] ); ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <td class="description" colspan="2">
							<?php
							printf(
							/* translators: %s: default value */
								__( 'You can customize the (hidden) accessibility message, or just leave it the default value: %s', 'contact-form-7-honeypot' ),
								'<em>' . __( 'Please leave this field empty.', 'contact-form-7-honeypot' ) . '</em>'
							);
							?>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th><label for="honeypot4cf7__w3c-valid-autocomplete"><?php esc_html_e( 'Use Standard Autocomplete Value', 'contact-form-7-honeypot' ); ?></label></th>
                        <td>
                            <input type="checkbox" name="honeypot4cf7_w3c_valid_autocomplete[]" id="honeypot4cf7__w3c-valid-autocomplete" value="true" <?php checked( $honeypot4cf7_config['w3c_valid_autocomplete'][0], 'true' ); ?>>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td class="description" colspan="2"><?php esc_html_e( 'To assure the honeypot isn\'t auto-completed by a browser, we add an atypical "autocomplete" attribute value. If you have any problems with this, you can switch it to the more standard (but less effective) "off" value. If you\'re unsure, leave this unchecked.', 'contact-form-7-honeypot' ); ?></td>
                    </tr>

                    <tr valign="top">
                        <th><label for="honeypot4cf7__move_inline_css"><?php esc_html_e( 'Move Inline CSS', 'contact-form-7-honeypot' ); ?></label></th>
                        <td>
                            <input type="checkbox" name="honeypot4cf7_move_inline_css[]" id="honeypot4cf7__move_inline_css" value="true" <?php checked( $honeypot4cf7_config['move_inline_css'][0], 'true' ); ?>>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td class="description" colspan="2"><?php esc_html_e( 'By default Honeypot uses inline CSS on the honeypot field to hide it. Checking this box moves that CSS to the footer of the page. It may help confuse bots.', 'contact-form-7-honeypot' ); ?></td>
                    </tr>

                    <tr valign="top">
                        <th><label for="honeypot4cf7__nomessage"><?php esc_html_e( 'Disable Accessibility Label', 'contact-form-7-honeypot' ); ?></label></th>
                        <td>
                            <input type="checkbox" name="honeypot4cf7_nomessage[]" id="honeypot4cf7__nomessage" value="true" <?php checked( $honeypot4cf7_config['nomessage'][0], 'true' ); ?>>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td class="description" colspan="2"><?php esc_html_e( 'If checked, the accessibility label will not be generated. This is not recommended, but may improve spam blocking. If you\'re unsure, leave this unchecked.', 'contact-form-7-honeypot' ); ?></td>
                    </tr>

                    <tr valign="top">
                        <th><label for="honeypot4cf7__timecheck_enabled"><?php esc_html_e( 'Enable Time Check', 'contact-form-7-honeypot' ); ?></label></th>
                        <td>
                            <input type="checkbox" name="honeypot4cf7_timecheck_enabled[]" id="honeypot4cf7__timecheck_enabled" value="true" <?php checked( $honeypot4cf7_config['timecheck_enabled'][0], 'true' ); ?>>&nbsp;&nbsp;<input type="number" class="small-text" name="honeypot4cf7_timecheck_value" id="honeypot4cf7__timecheck_value" value="<?php echo sanitize_text_field( $honeypot4cf7_config['timecheck_value'] ); ?>" step="1" min="1"> <?php esc_html_e('seconds', 'contact-form-7-honeypot'); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td class="description" colspan="2"><?php esc_html_e( 'If enabled, this will perform an additional check for spam bots using the time it takes to submit the form under the idea that bots submit forms faster than people. The value is set to 4 seconds by default, but adjust based on your needs. If you\'re not sure, leave this unchecked.', 'contact-form-7-honeypot' ); ?></td>
                    </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <input name="save" id="save" class="button button-primary" value="<?php esc_attr_e( 'Save', 'contact-form-7-honeypot' ); ?>" type="submit" />
                    <input name="clear" id="reset" class="button" value="<?php esc_attr_e( 'Reset to Defaults', 'contact-form-7-honeypot' ); ?>" type="submit" />

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
                <span class="dashicons dashicons-chart-area"></span>

				<?php
				$hpcf7_date_format = date( get_option( 'date_format' ), $honeypot4cf7_config['honeypot_install_date'] );
				if ( function_exists( 'wp_date' ) ) {
					$hpcf7_date_format = wp_date( get_option( 'date_format' ), $honeypot4cf7_config['honeypot_install_date'] );
				} elseif ( function_exists( 'date_i18n' ) ) {
					$hpcf7_date_format = date_i18n( get_option( 'date_format' ), $honeypot4cf7_config['honeypot_install_date'] );
				} elseif ( function_exists( 'gmdate' ) ){
					$hpcf7_date_format = gmdate( get_option( 'date_format' ), $honeypot4cf7_config['honeypot_install_date'] );
				}
				?>
				<?php
				printf(
				/* translators: 1: spam count 2: install date */
					__( 'Honeypot has stopped %1$s spam submissions since %2$s', 'contact-form-7-honeypot' ),
					'<strong>' . $honeypot4cf7_config['honeypot_count'] . '</strong>',
					$hpcf7_date_format
				);
				?>
            </p>
        </div>
    </div>
    <?php
}

add_action( 'honeypot4cf7_tab_all-forms', 'honeypot4cf7_all_forms_tab_content' );

function honeypot4cf7_all_forms_tab_content() {
    require_once HONEYPOT4CF7_PLUGIN_DIR . '/includes/class-honeypot4cf7-data-table.php';
    ?>
    <div class="honeypot4cf7-admin__primary">
        <div class="honeypot4cf7-admin__box">
            <?php
                $honeypot4cf7_forms = new Honeypot4cf7_Data_Table();
                $honeypot4cf7_forms->prepare_items();
                $honeypot4cf7_forms->display();
            ?>
        </div>
    </div>
    <?php
}


function honeypot4cf7_side_content() {
    ob_start();
    ?>
    <div class="honeypot4cf7-admin__secondary">
        <div class="">
            <div class="honeypot4cf7__banner-ad honeypot4cf7__banner-ad--2">
                <a target="_blank" href="https://postmansmtp.com/pricing/?utm_source=CF7_Perks&utm_medium=Banner&utm_id=CF7_Perks_Baner&utm_content=side_banner"><img src="<?php echo HONEYPOT4CF7_PLUGIN_DIR_URL; ?>/includes/images/banners/post-smtp-branding.png" border="0" /></a>
            </div>
        </div>

        <div class="honeypot4cf7-admin__box honeypot4cf7-admin__rate-us">
            <div class="honeypot4cf7-admin__stars">
                <a target="_blank" href="https://wordpress.org/support/plugin/contact-form-7-honeypot/reviews/?filter=5#new-post">
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                    <span class="dashicons dashicons-star-filled"></span>
                </a>
            </div>
			<?php
			printf(
			/* translators: %s: Plugin's reviews page link */
				__( 'Please rate us on %s. Thanks!!', 'contact-form-7-honeypot' ),
				'<a target="_blank" href="https://wordpress.org/support/plugin/contact-form-7-honeypot/reviews/?filter=5#new-post">wordpress.org</a>'
			);
			?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/* ***********
*
* Add admin page CSS
*
*********** */
function honeypot4cf7_admin_enqueues( $hook ) {
	wp_enqueue_script( 'honeypot4cf7-admin-js', plugins_url( 'includes/js/notice-update.js', HONEYPOT4CF7_PLUGIN ), array( 'jquery' ), HONEYPOT4CF7_VERSION, true );
	if ( strpos( $hook, 'honeypot4cf7' ) !== false ) {
		 wp_enqueue_style( 'honeypot4cf7-admin-css', plugins_url( 'includes/css/styles.css', HONEYPOT4CF7_PLUGIN ), array(), HONEYPOT4CF7_VERSION, 'all' );
	}
}
add_action( 'admin_enqueue_scripts', 'honeypot4cf7_admin_enqueues' );


if ( ! function_exists( 'str_contains' ) ) {
	/**
     * Check if a string contains another string.
     * Backward compatibility for PHP < 8.0
     *
	 * @param string $haystack
	 * @param string $needle
	 *
	 * @return bool
	 */
    function str_contains( $haystack, $needle ) {
        return '' !== $needle && false !== strpos( $haystack, $needle );
    }
}

add_filter( 'wpcf7_config_validator_available_error_codes', 'honeypot4cf7_remove_cf7_error_messages', 10, 2 );

function honeypot4cf7_remove_cf7_error_messages( $error_codes, $contact_form ) {

    $error_codes_to_disable = array( 'unsafe_email_without_protection' );

    $contact_form = $contact_form->get_properties();
    if ( isset( $contact_form['form'] ) ) {
	    $regx = '/\[honeypot [a-zA-Z]/i';
	    preg_match_all( $regx, $contact_form['form'], $matches );
        if ( ! empty( $matches[0] ) ) {
            $error_codes = array_diff( $error_codes, $error_codes_to_disable );
        }
    }

    return $error_codes;
}
