<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       
 * @since      1.0.0
 *
 * @package    GlitterCape
 * @subpackage GlitterCape/includes
 */

class Cape_Loader {

	protected $actions;

	protected $filters;

	public function __construct() {

		$this->actions = array();
		$this->filters = array();

	}

	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);
		return $hooks;

	}

	//executes all the actions and filters
	public function run() {

		if( count($this->filters) !== 0 ){
			foreach ( $this->filters as $hook ) {
			add_filter( $hook[ 'hook' ], array( $hook[ 'component' ], $hook[ 'callback' ] ), $hook[ 'priority' ], $hook[ 'accepted_args' ] );
			}	
		} 
		
		if( count( $this->actions ) !== 0 ){
			foreach ( $this->actions as $hook ) {
				add_action( $hook[ 'hook' ], array( $hook[ 'component' ], $hook[ 'callback' ] ), $hook[ 'priority' ], $hook[ 'accepted_args' ] );
			}	
		} 
	}

}
