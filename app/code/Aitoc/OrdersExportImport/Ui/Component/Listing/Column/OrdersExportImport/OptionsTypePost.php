<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\OrdersExportImport\Ui\Component\Listing\Column\OrdersExportImport;

use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

/**
 * Store Options for Cms Pages and Blocks
 */
class OptionsTypePost extends StoreOptions
{
    /**
     * All Store Views value
     */
    const LOCAL_SERVER = 'Local Server';
    const REMOTE_FTP = 'Remote FTP';
    const EMAIL = 'Email';

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }
        $options = [
            '0' => [
                'label' => __(self::LOCAL_SERVER),
                'value' => 0
            ],
            '1' => [
                'label' => __(self::REMOTE_FTP),
                'value' => 1
            ],
            '2' => [
                'label' => __(self::EMAIL),
                'value' => 2
            ],
        ];
        $this->options = $options;

        return $this->options;
    }
}
