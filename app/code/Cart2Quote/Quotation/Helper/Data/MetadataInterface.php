<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper\Data;

/**
 * Interface MetadataInterface
 *
 * @package Cart2Quote\Quotation\Helper\Data
 */
interface MetadataInterface
{
    /**
     * Get PHP version
     *
     * @return string
     */
    public function getPhpVersion();

    /**
     * Get magento version
     *
     * @return string
     */
    public function getMagentoVersion();

    /**
     * Get magento edition
     *
     * @return string
     */
    public function getMagentoEdition();

    /**
     * Get ioncube version
     *
     * @return bool|string
     */
    public function getIoncubeVersion();

    /**
     * This function gets the ionCube version from the integer version string
     * - It also has a fallback for ionCube < v3.1
     *
     * @return string
     */
    public function getIoncubeLoaderVersion();

    /**
     * Get C2Q version
     *
     * @return bool|string
     */
    public function getCart2QuoteVersion();

    /**
     * Get N2O version
     *
     * @return bool|string
     */
    public function getNot2OrderVersion();

    /**
     * Get SalesRep version
     *
     * @return bool|string
     */
    public function getSalesRepVersion();

    /**
     * Get support desk version
     *
     * @return bool|string
     */
    public function getSupportDeskVersion();

    /**
     * Get desk email version
     *
     * @return bool|string
     */
    public function getDeskEmailVersion();

    /**
     * Get the Module version and return unknown if not found
     *
     * @param string $moduleName
     * @return bool|string
     */
    public function getModuleVersion($moduleName);

    /**
     * @return string
     */
    public function getOrderId();

    /**
     * @param string $orderId
     * @return $this
     */
    public function setOrderId($orderId);
}
