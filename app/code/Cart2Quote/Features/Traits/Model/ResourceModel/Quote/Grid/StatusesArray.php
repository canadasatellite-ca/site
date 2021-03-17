<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Grid;
/**
 * Quotation quotes statuses option array
 */
trait StatusesArray
{
    /**
     * Return option array
     *
     * @return array
     */
    private function toOptionArray()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->options === null) {
            $options = $this->statusCollectionFactory->create()->toOptionArray();
            array_walk(
                $options,
                function (&$option) {
                    $option['__disableTmpl'] = true;
                }
            );
            $this->options = $options;
        }
        return $this->options;
		}
	}
}
