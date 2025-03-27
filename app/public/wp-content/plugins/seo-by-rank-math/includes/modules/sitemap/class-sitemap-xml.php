<?php
/**
 * Render XML output for sitemaps.
 *
 * @since      0.9.0
 * @package    RankMath
 * @subpackage RankMath\Sitemap
 * @author     Rank Math <support@rankmath.com>
 *
 * @copyright Copyright (C) 2008-2019, Yoast BV
 * The following code is a derivative work of the code from the Yoast(https://github.com/Yoast/wordpress-seo/), which is licensed under GPL v3.
 */

namespace RankMath\Sitemap;

use RankMath\Helper;
use RankMath\Traits\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Sitemap XML class.
 */
class Sitemap_XML extends XML {

	use Hooker;

	/**
	 * Hold sitemap type.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Sitemap cache.
	 *
	 * @var Cache
	 */
	public $cache;

	/**
	 * Hold the current page.
	 *
	 * @var int
	 */
	private $current_page = 1;

	/**
	 * Content of the sitemap to output.
	 *
	 * @var string
	 */
	protected $sitemap = '';

	/**
	 * Whether or not the XML sitemap was served from cache or not.
	 *
	 * @var boolean
	 */
	private $transient = false;

	/**
	 * Holds the stats for the sitemap generation.
	 *
	 * @var array
	 */
	private $stats = [];

	/**
	 * The Constructor.
	 *
	 * @param string $type Sitemap type.
	 */
	public function __construct( $type ) {
		remove_all_actions( 'widgets_init' );
		$this->filter( 'user_has_cap', 'filter_user_has_cap' );

		$this->type  = $type;
		$this->cache = new Cache();
		$this->set_n( get_query_var( 'sitemap_n' ) );
		$this->output();
	}

	/**
	 * Generate sitemap now.
	 */
	private function output() {
		global $wp_query;

		$this->init_stats();
		if ( ! $this->has_sitemap_in_cache() ) {
			$this->build_sitemap();
		}

		if ( empty( $this->sitemap ) ) {
			$wp_query->set_404();
			status_header( 404 );
			return;
		}

		$this->send_headers();
		echo $this->sitemap; // phpcs:ignore
		$this->output_credits();
		remove_all_actions( 'wp_footer' );
		die;
	}

	/**
	 * Output XML credits.
	 */
	private function output_credits() {
		if ( ! $this->do_filter( 'sitemap/remove_credit', false ) ) {
			echo "\n<!-- XML Sitemap generated by Rank Math SEO Plugin (c) Rank Math - rankmath.com -->";
		}

		if ( ! WP_DEBUG_DISPLAY || ! WP_DEBUG ) {
			return;
		}

		$time     = timer_stop( 0, 3 );
		$sql      = get_num_queries() - $this->stats['query'];
		$memory   = size_format( memory_get_usage() - $this->stats['memory'], 2 );
		$template = $this->transient ? 'Served from cache in %s second(s) (Memory usage: %s)' : 'This sitemap was originally generated in %s second(s). (Memory usage: %s) - %s queries';
		$template = sprintf( $template, $time, $memory, $sql );
		echo "\n<!-- {$template} -->"; // phpcs:ignore

		if ( defined( 'SAVEQUERIES' ) && SAVEQUERIES ) {
			$queries = print_r( $GLOBALS['wpdb']->queries, true ); // phpcs:ignore
			echo "\n<!-- {$queries} -->"; // phpcs:ignore
		}
	}

	/**
	 * Try to get the sitemap from cache.
	 *
	 * @return boolean If the sitemap has been retrieved from cache.
	 */
	private function has_sitemap_in_cache() {
		$this->transient = false;
		if ( true !== Sitemap::is_cache_enabled() ) {
			return false;
		}

		$this->sitemap = $this->cache->get_sitemap( $this->type, $this->current_page );

		if ( ! empty( $this->sitemap ) ) {
			$this->transient = true;
			return true;
		}

		// No cache was found, refresh it because cache is enabled.
		$this->build_sitemap();
		if ( ! empty( $this->sitemap ) ) {
			return $this->cache->store_sitemap( $this->type, $this->current_page, $this->sitemap );
		}

		return false;
	}

	/**
	 * Attempts to build the requested sitemap.
	 */
	public function build_sitemap() {
		$generator     = new Generator();
		$this->sitemap = $generator->get_output( $this->type, $this->current_page );
	}

	/**
	 * Set the sitemap current page to allow creating partial sitemaps with wp-cli
	 * in a one-off process.
	 *
	 * @param integer $current_page The part that should be generated.
	 */
	public function set_n( $current_page ) {
		if ( ! empty( $current_page ) && is_scalar( $current_page ) ) {
			$this->maybe_redirect( $current_page );
			$this->current_page = max( intval( $current_page ), 1 );
		}
	}

	/**
	 * Inits building some sitemap generation stats.
	 */
	private function init_stats() {
		timer_start();
		$this->stats['query']  = get_num_queries();
		$this->stats['memory'] = memory_get_usage();
	}

	/**
	 * Redirect page if $current_page has leading zeros.
	 *
	 * @param  mixed $current_page The page number.
	 * @return void
	 */
	private function maybe_redirect( $current_page ) {
		// Redirect when there are only zeros.
		if ( '' !== $current_page && intval( $current_page ) < 1 ) {
			Helper::redirect( preg_replace( '/0+\.xml$/', '.xml', Helper::get_current_page_url() ) );
			die();
		}

		// Redirect when there are leading zeros.
		$zeros_stripped = ltrim( $current_page, '0' );
		if ( (string) $zeros_stripped !== (string) $current_page ) {
			Helper::redirect( preg_replace( '/' . preg_quote( $current_page, '/' ) . '\.xml$/', $zeros_stripped . '.xml', Helper::get_current_page_url() ) );
			die();
		}
	}
}
