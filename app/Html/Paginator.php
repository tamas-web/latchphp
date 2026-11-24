<?php

namespace App\Html;

class Paginator {

	/**
	 * Total items
	 * @var int
	 */
	protected int $total = 0;

	/**
	 * Limit
	 * @var int
	 */
	protected int $limit = 20;

	/**
	 * Current page
	 * @var int
	 */
	protected int $currentPage = 1;

	/**
	 * Cache variable
	 * @var array first string, second int
	 */
	protected array $cache;

	/**
	 * Element classes
	 * @var array
	 */
	protected array $classes = [
		'ul'        => [ 'pagination' ],
		'li'        => [ 'page-item' ],
		'li.active' => [ 'active' ],
		'a'         => [ 'page-link' ],
	];

	/**
	 * Url
	 * @var string
	 */
	protected string $url = '?';

	/**
	 * Page number placeholder
	 * @var string
	 */
	protected string $pageNumberPlaceholder = 'page=##';

	/**
	 * Previous button html entity or markup
	 * @var string
	 */
	protected string $prevEntity = '&laquo;';

	/**
	 * Next button html entity or markup
	 * @var string
	 */
	protected string $nextEntity = '&raquo;';

	/**
	 * Number of elements to display in the middle of pagination
	 * @var int
	 */
	protected int $midSize = 2;

	/**
	 * Number of elements to display at the end of pagination
	 * @var int
	 */
	protected int $endSize = 1;

	/**
	 * Show all pagination
	 * @var boolean
	 */
	protected bool $showAll = false;

	/**
	 * Show pagination even if only one page is found
	 * @var boolean
	 */
	protected bool $showOnePagePagination = true;

	/**
	 * Create new pagination
	 * @var int $total
	 * @var int $limit
	 * @return self
	 */
	public function __construct( int $total = 0, int $limit = 0 ){

		$this->total = $total;
		$this->limit = $limit;

	}

	/**
	 * Set current page
	 * @var int $currentPage
	 * @return self
	 */
	public function setCurrentPage( int $currentPage ) : self {

		if( $this->getNumberOfPages() < $currentPage ){

			$currentPage = $this->getNumberOfPages();

		} elseif( $currentPage < 1 ){

			$currentPage = 1;

		}

		$this->currentPage = $currentPage;

		$this->deleteCache();

		return $this;

	}

	/**
	 * Set element classes
	 * @var string $element
	 * @var string $class
	 * @return self
	 */
	public function setClass( string $element, string $class ) : self {

		if( isset( $this->classes[ $element ] ) ){

			$this->classes[ $element ][] = $class;

			$this->deleteCache();

		}

		return $this;

	}

	/**
	 * Set previous html entity or markup
	 * @var string $entity
	 * @return self
	 */
	public function setPrevEntity( string $entity ) : self {

		$this->prevEntity = $entity;

		$this->deleteCache();

		return $this;

	}

	/**
	 * Set next html entity or markup
	 * @var string $entity
	 * @return self
	 */
	public function setNextEntity( string $entity ) : self {

		$this->nextEntity = $entity;

		$this->deleteCache();

		return $this;

	}

	/**
	 * Set URL of pagination items. {{pageNumber}}
	 * @var string $entity
	 * @return self
	 */
	public function setURL( string $url ) : self {

		if( false === stripos( $url, '?' ) ){

			$url.= '?';

		}

		$this->url = $url;

		$this->deleteCache();

		return $this;

	}

	/**
	 * Set middle size of pagination
	 * @var int
	 * @return self
	 */
	public function setMidSize( int $midSize ) : self {

		$this->midSize = $midSize;

		$this->deleteCache();

		return $this;

	}

	/**
	 * Set end size of pagination
	 * @var int
	 * @return self
	 */
	public function setEndSize( int $endSize ) : self {

		$this->endSize = $endSize;

		$this->deleteCache();

		return $this;

	}

	/**
	 * Get element classes
	 * @var string $element
	 * @return string
	 */
	public function getClass( string $element ) : string {

		if( isset( $this->classes[ $element ] ) ){

			return join( ' ', $this->classes[ $element ] );

		}

		return '';

	}

	/**
	 * Get the total
	 * @return int
	 */
	public function getTotal() : int {

		return $this->total;

	}

	/**
	 * Get limit
	 * @return int
	 */
	public function getLimit() : int {

		return $this->limit;

	}

	/**
	 * Get current page result offsets
	 * @return int
	 */
	public function getOffset() : int {

		return ( $this->getCurrentPage() - 1 ) * $this->getLimit();

	}

	/**
	 * Get the total number of pages
	 * @return int
	 */
	public function getNumberOfPages() : int {

		if( ! isset( $this->cache['numberofpages'] ) ){

			$this->cache['numberofpages'] = (int) ceil( $this->total / $this->limit );

		}

		return $this->cache['numberofpages'];

	}

	/**
	 * Get the current page number
	 * @return int
	 */
	public function getCurrentPage() : int {

		return $this->currentPage;

	}

	/**
	 * Get the previous page number
	 * @return int
	 */
	public function getPreviousPage() : int {

		$prev = $this->getCurrentPage() - 1;

		return $prev > 1 ? $prev : 1;

	}

	/**
	 * Get the next page number
	 * @return int
	 */
	public function getNextPage() : int {

		$next = $this->getCurrentPage() + 1;

		return $next <= $this->getNumberOfPages() ? $next : $this->getNumberOfPages();

	}

	/**
	 * Delete all cached data
	 * @return self
	 */
	protected function deleteCache() : self {

		unset( $this->cache );

		return $this;

	}

	/**
	 * Replace url value with page number
	 * @var int
	 * @return string
	 */
	protected function formatURL( string $page ) : string {

		// use ? when the base url has no query string yet, otherwise &amp;
		$separator = str_contains( $this->url, '?' ) ? '&amp;' : '?';

		return $this->url . $separator . str_replace( '##', $page, $this->pageNumberPlaceholder );

	}

	/**
	 * Render pagination and cache it
	 * @return string
	 */
	public function render() : void {

		echo $this->buffer();

	}

	/**
	 * Page numbers to display: the leading & trailing `endSize` pages plus a
	 * `midSize` window around the current page. A `null` entry marks a collapsed
	 * gap (rendered as `&hellip;`). Own implementation.
	 * @return array
	 */
	protected function pageWindow() : array {

		$pages   = $this->getNumberOfPages();
		$current = $this->getCurrentPage();

		if( $pages < 1 ){

			return [];

		}

		if( $this->showAll ){

			return range( 1, $pages );

		}

		// collect the page numbers worth showing
		$show = [];

		for( $page = 1; $page <= $pages; ++$page ){

			$leading  = $page <= $this->endSize;
			$trailing = $page > $pages - $this->endSize;
			$around   = $page >= ( $current - $this->midSize ) && $page <= ( $current + $this->midSize );

			if( $leading || $trailing || $around ){

				$show[] = $page;

			}

		}

		// walk the kept pages, inserting a null gap marker wherever the run jumps
		$window = [];
		$prev   = 0;

		foreach( $show as $page ){

			if( $prev && ( $page - $prev ) > 1 ){

				$window[] = null;

			}

			$window[] = $page;
			$prev     = $page;

		}

		return $window;

	}

	/**
	 * Generate the pagination markup
	 * @return string
	 */
	public function buffer() : string {

		if( ! isset( $this->cache['pagination'] ) ){

			$pagination = '';

			if( $this->showOnePagePagination || $this->getNumberOfPages() > 1 ){

				// ul start
				$pagination .= '<ul class="' . $this->getClass( 'ul' ) . '">';

				// previous link
				if( $this->getCurrentPage() < 2 ){

					$pagination .= '
					<li class="' . $this->getClass( 'li' ) . ' disabled">
						<span class="' . $this->getClass( 'a' ) . '">' . $this->prevEntity . '</span>
					</li>';

				} else {

					$pagination .= '
					<li class="' . $this->getClass( 'li' ) . ( $this->getCurrentPage() < 2 ? ' disabled' : '' ) . '">
						<a class="' . $this->getClass( 'a' ) . '" href="' . $this->formatURL( $this->getPreviousPage() ) . '">' . $this->prevEntity . '</a>
					</li>';

				}

				// paginate links - own implementation via pageWindow() (null = collapsed gap)
				$current = $this->getCurrentPage();

				foreach( $this->pageWindow() as $page ){

					if( null === $page ){

						$pagination .= '
						<li class="' . $this->getClass( 'li' ) . ' disabled">
							<span class="page-link">&hellip;</span>
						</li>';

					} elseif( $page === $current ){

						$pagination .= '
						<li class="' . $this->getClass( 'li' ) . ' ' . $this->getClass( 'li.active' ) . '">
							<span class="' . $this->getClass( 'a' ) . ' disabled">' . $page . '</span>
						</li>';

					} else {

						$pagination .= '
						<li class="' . $this->getClass( 'li' ) . '">
							<a class="' . $this->getClass( 'a' ) . '" href="' . $this->formatURL( $page ) . '">' . $page . '</a>
						</li>';

					}

				}

				// next link
				if( $this->getCurrentPage() >= $this->getNumberOfPages() ){

					$pagination .= '
					<li class="' . $this->getClass( 'li' ) . ' disabled">
						<span class="' . $this->getClass( 'a' ) . '">' . $this->nextEntity . '</span>
					</li>';

				} else {

					$pagination .= '
					<li class="' . $this->getClass( 'li' ) . ( $this->getCurrentPage() >= $this->getNumberOfPages() ? ' disabled' : '' ) . '">
						<a class="' . $this->getClass( 'a' ) . '" href="' . $this->formatURL( $this->getNextPage() ) . '">' . $this->nextEntity . '</a>
					</li>';

				}

				// ul end
				$pagination .= '</ul>';

			}

			$this->cache['pagination'] = $pagination;

		}

		return $this->cache['pagination'];

	}

}
