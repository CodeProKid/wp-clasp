<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 3/19/17
 * Time: 11:03 PM
 */

namespace Clasp;


class Context {

	private $instance;

	public static $template_type = '';

	public static $content_type = '';

	public function __construct() {

		if ( ! isset( $this->instance ) && ( ! $this->instance instanceof Context ) ) {
			$this->instance = new Context();
			$this->set_content_type();
			$this->set_template_type();
		}

		return $this->instance;

	}

	/**
	 * Sets the $content_type var for storing the content type of the current page. This will be used later when
	 * we are creating dynamic hooks for creating our templates.
	 *
	 * @access public
	 * @return string $type
	 */
	private function set_content_type() {

		if ( is_singular() ) {
			$type = get_post_type();
		} elseif ( is_tax() ) {
			$queried_object = get_queried_object();
			$type = $queried_object->taxonomy;
		} elseif ( is_archive() ) {
			if ( is_author() ) {
				$type = 'author';
			} else {
				$type = get_post_type();
			}
		} else {
			$type = '';
		}

		self::$content_type = $type;

	}

	/**
	 * Sets the $template_type var for storing the template type of the current page. This will be used later when
	 * we are creating dynamic hooks for creating our templates
	 *
	 * @access public
	 * @return void
	 */
	public function set_template_type() {

		if ( is_singular() ) {
			$type = 'single';
		} elseif ( is_tax() ) {
			$type = 'taxonomy';
		} elseif ( is_search() ) {
			$type = 'search';
		} elseif ( is_404() ) {
			$type = '404';
		} elseif ( is_archive() ) {
			$type = 'archive';
		} else {
			$type = '';
		}

		self::$template_type = $type;

	}

}