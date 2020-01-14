<?php
/*
Plugin Name: Web Crawler
Plugin URI: https://sps.vn
Description: Scan and collect information from any url. Customize the information fields you need in a very flexible way.
Author: spsdev
Author URI: http://dev.sps.vn
Version: 1.0
Text Domain: webcrl
*/
if (!defined('ABSPATH')) exit;

define('WEBCRL_PLUGIN_FILE', __FILE__);
define('WEBCRL_URL', untrailingslashit(plugins_url( '', WEBCRL_PLUGIN_FILE)));
define('WEBCRL_PATH', dirname(WEBCRL_PLUGIN_FILE));
define('WEBCRL_BASE', plugin_basename(WEBCRL_PLUGIN_FILE));

require WEBCRL_PATH . '/PhpSpreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Web_Crawler {

	public function __construct() {
		register_activation_hook( WEBCRL_PLUGIN_FILE, array( $this, 'activate' ) );
		/*
		Khởi tạo các biến nếu cần thiết
		 */
		
		$this->include();
		
		/*
		 Gọi các hook
		 */
		$this->hooks();
	}

	public function enqueue_scripts() {
		wp_enqueue_style('webcrl', WEBCRL_URL . '/style.css');
		wp_enqueue_script('webcrl', WEBCRL_URL . '/script.js', array('jquery'), '', true);
		$webcrl = array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('webcrl')
		);
		wp_localize_script('webcrl', 'webcrl', $webcrl);
	}

	public function crawler_page($template) {
		
		if( get_query_var( 'webcrawler', false ) !== false ) {
			$new_template = locate_template( array( 'web-crawler-page.php' ) );
			if( '' != $new_template ) {
				$template = $new_template;
			} else {
				$new_template = WEBCRL_PATH . '/web-crawler-page.php';
				if(file_exists($new_template)) {
					$template = $new_template;
				}
			}

		}

		return $template;
	}

	public function rewrites() {

		add_rewrite_endpoint( 'webcrawler', EP_ALL );
 
		if(get_transient( 'webcrl_flush' )) {
			delete_transient( 'webcrl_flush' );
			flush_rewrite_rules();
		}
	}

	public function webcrl_scan() {
		// source url
		$su = isset($_REQUEST['su']) ? esc_url_raw(untrailingslashit($_REQUEST['su'])) : '';

		// detail url pattern
		$dup = isset($_REQUEST['dup']) ? wp_unslash(sanitize_text_field($_REQUEST['dup'])) : '';
		$eup = isset($_REQUEST['eup']) ? wp_unslash(sanitize_text_field($_REQUEST['eup'])) : '';

		$return = array(
			'current_crawled' => '',
			'next_crawl' => array(),
			'error' => '',
			'links' => array()
		);

		$su_parser = parse_url($su);
		$domain_pattern = '/(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]/';

		if( isset($su_parser['host']) && preg_match($domain_pattern, $su_parser['host']) && $dup!='' ) {
			update_option( 'webcrl_su', $su );
			update_option( 'webcrl_dup', $dup );
			update_option( 'webcrl_eup', $eup );
			
			$urls = get_option( 'webcrl_urls', array() );
			$crawled = get_option( 'webcrl_crawled', array() );
			
			if( preg_match("/" . $dup . "/", $su, $matchs) ) {
				$urls[] = $matchs[0];
			}
			
			$crawled[] = $su;
			$return['current_crawled'] = $su;
			$next_crawl = array();

			$html = file_get_html($su);

			if( $html instanceof simple_html_dom ) {
				foreach( $html->find('a') as $element ) {
					if($element->href!='') {
						$href = untrailingslashit($element->href);

						$href_parser = parse_url($href);

						$get_href = '';

						if( isset($href_parser['host']) ) {
							if($href_parser['host']==$su_parser['host']) {
								$get_href = esc_url_raw($href);
							}
						} else {
							if( isset($href_parser['scheme']) && $href_parser['scheme']!='http' && $href_parser['scheme']!='https' ){
								$get_href = '';
							} else {
								if ( preg_match('/^\/[^\/]+.*/', $href) ) {
									$get_href = $su_parser['host'].'/'.ltrim($href,'/');
								} else if ( preg_match('/^\/\/[^\/]+.*/', $href) ) {
									$get_href = 'http:'.$href;
								} else if ( preg_match('/[^\:]*\:[^\:]*/', $href) ) {
									$get_href = '';
								} else if (  preg_match('/^\#/', $href)  ) {
									$get_href = '';
								} else {
									$get_href = $su.'/'.ltrim($href,'/');
								}
							}
						
						}

						if( $get_href != '' ) {
							$get_href = esc_url_raw( $get_href );
						}

						if($eup!='') {
							if($get_href != '') {
								if( preg_match("/" .$eup . "/", $get_href) ) {
									//$return['error'] .= 'Exclude: '.$get_href.'<br>';
									$get_href = '';
								}
							}
						}

						if( $get_href != '' ) {
							//$return['error'] .= 'Log: '.$dup.'<br>';
							if( preg_match("/" . $dup . "/", $get_href, $matchs) ) {

								if( !in_array($matchs[0], $urls) ) {
									//$return['error'] .= 'Log: '.$matchs[0].'<br>';
									$urls[] = $matchs[0];
								}
							}

							$urls = array_values(array_unique($urls));

							if( !in_array($get_href, $crawled) ) {
								$next_crawl[] = $get_href;
							}
						}
						update_option('webcrl_urls', $urls);
						update_option('webcrl_crawled', $crawled);
						$return['next_crawl'] = array_values(array_unique($next_crawl));
					}
				}
			} else {
				$return['error'] = 'Nguồn không lấy được!';
			}
			$return['links'] = $urls;
		} else {
			$return['error'] = 'Lỗi URL nguồn!';
		}

		wp_send_json($return);
		die;
	}

	public function webcrl_urls() {
		$urls = get_option('webcrl_urls', array());
		if(!empty($urls)) {
			foreach ($urls as $key => $value) {
				echo '<li>'.esc_url($value).'</li>';
			}
		}
		die;
	}

	public function webcrl_remove_urls() {
		/*
		 Xóa dữ liệu quét cũ trước khi quét mới
		 */
		update_option('webcrl_urls', array());
		update_option('webcrl_crawled', array());
		die;
	}

	public function hooks() {
		
		add_action('wp_ajax_webcrl_scan', array($this, 'webcrl_scan'));
		add_action('wp_ajax_nopriv_webcrl_scan', array($this, 'webcrl_scan'));

		add_action('wp_ajax_webcrl_urls', array($this, 'webcrl_urls'));
		add_action('wp_ajax_nopriv_webcrl_urls', array($this, 'webcrl_urls'));

		add_action('wp_ajax_webcrl_remove_urls', array($this, 'webcrl_remove_urls'));
		add_action('wp_ajax_nopriv_webcrl_remove_urls', array($this, 'webcrl_remove_urls'));

		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );

		add_action( 'template_include', array($this, 'crawler_page') );
		add_filter( 'init', array( $this, 'rewrites' ) );
	}

	public function include() {
		require_once WEBCRL_PATH . '/simplehtmldom/simple_html_dom.php';
		require_once WEBCRL_PATH . '/simplehtmldom/simple_html_dom.php';
	}

	public function activate() {
		set_transient( 'webcrl_flush', 1, 60 );
	}

}
new Web_Crawler;