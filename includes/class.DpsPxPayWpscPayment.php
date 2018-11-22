<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
* DPS PxPay payment request
*/
class DpsPxPayWpscPayment {

	const TXN_TYPE_CAPTURE			= 'Purchase';
	const TXN_TYPE_AUTHORISE		= 'Auth';

	#region "connection specific members"

	/**
	* default true, whether to validate the remote SSL certificate
	* @var boolean
	*/
	public $sslVerifyPeer;

	/**
	* HTTP user agent string identifying plugin, perhaps for debugging
	* @var string
	*/
	public $httpUserAgent;

	/**
	* Payment Express endpoint to post to
	* @var string
	*/
	public $endpoint;

	/**
	* account name / email address at DPS PxPay
	* @var string max. 8 characters
	*/
	public $userID;

	/**
	* account name / email address at DPS PxPay
	* @var string max. 8 characters
	*/
	public $userKey;

	#endregion // "connection specific members"

	#region "payment specific members"

	/**
	* total amount of payment, in dollars and cents as a floating-point number
	* @var float
	*/
	public $amount;

	/**
	* additional billing ID for recurring payments
	* @var string max. 32 characters
	*/
	public $billingID;

	/**
	* flag for enabling recurring billing
	* @var string max. 1 character
	*/
	public $enableRecurring;

	/**
	* currency code (AUD, NZD, etc.)
	* @var string max. 4 characters
	*/
	public $currency;

	/**
	* customer's email address
	* @var string max. 255 characters
	*/
	public $emailAddress;

	/**
	* an invoice reference to track by
	* @var string max. 64 characters
	*/
	public $invoiceReference;

	/**
	* optional additional information for use in shopping carts, etc.
	* @var string max. 255 characters
	*/
	public $option1;

	/**
	* optional additional information for use in shopping carts, etc.
	* @var string max. 255 characters
	*/
	public $option2;

	/**
	* optional additional information for use in shopping carts, etc.
	* @var string max. 255 characters
	*/
	public $option3;

	/**
	* type of transaction (Purchase, Auth)
	* @var string max. 8 characters
	*/
	public $txnType;

	/**
	* URL to redirect to on failure
	* @var string max. 255 characters
	*/
	public $urlFail;

	/**
	* URL to redirect to on success
	* @var string max. 255 characters
	*/
	public $urlSuccess;

	/**
	* transaction number
	* @var string max. 16 characters
	*/
	public $transactionNumber;

	#endregion // "payment specific members"

	#region "result specific members"

	/**
	* encrypted transaction result, to be decrypted by DPS PxPay service
	* @var string
	*/
	public $result;

	#endregion // "result specific members"

	/**
	* populate members with defaults, and set account and environment information
	*
	* @param string $userID DPS PxPay account ID
	* @param string $userKey DPS PxPay encryption key
	* @param string $endpoint Payment Express endpoint
	*/
	public function __construct($userID, $userKey, $endpoint) {
		$this->sslVerifyPeer	= true;
		$this->userID			= $userID;
		$this->userKey			= $userKey;
		$this->endpoint			= $endpoint;
		$this->httpUserAgent	= 'DPS PxPay for WP eCommerce ' . DPS_PXPAY_WPSC_VERSION;

		// default to single payment, not recurring
		$this->enableRecurring	= false;
	}

	/**
	* process a payment against DPS PxPay; throws exception on error with error described in exception message.
	*/
	public function processPayment() {
		$this->validate();
		$xml = $this->getPaymentXML();

		$responseXML = $this->xmlPostRequest($this->endpoint, $xml);

		$response = new DpsPxPayWpscResponseRequest();
		$response->loadResponse($responseXML);
		return $response;
	}

	/**
	* process a result against DPS PxPay; throws exception on error with error described in exception message.
	*/
	public function processResult() {
		$xml = $this->getResultXML();

		$resultXML = $this->xmlPostRequest($this->endpoint, $xml);

		$result = new DpsPxPayWpscResponseResult();
		$result->loadResponse($resultXML);
		return $result;
	}

	/**
	* validate the data members to ensure that sufficient and valid information has been given
	* @throws DpsPxPayWpscException
	*/
	protected function validate() {
		$errors = [];

		if (strlen($this->userID) === 0) {
			$errors[] = _x('userID cannot be empty.', 'validation error', 'dps-pxpay-for-wp-ecommerce');
		}
		if (strlen($this->userKey) === 0) {
			$errors[] = _x('userKey cannot be empty.', 'validation error', 'dps-pxpay-for-wp-ecommerce');
		}
		if (!is_numeric($this->amount) || $this->amount <= 0) {
			$errors[] = _x('amount must be given as a number in dollars and cents.', 'validation error', 'dps-pxpay-for-wp-ecommerce');
		}
		else if (!is_float($this->amount)) {
			$this->amount = (float) $this->amount;
		}
		if (strlen($this->currency) === 0) {
			$errors[] = _x('currency cannot be empty.', 'validation error', 'dps-pxpay-for-wp-ecommerce');
		}
		if (strlen($this->invoiceReference) === 0) {
			$errors[] = _x('invoice reference cannot be empty.', 'validation error', 'dps-pxpay-for-wp-ecommerce');
		}
		if (strlen($this->txnType) === 0) {
			$errors[] = _x('transaction type cannot be empty.', 'validation error', 'dps-pxpay-for-wp-ecommerce');
		}
		if (strlen($this->urlFail) === 0) {
			$errors[] = _x('URL for transaction fail cannot be empty.', 'validation error', 'dps-pxpay-for-wp-ecommerce');
		}
		if (strlen($this->urlSuccess) === 0) {
			$errors[] = _x('URL for transaction success cannot be empty.', 'validation error', 'dps-pxpay-for-wp-ecommerce');
		}

		if (count($errors) > 0) {
			$msg = implode("\n", $errors);
			throw new DpsPxPayWpscException($msg);
		}
	}

	/**
	* create XML request document for payment parameters
	* @return string
	*/
	public function getPaymentXML() {
		$xml = new XMLWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('GenerateRequest');

		$xml->writeElement('PxPayUserId',			substr($this->userID, 0, 32));
		$xml->writeElement('PxPayKey',				substr($this->userKey, 0, 64));
		$xml->writeElement('AmountInput',			self::formatCurrency($this->amount, $this->currency));
		$xml->writeElement('CurrencyInput',			substr($this->currency, 0, 4));
		$xml->writeElement('EnableAddBillCard',		$this->enableRecurring ? '1' : '0');
		$xml->writeElement('MerchantReference',		substr($this->invoiceReference, 0, 64));
		$xml->writeElement('TxnType',				substr($this->txnType, 0, 8));
		$xml->writeElement('TxnId',					substr($this->transactionNumber, 0, 16));
		$xml->writeElement('UrlFail',				substr($this->urlFail, 0, 255));
		$xml->writeElement('UrlSuccess',			substr($this->urlSuccess, 0, 255));

		if (!empty($this->billingID)) {
			$xml->writeElement('BillingId',			substr($this->billingID, 0, 32));
		}

		if (!empty($this->emailAddress)) {
			$xml->writeElement('EmailAddress',		substr($this->emailAddress, 0, 255));
		}

		if (!empty($this->option1)) {
			$xml->writeElement('TxnData1',			substr($this->option1, 0, 255));
		}

		if (!empty($this->option2)) {
			$xml->writeElement('TxnData2',			substr($this->option2, 0, 255));
		}

		if (!empty($this->option3)) {
			$xml->writeElement('TxnData3',			substr($this->option3, 0, 255));
		}

		$xml->endElement();		// GenerateRequest

		return $xml->outputMemory();
	}

	/**
	* format amount per currency
	* @param float $amount
	* @param string $currency
	* @return string
	*/
	protected static function formatCurrency($amount, $currency) {
		switch ($currency) {

			// Japanese Yen has no decimal fraction
			case 'JPY':
				$value = number_format($amount, 0, '', '');
				break;

			default:
				$value = number_format($amount, 2, '.', '');
				break;

		}

		return $value;
	}

	/**
	* create XML request document for result parameters
	* @return string
	*/
	protected function getResultXML() {
		$xml = new XMLWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		$xml->startElement('ProcessResponse');

		$xml->writeElement('PxPayUserId',			substr($this->userID, 0, 32));
		$xml->writeElement('PxPayKey',				substr($this->userKey, 0, 64));
		$xml->writeElement('Response',				$this->result);

		$xml->endElement();		// ProcessResponse

		return $xml->outputMemory();
	}

	/**
	* generalise an XML post request
	* @param string $url
	* @param string $request
	* @return string JSON response
	* @throws DpsPxPayWpscException
	*/
	protected function xmlPostRequest($url, $request) {
		// execute the request, and retrieve the response
		$response = wp_remote_post($url, [
			'user-agent'	=> $this->httpUserAgent,
			'sslverify'		=> $this->sslVerifyPeer,
			'timeout'		=> 60,
			'headers'		=>	[
									'Content-Type'		=> 'text/xml; charset=utf-8',
								],
			'body'			=> $request,
		]);

		// check for http error
		$this->checkHttpResponse($response);

		return wp_remote_retrieve_body($response);
	}

	/**
	* check http get/post response, throw exception if an error occurred
	* @param array $response
	* @throws DpsPxPayWpscException
	*/
	protected function checkHttpResponse($response) {
		// failure to handle the http request
		if (is_wp_error($response)) {
			$msg = $response->get_error_message();
			throw new DpsPxPayWpscException(sprintf(__('Error posting DPS Payment Express request: %s', 'dps-pxpay-for-wp-ecommerce'), $msg));
		}

		// error code returned by request
		$code = wp_remote_retrieve_response_code($response);
		if ($code !== 200) {
			$msg = wp_remote_retrieve_response_message($response);

			if (empty($msg)) {
				$msg = sprintf(__('Error posting DPS Payment Express request: %s', 'dps-pxpay-for-wp-ecommerce'), $code);
			}
			else {
				/* translators: 1. the error code; 2. the error message */
				$msg = sprintf(__('Error posting DPS Payment Express request: %1$s, %2$s', 'dps-pxpay-for-wp-ecommerce'), $code, $msg);
			}
			throw new DpsPxPayWpscException($msg);
		}
	}

}
