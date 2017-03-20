<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 3/19/17
 * Time: 11:50 PM
 */

namespace Clasp\Util;


class Markup {

	private static $hook_prefix = '';

	private static $default_settings = array();

	public function __construct( $hook_prefix ) {
		self::$hook_prefix = $hook_prefix;
		self::$default_settings = [
			'tag' => 'div',
			'id' => '',
			'classes' => array(),
			'attr' => array(),
		];
	}

	public static function output_start( $id ) {

		$markup_data = self::get_markup_data( $id );

		echo '<' . tag_escape( $markup_data['tag'] ) .
			 ' id="' . esc_attr( $markup_data['id'] ) . '"' .
			 ' class="' . esc_attr( implode( ' ', $markup_data['classes'] ) ) .
			 ' ' . self::attr_builder( $markup_data['attr'] ) .
			 '>';

	}

	public static function output_end( $id ) {

		$markup_data = self::get_markup_data( $id );

		echo '</' . tag_escape( $markup_data['tag'] ) . '>';

	}

	private static function attr_builder( $atts ) {

		if ( is_array( $atts ) && ! empty( $atts ) ) {

			$atts_rendered = [];

			foreach ( $atts as $attr => $value ) {
				$atts_rendered[ $attr ] = tag_escape( $attr ) . '="' . esc_attr( $value ) . '" ';
			}
		} else {
			return '';
		}

		return $atts_rendered;

	}

	private static function get_markup_data( $id ) {

		global $clasp_markup;
		$markup_data = $clasp_markup[ self::$hook_prefix ][ $id ];
		$filtered_markup_data = apply_filters( 'clasp_markup_' . $id, self::$hook_prefix );
		if ( is_array( $filtered_markup_data ) ) {
			$markup_data = $filtered_markup_data;
		}

		return wp_parse_args( $markup_data, self::$default_settings );

	}

}