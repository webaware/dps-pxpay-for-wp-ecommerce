<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
* payment gateway integration for WP eCommerce
* @link http://docs.wpecommerce.org/category/payment-gateways/
*/
class DpsPxPayWpscGateway extends wpsc_merchant {

	const WPSC_GATEWAY_NAME				= 'dps_pxpay';
	const OPTION_NAME					= 'dps_pxpay_wp_ecommerce';

	// end points for the DPS PxPay API
	const PXPAY_APIV2_URL				= 'https://sec.paymentexpress.com/pxaccess/pxpay.aspx';
	const PXPAY_APIV2_TEST_URL			= 'https://uat.paymentexpress.com/pxaccess/pxpay.aspx';

	// end point for return to website
	const PXPAY_RETURN					= '__dpspxpaywpsc';

	public $name						= self::WPSC_GATEWAY_NAME;

	protected $logger;
	protected $resultReq;

	protected static $dpsReturnArgs;	// data returned in Payment Express callback

	/**
	* register new payment gateway
	* @param array $gateways array of registered gateways
	* @return array
	*/
	public static function register($gateways) {
		// register the gateway class and additional functions
		$gateways[] = array (
			'name'						=> _x('DPS Payment Express PxPay', 'gateway name', 'dps-pxpay-for-wp-ecommerce'),
			'api_version'				=> 2.0,
			'image'						=> plugins_url('images/icon-dps-24x24.png', DPS_PXPAY_WPSC_PLUGIN_FILE),
			'internalname'				=> self::WPSC_GATEWAY_NAME,
			'class_name'				=> __CLASS__,
			'has_recurring_billing'		=> false,
			'wp_admin_cannot_cancel'	=> true,
			'display_name'				=> _x('PxPay Credit Card Payment', 'display name', 'dps-pxpay-for-wp-ecommerce'),
			'form'						=> array(__CLASS__, 'configForm'),
			'submit_function'			=> array(__CLASS__, 'saveConfig'),
			'payment_type'				=> 'credit_card',
			'requirements'				=> array(
												'php_version' => 5.2,
											),
		);

		// also register admin hooks if required
		if (is_admin()) {
			add_action('wpsc_billing_details_bottom', array(__CLASS__, 'actionBillingDetailsBottom'));
		}

		add_action('admin_print_styles-settings_page_wpsc-settings', array(__CLASS__, 'printSettingsCSS'));
		add_action('admin_footer-settings_page_wpsc-settings', array(__CLASS__, 'printSettingsScript'));

		self::maybeProcessDpsReturn();

		return $gateways;
	}

	/**
	* initialise class
	* @param int $purchase_id
	* @param bool $is_receiving
	*/
	public function __construct($purchase_id = null, $is_receiving = false) {
		$options = self::getOptions();

		// create logger
		$this->logger = new DpsPxPayWpscLogging('wp-ecommerce', $options['logging']);

		parent::__construct($purchase_id, $is_receiving);
	}

	/**
	* grab the gateway-specific data from the checkout form post
	*/
	public function construct_value_array() {
		$options = self::getOptions();

		$this->collected_gateway_data = array (
			// additional fields from checkout
			'txndata1'		=> self::getCollectedDataValue($options['txndata1']),
			'txndata2'		=> self::getCollectedDataValue($options['txndata2']),
			'txndata3'		=> self::getCollectedDataValue($options['txndata3']),
			'email'			=> self::getCollectedDataValue($options['email']),
		);

		if ($options['merchant_ref']) {
			$this->collected_gateway_data['merchant_ref'] = self::getCollectedDataValue($options['merchant_ref']);
		}
		else {
			$this->collected_gateway_data['merchant_ref'] = $this->purchase_id;
		}
	}

	/**
	* Read a field from form post input.
	*
	* Guaranteed to return a string, trimmed of leading and trailing spaces, sloshes stripped out.
	*
	* @return string
	* @param string $fieldname name of the field in the form post
	*/
	protected static function getPostValue($fieldname) {
		return isset($_POST[$fieldname]) ? wp_unslash(trim($_POST[$fieldname])) : '';
	}

	/**
	* Read a field from form post input.
	*
	* Guaranteed to return a string, trimmed of leading and trailing spaces, sloshes stripped out.
	*
	* @return string
	* @param string $fieldname name of the field in the form post
	*/
	protected static function getCollectedDataValue($fieldname) {
		if (!isset($_POST['collected_data'][$fieldname])) {
			return '';
		}

		if (is_array($_POST['collected_data'][$fieldname])) {
			return wp_unslash(trim($_POST['collected_data'][$fieldname][0]));
		}

		return wp_unslash(trim($_POST['collected_data'][$fieldname]));
	}

	/**
	* submit to gateway
	*/
	public function submit() {
		// get purchase logs
		if ($this->purchase_id > 0) {
			$purchase_logs = new WPSC_Purchase_Log($this->purchase_id);
			$this->session_id = $purchase_logs->get('sessionid');
		}
		elseif (!empty($this->session_id)) {
			$purchase_logs = new WPSC_Purchase_Log($this->session_id, 'sessionid');

			$this->purchase_id = $purchase_logs->get('id');
		}
		else {
			$this->set_error_message(_x('No cart ID and no active session!', 'payment error', 'dps-pxpay-for-wp-ecommerce'));
			return;
		}

		$options = self::getOptions();

		// build a payment request and execute on API
		$creds = $this->getDpsCredentials($options['useTest']);
		$paymentReq = new DpsPxPayWpscPayment($creds['userID'], $creds['userKey'], $creds['endpoint']);
		$paymentReq->txnType			= DpsPxPayWpscPayment::TXN_TYPE_CAPTURE;
		$paymentReq->amount				= $purchase_logs->get('totalprice');
		$paymentReq->currency			= self::getCurrentyCode();
		$paymentReq->transactionNumber	= $this->session_id;
		$paymentReq->invoiceReference	= $this->collected_gateway_data['merchant_ref'];
		$paymentReq->option1			= $this->collected_gateway_data['txndata1'];
		$paymentReq->option2			= $this->collected_gateway_data['txndata2'];
		$paymentReq->option3			= $this->collected_gateway_data['txndata3'];
		$paymentReq->emailAddress		= $this->collected_gateway_data['email'];
		$paymentReq->urlSuccess			= home_url(self::PXPAY_RETURN);
		$paymentReq->urlFail			= home_url(self::PXPAY_RETURN);			// NB: redirection will happen after transaction status is updated

		// allow plugins/themes to modify invoice description and reference, and set option fields
		$paymentReq->invoiceReference	= apply_filters('dps_pxpay_wpsc_invoice_ref',      $paymentReq->invoiceReference, $this->purchase_id);
		$paymentReq->option1			= apply_filters('dps_pxpay_wpsc_invoice_txndata1', $paymentReq->option1, $this->purchase_id);
		$paymentReq->option2			= apply_filters('dps_pxpay_wpsc_invoice_txndata2', $paymentReq->option2, $this->purchase_id);
		$paymentReq->option3			= apply_filters('dps_pxpay_wpsc_invoice_txndata3', $paymentReq->option3, $this->purchase_id);

		$this->log_debug('========= initiating transaction request');
		$this->log_debug(sprintf('%s account, invoice ref: %s, transaction: %s, amount: %s',
			$options['useTest'] ? 'test' : 'live',
			$paymentReq->invoiceReference, $paymentReq->transactionNumber, $paymentReq->amount));

		$this->log_debug(sprintf('success URL: %s', $paymentReq->urlSuccess));
		$this->log_debug(sprintf('failure URL: %s', $paymentReq->urlFail));

		$errorMessage = '';

		try {
			$response = $paymentReq->processPayment();

			if ($response->isValid && !empty($response->URI)) {
				$log_details = array(
					'processed'			=> WPSC_Purchase_Log::INCOMPLETE_SALE,
				);

				wpsc_update_purchase_log_details($this->purchase_id, $log_details);

				$this->log_debug(sprintf('processing, invoice ref: %1$s, session: %2$s, amount = %3$s, currency = %4$s',
					$paymentReq->invoiceReference, $paymentReq->transactionNumber, $paymentReq->amount, $paymentReq->currency));

				wp_redirect($response->URI);
				exit;
			}
			else {
				$errorMessage = _x('Payment Express request invalid.', 'payment error', 'dps-pxpay-for-wp-ecommerce');
			}
		}
		catch (DpsPxPayWpscException $e) {
			$errorMessage = nl2br(esc_html($e->getMessage()));
		}

		// if there were errors, fail the transaction so that user can fix things up
		if ($errorMessage) {
			$this->log_debug($errorMessage);
			$this->set_error_message($errorMessage);
			$this->set_purchase_processed_by_purchid(1);	// failed
			return;
		}

	 	exit();
	}

	/**
	* parse gateway notification
	*/
	public function parse_gateway_notification() {
		$options = self::getOptions();

		$creds = $this->getDpsCredentials($options['useTest']);
		$paymentReq = new DpsPxPayWpscPayment($creds['userID'], $creds['userKey'], $creds['endpoint']);
		$paymentReq->result = wp_unslash(self::$dpsReturnArgs['result']);

		try {
			self::log_debug('========= requesting transaction result');
			$resultReq = $paymentReq->processResult();

			if ($resultReq->isValid) {
				$this->resultReq = $resultReq;

				// get purchase log
				$purchase_logs = new WPSC_Purchase_Log($resultReq->TxnId, 'sessionid');
				$this->purchase_id = $purchase_logs->get('id');
				$this->session_id = $purchase_logs->get('sessionid');

				if (empty($this->purchase_id)) {
					throw new DpsPxPayWpscException('Invalid transaction ID: ' . $resultReq->TxnId);
				}
			}
		}
		catch (DpsPxPayWpscException $e) {
			// an exception occured, so record the error
			$this->log_error($e->getMessage());
			$this->set_error_message(nl2br(esc_html($e->getMessage())));
		}
	}

	/**
	* process gateway notification
	*/
	public function process_gateway_notification() {
		if (empty($this->purchase_id) || empty($this->resultReq)) {
			return;
		}

		$resultReq = $this->resultReq;

		try {
			if ($resultReq->isValid) {
				if ($resultReq->Success) {
					$log_details = array(
						'processed'			=> WPSC_Purchase_Log::ACCEPTED_PAYMENT,
						'transactid'		=> $resultReq->DpsTxnRef,
						'authcode'			=> $resultReq->AuthCode,
						'notes'				=> $resultReq->ResponseText,
					);

					wpsc_update_purchase_log_details($this->purchase_id, $log_details);

					$this->log_debug(sprintf('success, invoice ref: %1$s, transaction: %2$s, status = accepted payment, amount = %3$s, authcode = %4$s',
						$this->purchase_id, $resultReq->DpsTxnRef, $resultReq->AmountSettlement, $resultReq->AuthCode));

					$this->go_to_transaction_results($this->session_id);
				}
				else {
					// transaction was unsuccessful, so record transaction number and the error
					$this->set_error_message(nl2br(esc_html($resultReq->ResponseText)));

					$log_details = array(
						'processed'			=> WPSC_Purchase_Log::PAYMENT_DECLINED,
						'transactid'		=> $resultReq->DpsTxnRef,
						'notes'				=> $resultReq->ResponseText,
					);
					wpsc_update_purchase_log_details($this->purchase_id, $log_details);

					$this->log_debug(sprintf('failed; invoice ref: %1$s, error: %2$s', $this->purchase_id, $resultReq->ResponseText));

					$this->return_to_checkout();
				}
			}
		}
		catch (DpsPxPayWpscException $e) {
			// an exception occured, so record the error
			$this->log_error($e->getMessage());
			$this->set_error_message(nl2br(esc_html($e->getMessage())));
			$this->set_purchase_processed_by_purchid(WPSC_Purchase_Log::INCOMPLETE_SALE);
			$this->return_to_checkout();
		}
	}

	/**
	* hook billing details display on admin, to show transaction number and authcode
	*/
	public static function actionBillingDetailsBottom() {
		global $purchlogitem;

		if (empty($purchlogitem->extrainfo->gateway) || $purchlogitem->extrainfo->gateway !== self::WPSC_GATEWAY_NAME) {
			return;
		}

		if (!empty($purchlogitem->extrainfo->transactid) || !empty($purchlogitem->extrainfo->authcode)) {
			include DPS_PXPAY_WPSC_PLUGIN_ROOT . 'views/admin-wpsc-billing-details.php';
		}
	}

	/**
	* show select list options for checkout form fields
	* @param int $selected
	* @param string $defaultLabel
	*/
	public static function showCheckoutFormFields($selected, $defaultLabel = false) {
		static $fields = false;

		if ($fields === false) {
			global $wpdb;
			$fields = $wpdb->get_results(sprintf("select id,name,unique_name from `%s` where active = '1' and type != 'heading'", WPSC_TABLE_CHECKOUT_FORMS));
		}

		if (!$defaultLabel) {
			$defaultLabel = _x('Please choose', 'gateway settings', 'dps-pxpay-for-wp-ecommerce');
		}

		printf('<option value="">%s</option>', esc_html($defaultLabel));
		foreach ($fields as $field) {
			printf('<option value="%s"%s>%s (%s)</option>', esc_attr($field->id), selected($field->id, $selected, false), esc_html($field->name), esc_html($field->unique_name));
		}
	}

	/**
	* load settings page CSS
	*/
	public static function printSettingsCSS() {
		if (empty($_REQUEST['tab']) || $_REQUEST['tab'] !== 'gateway') {
			return;
		}

		echo '<style>';
		readfile(DPS_PXPAY_WPSC_PLUGIN_ROOT . 'css/admin-settings.css');
		echo '</style>';
	}

	/**
	* load settings page scripts
	*/
	public static function printSettingsScript() {
		if (empty($_REQUEST['tab']) || $_REQUEST['tab'] !== 'gateway') {
			return;
		}

		$min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		echo '<script>';
		readfile(DPS_PXPAY_WPSC_PLUGIN_ROOT . "js/admin-settings$min.js");
		echo '</script>';
	}

	/**
	* display additional fields for gateway config form
	* return string
	*/
	public static function configForm() {
		$options = self::getOptions();

		ob_start();
		include DPS_PXPAY_WPSC_PLUGIN_ROOT . 'views/admin-wpsc.php';
		return ob_get_clean();
	}

	/**
	* save config details from payment gateway admin
	*/
	public static function saveConfig() {
		if (isset($_POST['dps_pxpay_wp_ecommerce'])) {
			$newoptions = wp_unslash($_POST['dps_pxpay_wp_ecommerce']);
			$options    = array();

			$options['userID']			= isset($newoptions['userID'])  ? trim(strip_tags($newoptions['userID']))  : '';
			$options['userKey']			= isset($newoptions['userKey']) ? trim(strip_tags($newoptions['userKey'])) : '';
			$options['useTest']			= empty($newoptions['useTest']) ? 0 : 1;
			$options['testEnv']			= isset($newoptions['testEnv']) ? trim(strip_tags($newoptions['testEnv'])) : 'UAT';
			$options['testID']			= isset($newoptions['testID'])  ? trim(strip_tags($newoptions['testID']))  : '';
			$options['testKey']			= isset($newoptions['testKey']) ? trim(strip_tags($newoptions['testKey'])) : '';
			$options['logging']			= isset($newoptions['logging']) ? trim(strip_tags($newoptions['logging'])) : 'off';

			// mapped fields
			$options['merchant_ref']	= isset($newoptions['merchant_ref']) ? absint($newoptions['merchant_ref']) : 0;
			$options['txndata1']		= isset($newoptions['txndata1'])     ? absint($newoptions['txndata1'])     : 0;
			$options['txndata2']		= isset($newoptions['txndata2'])     ? absint($newoptions['txndata2'])     : 0;
			$options['txndata3']		= isset($newoptions['txndata3'])     ? absint($newoptions['txndata3'])     : 0;
			$options['email']			= isset($newoptions['email'])        ? absint($newoptions['email'])        : 0;

			update_option(self::OPTION_NAME, $options);
		}

		return true;
	}

	/**
	* get gateway options
	* @return array
	*/
	protected static function getOptions() {
		$defaults = array(
			'userID'		=> '',
			'userKey'		=> '',
			'useTest'		=> 1,
			'testEnv'		=> 'UAT',
			'testID'		=> '',
			'testKey'		=> '',
			'logging'		=> 'off',

			// mapped fields
			'merchant_ref'	=> 0,
			'txndata1'		=> 0,
			'txndata2'		=> 0,
			'txndata3'		=> 0,
			'email'			=> 0,
		);

		$options = get_option(self::OPTION_NAME, array());

		return wp_parse_args($options, $defaults);
	}

	/**
	* get DPS credentials for selected operation mode
	* @param bool $useTest
	* @return array
	*/
	protected function getDpsCredentials($useTest) {
		$options = self::getOptions();

		if ($useTest) {
			$creds = array(
				'userID'		=> $options['testID'],
				'userKey'		=> $options['testKey'],
				'endpoint'		=> $options['testEnv'] === 'UAT' ? self::PXPAY_APIV2_TEST_URL : self::PXPAY_APIV2_URL,
			);
		}
		else {
			$creds = array(
				'userID'		=> $options['userID'],
				'userKey'		=> $options['userKey'],
				'endpoint'		=> self::PXPAY_APIV2_URL,
			);
		}

		return $creds;
	}

	/**
	* check for request path containing our path element, and a result argument
	*/
	public static function maybeProcessDpsReturn() {
		$request_uri = parse_url($_SERVER['REQUEST_URI']);

		// path must contain our callback slug
		if (empty($request_uri['path']) || strpos($request_uri['path'], self::PXPAY_RETURN) === false) {
			return;
		}

		// there must be a query string
		if (empty($request_uri['query'])) {
			return;
		}

		// query string must have a result element
		parse_str($request_uri['query'], $args);
		if (!isset($args['result'])) {
			return;
		}

		// set up for processing the callback after everything has loaded properly
		// fire after WPSC has loaded the customer, i.e. when wpsc_ready fires (but that action isn't available pre WPSC-3.8.14)
		self::$dpsReturnArgs = $args;
		add_action('init', array(__CLASS__, 'processDpsReturn'), 100);

		// stop WooCommerce Payment Express Gateway from intercepting other integrations' transactions!
		unset($_GET['userid']);
		unset($_REQUEST['userid']);
	}

	/**
	* return from DPS PxPay website, redirect to form
	*/
	public static function processDpsReturn() {
		// simulate a call to the gateway notification endpoint
		$_GET['gateway'] = self::WPSC_GATEWAY_NAME;
		wpsc_gateway_notification();
	}

	/**
	* get currency code for configured currency type
	* @return string
	*/
	protected static function getCurrentyCode() {
		$currency_type = get_option('currency_type');

		// since WP eCommerce 3.8.14, can just ask for the code
		if (class_exists('WPSC_Countries', false)) {
			return WPSC_Countries::get_currency_code($currency_type);
		}

		// otherwise, we'll need to fetch it from the database
		global $wpdb;
		$sql = 'select `code` from `' . WPSC_TABLE_CURRENCY_LIST . '` where `id` = %d';
		return $wpdb->get_var($wpdb->prepare($sql, $currency_type));
	}

	/**
	* write an error log
	* @param string $message
	*/
	protected function log_error($message) {
		$this->logger->log('error', $message);
	}

	/**
	* write an debug message log
	* @param string $message
	*/
	protected function log_debug($message) {
		$this->logger->log('info', $message);
	}

}
