<?php

/**
 * Processing and sending data to URL.
 */
class SFDTUBasicLogic {
	/**
	 * Settings fields.
	 */
	private $options;

	/**
	 * Shortcode creation and pre-shipment information and shipment directly.
	 */
	public function __construct() {
		$this->options = get_option( SFDTU_OPTION );

		add_shortcode( 'sfdtu', array( $this, 'sfdtu_show_form' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'sfdtu_register_script' ) );

		add_action( 'wp_ajax_sfdtu_send_data', array( $this, 'sfdtu_send_data_callback' ) );
		add_action( 'wp_ajax_nopriv_sfdtu_send_data', array( $this, 'sfdtu_send_data_callback' ) );
	}

	/**
	 * Output form.
	 */
	public function sfdtu_show_form() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'sfdtu_js' );

		!empty($this->options['title']) ? $title_form = '<h4>'.$this->options['title'].'</h4>' : $title_form = '';
		$html = '
		<div class="form-sfdtu-wrapper">
			'.$title_form.'
			<form class="form-sfdtu">
				<input type="hidden" name="action" value="sfdtu_send_data" />
				<input type="text" name="fio" placeholder="' . __( 'Full name', SFDTU_TEXT_DOMAIN ) . '" required><br>
				<input type="number" name="age" placeholder="' . __( 'Age', SFDTU_TEXT_DOMAIN ) . '" required><br>
				<label><input type="radio" name="gender" value="man" required>' . __( 'Man', SFDTU_TEXT_DOMAIN ) . '</label>
				<label><input type="radio" name="gender" value="woman">' . __( 'Woman', SFDTU_TEXT_DOMAIN ) . '</label><br>
				<input type="date" name="date" required><br>
				<input type="text" name="email" placeholder="' . __( 'Email', SFDTU_TEXT_DOMAIN ) . '" required><br>
				<input type="submit" name="' . __( 'Send', SFDTU_TEXT_DOMAIN ) . '"><br>
			</form>
			<br><div class="sfdtu-message"></div>
		</div>
		';

		return $html;
	}

	/**
	 * Connecting scripts.
	 */
	public function sfdtu_register_script () {
		wp_register_script( 'sfdtu_js', SFDTU_URL_PLUGIN . 'sfdtu.js', array( 'jquery' ), filemtime( SFDTU_PATH_PLUGIN . 'sfdtu.js' ), true );
	}

	/**
	 * Receiving, processing and sending data to, URL.
	 */
	public function sfdtu_send_data_callback () {
		$res = array();
		
		if ($_SERVER["REQUEST_METHOD"] !== "POST") {
			$res['status'] = false;
			$res['error']  = 'method not POST';
			$this->output_result_ajax_sending($res);
		}

		$res['fields'] = array();
		$fio = $this->basic_field_preparation($_POST["fio"]);
		$age = $this->basic_field_preparation($_POST["age"]);
		$gender = $this->basic_field_preparation($_POST["gender"]);
		$date = $this->basic_field_preparation($_POST["date"]);
		$email = $this->basic_field_preparation($_POST["email"]);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$res['status'] = false;
			array_push($res['fields'], 'email');
			$res['error']  = 'Invalid email format';
			$this->output_result_ajax_sending($res);
		}

		if (empty($this->options['url'])) {
			$res['status'] = false;
			$res['error']  = 'Empty url';
			$this->output_result_ajax_sending($res);
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->options['url']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);

		$post_data = array (
			"fio" => $fio,
			"age" => $age,
			"gender" => $gender,
			"date" => $date,
			"email" => $email,
		);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

		$output = curl_exec($ch);

		if ($output === FALSE) {
			$res['status'] = false;
			$res['error']  = 'cURL Error: ' . curl_error($ch);
			curl_close($ch);
			$this->output_result_ajax_sending($res);
		}

		$res_curl = curl_getinfo($ch);
		curl_close($ch);

		$res['status'] = true;
		$res['code']  = $res_curl['http_code'];
		$res['message']  = $this->options['successful_text'];
		$this->output_result_ajax_sending($res);
	}

	/**
	 * Output json result.
	 *
	 * @param array $res
	 */
	public function output_result_ajax_sending ($res = array()) {
		echo json_encode( $res );
		wp_die();
	}

	/**
	 * Basic field processing: deleting spaces at beginning and end of line,
	 * deleting screened characters, converting special characters to html objects.
	 *
	 * @param string $data
	 *
	 * @return string
	 */
	public function basic_field_preparation ($data = '') {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
}