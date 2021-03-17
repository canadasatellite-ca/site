<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper\Data;

/**
 * Class Metadata
 *
 * @package Cart2Quote\Quotation\Helper\Data
 */
class Metadata extends \Magento\Framework\App\Helper\AbstractHelper implements MetadataInterface
{
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Framework\Module\ResourceInterface
     */
    private $resource;

    /**
     * @var string
     */
    private $orderId;

    /**
     * Metadata constructor.
     *
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Module\ResourceInterface $resource
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Module\ResourceInterface $resource,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->productMetadata = $productMetadata;
        $this->resource = $resource;
    }

    /**
     * Get PHP version
     *
     * @return string
     */
    public function getPhpVersion()
    {
        return phpversion();
    }

    /**
     * Get Magento version
     *
     * @return string
     */
    public function getMagentoVersion()
    {
        if (defined('Magento\Framework\AppInterface::VERSION')) {
            return \Magento\Framework\AppInterface::VERSION;
        } else {
            return $this->productMetadata->getVersion();
        }
    }

    /**
     * Get Magento edition
     *
     * @return string
     */
    public function getMagentoEdition()
    {
        if (defined('Magento\Framework\AppInterface::EDITION')) {
            return \Magento\Framework\AppInterface::EDITION;
        } else {
            return $this->productMetadata->getEdition();
        }
    }

    /**
     * Get ioncube version
     *
     * @return bool|string
     */
    public function getIoncubeVersion()
    {
        if (extension_loaded('ionCube Loader')) {
            return $this->getIoncubeLoaderVersion();
        }

        return false;
    }

    /**
     * Get ioncube load version
     *
     * @return string
     */
    public function getIoncubeLoaderVersion()
    {
        if (function_exists('ioncube_loader_iversion')) {
            $ioncubeLoaderIversion = ioncube_loader_iversion();
            $extra = 0;
            if ($ioncubeLoaderIversion >= 100000) {
                $extra = 1;
            }

            $ioncubeLoaderVersionMajor = (int)substr($ioncubeLoaderIversion, 0, 1 + $extra);
            $ioncubeLoaderVersionMinor = (int)substr($ioncubeLoaderIversion, 1 + $extra, 2);
            $ioncubeLoaderVersionRevision = (int)substr($ioncubeLoaderIversion, 3 + $extra, 2);

            $ioncubeLoaderVersion = "";
            $ioncubeLoaderVersion .= $ioncubeLoaderVersionMajor;
            $ioncubeLoaderVersion .= ".";
            $ioncubeLoaderVersion .= $ioncubeLoaderVersionMinor;
            $ioncubeLoaderVersion .= ".";
            $ioncubeLoaderVersion .= $ioncubeLoaderVersionRevision;
        } else {
            $ioncubeLoaderVersion = ioncube_loader_version();
        }

        return $ioncubeLoaderVersion;
    }

    /**
     * Get C2Q version
     *
     * @return bool|string
     */
    public function getCart2QuoteVersion()
    {
        return $this->getModuleVersion('Cart2Quote_Quotation');
    }

    /**
     * Get the version of a given module
     *
     * @param string $moduleName
     * @return bool|string
     */
    public function getModuleVersion($moduleName)
    {
        $version = $this->resource->getDbVersion($moduleName);
        if (isset($version) && !empty($version)) {
            return $version;
        }

        return false;
    }

    /**
     * Get N2O version
     *
     * @return bool|string
     */
    public function getNot2OrderVersion()
    {
        return $this->getModuleVersion('Cart2Quote_Not2Order');
    }

    /**
     * Get SalesRep version
     *
     * @return bool|string
     */
    public function getSalesRepVersion()
    {
        return $this->getModuleVersion('Cart2Quote_SalesRep');
    }

    /**
     * Get support desk version
     *
     * @return bool|string
     */
    public function getSupportDeskVersion()
    {
        return $this->getModuleVersion('Cart2Quote_Desk');
    }

    /**
     * Get desk email version
     *
     * @return bool|string
     */
    public function getDeskEmailVersion()
    {
        return $this->getModuleVersion('Cart2Quote_DeskEmail');
    }

    /**
     * Get AutoProposal version
     *
     * @return bool|string
     */
    public function getAutoProposalVersion()
    {
        return $this->getModuleVersion('Cart2Quote_AutoProposal');
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param $orderId
     * @return $this
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }
}
