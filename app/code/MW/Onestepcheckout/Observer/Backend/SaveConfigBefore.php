<?php

namespace MW\Onestepcheckout\Observer\Backend;

use Magento\Framework\Event\ObserverInterface;

class SaveConfigBefore implements ObserverInterface
{
	/**
	 * @var \MW\Onestepcheckout\Helper\Data
	 */
	protected $_dataHelper;

	/**
	 * @param \MW\Onestepcheckout\Helper\Data $dataHelper
	 */
	public function __construct(
		\MW\Onestepcheckout\Helper\Data $dataHelper
	) {
		$this->_dataHelper = $dataHelper;
	}

	/**
	 * Handle save configurations
	 *
	 * @param  \Magento\Framework\Event\Observer $observer
	 * @return $this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$onestepcheckoutConfigurations = $_POST;
		if (isset($onestepcheckoutConfigurations['config_state'])
			&& isset($onestepcheckoutConfigurations['config_state']['onestepcheckout_general'])
			&& $onestepcheckoutConfigurations['config_state']['onestepcheckout_general'] != ""
		) {
			if (isset($onestepcheckoutConfigurations['groups']['addfield']['fields'])
				&& isset($onestepcheckoutConfigurations['groups']['config']['fields'])
			) {
			 	$addfield = $onestepcheckoutConfigurations['groups']['addfield']['fields'];
				$config = $onestepcheckoutConfigurations['groups']['config']['fields'];
				$termcondition = $onestepcheckoutConfigurations['groups']['termcondition']['fields'];

				if (isset($config['enabled']['value']) && intval($config['enabled']['value'] == 1)) {
					// Set default country
					if (isset($config['default_country']['value'])) {
						$this->_dataHelper->saveStoreConfig('general/country/default', $config['default_country']['value']);
					}

					if (isset($addfield['street_lines']['value'])
						&& intval($addfield['street_lines']['value']) >= 1
						&& intval($addfield['street_lines']['value']) <= 4
					) {
						$this->_dataHelper->saveStoreConfig('customer/address/street_lines', $addfield['street_lines']['value']);
					} else {
						$this->_dataHelper->saveStoreConfig('customer/address/street_lines', 2);
					}

					if (isset($config['allowguestcheckout']['value'])) {
						$this->_dataHelper->saveStoreConfig('checkout/options/guest_checkout', 1);
						$this->_dataHelper->saveStoreConfig('catalog/downloadable/disable_guest_checkout', 0);
					}

					if (isset($addfield['prefix_show']['value'])) {
						$this->_dataHelper->saveStoreConfig('customer/address/prefix_show', $addfield['prefix_show']['value']);
					}

					if (isset($addfield['middlename_show']['value'])) {
						$this->_dataHelper->saveStoreConfig('customer/address/middlename_show', $addfield['middlename_show']['value']);
					}

					if (isset($addfield['suffix_show']['value'])) {
						$this->_dataHelper->saveStoreConfig('customer/address/suffix_show', $addfield['suffix_show']['value']);
					}

					if (isset($addfield['dob_show']['value'])) {
						$this->_dataHelper->saveStoreConfig('customer/address/dob_show', $addfield['dob_show']['value']);
					}

					if (isset($addfield['taxvat_show']['value'])) {
						$this->_dataHelper->saveStoreConfig('customer/address/taxvat_show', $addfield['taxvat_show']['value']);
					}

					if (isset($termcondition['allow_options']['value'])
						&& intval($termcondition['allow_options']['value']) == 1
					) {
						$this->_dataHelper->saveStoreConfig('checkout/options/enable_agreements', 0);
					}

					/**
					 * Fix Magento Enterprise
					 * TODO: Have to re-check in Enterprise version
					 */
					if (isset($addfield['taxvat_show']['value'])
						&& $addfield['taxvat_show']['value'] != 0
					) {
						$this->_dataHelper->saveStoreConfig('customer/create_account/vat_frontend_visibility',1);
					} else {
						$this->_dataHelper->saveStoreConfig('customer/create_account/vat_frontend_visibility',0);
					}

					if (isset($addfield['gender_show']['value'])) {
						$this->_dataHelper->saveStoreConfig('customer/address/gender_show',$addfield['gender_show']['value']);
					}

					// Set option or required for zip post code, state provice.
					// Config to zip postal code
					if (isset($addfield['zip']['value'])
						&& (intval($addfield['zip']['value']) == 1
							|| intval($addfield['zip']['value']) == 0)
					) {
						$country_allow = $this->_dataHelper->getStoreConfig('general/country/allow');
						$this->_dataHelper->saveStoreConfig('general/country/optional_zip_countries',$country_allow);
					}

					if(isset($addfield['zip']['value']) && intval($addfield['zip']['value']) == 2) {
						$this->_dataHelper->saveStoreConfig('general/country/optional_zip_countries','');
					}

					// Config state is option
					if(isset($addfield['state']['value'])
						&& (intval($addfield['state']['value']) == 1 || intval($addfield['state']['value']) == 0)
					) {
						$this->_dataHelper->saveStoreConfig('general/region/state_required','' );
						$this->_dataHelper->saveStoreConfig('general/region/display_all',1);
					}

					// Config state is required
					if(isset($addfield['state']['value']) && intval($addfield['state']['value']) == 2) {
						$country_allow = $this->_dataHelper->getStoreConfig('general/country/allow');
						$this->_dataHelper->saveStoreConfig('general/region/state_required',$country_allow);
						$this->_dataHelper->saveStoreConfig('general/region/display_all',1);
					}
				} else {
					// Required zip post code with every countries
					$this->_dataHelper->saveStoreConfig('general/country/optional_zip_countries','');
					$this->_dataHelper->saveStoreConfig('general/region/state_required','');
					$this->_dataHelper->saveStoreConfig('general/region/display_all',1);
				}
			}
		}

		return $this;
	}
}
