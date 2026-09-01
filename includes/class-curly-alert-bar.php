<?php
/**
 * Core class for Curly Alert Bar.
 *
 * Registers the admin settings page, the [alert_bar_text] shortcode, and the
 * front-end visibility script. The visible bar itself is an Oxygen 6 element
 * (class .alert-bar) with a close button (class .alert-bar-close); this plugin
 * only controls its content and whether it shows.
 *
 * @package CurlyAlertBar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Curly_Alert_Bar {

	const OPTION_GROUP   = 'curly_alert_bar_group';
	const OPTION_ENABLED = 'alert_bar_enabled';
	const OPTION_TEXT    = 'alert_bar_text';
	const CHAR_LIMIT     = 300;

	/** @var Curly_Alert_Bar|null */
	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_shortcode( 'alert_bar_text', array( $this, 'shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
	}

	/**
	 * Register the top-level Alert Bar admin page.
	 */
	public function register_admin_menu() {
		add_menu_page(
			__( 'Alert Bar', 'curly-alert-bar' ),
			__( 'Alert Bar', 'curly-alert-bar' ),
			'manage_options',
			'alert-bar-settings',
			array( $this, 'render_options_page' ),
			'dashicons-megaphone',
			30
		);
	}

	/**
	 * Register the settings and their sanitizers.
	 */
	public function register_settings() {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_ENABLED,
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default'           => 0,
			)
		);

		register_setting(
			self::OPTION_GROUP,
			self::OPTION_TEXT,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_text' ),
				'default'           => '',
			)
		);
	}

	/**
	 * Allow only a safe subset of HTML and cap length at CHAR_LIMIT.
	 *
	 * @param string $input Raw editor content.
	 * @return string
	 */
	public function sanitize_text( $input ) {
		$allowed_tags = array(
			'a'      => array( 'href' => array(), 'target' => array(), 'title' => array(), 'rel' => array() ),
			'strong' => array(),
			'b'      => array(),
			'em'     => array(),
			'i'      => array(),
		);
		$clean        = wp_kses( (string) $input, $allowed_tags );
		return mb_substr( $clean, 0, self::CHAR_LIMIT );
	}

	/**
	 * Render the admin options page (tinyMCE editor + character counter).
	 */
	public function render_options_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$enabled = get_option( self::OPTION_ENABLED, 0 );
		$text    = get_option( self::OPTION_TEXT, '' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Alert Bar Settings', 'curly-alert-bar' ); ?></h1>
			<p>
				<?php esc_html_e( 'Place the bar on the page with an Oxygen element of class "alert-bar" (with a close button of class "alert-bar-close") and insert the text there using the [alert_bar_text] shortcode.', 'curly-alert-bar' ); ?>
			</p>
			<form method="post" action="options.php">
				<?php settings_fields( self::OPTION_GROUP ); ?>
				<table class="form-table" role="presentation">
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Enable Alert Bar', 'curly-alert-bar' ); ?></th>
						<td>
							<label for="alert_bar_enabled">
								<input type="checkbox" id="alert_bar_enabled" name="<?php echo esc_attr( self::OPTION_ENABLED ); ?>" value="1" <?php checked( 1, $enabled ); ?> />
								<?php esc_html_e( 'Show Alert Bar on site', 'curly-alert-bar' ); ?>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Alert Bar Text', 'curly-alert-bar' ); ?></th>
						<td>
							<?php
							$editor_settings = array(
								'textarea_name' => self::OPTION_TEXT,
								'textarea_rows' => 4,
								'media_buttons' => false,
								'teeny'         => true,
								'quicktags'     => false,
								'tinymce'       => array(
									'toolbar1' => 'bold,italic,link,unlink',
									'toolbar2' => '',
								),
							);
							wp_editor( $text, 'alert_bar_text_editor', $editor_settings );
							?>
							<p id="alert-bar-char-counter" style="margin-top: 8px; font-weight: 600; color: #50575e;">
								<span id="char-count">0</span> / <?php echo (int) self::CHAR_LIMIT; ?> <?php esc_html_e( 'characters', 'curly-alert-bar' ); ?>
							</p>
						</td>
					</tr>
				</table>
				<?php submit_button( __( 'Save Alert Bar', 'curly-alert-bar' ) ); ?>
			</form>
		</div>

		<script>
		document.addEventListener('DOMContentLoaded', function () {
			var maxChars = <?php echo (int) self::CHAR_LIMIT; ?>;
			var countSpan = document.getElementById('char-count');

			function updateCounter(content) {
				var plainText = content.replace(/<[^>]*>/g, '');
				var length = plainText.length;
				countSpan.textContent = length;
				countSpan.style.color = length > maxChars ? '#d63638' : '#50575e';
			}

			if (typeof tinymce !== 'undefined') {
				tinymce.on('AddEditor', function (e) {
					if (e.editor.id === 'alert_bar_text_editor') {
						e.editor.on('init keyup change input NodeChange', function () {
							updateCounter(e.editor.getContent());
						});
					}
				});
			}
		});
		</script>
		<?php
	}

	/**
	 * [alert_bar_text] shortcode — outputs only the stored text (escaped).
	 *
	 * @return string
	 */
	public function shortcode() {
		if ( ! get_option( self::OPTION_ENABLED, 0 ) ) {
			return '';
		}
		return wp_kses_post( get_option( self::OPTION_TEXT, '' ) );
	}

	/**
	 * Enqueue the front-end visibility script.
	 *
	 * Runs on every page; the script no-ops when no .alert-bar element exists.
	 */
	public function enqueue_frontend_assets() {
		wp_enqueue_script(
			'curly-alert-bar',
			CURLY_ALERT_BAR_URL . 'assets/js/alert-bar.js',
			array(),
			CURLY_ALERT_BAR_VERSION,
			true
		);

		$enabled      = get_option( self::OPTION_ENABLED, 0 );
		$text         = trim( (string) get_option( self::OPTION_TEXT, '' ) );
		$server_hides = ( ! $enabled || '' === $text );

		wp_localize_script(
			'curly-alert-bar',
			'curlyAlertBar',
			array(
				'serverDisabled' => $server_hides,
				'closeKey'       => 'alertBarClosed',
			)
		);
	}
}