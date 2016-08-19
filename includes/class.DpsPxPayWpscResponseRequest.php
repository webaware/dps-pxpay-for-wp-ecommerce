<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
* DPS PxPay payment request response
*/
class DpsPxPayWpscResponseRequest extends DpsPxPayWpscResponse {

	/**
	* URL to redirect browser to where credit card details can be entered
	* @var string
	*/
	public $URI;

	/**
	* response code when error is returned
	* @var string 2 characters
	*/
	public $Reco;

	/**
	* textual response status, e.g. error message
	* @var string max. 32 characters
	*/
	public $ResponseText;

	/**
	* load DPS PxPay response data as XML string
	* @param string $response DPS PxPay response as a string (hopefully of XML data)
	* @throws DpsPxPayWpscException
	*/
	public function loadResponse($response) {
		parent::loadResponse($response);

		if (empty($this->URI) && (!empty($this->Reco) || !empty($this->ResponseText))) {
			$errors = array();
			if (!empty($this->ResponseText)) {
				$errors[] = $this->ResponseText;
			}
			if (!empty($this->Reco)) {
				$errors[] = self::getCodeDescription($this->Reco);
			}
			throw new DpsPxPayWpscException(implode("\n", $errors));
		}
	}

	/**
	* get 'invalid response' message for this response class
	* @return string
	*/
	protected function getMessageInvalid() {
		return __('Invalid response from Payment Express for payment request', 'dps-pxpay-for-wp-ecommerce');
	}

	/**
	* get description for response code
	* @param string $code
	* @return string
	*/
	protected static function getCodeDescription($code) {
		switch ($code) {

			case 'IC':
				$msg = _x('Invalid Key or Username. Also check that if a TxnId is being supplied that it is unique.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'ID':
				$msg = _x('Invalid transaction type. Ensure that the transaction type is either Auth or Purchase.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'IK':
				$msg = _x('Invalid UrlSuccess. Ensure that the URL being supplied does not contain a query string.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'IL':
				$msg = _x('Invalid UrlFail. Ensure that the URL being supplied does not contain a query string.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'IM':
				$msg = _x('Invalid PxPayUserId.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'IN':
				$msg = _x('Blank PxPayUserId.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'IP':
				$msg = _x('Invalid Access Info. Ensure the PxPayID and/or key are valid.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'IQ':
				$msg = _x('Invalid TxnType. Ensure the transaction type being submitted is either "Auth" or "Purchase".', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'IT':
				$msg = _x('Invalid currency. Ensure that the CurrencyInput is correct and in the correct format e.g. "USD".', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'IU':
				$msg = _x('Invalid AmountInput. Ensure that the amount is in the correct format e.g. "20.00".', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'NF':
				$msg = _x('Invalid Username.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'NK':
				$msg = _x('Request not found. Check the key and the mcrypt library if in use.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'NL':
				$msg = _x('User not enabled. Contact Payment Express.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'NM':
				$msg = _x('User not enabled. Contact Payment Express.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'NN':
				$msg = _x('Invalid MAC.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'NO':
				$msg = _x('Request contains non ASCII characters.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'NP':
				$msg = _x('Closing Request tag not found.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'NQ':
				$msg = _x('User not enabled for PxPay 2.0. Contact Payment Express.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			case 'NT':
				$msg = _x('Key is not 64 characters.', 'DPS coded response', 'dps-pxpay-for-wp-ecommerce');
				break;

			default:
				$msg = $code;
				break;

		}

		return apply_filters('dps_pxpay_wpsc_code_description', $msg, $code);
	}

}
