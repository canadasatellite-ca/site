<?php
/**
 * Cart2Quote
 */

namespace MageSuper\Casat\Model\PurchaseOrder\Pdf\Config;

/**
 * Class SchemaLocator
 * Attributes config schema locator
 *
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Config
 */
class SchemaLocator extends \Magento\Sales\Model\Order\Pdf\Config\SchemaLocator implements \Magento\Framework\Config\SchemaLocatorInterface
{
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
    function __construct(\Magento\Framework\Module\Dir\Reader $moduleReader)
    {
        $dir = $moduleReader->getModuleDir(\Magento\Framework\Module\Dir::MODULE_ETC_DIR, 'Cart2Quote_Quotation');
        $this->_schema = $dir . '/po_pdf.xsd';
        $this->_schemaFile = $dir . '/po_pdf_file.xsd';
    }

    /**
     * Get path to merged config schema
     */
    function getSchema()
    {
        return $this->_schema;
    }

    /**
     * Get path to per file validation schema
     */
    function getPerFileSchema()
    {
        return $this->_schemaFile;
    }
}
