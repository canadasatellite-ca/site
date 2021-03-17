<?php
	namespace Digit\Customshipprice\Model\Carrier;
	
	use Magento\Quote\Model\Quote\Address\RateRequest;
	use Magento\Shipping\Model\Rate\Result;
	
	class Customshipprice extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
	{
		
		protected $_code = 'customshipprice';
		protected $_session ;
		protected $_objectManager ;
		protected $_isFixed = false;
		
		
		/**
			* @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
			* @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
			* @param \Psr\Log\LoggerInterface $logger
			* @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
			* @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
			* @param array $data
		*/
		public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
		\Magento\Backend\Model\Session\Quote $quoteSession,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
		) {
			$this->_rateResultFactory = $rateResultFactory;
			$this->_rateMethodFactory = $rateMethodFactory;
			parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
			$this->_session = $quoteSession;
			$this->_objectManager = $objectManager;
		}
		
		/**
			* Enter description here...
			*
			
		*/
		public function collectRates(RateRequest $request)
		{
			
			$app_state  = $this->_objectManager->get('\Magento\Framework\App\State');
			$area_code  = $app_state->getAreaCode();
			if(!($app_state->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE))
			{
				return false;	
			}
			
			if (!$this->getConfigFlag('active')) {
				return false;
			}
			
			/** @var \Magento\Shipping\Model\Rate\Result $result */
			$result = $this->_rateResultFactory->create();
			
			/** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
			$method = $this->_rateMethodFactory->create();
			/*you can fetch shipping price from different sources over some APIs, we used price from config.xml - xml node price*/
			$shippingPrice = $this->_session->getCustomshippriceAmount();
			$baseShippingPrice = $this->_session->getCustomshippriceBaseAmount();
			$description = $this->_session->getCustomshippriceDescription();
			$method->setCarrier('customshipprice');
			$method->setCarrierTitle($this->getConfigData('title'));
			
			$method->setMethod('customshipprice');
			$method->setMethodTitle((strlen($description) > 0) ? $description : $this->getConfigData('name'));
			
			
			
			$shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);
			
			$method->setPrice($shippingPrice);
			$method->setCost($shippingPrice);
			
			$result->append($method);
			
			return $result;
		}
		
		
		/**
			* @return array
		*/
		public function getAllowedMethods()
		{
			return ['customshipprice' => $this->getConfigData('name')];
		}
	}
	
