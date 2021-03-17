<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Pdf\Config;
use Magento\Sales\Model\Order\Pdf\Config\SchemaLocator as MageSchemaLocator;
use Magento\Framework\Config\SchemaLocatorInterface;
/**
 * Trait SchemaLocator
 * - Attributes config schema locator
 *
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Config
 */
trait SchemaLocator
{
    /**
     * Get path to merged config schema
     */
    private function getSchema()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_schema;
		}
	}
    /**
     * Get path to per file validation schema
     */
    private function getPerFileSchema()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_schemaFile;
		}
	}
}
