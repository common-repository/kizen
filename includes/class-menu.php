<?php
/**
 * Setup menus in WP admin.
 *
 * @author   Kizen
 * @package  kizen
 * @version  1.0
 */

defined('ABSPATH') || exit; // Exit if accessed directly

if ( !class_exists('kizen_menu', false) ) :

/**
 * Class Menu.
 */
class kizen_menu {

	/**
	 * Hook in admin menu.
	 */
	public function __construct() {
		add_action('admin_menu', [$this, 'admin_menu']);
	}

	/**
	 * Init the admin page.
	 */
	public function admin_menu() {
		global $kizen;

		// register our setting.
		register_setting('kizen', 'kizen_basic', 'kizen_basic_sanitize');

		add_options_page(
			__('Kizen', 'kizen'),
			__('Kizen', 'kizen'),
			'manage_options',
			$kizen->option_page,
			[$this, 'admin_menu_page'],
		);
	}

	/**
	 * Sanitize Basic Settings
	 * This function is defined in register_setting().
	 */
	public function admin_basic_sanitize($settings) {
		$settings = sanitize_text_field($settings);
		return $settings;
	}

	/**
	 * Init the admin page.
	 */
	public function admin_menu_page() {
		global $kizen; ?>
		<div class="wrap">
			<?php if ( isset($_POST['action']) && $_POST['action'] == 'update') { $this->submit_data(); } ?>

			<?php $data = get_option($kizen->option_name); ?>

			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>

			<?php settings_errors(); ?>

			<form id="kizen" method="post">
				<?php settings_fields('kizen'); ?>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="business_id"><?php _e('Business ID', 'kizen'); ?> *</label>
							</th>
							<td>
								<input name="kizen[business_id]" type="text" id="business_id" class="regular-text code" value="<?php echo esc_html($data['business_id']); ?>" required>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="track_clicks"><?php _e('Track clicks', 'kizen'); ?></label>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span>Search engine visibility</span></legend>
									<label for="track_clicks">
										<input name="kizen[track_clicks]" type="checkbox" id="track_clicks" <?php echo ($data['track_clicks'] && $data['track_clicks'] == 'on') ? 'checked' : ''; ?>>
										view your contacts' clicks on your website.
									</label>
								</fieldset>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="track_views"><?php _e('Track views', 'kizen'); ?></label>
							</th>
							<td>
								<fieldset>
									<legend class="screen-reader-text"><span>Search engine visibility</span></legend>
									<label for="track_views">
										<input name="kizen[track_views]" type="checkbox" id="track_views" <?php echo ($data['track_views'] && $data['track_views'] == 'on') ? 'checked' : ''; ?>>
										view your contacts’ website impressions.
									</label>
								</fieldset>
							</td>
						</tr>
					</tbody>
				</table>

				<?php submit_button(esc_attr('Save Changes'), 'primary', 'submit', false);?>
			</form>
			<div>
				<p>
					<div>Example:</div>
					<span>Paste the code in template: </span>
					<code>&lt;?php do_shortcode('[kizen-form id="iuNLKOTy" type="survey"]'); ?&gt;</code>
				</p>
			</div>
			<div class="clear"></div>
		</div><!-- .wrap -->
	<?php }

	/**
	 * Submit Action.
	 */
	public function submit_data() {
		global $kizen;

		$is_valid_nonce = ( isset($_POST['_wpnonce']) && wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'kizen-options') ) ? true : false;

		// exit depending on the save status or if the nonce is not valid
		if ( !$is_valid_nonce ) {
			$message = '<strong>ERROR:</strong> Settings not updated.';
			add_settings_error('kizen_notice', 'kizen_notice', $message, 'error');
			return false;
		}

		if ( isset($_POST['action']) && !empty($_POST['action'] == 'update') )
		{
			$business_id = (isset($_POST['kizen']['business_id'])) ? sanitize_text_field($_POST['kizen']['business_id']) : '';

			if ( !wp_is_uuid($business_id) ) {
				add_settings_error('kizen_notice', 'kizen_notice', '<strong>ERROR:</strong> Business ID not valid.', 'error');
				return false;
			}

			$data_option = [
				'business_id'	=> $business_id,
				'track_clicks'	=> (isset($_POST['kizen']['track_clicks'])) ? 'on' : 'off',
				'track_views'	=> (isset($_POST['kizen']['track_views'])) ? 'on' : 'off',
			];

			update_option($kizen->option_name, $data_option);

			add_settings_error('kizen_notice', 'kizen_notice', 'Settings updated.', 'updated');
			return false;
		}
		else {
			add_settings_error('kizen_notice', 'kizen_notice', '<strong>ERROR:</strong> Settings not updated.', 'error');
			return false;
		}

		return false;
	}
}

endif;


// call menu
return new kizen_menu();
