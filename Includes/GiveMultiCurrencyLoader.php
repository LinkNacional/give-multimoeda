<?php
namespace Lkn\GiveMultimoedas\Includes;

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://www.linknacional.com.br/
 * @since      1.0.0
 *
 * @package    Give_Cielo_Qr_Code
 * @subpackage Give_Cielo_Qr_Code/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Give_Cielo_Qr_Code
 * @subpackage Give_Cielo_Qr_Code/includes
 * @author     Link Nacional <ticket@linknacional.com>
 */
final class GiveMultiCurrencyLoader {
    /**
     * The array of actions registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
     */
    protected $actions;

    /**
     * The array of filters registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
     */
    protected $filters;

    /**
     * The array of shortcodes registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $endpoints
     */
    protected $endpoints;

    /**
     * Initialize the collections used to maintain the actions, filters, and endpoints.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->actions = array();
        $this->filters = array();
        $this->endpoints = array();
    }

    /**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             The name of the WordPress action that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the action is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
     * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
     */
    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1): void {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             The name of the WordPress filter that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the filter is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
     * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
     */
    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1): void {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * @param  string $namespace
     * @param  string $route
     * @param  string $method
     * @param  callable $callback
     * @param  string $permission_callback
     *
     * @return void
     */
    public function add_endpoint($namespace, $route, $method, $callback, $permission_callback): void {
        $this->endpoints[] = array(
            'namespace' => $namespace,
            'route' => $route,
            'method' => $method,
            'callback' => $callback,
            'permission_callback' => $permission_callback
        );
    }

    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     *
     * @since    1.0.0
     * @access   private
     * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
     * @param    string               $hook             The name of the WordPress filter that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the filter is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         The priority at which the function should be fired.
     * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
     * @return   array                                  The collection of actions and filters registered with WordPress.
     */
    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {
        $hooks[] = array(
            'hook' => $hook,
            'component' => $component,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args
        );

        return $hooks;
    }

    /**
     * Register the filters and actions with WordPress.
     *
     * @since    1.0.0
     */
    public function run(): void {
        foreach ($this->filters as $hook) {
            add_filter($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }

        foreach ($this->actions as $hook) {
            add_action($hook['hook'], array($hook['component'], $hook['callback']), $hook['priority'], $hook['accepted_args']);
        }

        foreach ($this->endpoints as $endpoint) {
            add_action('rest_api_init', function () use ($endpoint): void {
                register_rest_route(
                    $endpoint['namespace'],
                    $endpoint['route'],
                    array(
                        'methods' => $endpoint['method'],
                        'callback' => $endpoint['callback'],
                        'permission_callback' => $endpoint['permission_callback']
                    )
                );
            });
        }
    }
}
