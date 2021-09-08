<?php

/**
 *
 * Initialize the shortcode
 * 		This lets CF7 know about Mr. Honeypot.
 * 
 */
add_action( 'wpcf7_init', 'honeypot4cf7_add_form_tag', 10 );
function honeypot4cf7_add_form_tag() {
	
	$honeypot4cf7_config = honeypot4cf7_get_config();
	$do_not_store = ( empty( $honeypot4cf7_config['store_honeypot'] ) ) ? true : false;

	// Test if new 4.6+ functions exists
	if ( function_exists( 'wpcf7_add_form_tag' ) ) {
		wpcf7_add_form_tag( 
			'honeypot', 
			'honeypot4cf7_form_tag_handler', 
			array( 
				'name-attr' => true, 
				'do-not-store' => $do_not_store,
				'not-for-mail' => true,
			)
		);
	} else {
		wpcf7_add_shortcode( 'honeypot', 'honeypot4cf7_form_tag_handler', true );
	}
}


/**
 * 
 * Form Tag handler
 * 		This is where we generate the honeypot HTML from the shortcode options
 * 
 */
function honeypot4cf7_form_tag_handler( $tag ) {

	// Test if new 4.6+ functions exists
	$tag = ( class_exists( 'WPCF7_FormTag' ) ) ? new WPCF7_FormTag( $tag ) : new WPCF7_Shortcode( $tag );

	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$honeypot4cf7_config = honeypot4cf7_get_config();

	$class = wpcf7_form_controls_class( 'text' );
	
	$placeholder = (string) reset( $tag->values );

	$accessibility_message = ( ! empty( $honeypot4cf7_config['accessibility_message'] ) ) ? $honeypot4cf7_config['accessibility_message'] : __( 'Please leave this field empty.', 'contact-form-7-honeypot' );

	$atts = array(
		'class' 			=> $tag->get_class_option( $class ),
		'id'				=> $tag->get_option( 'id', 'id', true ),
		'wrapper_id' 		=> $tag->get_option( 'wrapper-id' ),
		'placeholder' 		=> ( $placeholder ) ? $placeholder : $honeypot4cf7_config['placeholder'],
		'message' 			=> apply_filters( 'wpcf7_honeypot_accessibility_message', $accessibility_message ),
		'name'				=> $tag->name,
		'type'				=> $tag->type,
		'validautocomplete'	=> ( $tag->get_option( 'validautocomplete' ) ) ? $tag->get_option( 'validautocomplete' ) : $honeypot4cf7_config['w3c_valid_autocomplete'],
		'move_inline_css'	=> ( $tag->get_option( 'move-inline-css' ) ) ? $tag->get_option( 'move-inline-css' ) : $honeypot4cf7_config['move_inline_css'],
		'nomessage'			=> ( $tag->get_option( 'nomessage' ) ) ? $tag->get_option( 'nomessage' ) : $honeypot4cf7_config['nomessage'],
		'timecheck_enabled'	=> ( $tag->get_option( 'timecheck_enabled' ) ) ? $tag->get_option( 'timecheck_enabled' ) : $honeypot4cf7_config['timecheck_enabled'],
		'timecheck_value'	=> ( $timecheck_value = $tag->get_option( 'timecheck_value' ) ) ? reset($timecheck_value) : $honeypot4cf7_config['timecheck_value'],
		'validation_error'	=> $validation_error,
		'css'				=> apply_filters( 'wpcf7_honeypot_container_css', 'display:none !important; visibility:hidden !important;' ),
	);

	$unique_id = uniqid( 'wpcf7-' );
	$wrapper_id = ( ! empty($atts['wrapper_id'] ) ) ? reset( $atts['wrapper_id'] ) : $unique_id . '-wrapper';
	$input_placeholder = ( ! empty( $atts['placeholder'] ) ) ? ' placeholder="' . $atts['placeholder'] . '" ' : '';
	$input_id = ( ! empty( $atts['id'] ) ) ? $atts['id'] : $unique_id . '-field';
	$autocomplete_value = ( $atts['validautocomplete'][0] === 'true' ) ? 'off' : 'new-password';

	// Check if we should move the CSS off the element and into the footer
	if ( ! empty( $atts['move_inline_css'] ) && $atts['move_inline_css'][0] === 'true' ) {
		$hp_css = '#' . $wrapper_id . ' {' . $atts['css'] . '}';
		wp_register_style( $unique_id . '-inline', false );
		wp_enqueue_style( $unique_id . '-inline' );
		wp_add_inline_style( $unique_id . '-inline', $hp_css );
		$el_css = '';
	} else {
		$el_css = 'style="' . $atts['css'] . '"';
	}

	$html = '<span id="' . $wrapper_id . '" class="wpcf7-form-control-wrap ' . $atts['name'] . '-wrap" ' . $el_css . '>';
	
	if ( !empty( $atts['timecheck_enabled'] ) && $atts['timecheck_enabled'][0] !== 'false' ) {
		$html .= '<input type="hidden" name="'.$atts['name'].'-time-start" value="'.time().'">';
		$html .= '<input type="hidden" name="'.$atts['name'].'-time-check" value="'.$atts['timecheck_value'].'">';
	}

	if ( empty( $atts['nomessage'] ) || $atts['nomessage'][0] === 'false' ) {
		$html .= '<label for="' . $input_id . '" class="hp-message">' . $atts['message'] . '</label>';
	}

	$html .= '<input id="' . $input_id . '" ' . $input_placeholder . ' class="' . $atts['class'] . '" type="text" name="' . $atts['name'] . '" value="" size="40" tabindex="-1" autocomplete="'. $autocomplete_value . '" />';
	$html .= $validation_error . '</span>';

	// Hook for filtering finished Honeypot form element.
	return apply_filters( 'wpcf7_honeypot_html_output' , $html, $atts );
}


/**
 * 
 * Honeypot Spam Check
 * 		Bots beware!
 * 
 */

if ( version_compare(HONEYPOT4CF7_WPCF7_VERSION, '5.3.0', '>=' ) ) {
	// Newer Spam filter - with log
	add_filter( 'wpcf7_spam', 'honeypot4cf7_spam_check', 10, 2 );
} elseif ( version_compare(HONEYPOT4CF7_WPCF7_VERSION, '3.0', '>=' ) ) {
	// Older Spam filter - no log
	add_filter( 'wpcf7_spam', 'honeypot4cf7_spam_check', 10, 1 );
} else {
	// Real old - unsupported
	return false;
}

function honeypot4cf7_spam_check( $spam, $submission = null ) {

	if ( $spam ) {
		return $spam;
	}

	$cf7form = WPCF7_ContactForm::get_current();
	$form_tags = $cf7form->scan_form_tags();
	
	foreach ( $form_tags as $tag ) {
		if ( $tag->type == 'honeypot' ) {
			$hp_ids[] = $tag->name;
		}
	}

	// Check if form has Honeypot fields, if not, exit
	if ( empty( $hp_ids ) ) {
		return $spam;
	}

	foreach ( $hp_ids as $hpid ) {
		$honeypot4cf7_config = honeypot4cf7_get_config();
		$value = isset( $_POST[$hpid] ) ? $_POST[$hpid] : '';
		

		// SPAM CHECK #1: Let's check submission speed first, as it's time sensitive. :)
		$timecheck_start = isset( $_POST[$hpid.'-time-start'] ) ? $_POST[$hpid.'-time-start'] : '';

		if ( $timecheck_start ) {
			$submission_time = time();
			$timecheck_value = isset( $_POST[$hpid.'-time-check'] ) ? $_POST[$hpid.'-time-check'] : '';
			$submission_interval = $submission_time - $timecheck_start;
			
			if ( $submission_interval < $timecheck_value ) {
				// Fast Bots!
				$spam = true;
				
				if ( $submission ) {
					$submission->add_spam_log( array(
						'agent' => 'honeypot',
						'reason' => sprintf(
							/* translators: 1: submission interval integer 2: honeypot field ID */
							__( 'Honeypot detected form submitted too fast (%1$s seconds). Field ID = %2$s', 'contact-form-7-honeypot' ), 
							$submission_interval,
							$hpid
						),
					) );
				}

				$honeypot4cf7_config['honeypot_count'] = ( isset( $honeypot4cf7_config['honeypot_count'] ) ) ? $honeypot4cf7_config['honeypot_count'] + 1 : 1;
				update_option( 'honeypot4cf7_config', $honeypot4cf7_config );
				
				return $spam; // There's no need to go on, this is most likely a bot submission.

			}
		}

		// SPAM CHECK #2: Now we check the honeypot!
		if ( $value != '' ) {
			// Chatty Bots!
			$spam = true;
			
			if ( $submission ) {
				$submission->add_spam_log( array(
					'agent' => 'honeypot',
					'reason' => sprintf(
						/* translators: %s: honeypot field ID */
						__( 'Something is stuck in the honey. Field ID = %s', 'contact-form-7-honeypot' ), 
						$hpid
					),
				) );
			}

			$honeypot4cf7_config['honeypot_count'] = ( isset( $honeypot4cf7_config['honeypot_count'] ) ) ? $honeypot4cf7_config['honeypot_count'] + 1 : 1;
			update_option( 'honeypot4cf7_config', $honeypot4cf7_config );
			
			return $spam; // There's no need to go on, we've got flies in the honey.
		}

	}

	return $spam;
}


/**
 * 
 * Tag generator & handler
 * 		Adds Honeypot to the CF7 form editor
 * 
 */
add_action( 'wpcf7_admin_init', 'honeypot4cf7_generate_form_tag', 10, 0 );

function honeypot4cf7_generate_form_tag() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'honeypot', __( 'Honeypot', 'contact-form-7-honeypot' ), 'honeypot4cf7_form_tag_generator' );
}

function honeypot4cf7_form_tag_generator( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$description = __( 'Generate a form-tag for a spam-stopping honeypot field. For more details, see %s.', 'contact-form-7-honeypot' );
	$desc_link = '<a href="https://wordpress.org/plugins/contact-form-7-honeypot/" target="_blank">' . __( 'Honeypot for CF7', 'contact-form-7-honeypot' ) . '</a>';
	?>
	<div class="control-box">
		<fieldset>
			<legend>
				<?php 
				$honeypotcf7_config_url = esc_url( add_query_arg('page','honeypot4cf7',get_admin_url() . 'admin.php') );
				$honeypotcf7_settings_link = "<a href='$honeypotcf7_config_url'>" . __( 'Honeypot Settings', 'contact-form-7-honeypot' ) . '</a>';
				printf(
					/* translators: %s: Link to Honeypot settings page */
					esc_html( __( 'Generate a form-tag for a spam-stopping honeypot field. Check out %s for more settings/info.', 'contact-form-7-honeypot' ) ),
					$honeypotcf7_settings_link
				); 
				?>
			</legend>

			<table class="form-table form-table--honeypotcf7"><tbody>
				<tr>
					<th style="width:50%;" scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php esc_html_e( 'Name', 'contact-form-7-honeypot' ); ?></label>
					</th>
					<td>
						<input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /><br>
					</td>
				</tr>
				<tr>
					<td colspan="2"><em><?php esc_html_e( 'For better security, change "honeypot" to something more appealing to a bot, such as text including "email" or "website".', 'contact-form-7-honeypot' ); ?></em></td>
				</tr>

				<tr style="background:#efefef;">
					<td colspan="2" style="text-transform:uppercase;text-align:center;font-weight:bold;padding-top:5px;padding-bottom:5px;">
						<?php esc_html_e( 'Optional Settings', 'contact-form-7-honeypot' ); ?>
					</td>
				</tr>

				<tr>
					<th style="width:50%;" scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php esc_html_e( 'ID', 'contact-form-7-honeypot' ); ?></label>
					</th>
					<td>
						<input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" />
					</td>
				</tr>

				<tr>
					<th style="width:50%;" scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php esc_html_e( 'Class', 'contact-form-7-honeypot' ); ?></label>
					</th>
					<td>
						<input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" />
					</td>
				</tr>

				<tr>
					<th style="width:50%;" scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-wrapper-id' ); ?>"><?php esc_html_e( 'Wrapper ID', 'contact-form-7-honeypot' ); ?></label>
					</th>
					<td>
						<input type="text" name="wrapper-id" class="wrapper-id-value oneline option" id="<?php echo esc_attr( $args['content'] . '-wrapper-id' ); ?>" /><br>
					</td>
				</tr>

				<tr>
					<th style="width:50%;" scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Placeholder', 'contact-form-7-honeypot' ) ); ?></label>
					</th>
					<td>
						<input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" />
					</td>
				</tr>

				<tr>
					<th style="width:50%;" scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-validautocomplete' ); ?>"><?php esc_html_e( 'Use Standard Autocomplete Value', 'contact-form-7-honeypot' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="validautocomplete:true" id="<?php echo esc_attr( $args['content'] . '-validautocomplete' ); ?>" class="validautocompletevalue option" />
					</td>
				</tr>

				<tr>
					<th style="width:50%;" scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-move-inline-css' ); ?>"><?php esc_html_e( 'Move inline CSS', 'contact-form-7-honeypot' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="move-inline-css:true" id="<?php echo esc_attr( $args['content'] . '-move-inline-css' ); ?>" class="move-inline-css-value option" />
					</td>
				</tr>

				<tr>
					<th style="width:50%;" scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-nomessage' ); ?>"><?php esc_html_e( 'Disable Accessibility Label', 'contact-form-7-honeypot' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="nomessage:true" id="<?php echo esc_attr( $args['content'] . '-nomessage' ); ?>" class="messagekillvalue option" />
					</td>
				</tr>

				<tr>
					<th style="width:50%;" scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-timecheck-enabled' ); ?>"><?php esc_html_e( 'Enable Time Check', 'contact-form-7-honeypot' ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="timecheck_enabled:true" id="<?php echo esc_attr( $args['content'] . '-timecheck-enabled' ); ?>" class="option" />
						<input type="number" step="1" min="1" placeholder="4" value="" name="timecheck_value" class="oneline option" id="<?php echo esc_attr( $args['content'] . '-timecheck-value' ); ?>" /> <?php esc_html_e('seconds', 'contact-form-7-honeypot'); ?>
					</td>
				</tr>

			</tbody></table>
		</fieldset>
	</div>

	<div class="insert-box">
		<input type="text" name="honeypot" class="tag code" readonly="readonly" onfocus="this.select()" />

		<div class="submitbox">
			<input type="button" class="button button-primary insert-tag" value="<?php esc_attr_e( 'Insert Tag', 'contact-form-7-honeypot' ); ?>" />
		</div>

		<br class="clear" />
	</div>
<?php }