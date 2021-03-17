<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Pdf\Config;

use Magento\Sales\Model\Order\Pdf\Config\SchemaLocator as MageSchemaLocator;
use Magento\Framework\Config\SchemaLocatorInterface;

/**
 * Class SchemaLocator
 * - Attributes config schema locator
 *
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Config
 */
class SchemaLocator extends MageSchemaLocator implements SchemaLocatorInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\Pdf\Config\SchemaLocator {
        getSchema as private traitGetSchema;
        getPerFileSchema as private traitGetPerFileSchema;
    }

    /**
     * Path to corresponding XSD file with validation rules for merged configs
     *
     * @var string
     */
    private $_schema;

    /**
     * Path to corresponding XSD file with validation rules for individual configs
     *
     * @var string
     */
    private $_schemaFile;

    /**
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     */
    public function __construct(\Magento\Framework\Module\Dir\Reader $moduleReader)
    {
        $dir = $moduleReader->getModuleDir(\Magento\Framework\Module\Dir::MODULE_ETC_DIR, 'Cart2Quote_Quotation');
        $this->_schema = $dir . '/quote_pdf.xsd';
        $this->_schemaFile = $dir . '/quote_pdf_file.xsd';
    }

    /**
     * Get path to merged config schema
     */
    public function getSchema()
    {
        return $this->traitGetSchema();
    }

    /**
     * Get path to per file validation schema
     */
    public function getPerFileSchema()
    {
        return $this->traitGetPerFileSchema();
    }
}
