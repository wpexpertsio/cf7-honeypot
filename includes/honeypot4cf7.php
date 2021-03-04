<?php

/**
 *
 * Initialize the shortcode
 * 		This lets CF7 know about Mr. Honeypot.
 * 
 */
add_action('wpcf7_init', 'honeypot4cf7_add_form_tag', 10);
function honeypot4cf7_add_form_tag() {
	
	$honeypot4cf7_config = get_option('honeypot4cf7_config');
	$do_not_store = (empty($honeypot4cf7_config['store_honeypot'])) ? true : false;

	// Test if new 4.6+ functions exists
	if (function_exists('wpcf7_add_form_tag')) {
		wpcf7_add_form_tag( 
			'honeypot', 
			'honeypot4cf7_form_tag_handler', 
			array( 
				'name-attr' => true, 
				'do-not-store' => $do_not_store,
				'not-for-mail' => true
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
	$tag = (class_exists('WPCF7_FormTag')) ? new WPCF7_FormTag( $tag ) : new WPCF7_Shortcode( $tag );

	if ( empty( $tag->name ) )
		return '';

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$honeypot4cf7_config = get_option('honeypot4cf7_config');

	$class = wpcf7_form_controls_class( 'text' );
	
	$placeholder = (string) reset( $tag->values );

	$accessibility_message = ($honeypot4cf7_config['accessibility_message']) ? $honeypot4cf7_config['accessibility_message'] : __('Please leave this field empty.',HONEYPOT4CF7_TEXT_DOMAIN);

	$atts = array(
		'class' 			=> $tag->get_class_option( $class ),
		'id'				=> $tag->get_option( 'id', 'id', true ),
		'wrapper_id' 		=> $tag->get_option('wrapper-id'),
		'placeholder' 		=> ($placeholder) ? $placeholder : $honeypot4cf7_config['placeholder'],
		'message' 			=> apply_filters('wpcf7_honeypot_accessibility_message', $accessibility_message),
		'name'				=> $tag->name,
		'type'				=> $tag->type,
		'validautocomplete'	=> ($tag->get_option('validautocomplete')) ? $tag->get_option('validautocomplete') : $honeypot4cf7_config['w3c_valid_autocomplete'],
		'move_inline_css'	=> ($tag->get_option('move-inline-css')) ? $tag->get_option('move-inline-css') : $honeypot4cf7_config['move_inline_css'],
		'nomessage'			=> ($tag->get_option('nomessage')) ? $tag->get_option('nomessage') : $honeypot4cf7_config['nomessage'],
		'validation_error'	=> $validation_error,
		'css'				=> apply_filters('wpcf7_honeypot_container_css', 'display:none !important; visibility:hidden !important;')
	);

	$unique_id = uniqid('wpcf7-');
	$wrapper_id = (!empty($atts['wrapper_id'])) ? reset($atts['wrapper_id']) : $unique_id.'-wrapper';
	$input_placeholder = (!empty($atts['placeholder'])) ? ' placeholder="'.$atts['placeholder'].'" ' : '';
	$input_id = (!empty($atts['id'])) ? $atts['id'] : $unique_id.'-field';
	$autocomplete_value = ($atts['validautocomplete'][0] === 'true') ? 'off' : 'new-password';

	// Check if we should move the CSS off the element and into the footer
	if (!empty($atts['move_inline_css']) && $atts['move_inline_css'][0] === 'true') {
		$hp_css = '#'.$wrapper_id.' {'.$atts['css'].'}';
		wp_register_style( $unique_id.'-inline', false);
		wp_enqueue_style( $unique_id.'-inline' );
		wp_add_inline_style( $unique_id.'-inline', $hp_css );
		$el_css = '';
	} else {
		$el_css = 'style="'.$atts['css'].'"';
	}

	$html = '<span id="'.$wrapper_id.'" class="wpcf7-form-control-wrap ' . $atts['name'] . '-wrap" '.$el_css.'>';
	
	if (empty($atts['nomessage']) || $atts['nomessage'][0] === 'false') {
		$html .= '<label for="' . $input_id . '" class="hp-message">'.$atts['message'].'</label>';
	}

	$html .= '<input id="' . $input_id .'"' . $input_placeholder . 'class="' . $atts['class'] . '"  type="text" name="' . $atts['name'] . '" value="" size="40" tabindex="-1" autocomplete="'.$autocomplete_value.'" />';
	$html .= $validation_error . '</span>';

	// Hook for filtering finished Honeypot form element.
	return apply_filters('wpcf7_honeypot_html_output',$html, $atts);
}


/**
 * 
 * Honeypot Spam Check
 * 		Bots beware!
 * 
 */
add_filter( 'wpcf7_spam', 'honeypot4cf7_spam_check', 10, 2 );

function honeypot4cf7_spam_check($spam, $submission) {
	if ( $spam ) return $spam;

	$cf7form = WPCF7_ContactForm::get_current();
	$form_tags = $cf7form->scan_form_tags();
	
	foreach ($form_tags as $tag) :
		if ($tag->type == 'honeypot') :
			$hp_ids[] = $tag->name;
		endif;
	endforeach;

	// Check if form has Honeypot fields, if not, exit
	if (empty($hp_ids)) return $spam;

	foreach ($hp_ids as $hpid) :
		$value = isset( $_POST[$hpid] ) ? $_POST[$hpid] : '';
		
		if ( $value != '' ) :
			// Bots!
			$spam = true;
			$submission->add_spam_log( array(
				'agent' => 'honeypot',
				'reason' => __( 'Something is stuck in the honey. Field ID = '. $hpid, HONEYPOT4CF7_TEXT_DOMAIN ),
			) );

			$honeypot4cf7_config = get_option('honeypot4cf7_config');
			$honeypot4cf7_config['honeypot_count'] = (isset($honeypot4cf7_config['honeypot_count'])) ? $honeypot4cf7_config['honeypot_count'] + 1 : 1;
			update_option( 'honeypot4cf7_config', $honeypot4cf7_config );
			
			return $spam; // There's no need to go on, we've got flies in the honey.
		endif;

	endforeach;

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
	$tag_generator->add( 'honeypot', __( 'Honeypot', HONEYPOT4CF7_TEXT_DOMAIN ), 'honeypot4cf7_form_tag_generator' );
}

function honeypot4cf7_form_tag_generator($contact_form, $args = '') {
	$args = wp_parse_args( $args, array() );
	$description = __( "Generate a form-tag for a spam-stopping honeypot field. For more details, see %s.", HONEYPOT4CF7_TEXT_DOMAIN );
	$desc_link = '<a href="https://wordpress.org/plugins/contact-form-7-honeypot/" target="_blank">'.__( 'Honeypot for CF7', HONEYPOT4CF7_TEXT_DOMAIN ).'</a>';
	?>
	<div class="control-box">
		<fieldset>
			<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

			<table class="form-table"><tbody>
				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', HONEYPOT4CF7_TEXT_DOMAIN ) ); ?></label>
					</th>
					<td>
						<input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /><br>
						<em><?php echo esc_html( __( 'This can be anything, but should be changed from the default generated "honeypot". For better security, change "honeypot" to something more appealing to a bot, such as text including "email" or "website".', HONEYPOT4CF7_TEXT_DOMAIN ) ); ?></em>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'ID (optional)', HONEYPOT4CF7_TEXT_DOMAIN ) ); ?></label>
					</th>
					<td>
						<input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" />
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class (optional)', HONEYPOT4CF7_TEXT_DOMAIN ) ); ?></label>
					</th>
					<td>
						<input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" />
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-wrapper-id' ); ?>"><?php echo esc_html( __( 'Wrapper ID (optional)', HONEYPOT4CF7_TEXT_DOMAIN ) ); ?></label>
					</th>
					<td>
						<input type="text" name="wrapper-id" class="wrapper-id-value oneline option" id="<?php echo esc_attr( $args['content'] . '-wrapper-id' ); ?>" /><br>
						<em><?php echo esc_html( __( 'By default the markup that wraps this form item has a random ID. You can customize it here. If you\'re unsure, leave blank.', HONEYPOT4CF7_TEXT_DOMAIN ) ); ?></em>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Placeholder (optional)', HONEYPOT4CF7_TEXT_DOMAIN ) ); ?></label>
					</th>
					<td>
						<input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /><br>
						<em><?php echo esc_html( __( 'If using placeholders on other fields, this can help honeypot mimic a "real" field. If you\'re unsure, leave blank.', HONEYPOT4CF7_TEXT_DOMAIN ) ); ?></em>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-validautocomplete' ); ?>"><?php echo esc_html( __( 'Use Standard Autocomplete Value (optional)', HONEYPOT4CF7_TEXT_DOMAIN ) ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="validautocomplete:true" id="<?php echo esc_attr( $args['content'] . '-validautocomplete' ); ?>" class="validautocompletevalue option" /><br />
						<em><?php echo __('To assure the honeypot isn\'t auto-completed by a browser, we add an atypical "autocomplete" attribute value. If you have any problems with this, you can switch it to the more standard (but less effective) "off" value. If you\'re unsure, leave this unchecked.', HONEYPOT4CF7_TEXT_DOMAIN); ?></em>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-move-inline-css' ); ?>"><?php echo esc_html( __( 'Move inline CSS (optional)', HONEYPOT4CF7_TEXT_DOMAIN ) ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="move-inline-css:true" id="<?php echo esc_attr( $args['content'] . '-move-inline-css' ); ?>" class="move-inline-css-value option" /><br />
						<em><?php echo __('Moves the CSS to hide the honeypot from the element to the footer of the page. May help confuse bots.',HONEYPOT4CF7_TEXT_DOMAIN); ?></em>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="<?php echo esc_attr( $args['content'] . '-nomessage' ); ?>"><?php echo esc_html( __( 'Disable Accessibility Label (optional)', HONEYPOT4CF7_TEXT_DOMAIN ) ); ?></label>
					</th>
					<td>
						<input type="checkbox" name="nomessage:true" id="<?php echo esc_attr( $args['content'] . '-nomessage' ); ?>" class="messagekillvalue option" /><br />
						<em><?php echo __('If checked, the accessibility label will not be generated. This is not recommended, but may improve spam blocking. If you\'re unsure, leave this unchecked.',HONEYPOT4CF7_TEXT_DOMAIN); ?></em>
					</td>
				</tr>

			</tbody></table>
		</fieldset>
	</div>

	<div class="insert-box">
		<input type="text" name="honeypot" class="tag code" readonly="readonly" onfocus="this.select()" />

		<div class="submitbox">
			<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', HONEYPOT4CF7_TEXT_DOMAIN ) ); ?>" />
		</div>

		<br class="clear" />
	</div>
<?php }