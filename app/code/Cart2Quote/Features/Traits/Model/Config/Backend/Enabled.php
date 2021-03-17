<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Config\Backend;
/**
 * Trait Enabled
 * @package Cart2Quote\Quotation\Model\Config\Backend
 */
trait Enabled
{
    /**
     * @return \Magento\Framework\App\Config\Value
     */
    private function beforeSave()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$outputPath = "advanced/modules_disable_output/Cart2Quote_Quotation";
        $this->configWriter->save($outputPath, !boolval($this->getValue()));
        return parent::beforeSave();
		}
	}
}