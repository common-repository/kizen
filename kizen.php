<?php
/**
 * Plugin Name: Kizen
 * Plugin URI: https://kizen.com/
 * Description: Use the Kizen Plug-In to embed Kizen Forms and Surveys on your website.
 * Version: 1.0
 * Author: Kizen
 * Author URI: https://kizen.com/
 * Requires at least: 1.0
 * Tested up to: 1.0
 * Tags: kizen
 *
 * Text Domain: kizen
 *
 * @package kizen
 * @category Core
 * @author Kizen
 */

defined('ABSPATH') || exit; // Exit if accessed directly.


if ( !class_exists('kizen') ) :

/**
 * Main Class.
 *
 * @class kizen
 * @version	1.0
 */
final class kizen {

	/**
	 * Version.
	 *
	 * @var string
	 */
	public $version = '1.0';

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	public $plugin_slug = 'kizen';

	/**
	 * Options name.
	 *
	 * @var string
	 */
	public $option_name = 'kizen_option';

	/**
	 * Options name.
	 *
	 * @var string
	 */
	public $option_page = 'kizen-page';

	/**
	 * The single instance of the class.
	 */
	protected static $_instance = null;

	/**
	 * Main Instance.
	 *
	 * Ensures only one instance of kizen is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @return kizen - Main instance.
	 */
	public static function instance() {
		if ( is_null(self::$_instance) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 * @since 1.0
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Hook into actions and filters.
	 * @since  1.0
	 */
	private function init_hooks() {
		add_action('wp_enqueue_scripts', [$this, 'custom_javascript']);
	}

	/**
	 * Define Constants.
	 */
	private function define_constants() {
		$this->define('KIZEN_ABSURL', plugins_url('/', __FILE__ ));
		$this->define('KIZEN_ABSPATH', dirname(__FILE__) . '/');
		$this->define('KIZEN_VERSION', $this->version);
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define($name, $value) {
		if ( !defined($name) ) {
			define($name, $value);
		}
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request($type) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined('DOING_AJAX');
			case 'cron' :
				return defined('DOING_CRON');
			case 'frontend' :
				return ( !is_admin() || defined('DOING_AJAX') ) && !defined('DOING_CRON');
		}
	}

	/**
	 * Include required core files used in admin.
	 */
	public function includes() {
		if ( $this->is_request('admin') ) {
			include_once(KIZEN_ABSPATH . 'includes/class-menu.php');
		}

		if ( $this->is_request('frontend') ) {
			include_once(KIZEN_ABSPATH . 'includes/class-shortcode.php');
		}
	}

	/**
	 * Include required core files used in admin.
	 */
	function custom_javascript() {
		$data = get_option($this->option_name);
		if (isset($data['business_id']) && !empty($data['business_id'])) : ?>
			<script type="text/javascript">
				var config = { 
					business_id: '<?php esc_html_e($data['business_id']); ?>',
					track_clicks: <?php echo isset($data['track_clicks']) ? 'true' : 'false'; ?>,
					track_views: <?php echo isset($data['track_views']) ? 'true' : 'false'; ?>
				};
				(function(d,w,c,s,f,e,a){if(!w.KIZEN){w.KIZEN=f=function(){f.a.push([d.currentScript].concat([].slice.apply(arguments)))};f.a=[];f.c=c;e=d.createElement(s);e.async=!0;a=d.getElementsByTagName(s)[0];e.src="https://cdn.usekzn.com/embed/kzn.js";a.parentNode.insertBefore(e,a)}})(document,window,config,"script");
			</script>
		<?php endif;
	}
}

endif;



/**
 * Main instance of kizen_instance.
 *
 * Returns the main instance of kizen_instance to prevent the need to use globals.
 *
 * @since  1.0
 * @return kizen
 */
function kizen_instance() {
	return kizen::instance();
}

// Global for backwards compatibility.
$GLOBALS['kizen'] = kizen_instance();
