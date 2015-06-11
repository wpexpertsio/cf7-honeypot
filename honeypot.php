<?php
/*
Plugin Name: Contact Form 7 Honeypot
Plugin URI: http://www.daobydesign.com/free-plugins/honeypot-module-for-contact-form-7-wordpress-plugin
Description: Add honeypot anti-spam functionality to the popular Contact Form 7 plugin.
Author: Dao By Design
Author URI: http://www.daobydesign.com
Version: 1.7
*/

/*  Copyright 2015  Dao By Design  (email : info@daobydesign.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

add_action('wpcf7_init', 'wpcf7_honeypot_loader', 10);

function wpcf7_honeypot_loader() {
	global $pagenow;
	if (function_exists('wpcf7_add_shortcode')) {
		wpcf7_add_shortcode( 'honeypot', 'wpcf7_honeypot_shortcode_handler', true );
	} else {
		if ($pagenow != 'plugins.php') { return; }
		add_action('admin_notices', 'cfhiddenfieldserror');
		wp_enqueue_script('thickbox');
		function cfhiddenfieldserror() {
			$out = '<div class="error" id="messages"><p>';
			if(file_exists(WP_PLUGIN_DIR.'/contact-form-7/wp-contact-form-7.php')) {
				$out .= __('The Contact Form 7 is installed, but <strong>you must activate Contact Form 7</strong> below for the Honeypot Module to work.','wpcf7_honeypot');
			} else {
				$out .= __('The Contact Form 7 plugin must be installed for the Honeypot Module to work. <a href="'.admin_url('plugin-install.php?tab=plugin-information&plugin=contact-form-7&from=plugins&TB_iframe=true&width=600&height=550').'" class="thickbox" title="Contact Form 7">Install Now.</a>', 'wpcf7_honeypot');
			}
			$out .= '</p></div>';	
			echo $out;
		}
	}
}


/**
** A base module for [honeypot]
**/

/* Shortcode handler */
function wpcf7_honeypot_shortcode_handler( $tag ) {
	$tag = new WPCF7_Shortcode( $tag );

	if ( empty( $tag->name ) )
		return '';

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( 'text' );
	
	$atts = array();
	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_option( 'id', 'id', true );
	$atts['message'] = __('Please leave this field empty.','wpcf7_honeypot');
	$atts['name'] = $tag->name;
	$atts['type'] = $tag->type;
	$atts['nomessage'] = $tag->get_option('nomessage');
	$atts['validation_error'] = $validation_error;
	$inputid = (!empty($atts['id'])) ? 'id="'.$atts['id'].'" ' : '';
	$html = '<span class="wpcf7-form-control-wrap ' . $atts['name'] . '-wrap" style="display:none !important;visibility:hidden !important;">';
	$html .= '<input ' . $inputid . 'class="' . $atts['class'] . '"  type="text" name="' . $atts['name'] . '" value="" size="40" tabindex="-1" />';
	if (!$atts['nomessage']) {
		$html .= '<span class="hp-message">'.$atts['message'].'</span>';
	}
	$html .= $validation_error . '</span>';

	// Hook for filtering finished Honeypot form element.
	return apply_filters('wpcf7_honeypot_html_output',$html, $atts);
}


/* Honeypot Validation Filter */
add_filter( 'wpcf7_validate_honeypot', 'wpcf7_honeypot_filter' ,10,2);

function wpcf7_honeypot_filter ( $result, $tag ) {
	$tag = new WPCF7_Shortcode( $tag );

	$name = $tag->name;

	$value = isset( $_POST[$name] ) ? $_POST[$name] : '';
	
	if ( $value != '' ) {
		$result['valid'] = false;
		$result['reason'] = array( $name => wpcf7_get_message( 'spam' ) );
	}

	return $result;
}


/* Tag generator */

add_action( 'admin_init', 'wpcf7_add_tag_generator_honeypot', 35 );

function wpcf7_add_tag_generator_honeypot() {
	if (class_exists('WPCF7_TagGenerator')) {
		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add( 'honeypot', __( 'Honeypot', 'contact-form-7' ), 'wpcf7_tg_pane_honeypot' );
	} else if (function_exists('wpcf7_add_tag_generator')) {
		wpcf7_add_tag_generator( 'honeypot', __( 'Honeypot', 'wpcf7' ),	'wpcf7-tg-pane-honeypot', 'wpcf7_tg_pane_honeypot' );
	}
}

function wpcf7_tg_pane_honeypot($contact_form, $args = '') {
	if (class_exists('WPCF7_TagGenerator')) {
		$args = wp_parse_args( $args, array() );
		$description = __( "Generate a form-tag for a spam-stopping honeypot field. For more details, see %s.", 'wpcf7_honeypot' );
		$desc_link = '<a href="https://wordpress.org/plugins/contact-form-7-honeypot/" target="_blank">'.__( 'CF7 Honeypot', 'wpcf7_honeypot' ).'</a>';
		?>
		<div class="control-box">
			<fieldset>
				<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

				<table class="form-table"><tbody>
					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label>
						</th>
						<td>
							<input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /><br>
							<em><?php echo esc_html( __( 'For better security, change "honeypot" to something less bot-recognizable.', 'wpcf7_honeypot' ) ); ?></em>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'ID (optional)', 'contact-form-7' ) ); ?></label>
						</th>
						<td>
							<input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" />
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class (optional)', 'contact-form-7' ) ); ?></label>
						</th>
						<td>
							<input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" />
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( $args['content'] . '-nomessage' ); ?>"><?php echo esc_html( __( 'Don\'t Use Useability Message (optional)', 'contact-form-7' ) ); ?></label>
						</th>
						<td>
							<input type="checkbox" name="nomessage:true" id="<?php echo esc_attr( $args['content'] . '-nomessage' ); ?>" class="messagekillvalue option" /><br />
							<em><small><?php echo __('If checked, the useability message will not be generated. <strong>This is not recommended</strong>. If you\'re unsure, leave this unchecked.','wpcf7_honeypot'); ?>"</small></em>
						</td>
					</tr>

				</tbody></table>
			</fieldset>
		</div>

		<div class="insert-box">
			<input type="text" name="honeypot" class="tag code" readonly="readonly" onfocus="this.select()" />

			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
			</div>

			<br class="clear" />
		</div>
	<?php } else { ?>
		<div id="wpcf7-tg-pane-honeypot" class="hidden">
			<form action="">
				<table>
					<tr>
						<td>
							<?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?><br />
							<input type="text" name="name" class="tg-name oneline" /><br />
							<em><small><?php echo esc_html( __( 'For better security, change "honeypot" to something less bot-recognizable.', 'wpcf7_honeypot' ) ); ?></small></em>
						</td>
						<td></td>
					</tr>
					
					<tr>
						<td colspan="2"><hr></td>
					</tr>

					<tr>
						<td>
							<?php echo esc_html( __( 'ID (optional)', 'contact-form-7' ) ); ?><br />
							<input type="text" name="id" class="idvalue oneline option" />
						</td>
						<td>
							<?php echo esc_html( __( 'Class (optional)', 'contact-form-7' ) ); ?><br />
							<input type="text" name="class" class="classvalue oneline option" />
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="checkbox" name="nomessage:true" id="nomessage" class="messagekillvalue option" /> <label for="nomessage"><?php echo esc_html( __( 'Don\'t Use Useability Message (optional)', 'contact-form-7' ) ); ?></label><br />
							<em><small><?php echo __('If checked, the useability message will not be generated. <strong>This is not recommended</strong>. If you\'re unsure, leave this unchecked.','wpcf7_honeypot'); ?>"</small></em>
						</td>
					</tr>

					<tr>
						<td colspan="2"><hr></td>
					</tr>			
				</table>
				
				<div class="tg-tag"><?php echo esc_html( __( "Copy this code and paste it into the form left.", 'wpcf7_honeypot' ) ); ?><br /><input type="text" name="honeypot" class="tag" readonly="readonly" onfocus="this.select()" /></div>
			</form>
		</div>
	<?php }
}