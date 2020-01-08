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

class Web_Crawler {

	public function __construct() {
		register_activation_hook( WEBCRL_PLUGIN_FILE, array( $this, 'activate' ) );
		/*
		Khởi tạo các biến nếu cần thiết
		 */
		
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
		$su = isset($_REQUEST['su']) ? sanitize_url($_REQUEST['su']) : '';

		// detail url pattern
		$dup = isset($_REQUEST['dup']) ? sanitize_url($_REQUEST['dup']) : '';

		echo '<p>Ajax quét các url từ "'.esc_url($su).'". Kết quả trả về là một danh sách các url trang cần trích xuất thông tin chi tiết. Để lấy được danh sách url đó là dựa vào biểu mẫu "'.esc_html($dup).'". Danh sách url này sẽ được lưu lại để dùng cho bước tiếp theo.</p>';
		die;
	}

	public function hooks() {
		add_action('wp_ajax_webcrl_scan', array($this, 'webcrl_scan'));
		add_action('wp_ajax_nopriv_webcrl_scan', array($this, 'webcrl_scan'));

		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );

		add_action( 'template_include', array($this, 'crawler_page') );
		add_filter( 'init', array( $this, 'rewrites' ) );
	}

	public function activate() {
		set_transient( 'webcrl_flush', 1, 60 );
	}

}
new Web_Crawler;