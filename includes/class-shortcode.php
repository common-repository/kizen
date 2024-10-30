<?php
/**
 * Class shortcode.
 *
 * @author   Kizen
 * @package  kizen
 * @version  1.0
 */

defined('ABSPATH') || exit; // Exit if accessed directly

if ( !class_exists('kizen_shortcode', false) ) :

/**
 * Class Menu.
 */
class kizen_shortcode {

	/**
	 * Hook in admin menu.
	 */
	public function __construct() {
		add_shortcode('kizen-form', [$this, 'register_shortcode']);
	}

	/**
	 * Init the admin page.
	 */
	public function register_shortcode($atts) {
		$default = [
			'id'	=> '',
			'type'	=> 'form',
		];

		$data = shortcode_atts($default, $atts);

		if (!$data['id']) {
			return false;
		}

		echo "<script type=\"text/javascript\">KIZEN('embedForm', '". esc_url('https://cdn.usekzn.com/'.$data['type'].'/'.$data['id']) ."');</script>";
	}
}

endif;


// call shortcode
return new kizen_shortcode();
