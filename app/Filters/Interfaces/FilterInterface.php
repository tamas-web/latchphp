<?php

namespace App\Filters\Interfaces;

/**
 * Filter intreface for data manipulation.
 */
interface FilterInterface {

    /**
     * Add filter to a tag
     *
     * @return bool
     *   True if added successfully.
     *   False if callback is not callable.
     */
	static public function add( string $tag, $callback, int $priority = 10 ) : bool;

    /**
     * Apply all filters to a tag
     *
     * @return mixed
     */
	static public function apply( string $tag, mixed $value, ...$args ) : mixed;

    /**
     * Remove filter from a tag
     *
     * @return bool
     *   True if added successfully.
     *   False if callback is not callable.
     */
	static public function remove( string $tag, $callback, int $priority = 10 ) : bool;

    /**
     * Remove all filters from a tag
     *
     * @return void
     */
	static public function removeAll( string $tag ) : void;

	// idk, if we should implement at this moment, to check if has filters?!?
	//~ static public function has( string $tag ) : bool;

    /**
     * Get all filters for a tag
     *
     * @return array
     *   List of filter callbacks for a tag
     */
	static public function getFiltersForTag( $tag ) : array;

}
