<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 3/19/17
 * Time: 10:37 PM
 */

namespace Clasp\Util;


class Functions {

	/**
	 * Stores the base string for all of the template hooks
	 *
	 * @var string $hook_base
	 * @access public
	 */
	public static $hook_base = 'clasp_template_';

	/**
	 * Stores the base string for the priority filter
	 *
	 * @var string $filter_base
	 * @access public
	 */
	public static $filter_base = 'clasp_priority_';

	/**
	 * Stores all of the filtered priorities for callbacks
	 *
	 * @var $filtered_priorities array
	 * @access public
	 */
	public static $filtered_priorities = array();

	private $instance;

	public function __construct( $context ) {

		if ( ! isset( $this->instance ) && ( ! $this->instance instanceof Functions ) ) {
			
		}

		return $this->instance;

	}

	/**
	 * Handles the creation of template hooks
	 *
	 * @param string $hook_name The name of the hook you want to create
	 * @param array $context Arguments you want to pass to the hook as context
	 * @access public
	 * @return void
	 */
	public static function create_hook( $hook_name, $context = array() ) {
		do_action( self::$hook_base . $hook_name, $context );
	}

	/**
	 * Handles the adding of a single hook to an action created with create_hook
	 *
	 * @uses add_action
	 * @param string $hook_name Name of the hook you want to hook into (without prefix)
	 * @param mixed|string|array|callable $callback The callback to add to the hook
	 * @param int $priority Order in which you would like the callback to execute
	 * @param int $args Number of arguments passed from the hook to the callback
	 * @access public
	 * @return void
	 */
	public static function add_callback( $hook_name, $callback, $priority = 10, $args = 1 ) {

		// We only want the string name of the function or method for the priority filter below,
		// so if the callback is a method within a class, just grab the method name.
		if ( is_array( $callback ) ) {
			$func = $callback[1];
		} else {
			$func = $callback;
		}

		add_action(
			self::$hook_base . $hook_name,
			$callback,

			/**
			 * dfm_priority_$hook_name_$function
			 *
			 * Filter to change the order in which a callback gets executed. An example of what the filter tag might
			 * look like when fully build is as follows:
			 *
			 * Assuming the name of the hook is "article_loop" and the function name is "do_loop",
			 * the filter tag will be "dfm_priority_article_loop_do_loop".
			 *
			 * @param int $priority The priority set when adding the callback to the hook
			 * @param string $hook_name Name of the hook (without prefix) for context
			 * @param string $func Name of the callback function or method
			 * @return int $priority Returns the priority integer
			 */
			apply_filters( self::$filter_base . $hook_name . '_' . $func, $priority, $hook_name, $func ),
			$args
		);
	}

	/**
	 * Wrapper for add_callback to register multiple hooks at once.
	 *
	 * @uses self::add_hook
	 *
	 * @param string $hook_name Name of the hook you want to hook into
	 * @param array $callbacks Array of callbacks with their respective priorities. {
	 * 		@param int $priority The priority the callback should have when executing within the hook. Key of the array
	 * 		@param mixed|string|array|callable $callback The callback function to be added to the hook. Value of the array.
	 *
	 * 		Should take the form of the example below. The priority is the key, and the callback is the value.
	 * 		array(
	 * 			10 => 'dfm_callback_1`,
	 * 			15 => array( $this, 'dfm_callback_2' ),
	 * 		);
	 * }
	 * @param int $args
	 * @access public
	 * @return void
	 */
	public static function add_callbacks( $hook_name, $callbacks, $args = 1 ) {

		if ( is_array( $callbacks ) && ! empty( $callbacks ) ) {
			foreach ( $callbacks as $priority => $callback ) {
				self::add_callback( $hook_name, $callback, $priority, $args );
			}
		}

	}

	/**
	 * Will remove a callback from a hook if a callback is provided. It will remove all callbacks from a
	 * specified hook if no callback is provided.
	 *
	 * @param string $hook_name Name of the hook you would like to remove your callback from
	 * @param mixed|string|array|callable $callback The callback you would like to remove (optional)
	 * @param int $priority Priority of the callback you would like to remove
	 * @access public
	 * @return void
	 */
	public static function remove_callback( $hook_name, $callback = '', $priority = 10 ) {

		if ( is_array( $callback ) ) {
			$func = $callback[1];
		} else {
			$func = $callback;
		}

		if ( ! empty( $callback ) ) {
			remove_action(
				self::$hook_base . $hook_name,
				$callback,
				// @see self::add_hook()
				apply_filters( self::$filter_base . $hook_name . '_' . $func, $priority, $hook_name, $func )
			);
		} else {
			remove_all_actions( self::$hook_base . $hook_name );
		}

	}

	/**
	 * Wrapper for remove_callback to remove multiple callbacks at once. Similar to add_hooks
	 *
	 * @uses self::remove_hook()
	 * @see self::add_hooks()
	 *
	 * @param string $hook_name Name of the hook you want to remove callbacks from
	 * @param array $callbacks Array of callbacks that you would like to remove with their priority as the key.
	 * Same format as the add_hooks method, see that method if you are unsure of the format.
	 * @access public
	 * @return void
	 */
	public static function remove_callbacks( $hook_name, $callbacks ) {

		if ( is_array( $callbacks ) && ! empty( $callbacks ) ) {
			foreach ( $callbacks as $priority => $callback ) {
				self::remove_callback( $hook_name, $callback, $priority );
			}
		}

	}

	/**
	 * Handles the reordering of callbacks for a hook.
	 *
	 * @param string $hook_name Name of the hook the callback is hooked to
	 * @param mixed|string|array|callable $callback Callback function you want to change the priority for
	 * @param int $new_priority New integer priority you want the callback to be called in
	 * @access public
	 * @return void
	 */
	public static function reorder_callback( $hook_name, $callback, $new_priority ) {

		// Create a new value for the new priority for this callback
		self::$filtered_priorities[ $hook_name . '_' . $callback ] = $new_priority;

		add_filter( self::$filter_base . $hook_name . '_' . $callback, function( $priority, $hook_name, $callback ) {

			// grab the new priority and set it to a local variable.
			$new_priority = self::$filtered_priorities[ $hook_name . '_' . $callback ];

			// Only use the new priority if it's a usable value
			if ( ! empty( $new_priority ) ) {
				$priority = $new_priority;
			}

			return (int) $priority;

		}, 10, 3 );

	}

	/**
	 * Wrapper for reorder_callback to reorder multiple callbacks at once. Similar to add_hooks and remove_hooks
	 *
	 * @uses self::reorder_hook()
	 * @see self::add_hooks()
	 *
	 * @param string $hook_name Name of the hook the callback is hooked to
	 * @param array $callbacks Array of callbacks that you would like to reorder with their *NEW* priority as the key.
	 * Same format as the add_hooks method, see that method if you are unsure of the format.
	 * @access public
	 * @return void
	 */
	public static function reorder_callbacks( $hook_name, $callbacks ) {

		if ( is_array( $callbacks ) && ! empty( $callbacks ) ) {
			foreach ( $callbacks as $priority => $callback ) {
				self::reorder_callback( $hook_name, $callback, $priority );
			}
		}

	}

	/**
	 * Method to check whether or not a hook has any callbacks attached to it. A callback can be passed to check for
	 * a specific callback.
	 *
	 * @uses has_action()
	 *
	 * @param string $hook_name Name of the hook you want to check
	 * @param mixed|string|array|callable $callback Callback function to check for
	 * @access public
	 * @return bool|int
	 */
	public static function has_hooks( $hook_name, $callback = false ) {
		return has_action( self::$hook_base . $hook_name, $callback );
	}

}