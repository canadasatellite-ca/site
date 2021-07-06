<?php
namespace Schogini\Beanstream\Model;
use Magento\Framework\DataObject as _DO;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\ObjectManager\NoninterceptableInterface as INonInterceptable;
use Magento\Framework\Phrase;
use Magento\Payment\Model\Info as I;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Api\Data\CartInterface as ICart;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
# 2021-06-27 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
# "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
final class Beanstream extends \Magento\Payment\Model\Method\Cc implements INonInterceptable {
	const CODE = 'beanstream';
	protected $_code = self::CODE;
	protected $_isGateway = true;
	protected $_canAuthorize = true;
	protected $_canCapture = true;
	protected $_canCapturePartial = false;
	protected $_canRefund = true;
	protected $_canRefundInvoicePartial = true;
	protected $_canVoid = true;
	protected $_canUseInternal = true;
	protected $_canUseCheckout = true;
	protected $_canUseForMultishipping = true;
	protected $_canSaveCc = false;
	protected $_canOrder = false;

	/**
	 * 2021-06-27 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::authorize()
	 * @used-by \Magento\Sales\Model\Order\Payment\Operations\AuthorizeOperation::authorize()
	 * https://github.com/magento/magento2/blob/2.1.5/app/code/Magento/Sales/Model/Order/Payment/Operations/AuthorizeOperation.php#L45
	 * 2021-07-01
	 * $a is a string because it is a result of the @see \Magento\Sales\Model\Order\Payment::formatAmount() call:
	 * 		$amount = $payment->formatAmount($amount, true);
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Sales/Model/Order/Payment/Operations/AuthorizeOperation.php#L36
	 * @param II|I|OP $i
	 * @param string|float $a
	 * @return $this
	 * @throws LE
	 */
	function authorize(II $i, $a) {
		if (0 >= $a) {
			self::err('Invalid amount for authorization.');
		}
		$m = false; /** @var string|false $m */
		$i->setAmount($a);
		$req = $this->buildRequest($i, self::$AUTH_ONLY); /** @var _DO $req */
		$res = $this->postRequest($req, self::$AUTH_ONLY); /** @var _DO $res */
		$i->setCcApproval($res->getApprovalCode())->setLastTransId($res->getTransactionId())->setCcTransId($res->getTransactionId())->setCcAvsStatus($res->getAvsResultCode())->setCcCidStatus($res->getCardCodeResponseCode());
		$reasonC = $res->getResponseReasonCode();
		$reasonS = $res->getResponseReasonText();
		switch ($res->getResponseCode()) {
			case self::$APPROVED:
				$i->setStatus(self::STATUS_APPROVED);
				if ($res->getTransactionId() != $i->getParentTransactionId()) {
					$i->setTransactionId($res->getTransactionId());
				}
				$i->setIsTransactionClosed(0)->setTransactionAdditionalInfo('real_transaction_id', $res->getTransactionId());
				break;
			case 2:
				$m = "Payment authorization transaction has been declined. \n$reasonS";
				break;
			default:
				$m = "Payment authorization error. \n$reasonS";
		}
		if ($m) {
			dfp_report($this, ['request' => $req->getData(), 'response' => $res->getData()]);
			self::err($m);
		}
		return $this;
	}

	/**
	 * 2021-06-27 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::capture()
	 * @used-by \Magento\Sales\Model\Order\Payment\Operations\CaptureOperation::capture():
	 * 		$method->capture($payment, $amountToCapture);
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Sales/Model/Order/Payment/Operations/CaptureOperation.php#L82
	 * 2021-07-01
	 * $a is a string because it is a result of the @see \Magento\Sales\Model\Order\Payment::formatAmount() call:
	 * 		$amountToCapture = $payment->formatAmount($invoice->getBaseGrandTotal());
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Sales/Model/Order/Payment/Operations/CaptureOperation.php#L37
	 * @param II|I|OP $i
	 * @param string|float $a
	 * @return $this
	 * @throws LE
	 */
	function capture(II $i, $a) {
		$m = false; /** @var string|false $m */
		$i->setAmount($a);
		$type = $i->getParentTransactionId() ? self::$PRIOR_AUTH_CAPTURE : self::$AUTH_CAPTURE; /** @var string $type */
		/** @var _DO $req */
		$req = $this->buildRequest($i, $type);
		$res = $this->postRequest($req, $type); /** @var _DO $res */
		if ($res->getResponseCode() == self::$APPROVED) {
			$i->setStatus(self::STATUS_APPROVED);
			$i->setCcTransId($res->getTransactionId());
			$i->setLastTransId($res->getTransactionId());
			if ($res->getTransactionId() != $i->getParentTransactionId()) {
				$i->setTransactionId($res->getTransactionId());
			}
			$i->setIsTransactionClosed(0)->setTransactionAdditionalInfo('real_transaction_id', $res->getTransactionId());
		}
		else {
			$m = $res->getResponseReasonText() ?: 'Error in capturing the payment';
			$oq = $i->getOrder() ?: $i->getQuote();
			$oq->addStatusToHistory($oq->getStatus(), urldecode($m) . ' at Beanstream', $m . ' from Beanstream');
		}
		if ($m) {
			dfp_report($this, ['request' => $req->getData(), 'response' => $res->getData()]);
			self::err($m);
		}
		return $this;
	}

	/**
	 * 2021-06-27 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::isAvailable()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L343-L350
	 * @see \Magento\Payment\Model\Method\AbstractMethod::isAvailable()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L805-L825
	 * @used-by \Magento\Payment\Block\Form\Container::getMethods()
	 * @used-by \Magento\Payment\Helper\Data::getStoreMethods()
	 * @used-by \Magento\Payment\Model\MethodList::getAvailableMethods()
	 * @used-by \Magento\Quote\Model\Quote\Payment::importData()
	 * @used-by \Magento\Sales\Model\AdminOrder\Create::_validate()
	 * @param ICart|Q|null $q
	 * @return array|bool|mixed|null
	 */
	function isAvailable(ICart $q = null) {/** @var bool $r */
		if ($r = $this->isActive($q ? $q->getStoreId() : null)) {
			df_dispatch('payment_method_is_active', ['method_instance' => $this, 'quote' => $q,
				'result' => ($evR = new _DO(['is_available' => true])) /** @var _DO $evR */
			]);
			$r = $evR['is_available'];
		}
		return $r;
	}

	/**
	 * 2021-06-28 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * 2021-07-02
	 * $a is a string because it is a result of the @see \Magento\Sales\Model\Order\Payment::formatAmount() call:
	 * 		$baseAmountToRefund = $this->formatAmount($creditmemo->getBaseGrandTotal());
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Sales/Model/Order/Payment.php#L655
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::refund()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L269-L277
	 * @see \Magento\Payment\Model\Method\AbstractMethod::refund()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L640-L656
	 * @used-by \Magento\Sales\Model\Order\Payment::refund()
	 * 		$gateway->refund($this, $baseAmountToRefund);
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Sales/Model/Order/Payment.php#L684
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Sales/Model/Order/Payment.php#L701
	 * @param II|I|OP $payment
	 * @param string|float $a
	 * @return $this
	 */
	function refund(II $i, $a) {
		$m = false; /** @var Phrase|string|false $m */
		df_assert_gt0($a);
		# 2021-07-06 A string like «10000003».
		df_assert_sne($parentId = $i->getParentTransactionId()); /** @var string $parentId */
		$req = $this->buildRequest($i, self::$REFUND);
		$req->setXAmount($a);
		$res = $this->postRequest($req, self::$REFUND);
		if ($res->getResponseCode() == self::$APPROVED) {
			$i->setStatus(self::STATUS_SUCCESS);
			if ($res->getTransactionId() != $parentId) {
				$i->setTransactionId($res->getTransactionId());
			}
			$sp41f7d8 = $i->getOrder()->canCreditmemo() ? 0 : 1;
			$i->setIsTransactionClosed(1)->setShouldCloseParentTransaction($sp41f7d8)->setTransactionAdditionalInfo('real_transaction_id', $res->getTransactionId());
		}
		else {
			$m = $res->getResponseReasonText();
		}
		if ($m !== false) {
			self::err($m);
		}
		return $this;
	}
	
	/**
	 * 2021-06-28 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @override
	 * How is a payment method's validate() used? https://mage2.pro/t/698
	 * @see \Magento\Payment\Model\MethodInterface::validate()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L230-L237
	 * @see \Magento\Payment\Model\Method\AbstractMethod::validate()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L566-L583
	 * @used-by \Magento\Quote\Model\Quote\Payment::importData()
	 * 		$method->validate();
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Quote/Model/Quote/Payment.php#L202
	 * @used-by \Magento\Sales\Model\AdminOrder\Create::_validate()
	 * 		$method->validate();
	 * https://github.com/magento/magento2/blob/2.3.5-p2/app/code/Magento/Sales/Model/AdminOrder/Create.php#L2012
	 * @used-by \Magento\Sales\Model\Order\Payment::place()
	 * 		$methodInstance->validate();
	 * @return $this
	 * @throws LE
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	function validate() {
		$i = $this->getInfoInstance(); /** @var QP $i */
		$i->setCcNumber($n = preg_replace('/[\-\s]+/', '', $i->getCcNumber()));
		$i->setCcType(self::cardBrand($n));
		return parent::validate();
	}	

	/**
	 * 2021-06-28 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @override
	 * @see \Magento\Payment\Model\MethodInterface::void()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/MethodInterface.php#L288-L295
	 * @see \Magento\Payment\Model\Method\AbstractMethod::void()
	 * https://github.com/magento/magento2/blob/6ce74b2/app/code/Magento/Payment/Model/Method/AbstractMethod.php#L671-L686
	 * @param II|I|OP $i
	 * @return $this
	 * @uses _void()
	 */
	function void(II $i) {
		$i->setAmount(df_assert_gt0($a = $i->getAmountAuthorized()));
		# 2021-07-06 A string like «10000003».
		df_assert_sne($parentId = $i->getParentTransactionId()); /** @var string $parentId */
		$req = $this->buildRequest($i, self::$VOID);
		$res = $this->postRequest($req, self::$VOID);
		if (self::$APPROVED != $res->getResponseCode()) {
			self::err($res->getResponseReasonText());
		}
		$i->setStatus(self::STATUS_VOID);
		if ($res->getTransactionId() != $parentId) {
			$i->setTransactionId($res->getTransactionId());
		}
		$i->setIsTransactionClosed(1);
		$i->setShouldCloseParentTransaction(1);
		$i->setTransactionAdditionalInfo('real_transaction_id', $res->getTransactionId());
		return $this;
	}

	/**
	 * 2021-06-27 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @used-by validate()
	 * @see \Df\Payment\BankCardNetworkDetector::p()
	 * @param string $n
	 * @return string
	 */
	private static function cardBrand($n) {
		$r = null; /** @var string|null $list */
		$list = [
			//Solo, Switch or Maestro. International safe
			'SO' => '/(^(6334)[5-9](\d{11}$|\d{13,14}$))|(^(6767)(\d{12}$|\d{14,15}$))/',
			'SM' => '/(^(5[0678])\d{11,18}$)|(^(6[^05])\d{11,18}$)|(^(601)[^1]\d{9,16}$)|(^(6011)\d{9,11}$)' .
				'|(^(6011)\d{13,16}$)|(^(65)\d{11,13}$)|(^(65)\d{15,18}$)' .
				'|(^(49030)[2-9](\d{10}$|\d{12,13}$))|(^(49033)[5-9](\d{10}$|\d{12,13}$))' .
				'|(^(49110)[1-2](\d{10}$|\d{12,13}$))|(^(49117)[4-9](\d{10}$|\d{12,13}$))' .
				'|(^(49118)[0-2](\d{10}$|\d{12,13}$))|(^(4936)(\d{12}$|\d{14,15}$))/',
			// Visa
			'VI' => '/^4[0-9]{12}([0-9]{3})?$/',
			// Master Card
			'MC' => '/^(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}$/',
			// American Express
			'AE' => '/^3[47][0-9]{13}$/',
			// Discover
			'DI' => '/^(6011((0|9|[2-4])[0-9]{11,14}|(74|7[7-9]|8[6-9])[0-9]{10,13})|6(4[4-9][0-9]{13,16}|' .
				'5[0-9]{14,17}))/',
			'DN' => '/^3(0[0-5][0-9]{13,16}|095[0-9]{12,15}|(6|[8-9])[0-9]{14,17})/',
			// UnionPay
			'UN' => '/^622(1(2[6-9][0-9]{10,13}|[3-9][0-9]{11,14})|[3-8][0-9]{12,15}|9([[0-1][0-9]{11,14}|' .
				'2[0-5][0-9]{10,13}))|62[4-6][0-9]{13,16}|628[2-8][0-9]{12,15}/',
			// JCB
			'JCB' => '/^35(2[8-9][0-9]{12,15}|[3-8][0-9]{13,16})/',
			'MI' => '/^(5(0|[6-9])|63|67(?!59|6770|6774))\d*$/',
			'MD' => '/^(6759(?!24|38|40|6[3-9]|70|76)|676770|676774)\d*$/',
		];
		foreach($list as $brand => $exp) {
			if (preg_match($exp, $n)){
				$r = $brand;
				break;
			}
		}
		return $r;
	}	

	/**
	 * 2021-06-29 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @used-by postRequest()
	 * @param $sp21957c
	 * @param string $type
	 * @return array
	 * @throws LE
	 */
	private function beanstreamapi($sp21957c, $type) {
		$sp9c6e65 = $this->getConfigData('merchant_id');
		$spf4dcd7 = $this->getConfigData('merchant_username');
		$sp909eb6 = $this->getConfigData('merchant_password');
		$spb9c31a = substr($sp21957c['x_exp_date'], 0, 2);
		$sp6f57cf = substr($sp21957c['x_exp_date'], -2);
		$spd54e5d = [
			'AB' => 'Alberta',
			'BC' => 'British Columbia',
			'MB' => 'Manitoba',
			'NB' => 'New Brunswick',
			'NL' => 'Newfoundland and Labrador',
			'NS' => 'Nova Scotia',
			'NT' => 'Northwest Territories',
			'NU' => 'Nunavut',
			'ON' => 'Ontario',
			'PE' => 'Prince Edward Island',
			'QC' => 'Quebec',
			'SK' => 'Saskatchewan',
			'YT' => 'Yukon Territory'
	];
		$spd54e5d = array_flip($spd54e5d);
		if (isset($spd54e5d[$sp21957c['x_state']])) {
			$sp21957c['x_state'] = $spd54e5d[$sp21957c['x_state']];
		}
		$spb0dc2a = [];
		$spb0dc2a['Alabama'] = 'AL';
		$spb0dc2a['Alaska'] = 'AK';
		$spb0dc2a['American Samoa'] = 'AS';
		$spb0dc2a['Arizona'] = 'AZ';
		$spb0dc2a['Arkansas'] = 'AR';
		$spb0dc2a['Armed Forces Africa'] = 'AF';
		$spb0dc2a['Armed Forces Americas'] = 'AA';
		$spb0dc2a['Armed Forces Canada'] = 'AC';
		$spb0dc2a['Armed Forces Europe'] = 'AE';
		$spb0dc2a['Armed Forces Middle East'] = 'AM';
		$spb0dc2a['Armed Forces Pacific'] = 'AP';
		$spb0dc2a['California'] = 'CA';
		$spb0dc2a['Colorado'] = 'CO';
		$spb0dc2a['Connecticut'] = 'CT';
		$spb0dc2a['Delaware'] = 'DE';
		$spb0dc2a['District of Columbia'] = 'DC';
		$spb0dc2a['Federated States Of Micronesia'] = 'FM';
		$spb0dc2a['Florida'] = 'FL';
		$spb0dc2a['Georgia'] = 'GA';
		$spb0dc2a['Guam'] = 'GU';
		$spb0dc2a['Hawaii'] = 'HI';
		$spb0dc2a['Idaho'] = 'ID';
		$spb0dc2a['Illinois'] = 'IL';
		$spb0dc2a['Indiana'] = 'IN';
		$spb0dc2a['Iowa'] = 'IA';
		$spb0dc2a['Kansas'] = 'KS';
		$spb0dc2a['Kentucky'] = 'KY';
		$spb0dc2a['Louisiana'] = 'LA';
		$spb0dc2a['Maine'] = 'ME';
		$spb0dc2a['Marshall Islands'] = 'MH';
		$spb0dc2a['Maryland'] = 'MD';
		$spb0dc2a['Massachusetts'] = 'MA';
		$spb0dc2a['Michigan'] = 'MI';
		$spb0dc2a['Minnesota'] = 'MN';
		$spb0dc2a['Mississippi'] = 'MS';
		$spb0dc2a['Missouri'] = 'MO';
		$spb0dc2a['Montana'] = 'MT';
		$spb0dc2a['Nebraska'] = 'NE';
		$spb0dc2a['Nevada'] = 'NV';
		$spb0dc2a['New Hampshire'] = 'NH';
		$spb0dc2a['New Jersey'] = 'NJ';
		$spb0dc2a['New Mexico'] = 'NM';
		$spb0dc2a['New York'] = 'NY';
		$spb0dc2a['North Carolina'] = 'NC';
		$spb0dc2a['North Dakota'] = 'ND';
		$spb0dc2a['Northern Mariana Islands'] = 'MP';
		$spb0dc2a['Ohio'] = 'OH';
		$spb0dc2a['Oklahoma'] = 'OK';
		$spb0dc2a['Oregon'] = 'OR';
		$spb0dc2a['Palau'] = 'PW';
		$spb0dc2a['Pennsylvania'] = 'PA';
		$spb0dc2a['Puerto Rico'] = 'PR';
		$spb0dc2a['Rhode Island'] = 'RI';
		$spb0dc2a['South Carolina'] = 'SC';
		$spb0dc2a['South Dakota'] = 'SD';
		$spb0dc2a['Tennessee'] = 'TN';
		$spb0dc2a['Texas'] = 'TX';
		$spb0dc2a['Utah'] = 'UT';
		$spb0dc2a['Vermont'] = 'VT';
		$spb0dc2a['Virgin Islands'] = 'VI';
		$spb0dc2a['Virginia'] = 'VA';
		$spb0dc2a['Washington'] = 'WA';
		$spb0dc2a['West Virginia'] = 'WV';
		$spb0dc2a['Wisconsin'] = 'WI';
		$spb0dc2a['Wyoming'] = 'WY';
		if ($sp21957c['x_country'] == '') {
			if ($sp21957c['x_state'] != '') {
				if (isset($spd54e5d[$sp21957c['x_state']])) {
					$sp21957c['x_country'] = 'CA';
				} elseif (isset($spb0dc2a[$sp21957c['x_state']])) {
					$sp21957c['x_country'] = 'US';
				}
			}
		}
		if ($sp21957c['x_country'] == 'US') {
			$sp21957c['x_state'] = $spb0dc2a[$sp21957c['x_state']];
		}

		if ($sp21957c['x_country'] != 'US' && $sp21957c['x_country'] != 'CA') {
			$sp21957c['x_state'] = '--';
		}
		if (isset($sp21957c['x_card_code']) && !empty($sp21957c['x_card_code'])) {
			$sp2bde2d = $sp21957c['x_card_code'];
		}
		else {
			$sp2bde2d = '';
		}
		$spbd0c59 = 'P';
		$sp8d1f04 = '';
		if ($type == self::$AUTH_CAPTURE) {
			$spbd0c59 = 'P';
		}
		elseif ($type == self::$AUTH_ONLY) {
			$spbd0c59 = 'PA';
		}
		elseif ($type == self::$PRIOR_AUTH_CAPTURE) {
			$spbd0c59 = 'PAC';
			$sp8d1f04 = '&adjId=' . $sp21957c['x_trans_id'];
		}
		elseif ($type == self::$VOID) {
			$spbd0c59 = 'PAC';
			$spd28804 = explode('--', $sp21957c['x_trans_id']);
			$sp8d1f04 = '&adjId=' . $spd28804[0];
			$sp21957c['x_amount'] = 0.0;
		}
		$custIp = $sp21957c['x_customer_ip'];
		if (array_key_exists("HTTP_CF_CONNECTING_IP", $_SERVER)) {
			$custIp = $_SERVER["HTTP_CF_CONNECTING_IP"];
		}
		$sp05e2c8 = "requestType=BACKEND&merchant_id={$sp9c6e65}&
		username={$spf4dcd7}&
		password={$sp909eb6}&
		trnType={$spbd0c59}&
		trnAmount={$sp21957c['x_amount']}&
		trnOrderNumber={$sp21957c['x_invoice_num']}&
		trnCardOwner=" . urlencode($sp21957c['x_first_name']) . '+' . urlencode($sp21957c['x_last_name']) . "&
		trnCardNumber={$sp21957c['x_card_num']}&
		trnExpMonth={$spb9c31a}&
		trnExpYear={$sp6f57cf}&
		trnCardCvd={$sp2bde2d}&
		customerIp={$custIp}&
		ordEmailAddress={$sp21957c['x_email']}&
		ordName=" . urlencode($sp21957c['x_first_name'] . ' ' . $sp21957c['x_last_name']) . "&
		ordPhoneNumber={$sp21957c['x_phone']}&
		ordAddress1=" . urlencode($sp21957c['x_address']) . '&
		ordAddress2=&ordCity=' . urlencode($sp21957c['x_city']) . '&
		ordProvince=' . urlencode($sp21957c['x_state']) . '&
		ordPostalCode=' . urlencode($sp21957c['x_zip']) . "&
		ordCountry={$sp21957c['x_country']}" . $sp8d1f04;
		$specd301 = curl_init();
		curl_setopt($specd301, CURLOPT_URL, 'https://www.beanstream.com/scripts/process_transaction.asp');
		curl_setopt($specd301, CURLOPT_POST, 1);

		curl_setopt($specd301, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($specd301, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($specd301, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($specd301, CURLOPT_POSTFIELDS, $sp05e2c8);
		$spf8f74c = curl_exec($specd301);
		$sp35fa42 = '';
		$sp35fa42 = curl_error($specd301);
		curl_close($specd301);
		if ($sp35fa42 != '') {
			df_log_l($this, ['request' => $sp05e2c8, 'response' => $sp35fa42], 'error-curl');
			self::err('Error: ' . $sp35fa42);
		}
		# 2021-03-20 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		# "Beanstream: «Microsoft OLE DB Driver for SQL Server» / «TCP Provider: The wait operation timed out» /
		# «C:\INETPUB\BEANSTREAM\ERRORPAGES\../admin/include/VBScript_ado_connection_v2.asp»":
		# https://github.com/canadasatellite-ca/site/issues/18
		if (df_contains($spf8f74c, 'Microsoft OLE DB Driver for SQL Server')) {
			df_log_l($this, ['request' => $sp05e2c8, 'response' => $spf8f74c], 'error-ole');
			self::err('Error: ' . $spf8f74c);
		}
		$sp1e8be2 = explode('&', $spf8f74c);
		$spb41165 = [];
		foreach (@$sp1e8be2 as $sp107d68) {
			list($sp005512, $sp5b9bbc) = explode('=', $sp107d68);
			$spb41165[$sp005512] = strip_tags(urldecode($sp5b9bbc));
		}
		# 2021-03-20 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		# "Prevent the `Schogini_Beanstream` module from logging successful transactions to `beanstream.log`":
		# https://github.com/canadasatellite-ca/site/issues/17
		if ('N' !== ($errorType = dfa($spb41165, 'errorType', 'unknown'))) { /** @var string $errorType */
			df_log_l($this, [
				'request' => $sp05e2c8, 'response parsed' => $spb41165, 'response raw' => $spf8f74c
			], "error-$errorType");
		}
		$spc59ec5 = [];
		$spc59ec5['response_code'] = '1';
		$spc59ec5['response_subcode'] = '1';
		$spc59ec5['response_reason_code'] = '1';
		$spc59ec5['response_reason_text'] = '(TESTMODE2) This transaction has been approved.';
		$spc59ec5['approval_code'] = '000000';
		$spc59ec5['avs_result_code'] = 'P';
		$spc59ec5['transaction_id'] = '0';
		$spc59ec5['md5_hash'] = '382065EC3B4C2F5CDC424A730393D2DF';
		$spc59ec5['card_code_response'] = '';
		if ($spb41165['trnApproved'] == 1) {
			$spc59ec5['response_reason_text'] = '';
			$spc59ec5['response_code'] = '1';
			if (isset($spb41165['messageText']) && !empty($spb41165['messageText'])) {
				$spc59ec5['response_reason_text'] = $spb41165['messageText'];
			}
			if (isset($spb41165['messageId']) && !empty($spb41165['messageId'])) {
				$spc59ec5['response_reason_code'] = $spb41165['messageId'];
			}
			if (isset($spb41165['authCode']) && !empty($spb41165['authCode'])) {
				$spc59ec5['approval_code'] = $spb41165['authCode'];
			}
			if (isset($spb41165['avsResult']) && !empty($spb41165['avsResult'])) {
				$spc59ec5['avs_result_code'] = $spb41165['avsResult'];
			}
			if (isset($spb41165['trnId']) && !empty($spb41165['trnId'])) {
				$spc59ec5['transaction_id'] = $spb41165['trnId'];
			}
		} else {
			$spc59ec5['response_code'] = '0';
			$spc59ec5['response_subcode'] = '0';
			$spc59ec5['response_reason_code'] = '0';
			$spc59ec5['approval_code'] = '000000';
			$spc59ec5['avs_result_code'] = 'P';
			$spc59ec5['transaction_id'] = '0';
			$spc59ec5['response_reason_text'] = '';
			if (isset($spb41165['messageText']) && !empty($spb41165['messageText'])) {
				$spc59ec5['response_reason_text'] = $spb41165['messageText'];
			}
			if (empty($spb41165['errorFields'])) {
				$spb41165['errorFields'] = 'Transaction has been DECLINED.';
			}
			$spc59ec5['response_reason_text'] .= '-' . $spb41165['errorFields'];
		}
		return $spc59ec5;
	}

	/**
	 * 2021-06-28 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @used-by authorize()
	 * @used-by capture()
	 * @used-by refund()
	 * @used-by void()
	 * @param II $i
	 * @param string $type
	 * @return _DO
	 */
	private function buildRequest(II $i, $type) {
		$o = $i->getOrder(); /** @var O $o */
		$req = new _DO;
		$req->setXType($type);
		if ($i->getAmount()) {
			$req->setXAmount($i->getAmount());
			$req->setXCurrencyCode($o->getBaseCurrencyCode());
		}
		switch ($type) {
			case self::$REFUND:
			case self::$VOID:
			case self::$PRIOR_AUTH_CAPTURE:
				$req->setXCardCode($i->getCcCid());
				$req->setXCardName($i->getCcOwner());
				$req->setXCardNum($i->getCcNumber());
				$req->setXExpDate(sprintf('%02d-%04d', $i->getCcExpMonth(), $i->getCcExpYear()));
				$req->setXTransId($i->getCcTransId());
				break;
		}
		if (!empty($o)) {
			$req->setXInvoiceNum($o->getIncrementId());
			$ba = $o->getBillingAddress();
			if (!empty($ba)) {
				$sp864f41 = $ba->getEmail();
				if (!$sp864f41) {
					$sp864f41 = $o->getBillingAddress()->getEmail();
				}
				if (!$sp864f41) {
					$sp864f41 = $o->getCustomerEmail();
				}
				$req->setXFirstName($ba->getFirstname())
					->setXLastName($ba->getLastname())
					->setXCompany($ba->getCompany())
					->setXAddress($ba->getStreet(1)[0])
					->setXCity($ba->getCity())
					->setXState($ba->getRegion())
					->setXZip($ba->getPostcode())
					->setXCountry($ba->getCountry())
					->setXPhone($ba->getTelephone())
					->setXFax($ba->getFax())
					->setXCustId($ba->getCustomerId())
# 2021-06-11 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
# «Ensure that the Customer IP address is being passed in the API request for all transactions»:
# https://github.com/canadasatellite-ca/site/issues/175
					->setXCustomerIp(df_visitor_ip())
					->setXCustomerTaxId($ba->getTaxId())
					->setXEmail($sp864f41)
					->setXEmailCustomer($this->getConfigData('email_customer'))
					->setXMerchantEmail($this->getConfigData('merchant_email'));
				if (!$req->getXCountry()) {
					$req->setXCountry($ba->getCountryId());
				}
			}
			$sa = $o->getShippingAddress();
			if (!$sa) {
				$sa = $ba;
			}
			$amtShipping = $o->getShippingAmount(); /** @var float $amtShipping */
			$amtTax = $o->getTaxAmount(); /** @var float $amtTax */
			$subtotal = $o->getSubtotal(); /** @var float $subtotal */
			if (!empty($sa)) {
				$req->setXShipToFirstName($sa->getFirstname())
					->setXShipToLastName($sa->getLastname())
					->setXShipToCompany($sa->getCompany())
					->setXShipToAddress($sa->getStreet(1)[0])
					->setXShipToCity($sa->getCity())
					->setXShipToState($sa->getRegion())
					->setXShipToZip($sa->getPostcode())
					->setXShipToCountry($sa->getCountry());

				if (!isset($amtShipping) || $amtShipping <= 0) {
					$amtShipping = $sa->getShippingAmount();
				}
				if (!isset($amtTax) || $amtTax <= 0) {
					$amtTax = $sa->getTaxAmount();
				}
				if (!isset($subtotal) || $subtotal <= 0) {
					$subtotal = $sa->getSubtotal();
				}
			}
			$req->setXPoNum($i->getPoNumber())->setXTax($amtTax)->setXSubtotal($subtotal)->setXFreight($amtShipping);
		}
		if ($i->getCcNumber()) {
			$req->setXCardNum($i->getCcNumber())->setXExpDate(sprintf('%02d-%04d', $i->getCcExpMonth(), $i->getCcExpYear()))->setXCardCode($i->getCcCid())->setXCardName($i->getCcOwner());
		}
		return $req;
	}

	/**
	 * 2021-06-28 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @used-by authorize()
	 * @used-by capture()
	 * @used-by refund()
	 * @used-by void()
	 * @param _DO $req
	 * @param string $type
	 * @return mixed
	 * @throws LE
	 */
	private function postRequest(_DO $req, $type) {
		$res = new _DO;
		$sp21957c = $req->getData();
		$spa81281 = array(
			0 => '1',
			1 => '1',
			2 => '1',
			3 => '(TESTMODE) This transaction has been approved.',
			4 => '000000',
			5 => 'P',
			6 => '0',
			7 => '100000018',
			8 => '',
			9 => '2704.99',
			10 => 'CC',
			11 => 'auth_only',
			12 => '',
			13 => 'Sreeprakash',
			14 => 'N.',
			15 => 'Schogini',
			16 => 'XYZ',
			17 => 'City',
			18 => 'Idaho',
			19 => '695038',
			20 => 'US',
			21 => '1234567890',
			22 => '',
			23 => '',
			24 => 'Sreeprakash',
			25 => 'N.',
			26 => 'Schogini',
			27 => 'XYZ',
			28 => 'City',
			29 => 'Idaho',
			30 => '695038',
			31 => 'US',
			32 => '',
			33 => '',
			34 => '',
			35 => '',
			36 => '',
			37 => '382065EC3B4C2F5CDC424A730393D2DF',
			38 => '',
			39 => '',
			40 => '',
			41 => '',
			42 => '',
			43 => '',
			44 => '',
			45 => '',
			46 => '',
			47 => '',
			48 => '',
			49 => '',
			50 => '',
			51 => '',
			52 => '',
			53 => '',
			54 => '',
			55 => '',
			56 => '',
			57 => '',
			58 => '',
			59 => '',
			60 => '',
			61 => '',
			62 => '',
			63 => '',
			64 => '',
			65 => '',
			66 => '',
			67 => ''
		);
		$spa81281[7] = $sp21957c['x_invoice_num'];
		$spa81281[8] = '';
		$spa81281[9] = $sp21957c['x_amount'];
		$spa81281[10] = null;
		$spa81281[11] = $type;
		$spa81281[12] = $sp21957c['x_cust_id'];
		$spa81281[13] = $sp21957c['x_first_name'];
		$spa81281[14] = $sp21957c['x_last_name'];
		$spa81281[15] = $sp21957c['x_company'];
		$spa81281[16] = $sp21957c['x_address'];
		$spa81281[17] = $sp21957c['x_city'];
		$spa81281[18] = $sp21957c['x_state'];
		$spa81281[19] = $sp21957c['x_zip'];
		$spa81281[20] = $sp21957c['x_country'];
		$spa81281[21] = $sp21957c['x_phone'];
		$spa81281[22] = $sp21957c['x_fax'];
		$spa81281[23] = '';
		$sp21957c['x_ship_to_first_name'] = !isset($sp21957c['x_ship_to_first_name']) ? $sp21957c['x_first_name'] : $sp21957c['x_ship_to_first_name'];
		$sp21957c['x_ship_to_first_name'] = !isset($sp21957c['x_ship_to_first_name']) ? $sp21957c['x_first_name'] : $sp21957c['x_ship_to_first_name'];
		$sp21957c['x_ship_to_last_name'] = !isset($sp21957c['x_ship_to_last_name']) ? $sp21957c['x_last_name'] : $sp21957c['x_ship_to_last_name'];
		$sp21957c['x_ship_to_company'] = !isset($sp21957c['x_ship_to_company']) ? $sp21957c['x_company'] : $sp21957c['x_ship_to_company'];
		$sp21957c['x_ship_to_address'] = !isset($sp21957c['x_ship_to_address']) ? $sp21957c['x_address'] : $sp21957c['x_ship_to_address'];
		$sp21957c['x_ship_to_city'] = !isset($sp21957c['x_ship_to_city']) ? $sp21957c['x_city'] : $sp21957c['x_ship_to_city'];
		$sp21957c['x_ship_to_state'] = !isset($sp21957c['x_ship_to_state']) ? $sp21957c['x_state'] : $sp21957c['x_ship_to_state'];
		$sp21957c['x_ship_to_zip'] = !isset($sp21957c['x_ship_to_zip']) ? $sp21957c['x_zip'] : $sp21957c['x_ship_to_zip'];
		$sp21957c['x_ship_to_country'] = !isset($sp21957c['x_ship_to_country']) ? $sp21957c['x_country'] : $sp21957c['x_ship_to_country'];
		$spa81281[24] = $sp21957c['x_ship_to_first_name'];
		$spa81281[25] = $sp21957c['x_ship_to_last_name'];
		$spa81281[26] = $sp21957c['x_ship_to_company'];
		$spa81281[27] = $sp21957c['x_ship_to_address'];
		$spa81281[28] = $sp21957c['x_ship_to_city'];
		$spa81281[29] = $sp21957c['x_ship_to_state'];
		$spa81281[30] = $sp21957c['x_ship_to_zip'];
		$spa81281[31] = $sp21957c['x_ship_to_country'];
		$spa81281[0] = '1';
		$spa81281[1] = '1';
		$spa81281[2] = '1';
		$spa81281[3] = '(TESTMODE2) This transaction has been approved.';
		$spa81281[4] = '000000';
		$spa81281[5] = 'P';
		$spa81281[6] = '0';
		$spa81281[37] = '382065EC3B4C2F5CDC424A730393D2DF';
		$spa81281[39] = '';
		$spc59ec5 = $this->beanstreamapi($sp21957c, $type);
		$spa81281[0] = $spc59ec5['response_code'];
		$spa81281[1] = $spc59ec5['response_subcode'];
		$spa81281[2] = $spc59ec5['response_reason_code'];
		$spa81281[3] = $spc59ec5['response_reason_text'];
		$spa81281[4] = $spc59ec5['approval_code'];
		$spa81281[5] = $spc59ec5['avs_result_code'];
		$spa81281[6] = $spc59ec5['transaction_id'];
		$spa81281[37] = $spc59ec5['md5_hash'];
		$spa81281[39] = $spc59ec5['card_code_response'];
		if (!$spa81281) {
			self::err('Error in payment gateway');
		}
		$res->setResponseCode((int)str_replace('"', '', $spa81281[0]));
		$res->setResponseSubcode((int)str_replace('"', '', $spa81281[1]));
		$res->setResponseReasonCode((int)str_replace('"', '', $spa81281[2]));
		$res->setResponseReasonText($spa81281[3]);
		$res->setApprovalCode($spa81281[4]);
		$res->setAvsResultCode($spa81281[5]);
		$res->setTransactionId($spa81281[6]);
		$res->setInvoiceNumber($spa81281[7]);
		$res->setDescription($spa81281[8]);
		$res->setAmount($spa81281[9]);
		$res->setMethod($spa81281[10]);
		$res->setTransactionType($spa81281[11]);
		$res->setCustomerId($spa81281[12]);
		$res->setMd5Hash($spa81281[37]);
		$res->setCardCodeResponseCode($spa81281[39]);
		return $res;
	}

	/**
	 * 2021-06-29 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @used-by authorize()
	 * @used-by beanstreamapi()
	 * @used-by capture()
	 * @used-by postRequest()
	 * @used-by refund()
	 * @used-by void()
	 * @param Phrase|string|null $m [optional]
	 * @throws LE
	 */
	private static function err($m = null) {throw new LE(__($m ?: 'Payment error occurred.'));}

	/**
	 * 2021-07-01 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @used-by authorize()
	 * @used-by capture()
	 * @used-by refund()
	 * @used-by void()
	 * @var int
	 */
	private static $APPROVED = 1;

	/**
	 * 2021-07-06 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @used-by beanstreamapi()
	 * @used-by capture()
	 * @var string
	 */
	private static $AUTH_CAPTURE = 'AUTH_CAPTURE';

	/**
	 * 2021-07-06 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @used-by authorize()
	 * @used-by beanstreamapi()
	 * @var string
	 */
	private static $AUTH_ONLY = 'AUTH_ONLY';

	/**
	 * 2021-07-01 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @used-by beanstreamapi()
	 * @used-by buildRequest()
	 * @used-by capture()
	 * @var string
	 */
	private static $PRIOR_AUTH_CAPTURE = 'PRIOR_AUTH_CAPTURE';		
	
	/**
	 * 2021-07-01 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @used-by buildRequest()
	 * @used-by refund()
	 * @var string 
	 */
	private static $REFUND = 'REFUND';

	/**
	 * 2021-07-01 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `Schogini_Beanstream` module": https://github.com/canadasatellite-ca/site/issues/176
	 * @used-by beanstreamapi()
	 * @used-by buildRequest()
	 * @used-by void()
	 * @var string
	 */
	private static $VOID = 'VOID';
}