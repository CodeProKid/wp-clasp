<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 3/19/17
 * Time: 11:24 PM
 */

namespace Clasp;


abstract class Template {

	protected $hook_prefix = '';

	public function __construct( $template_type = '', $content_type = '', $markup ) {
		$this->build_prefix( $template_type, $content_type );
		$this->set_markup_config( $markup );
	}

	protected function build_prefix( $template_type, $content_type ) {

		$prefix = '';

		if ( ! empty ( $template_type ) ) {
			$prefix .= $template_type . '_';
		}

		if ( ! empty( $content_type ) ) {
			$prefix .= $content_type . '_';
		}

		$this->hook_prefix = $prefix;

	}

	private function set_markup_config( $markup ) {

		global $clasp_markup;

		if ( empty( $clasp_markup ) ) {
			$clasp_markup = [];
		}

		if ( is_array( $markup ) && ! empty( $markup ) ) {
			$clasp_markup[ $this->hook_prefix ] = $markup;
		}

	}

	protected function get_markup( $id ) {

		global $clasp_markup;

		if ( isset( $clasp_markup[ $id ] ) ) {
			return $clasp_markup[ $id ];
		} else {
			return [];
		}

	}

}