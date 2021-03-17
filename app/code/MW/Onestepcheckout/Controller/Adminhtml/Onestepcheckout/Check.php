<?php

namespace MW\Onestepcheckout\Controller\Adminhtml\Onestepcheckout;

class Check extends \MW\Onestepcheckout\Controller\Adminhtml\Onestepcheckout
{
	public function execute()
	{
        // Read config.xml file to get default config values
        $configXmlFile = $this->_moduleReader->getModuleDir('etc', 'MW_Onestepcheckout') . '/config.xml';
        $configXml = $this->_xmlParser->load($configXmlFile);
        $configValues = $configXml->xmlToArray();
        $defaultValues = $configValues['config']['_value']['default'];

        // Push default config values to an array
        $result = [];
        foreach ($defaultValues as $key => $value) {
            $result[$key] = (array)$value;
            $childValues = (array)$value;

            foreach ($childValues as $childKey => $childValue) {
                $result[$key][$childKey] = (array)$childValue;
            }
        }

        $this->_dataHelper->saveStoreConfig('onestepcheckout/deliverydate/weekend', '');

        // Return json data
        $response = $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result);
        $this->getResponse()->setBody($response);

        return;
	}
}
