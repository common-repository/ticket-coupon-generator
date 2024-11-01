<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://vagelis.dev
 * @since      1.0.0
 *
 * @package    Ticket_Coupon_Generator
 * @subpackage Ticket_Coupon_Generator/admin
 */

/**
 * Defines the VPGC\Admin namespace for the admin-specific functionality of the plugin.
 */

namespace VPGC\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ticket_Coupon_Generator
 * @subpackage Ticket_Coupon_Generator/admin
 * @author     Vagelis Papaioannou <hello@vagelis.dev>
 */
class Ticket_Coupon_Generator_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ticket_Coupon_Generator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ticket_Coupon_Generator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/ticket-coupon-generator-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ticket_Coupon_Generator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ticket_Coupon_Generator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/ticket-coupon-generator-admin.js', array('jquery'), $this->version, true);
		wp_localize_script($this->plugin_name, 'ticket_coupon_generator_log', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'log_file_url' => '/wp-content/' . VPCG_LOG_FILE_NAME,
			'ajax_nonce' => wp_create_nonce('coupon_log_nonce')
		));
	}

	/**
	 * Register the plugin Settings.
	 *
	 * @since    1.0.0
	 */
	public function register_settings()
	{
		// Define the settings group, plugin page, and settings section variables
		$settings_group   = 'coupon_generator_settings_group';
		$plugin_page      = $this->plugin_name;
		$settings_section = 'coupon_generator_settings_section';

		// Register email subject and content settings
		register_setting($settings_group, 'ticket_coupon_generator_email_subject');
		register_setting($settings_group, 'ticket_coupon_generator_email_body');

		// Add a new settings section for the plugin
		add_settings_section($settings_section, 'Email Settings', null, $plugin_page);

		// Add the email subject settings field to the settings section
		add_settings_field('ticket_coupon_generator_email_subject', 'Email Subject', array($this, 'email_subject'), $plugin_page, $settings_section);

		// Add the email body settings field to the settings section
		add_settings_field('ticket_coupon_generator_email_body', 'Email Body', array($this, 'email_body'), $plugin_page, $settings_section);
	}

	/**
	 * Email Subject Settings Field Callback.
	 *
	 * @since    1.0.5
	 */
	public function email_subject()
	{
		// Get the value of the email_subject option from the database
		$email_subject = get_option('ticket_coupon_generator_email_subject');

		// If the email_subject option is empty, delete it from the database
		if (empty($email_subject)) {
			delete_option('ticket_coupon_generator_email_subject');
		}

		// Display the input field for the email_subject option
		echo '<input type="text" name="ticket_coupon_generator_email_subject" value="' . esc_attr($email_subject) . '" />';
	}

	/**
	 * Email Content Settings Field Callback.
	 *
	 * @since    1.0.5
	 */
	public function email_body()
	{
		// Get the value of the email_body option from the database
		$email_body = get_option('ticket_coupon_generator_email_body');

		// If the email_body option is empty, delete it from the database
		if (empty($email_body)) {
			delete_option('ticket_coupon_generator_email_body');
		}

		// Display the textarea field for the email_body option
		echo '<textarea name="ticket_coupon_generator_email_body" rows="5" cols="50">' . esc_textarea($email_body) . '</textarea>';

		// Display some help text
		echo '<p>Use the following placeholders in the email body:</p>';
		echo '<ul>';
		echo '<p><strong>%COUPON_CODE%</strong> - The coupon code</p>';
		echo '</ul>';
		echo '<p>Feel free to use HTML in the email body.</p>';
		echo '<p>Example:</p>';
		echo '<p><code>';
		echo esc_html__('<p>Hello there,<br> your coupon code is <strong>%COUPON_CODE%</strong>.</p><p>Thank you.</p>', 'ticket-coupon-generator');
		echo '</code></p>';
	}

	/**
	 * Register the menu pages.
	 *
	 * @since    1.0.0
	 */
	public function add_menu_pages()
	{

		add_menu_page(
			__('Generate Coupons', 'ticket-coupon-generator'),
			'Generate Coupons',
			'manage_options',
			'ticket_coupons_generator',
			array($this, 'display_coupons_page'),
			'dashicons-tickets-alt',
			26
		);

		add_submenu_page(
			'ticket_coupons_generator',
			esc_html__('Email Settings', 'ticket-coupon-generator'),
			esc_html__('Email Settings', 'ticket-coupon-generator'),
			'manage_options',
			'ticket_coupons_generator_settings_page',
			array($this, 'display_settings_page')
		);
	}


	/**
	 * Render the Menu Page for Coupon Generation.
	 *
	 * @since    1.0.0
	 */
	public function display_coupons_page()
	{

		// Start output buffering
		ob_start();
		?>
		<div class="wrap">
			<h1><?php echo esc_html__('Generate Coupons', 'ticket-coupon-generator'); ?></h1>
			<form method="post" enctype="multipart/form-data">
				<?php wp_nonce_field('generate_coupons', 'generate_coupons_nonce'); ?>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="coupons_file"><?php echo esc_html__('CSV File:', 'ticket-coupon-generator'); ?></label>
						</th>
						<td>
							<input type="file" name="coupons_file" id="coupons_file" required>
						</td>
					</tr>
				</table>
				<?php submit_button(esc_html__('Generate Coupons', 'ticket-coupon-generator')); ?>
			</form>
			<?php if (file_exists(VPCG_LOG_FILE_PATH)) { ?>
				<?php
				$last_time = filemtime(VPCG_LOG_FILE_PATH);
				echo wp_kses(sprintf(__('Last run was on %s', 'ticket-coupon-generator'), '<strong>' . date('F d, Y H:i A', $last_time) . '</strong>'), array('strong' => array()));
				?>
				<p>
					<a id="ccg_log_display" href="#" class="button"><?php echo esc_html__('View Log', 'ticket-coupon-generator'); ?></a>
				</p>
				<p id="ccg_log_content"></p>
				<p><a id="ccg_log_download" href="<?php echo esc_url('/wp-content/' . VPCG_LOG_FILE_NAME); ?>" class="button" download><?php echo esc_html__('Download Log', 'ticket-coupon-generator'); ?></a></p>
			<?php } ?>
		</div>
		<?php

		// End output buffering and display the content
		echo ob_get_clean();

		// Handle coupon generation if form is submitted
		$message = $this->handle_form_submission();

		// Display message if not empty
		if (!empty($message)) {
			echo '<div"><p>' . esc_html($message) . '</p></div>';
		}
	}

	/**
	 * Render the Settings Page form email content etc.
	 *
	 * @since    1.0.5
	 */
	public function display_settings_page()
	{
		// Check if the user has the required capability to manage options
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', 'ticket-coupon-generator'));
		}

		// Render the settings page
		?>
		<div class="wrap">
			<h1><?php _e('Plugin Settings', 'ticket-coupon-generator'); ?></h1>
			<form method="post" action="options.php">
				<?php
				// Output the settings fields and sections
				settings_fields('coupon_generator_settings_group');
				do_settings_sections($this->plugin_name);

				// Output the submit button
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Handles form submission.
	 *
	 * @since    1.0.5
	 */
	private function handle_form_submission()
	{
		$message = '';
		// $this->display_error_message('The submitted file is not a CSV. Please upload a valid CSV file.');
		if (isset($_FILES['coupons_file'])) {
			// Check user capabilities
			if (!current_user_can('manage_options')) {
				wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'ticket-coupon-generator'));
			}

			// Check nonce
			check_admin_referer('generate_coupons', 'generate_coupons_nonce');

			// Validate and sanitize file input
			$file = $_FILES['coupons_file'];
			if ($file['type'] !== 'text/csv') {
				wp_die(esc_html__('Invalid file type. Please upload a CSV file.', 'ticket-coupon-generator'));
			}

			// Process the file
			$this->generate_coupons_from_file($file['tmp_name']);
			$message = esc_html__('Coupons have been generated successfully.', 'ticket-coupon-generator');
		}
		return $message;
	}

	/**
	 * Generate coupons from a CSV file.
	 *
	 * @param string $file The path to the CSV file.
	 * @since 1.0.0
	 */
	public function generate_coupons_from_file($file)
	{
		// Check that the file exists.
		if (!file_exists($file)) {
			wp_die(esc_html__('File not found.', 'ticket-coupon-generator'));
		}

		// Open the file.
		$handle = fopen($file, 'r');
		if (!$handle) {
			wp_die(esc_html__('Error opening file.', 'ticket-coupon-generator'));
		}

		// Read the header row and get the column names.
		$header = fgetcsv($handle);
		$columns = array_combine($header, range(0, count($header) - 1));

		// Check that the required columns are present.
		$required_columns = array('code', 'quantity');
		foreach ($required_columns as $required_column) {
			if (!isset($columns[$required_column])) {
				wp_die(sprintf(esc_html__('Missing required column: %s', 'ticket-coupon-generator'), $required_column));
			}
		}

		// Create/Wipe the log file
		$log_file = fopen(VPCG_LOG_FILE_PATH, 'w');

		// Generate coupons from data rows.
		while ($row = fgetcsv($handle)) {
			// Get coupon data from the row.
			$code = $row[$columns['code']];
			$discount_price = isset($row[$columns['discount_price']]) ? $row[$columns['discount_price']] : null;
			$percent = isset($row[$columns['discount_percent']]) ? $row[$columns['discount_percent']] : null;
			$quantity = $row[$columns['quantity']];
			$recipient = isset($row[$columns['email']]) ? $row[$columns['email']] : null;

			// Check if the coupon already exists.
			$existing_coupon = get_posts(array(
				'post_type' => 'tix_coupon',
				'meta_key' => 'tix_code',
				'meta_value' => $code,
				'posts_per_page' => 1,
			));

			// Create coupon if it doesn't exist.
			if (!$existing_coupon) {
				$coupon_id = wp_insert_post(array(
					'post_type' => 'tix_coupon',
					'post_title' => $code,
					'post_status' => 'publish',
				));

				// Set coupon meta.
				// Prepare an array with the meta keys and their respective values.
				$meta_data = array(
					'tix_code' => $code,
					'tix_discount_price' => $discount_price,
					'tix_discount_percent' => $percent,
					'tix_coupon_quantity' => $quantity
				);

				// Loop through the meta_data array, setting each key and value.
				foreach ($meta_data as $key => $value) {
					// Check if the value is not null before updating the post meta.
					if ($value) {
						update_post_meta($coupon_id, $key, $value);
					}
				}

				// Send out email.
				if ($recipient) {
					// Prepare email.
					$default_subject = __('Your personal ticket coupon', 'ticket-coupon-generator');
					$subject = get_option('ticket_coupon_generator_email_subject', $default_subject);

					$default_body = __("<p>Here's your coupon code: <strong>%COUPON_CODE%</strong>,<br> please claim your ticket as soon as possible. <p>Thank you</p>", 'ticket-coupon-generator');
					$body = get_option('ticket_coupon_generator_email_body', $default_body);

					// check if the body contains the placeholder just in case
					if (strpos($body, '%COUPON_CODE%') === false) {
						// if not, append it to the end of the body
						$body .= '<p>Your personal coupon: %COUPON_CODE%</p>';
					}
					// Replace the %COUPON_CODE% placeholder with the actual coupon code.
					$body = str_replace('%COUPON_CODE%', $code, $body);

					$headers = array('Content-Type: text/html; charset=UTF-8');

					// Send the email.
					$email_sent = wp_mail($recipient, $subject, $body, $headers);
				} else {
					$email_sent = false;
				}
				// Log successful coupon creation.
				$this->log_coupon_creation($code, true, $recipient, $email_sent);
			} else {
				// Log failed/skipped coupon creation.
				$this->log_coupon_creation($code, false, $recipient, false);
			}
		}
		// Close the log file.
		fclose($log_file);

		// Close the CSV file.
		fclose($handle);
	}

	/**
	 * Logs the coupon creation process.
	 *
	 * @param string $code The coupon code.
	 * @param bool $created Whether the coupon was created or not.
	 * @param bool $email_sent Whether the email was sent or not.
	 * @since 1.0.0
	 */
	public function log_coupon_creation($code, $created, $email_address, $email_sent)
	{
		// Create the log entry.
		$log_data = sprintf(
			'%s,%s,%s,%s,%s' . PHP_EOL,
			date('F d Y H:i A'),
			$code,
			($created ? 'created' : 'already exists'),
			$email_address,
			($email_sent ? 'sent' : 'not sent')
		);

		// Write the log data to the log file.
		file_put_contents(VPCG_LOG_FILE_PATH, $log_data, FILE_APPEND);
	}

	/**
	 * Display the log file's contents.
	 *
	 * @since    1.0.0
	 */
	public function show_coupon_log()
	{
		// Check the nonce
		check_ajax_referer('coupon_log_nonce', 'security');

		if (file_exists(VPCG_LOG_FILE_PATH)) {
			$log_content = file_get_contents(VPCG_LOG_FILE_PATH);
			$log_lines = explode(PHP_EOL, $log_content);

			if (!empty($log_lines)) {
				?>
				<table>
					<thead>
						<tr>
							<th>Date</th>
							<th>Coupon Code</th>
							<th>Status</th>
							<th>Email Address</th>
							<th>Email Sent</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($log_lines as $line) {
							if ($line !== '') {
								list($date, $code, $status, $email_address, $email_sent) = explode(',', $line);
								printf(
									'<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
									esc_html($date),
									esc_html($code),
									esc_html($status),
									esc_html($email_address),
									esc_html($email_sent)
								);
							}
						}
						?>
					</tbody>
				</table>
				<?php
			} else {
				echo esc_html__('No log data found.', 'ticket-coupon-generator');
			}
		} else {
			printf(
				esc_html__('Log file not found: %s', 'ticket-coupon-generator'),
				VPCG_LOG_FILE_PATH
			);
		}
		wp_die();
	}
}
