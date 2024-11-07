<?php
/**
 * Class Honeypot4CF7_Data_Table
 *
 * @package Honeypot4CF7
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Honeypot4cf7_Data_Table' ) && class_exists( 'WP_List_Table' ) ) {
	class Honeypot4cf7_Data_Table extends WP_List_Table {
		public function get_columns() {
			return array(
				'title'     => __( 'Title', 'contact-form-7-honeypot' ),
				'shortcode' => __( 'Shortcode', 'contact-form-7-honeypot' ),
				'honeypot'  => __( 'Honeypot', 'contact-form-7-honeypot' ),
				'date'      => __( 'Date', 'contact-form-7-honeypot' ),
				'action'    => __( 'Action', 'contact-form-7-honeypot' ),
			);
		}

		public function prepare_items() {
			$per_page = 20;
			$columns  = $this->get_columns();
			$hidden   = array();
			$sortable = array();
			$this->_column_headers = array( $columns, $hidden, $sortable );

			$data = array();

			$cf7_forms = get_posts( array(
				'post_type'      => 'wpcf7_contact_form',
				'posts_per_page' => -1,
			) );

			foreach ( $cf7_forms as $cf7_form ) {
				$data[] = array(
					'title'     => $cf7_form->post_title,
					'id'        => $cf7_form->ID,
				);
			}

			$this->items = $data;

			$this->set_pagination_args( array(
				'total_items' => count( $data ),
				'per_page'    => $per_page,
			) );
		}

		public function column_default( $item, $column_name ) {
			switch ( $column_name ) {
				case 'title':
					return $item['title'];
				case 'shortcode':
					$cf7_title = get_the_title( $item['id'] );
					$cf7_hash  = get_post_meta( $item['id'], '_hash', true );

					return '[contact-form-7 id="' . substr( $cf7_hash, 0, 7 ) . '" title="' . $cf7_title . '"]';
				case 'honeypot':
					$cf7_form = get_post_meta( $item['id'], '_form', true );

					$regx = '/\[honeypot [a-zA-Z]/i';
					preg_match_all( $regx, $cf7_form, $matches );
					if ( ! empty( $matches[0] ) ) {
						return __( 'yes', 'contact-form-7-honeypot' );
					}

					return __( 'no', 'contact-form-7-honeypot' );
				case 'date':
					return get_the_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $item['id'] );
				case 'action':
					$edit_url = add_query_arg(
						array(
							'page' => 'wpcf7',
							'post' => $item['id'],
							'action' => 'edit',
						),
						admin_url( 'admin.php' )
					);
					return '<a href="' . esc_attr( $edit_url ) . '">' . __( 'Edit', 'contact-form-7-honeypot' ) . '</a>';
				default:
					return print_r( $item, true );
			}
		}
	}
}
