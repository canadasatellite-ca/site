<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper;

/**
 * Class FileUpload
 * @package Cart2Quote\Quotation\Helper
 */
class FileUpload extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Path to allowed_file_extensions in system.xml
     */
    const XML_PATH_ALLOWED_FILE_EXTENSION = 'cart2quote_advanced/general/allowed_file_extensions';

    /**
     * @return array|null
     */
    public function getAllowedFileExtensions()
    {
        $allowedExtensions = $this->scopeConfig->getValue(
            self::XML_PATH_ALLOWED_FILE_EXTENSION,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $allowedExtensionsTrimmed = preg_replace('/\s*,\s*/', ',', $allowedExtensions);

        return preg_split('/,/', $allowedExtensionsTrimmed);
    }
}
