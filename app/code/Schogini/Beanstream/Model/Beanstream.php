<?php
namespace Schogini\Beanstream\Model;

use Schogini\Beanstream\Model\BeanstreamException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Model\Order;
use Monolog\Logger;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Framework\DataObject;

class Beanstream extends \Magento\Payment\Model\Method\Cc
{
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
    const REQUEST_TYPE_AUTH_CAPTURE = 'AUTH_CAPTURE';
    const REQUEST_TYPE_AUTH_ONLY = 'AUTH_ONLY';
    const REQUEST_TYPE_CAPTURE_ONLY = 'CAPTURE_ONLY';
    const REQUEST_TYPE_CREDIT = 'REFUND';
    const REQUEST_TYPE_VOID = 'VOID';
    const REQUEST_TYPE_PRIOR_AUTH_CAPTURE = 'PRIOR_AUTH_CAPTURE';
    const RESPONSE_CODE_APPROVED = 1;
    const RESPONSE_CODE_DECLINED = 2;
    const RESPONSE_CODE_ERROR = 3;
    const RESPONSE_CODE_HELD = 4;

    public $beanstreamLogger;

    public function __construct(
        \Magento\Framework\Model\Context $sp58b303,
        \Magento\Framework\Registry $sp7a3bd5,
        \Magento\Framework\Api\ExtensionAttributesFactory $spefc1b3,
        \Magento\Framework\Api\AttributeValueFactory $sp6c48a7,
        \Magento\Payment\Helper\Data $spc7669b,
        \Magento\Framework\App\Config\ScopeConfigInterface $sp49d401,
        \Magento\Payment\Model\Method\Logger $spbabc0e,
        \Magento\Framework\Module\ModuleListInterface $sp1d4edd,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $sp94b432,
        \Schogini\Beanstream\Model\Request\Factory $spd5edf9,
        \Schogini\Beanstream\Model\Response\Factory $spd4c858,
        \Magento\Backend\Model\Auth\Session $spa652ea,
        \Schogini\Beanstream\Model\Logger\Logger $beanstreamLogger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $spf17733 = null,
        \Magento\Framework\Data\Collection\AbstractDb $spd3c755 = null,
        array $sp7cb355 = array()
    ) {
        $this->requestFactory = $spd5edf9;
        $this->responseFactory = $spd4c858;
        $this->authSession = $spa652ea;
        $this->beanstreamLogger = $beanstreamLogger;
        parent::__construct($sp58b303, $sp7a3bd5, $spefc1b3, $sp6c48a7, $spc7669b, $sp49d401, $spbabc0e, $sp1d4edd, $sp94b432, $spf17733, $spd3c755, $sp7cb355);
    }

    public function isAvailable(CartInterface $sp6f1aa8 = null)
    {
        $quote = $sp6f1aa8;
        if (!$this->isActive($quote ? $quote->getStoreId() : null)) {
            return false;
        }

        $checkResult = new DataObject();
        $checkResult->setData('is_available', true);

        // for future use in observers
        $this->_eventManager->dispatch(
            'payment_method_is_active',
            [
                'result' => $checkResult,
                'method_instance' => $this,
                'quote' => $quote
            ]
        );

        return $checkResult->getData('is_available');
    }
    public function authorize(\Magento\Payment\Model\InfoInterface $sp46490f, $spb954c6)
    {
        if ($spb954c6 <= 0) {
            self::throwException(__('Invalid amount for capture.'));
        }
        $sp62e25f = false;
        if ($spb954c6 > 0) {
            $sp46490f->setAnetTransType(self::REQUEST_TYPE_AUTH_ONLY);
            $sp46490f->setAmount($spb954c6);
            $sp3382ae = $this->_buildRequest($sp46490f);
            $sp1d8681 = $this->_postRequest($sp3382ae);
            $sp46490f->setCcApproval($sp1d8681->getApprovalCode())->setLastTransId($sp1d8681->getTransactionId())->setCcTransId($sp1d8681->getTransactionId())->setCcAvsStatus($sp1d8681->getAvsResultCode())->setCcCidStatus($sp1d8681->getCardCodeResponseCode());
            $spbd1c75 = $sp1d8681->getResponseReasonCode();
            $spd17c47 = $sp1d8681->getResponseReasonText();
            switch ($sp1d8681->getResponseCode()) {
                case self::RESPONSE_CODE_APPROVED:
                    $sp46490f->setStatus(self::STATUS_APPROVED);
                    if ($sp1d8681->getTransactionId() != $sp46490f->getParentTransactionId()) {
                        $sp46490f->setTransactionId($sp1d8681->getTransactionId());
                    }
                    $sp46490f->setIsTransactionClosed(0)->setTransactionAdditionalInfo('real_transaction_id', $sp1d8681->getTransactionId());
                    break;
                case self::RESPONSE_CODE_DECLINED:
                    $sp62e25f = __('Payment authorization transaction has been declined. ' . "\n{$spd17c47}");
                    break;
                default:
                    $sp62e25f = __('Payment authorization error. ' . "\n{$spd17c47}");
                    break;
            }
        } else {
            $sp62e25f = __('Invalid amount for authorization.');
        }
        if ($sp62e25f !== false) {
            self::throwException($sp62e25f);
        }
        return $this;
    }

    public function capture(\Magento\Payment\Model\InfoInterface $sp46490f, $spb954c6)
    {
        $sp62e25f = false;
        if ($sp46490f->getParentTransactionId()) {
            $sp46490f->setAnetTransType(self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE);
        } else {
            $sp46490f->setAnetTransType(self::REQUEST_TYPE_AUTH_CAPTURE);
        }
        $sp46490f->setAmount($spb954c6);
        $sp3382ae = $this->_buildRequest($sp46490f);
        $sp1d8681 = $this->_postRequest($sp3382ae);
        if ($sp1d8681->getResponseCode() == self::RESPONSE_CODE_APPROVED) {
            $sp46490f->setStatus(self::STATUS_APPROVED);
            $sp46490f->setCcTransId($sp1d8681->getTransactionId());
            $sp46490f->setLastTransId($sp1d8681->getTransactionId());
            if ($sp1d8681->getTransactionId() != $sp46490f->getParentTransactionId()) {
                $sp46490f->setTransactionId($sp1d8681->getTransactionId());
            }
            $sp46490f->setIsTransactionClosed(0)->setTransactionAdditionalInfo('real_transaction_id', $sp1d8681->getTransactionId());
        } else {
            if ($sp1d8681->getResponseReasonText()) {
                $sp62e25f = $sp1d8681->getResponseReasonText();
            } else {
                $sp62e25f = __('Error in capturing the payment');
            }
            if (!($sp300378 = $sp46490f->getOrder())) {
                $sp300378 = $sp46490f->getQuote();
            }
            $sp300378->addStatusToHistory($sp300378->getStatus(), urldecode($sp62e25f) . ' at Beanstream', $sp62e25f . ' from Beanstream');
        }
        if ($sp62e25f !== false) {
            self::throwException($sp62e25f);
        }
        return $this;
    }

    public function refund(\Magento\Payment\Model\InfoInterface $sp46490f, $spb954c6)
    {
        $sp62e25f = false;
        $sp57fc4d = $sp46490f->getRefundTransactionId();
        if (empty($sp57fc4d)) {
            $sp57fc4d = $sp46490f->getParentTransactionId();
        }
        if (($this->getConfigData('test') && $sp57fc4d == 0 || $sp57fc4d) && $spb954c6 > 0) {
            $sp46490f->setAnetTransType(self::REQUEST_TYPE_CREDIT);
            $sp3382ae = $this->_buildRequest($sp46490f);
            $sp3382ae->setXAmount($spb954c6);
            $sp1d8681 = $this->_postRequest($sp3382ae);
            if ($sp1d8681->getResponseCode() == self::RESPONSE_CODE_APPROVED) {
                $sp46490f->setStatus(self::STATUS_SUCCESS);
                if ($sp1d8681->getTransactionId() != $sp46490f->getParentTransactionId()) {
                    $sp46490f->setTransactionId($sp1d8681->getTransactionId());
                }
                $sp41f7d8 = $sp46490f->getOrder()->canCreditmemo() ? 0 : 1;
                $sp46490f->setIsTransactionClosed(1)->setShouldCloseParentTransaction($sp41f7d8)->setTransactionAdditionalInfo('real_transaction_id', $sp1d8681->getTransactionId());
            } else {
                $spfc96e2 = $sp1d8681->getResponseReasonText();
                $sp62e25f = true;
            }
        } else {
            $spfc96e2 = __('Error in refunding the payment');
            $sp62e25f = true;
        }
        if ($sp62e25f !== false) {
            self::throwException($spfc96e2);
        }
        return $this;
    }

    public function void(\Magento\Payment\Model\InfoInterface $sp46490f)
    {
        $sp62e25f = false;
        $sp57fc4d = $sp46490f->getVoidTransactionId();
        if (empty($sp57fc4d)) {
            $sp57fc4d = $sp46490f->getParentTransactionId();
        }
        $spb954c6 = $sp46490f->getAmount();
        if ($spb954c6 <= 0) {
            $spb954c6 = $sp46490f->getAmountAuthorized();
            $sp46490f->setAmount($sp46490f->getAmountAuthorized());
        }
        if ($sp57fc4d && $spb954c6 > 0) {
            $sp46490f->setAnetTransType(self::REQUEST_TYPE_VOID);
            $sp3382ae = $this->_buildRequest($sp46490f);
            $sp1d8681 = $this->_postRequest($sp3382ae);
            if ($sp1d8681->getResponseCode() == self::RESPONSE_CODE_APPROVED) {
                $sp46490f->setStatus(self::STATUS_VOID);
                if ($sp1d8681->getTransactionId() != $sp46490f->getParentTransactionId()) {
                    $sp46490f->setTransactionId($sp1d8681->getTransactionId());
                }
                $sp46490f->setIsTransactionClosed(1)->setShouldCloseParentTransaction(1)->setTransactionAdditionalInfo('real_transaction_id', $sp1d8681->getTransactionId());
            } else {
                $spfc96e2 = $sp1d8681->getResponseReasonText();
                $sp62e25f = true;
            }
        } else {
            if (!$sp57fc4d) {
                $spfc96e2 = __('Error in voiding the payment. Transaction ID not found');
                $sp62e25f = true;
            } else {
                if ($spb954c6 <= 0) {
                    $spfc96e2 = __('Error in voiding the payment. Payment amount is 0');
                    $sp62e25f = true;
                } else {
                    $spfc96e2 = __('Error in voiding the payment');
                    $sp62e25f = true;
                }
            }
        }
        if ($sp62e25f !== false) {
            self::throwException($spfc96e2);
        }
        return $this;
    }

    protected function _buildRequest(\Magento\Payment\Model\InfoInterface $sp46490f)
    {
        $sp300378 = $sp46490f->getOrder();
        $sp3382ae = $this->requestFactory->create();
        $sp3382ae->setXTestRequest($this->getConfigData('test') ? 'TRUE' : 'FALSE');
        $sp3382ae->setXLogin($this->getConfigData('login'))->setXTranKey($this->getConfigData('trans_key'))->setXType($sp46490f->getAnetTransType())->setXMethod($sp46490f->getAnetTransMethod());
        if ($sp46490f->getAmount()) {
            $sp3382ae->setXAmount($sp46490f->getAmount(), 2);
            $sp3382ae->setXCurrencyCode($sp300378->getBaseCurrencyCode());
        }
        switch ($sp46490f->getAnetTransType()) {
            case self::REQUEST_TYPE_CREDIT:
            case self::REQUEST_TYPE_VOID:
            case self::REQUEST_TYPE_PRIOR_AUTH_CAPTURE:
                $sp3382ae->setXTransId($sp46490f->getCcTransId());
                $sp3382ae->setXCardNum($sp46490f->getCcNumber())->setXExpDate(sprintf('%02d-%04d', $sp46490f->getCcExpMonth(), $sp46490f->getCcExpYear()))->setXCardCode($sp46490f->getCcCid())->setXCardName($sp46490f->getCcOwner());
                break;
            case self::REQUEST_TYPE_CAPTURE_ONLY:
                $sp3382ae->setXAuthCode($sp46490f->getCcAuthCode());
                break;
        }
        if (!empty($sp300378)) {
            $spcf7599 = $sp300378->getShippingAmount();
            $spba68ac = $sp300378->getTaxAmount();
            $sp9dfdb6 = $sp300378->getSubtotal();
            $sp3382ae->setXInvoiceNum($sp300378->getIncrementId());
            $sp4ed284 = $sp300378->getBillingAddress();
            if (!empty($sp4ed284)) {
                $sp864f41 = $sp4ed284->getEmail();
                if (!$sp864f41) {
                    $sp864f41 = $sp300378->getBillingAddress()->getEmail();
                }
                if (!$sp864f41) {
                    $sp864f41 = $sp300378->getCustomerEmail();
                }
                $sp3382ae->setXFirstName($sp4ed284->getFirstname())
                    ->setXLastName($sp4ed284->getLastname())
                    ->setXCompany($sp4ed284->getCompany())
                    ->setXAddress($sp4ed284->getStreet(1)[0])
                    ->setXCity($sp4ed284->getCity())
                    ->setXState($sp4ed284->getRegion())
                    ->setXZip($sp4ed284->getPostcode())
                    ->setXCountry($sp4ed284->getCountry())
                    ->setXPhone($sp4ed284->getTelephone())
                    ->setXFax($sp4ed284->getFax())
                    ->setXCustId($sp4ed284->getCustomerId())
                    ->setXCustomerIp($sp300378->getRemoteIp())
                    ->setXCustomerTaxId($sp4ed284->getTaxId())
                    ->setXEmail($sp864f41)
                    ->setXEmailCustomer($this->getConfigData('email_customer'))
                    ->setXMerchantEmail($this->getConfigData('merchant_email'));

                if (!$sp3382ae->getXCountry()) {
                    $sp3382ae->setXCountry($sp4ed284->getCountryId());
                }
            }
            $sp79f718 = $sp300378->getShippingAddress();
            if (!$sp79f718) {
                $sp79f718 = $sp4ed284;
            }
            if (!empty($sp79f718)) {
                $sp3382ae->setXShipToFirstName($sp79f718->getFirstname())
                    ->setXShipToLastName($sp79f718->getLastname())
                    ->setXShipToCompany($sp79f718->getCompany())
                    ->setXShipToAddress($sp79f718->getStreet(1)[0])
                    ->setXShipToCity($sp79f718->getCity())
                    ->setXShipToState($sp79f718->getRegion())
                    ->setXShipToZip($sp79f718->getPostcode())
                    ->setXShipToCountry($sp79f718->getCountry());

                if (!isset($spcf7599) || $spcf7599 <= 0) {
                    $spcf7599 = $sp79f718->getShippingAmount();
                }
                if (!isset($spba68ac) || $spba68ac <= 0) {
                    $spba68ac = $sp79f718->getTaxAmount();
                }
                if (!isset($sp9dfdb6) || $sp9dfdb6 <= 0) {
                    $sp9dfdb6 = $sp79f718->getSubtotal();
                }
            }
            $sp3382ae->setXPoNum($sp46490f->getPoNumber())->setXTax($spba68ac)->setXSubtotal($sp9dfdb6)->setXFreight($spcf7599);
        }
        if ($sp46490f->getCcNumber()) {
            $sp3382ae->setXCardNum($sp46490f->getCcNumber())->setXExpDate(sprintf('%02d-%04d', $sp46490f->getCcExpMonth(), $sp46490f->getCcExpYear()))->setXCardCode($sp46490f->getCcCid())->setXCardName($sp46490f->getCcOwner());
        }
        return $sp3382ae;
    }

    protected function _postRequest(\Schogini\Beanstream\Model\Request $sp3382ae)
    {
        $sp1d8681 = $this->responseFactory->create();
        $sp21957c = $sp3382ae->getData();
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
        $spa81281[10] = $sp21957c['x_method'];
        $spa81281[11] = $sp21957c['x_type'];
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
        $spc59ec5 = $this->_beanstreamapi($sp21957c);
        $spa81281[0] = $spc59ec5['response_code'];
        $spa81281[1] = $spc59ec5['response_subcode'];
        $spa81281[2] = $spc59ec5['response_reason_code'];
        $spa81281[3] = $spc59ec5['response_reason_text'];
        $spa81281[4] = $spc59ec5['approval_code'];
        $spa81281[5] = $spc59ec5['avs_result_code'];
        $spa81281[6] = $spc59ec5['transaction_id'];
        $spa81281[37] = $spc59ec5['md5_hash'];
        $spa81281[39] = $spc59ec5['card_code_response'];
        if ($spa81281) {
            $sp1d8681->setResponseCode((int)str_replace('"', '', $spa81281[0]));
            $sp1d8681->setResponseSubcode((int)str_replace('"', '', $spa81281[1]));
            $sp1d8681->setResponseReasonCode((int)str_replace('"', '', $spa81281[2]));
            $sp1d8681->setResponseReasonText($spa81281[3]);
            $sp1d8681->setApprovalCode($spa81281[4]);
            $sp1d8681->setAvsResultCode($spa81281[5]);
            $sp1d8681->setTransactionId($spa81281[6]);
            $sp1d8681->setInvoiceNumber($spa81281[7]);
            $sp1d8681->setDescription($spa81281[8]);
            $sp1d8681->setAmount($spa81281[9]);
            $sp1d8681->setMethod($spa81281[10]);
            $sp1d8681->setTransactionType($spa81281[11]);
            $sp1d8681->setCustomerId($spa81281[12]);
            $sp1d8681->setMd5Hash($spa81281[37]);
            $sp1d8681->setCardCodeResponseCode($spa81281[39]);
        } else {
            self::throwException(__('Error in payment gateway'));
        }
        return $sp1d8681;
    }

    function _beanstreamapi($sp21957c)
    {
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
        $spb0dc2a = array();
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
        } else {
            $sp2bde2d = '';
        }
        $spbd0c59 = 'P';
        $sp8d1f04 = '';
        if ($sp21957c['x_type'] == 'AUTH_CAPTURE') {
            $spbd0c59 = 'P';
        } elseif ($sp21957c['x_type'] == 'AUTH_ONLY') {
            $spbd0c59 = 'PA';
        } elseif ($sp21957c['x_type'] == 'CAPTURE_ONLY' || $sp21957c['x_type'] == 'PRIOR_AUTH_CAPTURE') {
            $spbd0c59 = 'PAC';
            $sp8d1f04 = '&adjId=' . $sp21957c['x_trans_id'];
        } elseif ($sp21957c['x_type'] == 'CREDIT') {
            $spbd0c59 = 'R';
            $spd28804 = explode('--', $sp21957c['x_trans_id']);
            $sp8d1f04 = '&adjId=' . $spd28804[0];
        } elseif ($sp21957c['x_type'] == 'VOID') {
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

        $this->logit('Calling CURL with POST', [$sp05e2c8]);
        $this->beanstreamLogger('Calling CURL with POST', [$sp05e2c8]);
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
            $this->logit('_beanstreamapi curl_error', array('curl_error' => $sp35fa42));
            $this->beanstreamLogger('error', [$sp35fa42]);
            self::throwException('Error: ' . $sp35fa42);
        }
        $this->logit('_beanstreamapi curl_response', [$spf8f74c]);
        $this->beanstreamLogger('beanstreamapi curl_response', [$spf8f74c]);
        # 2021-03-20 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		# "Beanstream: «Microsoft OLE DB Driver for SQL Server» / «TCP Provider: The wait operation timed out» /
		# «C:\INETPUB\BEANSTREAM\ERRORPAGES\../admin/include/VBScript_ado_connection_v2.asp»":
		# https://github.com/canadasatellite-ca/site/issues/18
        if (df_contains($spf8f74c, 'Microsoft OLE DB Driver for SQL Server')) {
        	df_log_l($this, ['request' => $sp05e2c8, 'response' => $spf8f74c], 'error-ole');
		}
        $sp1e8be2 = explode('&', $spf8f74c);
        $spb41165 = array();
        foreach (@$sp1e8be2 as $sp107d68) {
            list($sp005512, $sp5b9bbc) = explode('=', $sp107d68);
            $spb41165[$sp005512] = strip_tags(urldecode($sp5b9bbc));
        }
        $this->logit('Response Array', $spb41165);
        $this->beanstreamLogger('Response Array', [$spb41165]);

        $spc59ec5 = array();
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

    public static function throwException($sp485bd8 = null)
    {
        if (is_null($sp485bd8)) {
            $sp485bd8 = __('Payment error occurred.');
        }
        if (!($sp485bd8 instanceof \Magento\Framework\Phrase)) {
            $sp485bd8 = __($sp485bd8);
        }
        throw new \Magento\Framework\Exception\LocalizedException($sp485bd8);
    }

    public static function getResourceModel($sp069fc3)
    {
        $sp88ad3c = \Magento\Framework\App\ObjectManager::getInstance();
        return $sp88ad3c->get($sp069fc3);
    }

    public function decrypt($sp882b00)
    {
        return self::getResourceModel('\\Magento\\Framework\\Encryption\\EncryptorInterface')->decrypt($sp882b00);
    }

    private function beanstreamLogger($title, $sp1e8be2 = [])
    {
        $spb0dc2a = '';
        if (count($sp1e8be2) > 0) {
            $spb0dc2a = var_export($sp1e8be2, true);
        }
        $sp349edd = '<creditcard>
						<cardnumber>xxxxxxxxxxxxxxxx</cardnumber>
						<cardexpmonth>xx</cardexpmonth>
						<cardexpyear>xx</cardexpyear>
						<cvmvalue>xxx</cvmvalue>
						<cvmindicator>provided</cvmindicator>
					</creditcard>';
        $spb0dc2a = preg_replace('/<creditcard>(.*)<\\/creditcard>/smUi', $sp349edd, $spb0dc2a);
        $this->beanstreamLogger->info($title . '   ----------   ' . $spb0dc2a);
    }

    function logit($sp914372, $sp1e8be2 = array())
    {
        if (!$this->getConfigData('debug') || !$this->getConfigData('test')) {
            return;
        }
        $spb0dc2a = '';
        if (count($sp1e8be2) > 0) {
            $spb0dc2a = var_export($sp1e8be2, true);
        }
        $sp349edd = '<creditcard>
						<cardnumber>xxxxxxxxxxxxxxxx</cardnumber>
						<cardexpmonth>xx</cardexpmonth>
						<cardexpyear>xx</cardexpyear>
						<cvmvalue>xxx</cvmvalue>
						<cvmindicator>provided</cvmindicator>
					</creditcard>';
        $spb0dc2a = preg_replace('/<creditcard>(.*)<\\/creditcard>/smUi', $sp349edd, $spb0dc2a);
        $spbabc0e = self::getResourceModel('\\Magento\\Framework\\Logger\\Monolog');
        $spbabc0e->addRecord(Logger::INFO, '----- Inside ' . $sp914372 . ' =1= ' . date('d/M/Y H:i:s') . ' -----' . '
' . $spb0dc2a);
    }


    /**
     * Validate payment method information object
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validate()
    {
        /*
         * calling parent validate function
         */
        $info = $this->getInfoInstance();
        $ccNumber = $info->getCcNumber();

        // remove credit card number delimiters such as "-" and space
        $ccNumber = preg_replace('/[\-\s]+/', '', $ccNumber);
        $info->setCcNumber($ccNumber);
        $autoDetectCcType = $this->autoDetectCcType($ccNumber);
        $info->setCcType($autoDetectCcType);
        return  parent::validate();
    }

    public function autoDetectCcType($ccNumber)
    {
        $ccTypeRegExpList = [
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
        foreach($ccTypeRegExpList as $cardType=>$regExp){
            if (preg_match($ccTypeRegExpList[$cardType],$ccNumber)){
                return $cardType;
            }
        }
    }
}
