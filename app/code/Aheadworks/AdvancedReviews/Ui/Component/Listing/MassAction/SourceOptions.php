<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Listing\MassAction;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\UrlInterface;
# 2021-04-25 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
# «Class argument is invalid: Aheadworks\AdvancedReviews\Ui\Component\Review\Listing\MassAction\Statuses»:
# https://github.com/canadasatellite-ca/site/issues/80
use Laminas\Stdlib\JsonSerializable;

/**
 * Class SourceOptions
 * @package Aheadworks\AdvancedReviews\Ui\Component\Listing\MassAction
 */
class SourceOptions implements JsonSerializable
	# 2021-04-25 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	# «Class argument is invalid: Aheadworks\AdvancedReviews\Ui\Component\Review\Listing\MassAction\Statuses»:
	# https://github.com/canadasatellite-ca/site/issues/80
	,OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * Additional options params
     *
     * @var array
     */
    private $data;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Base URL for subactions
     *
     * @var string
     */
    private $urlPath;

    /**
     * Param name for subactions
     *
     * @var string
     */
    private $paramName;

    /**
     * Additional params for subactions
     *
     * @var array
     */
    private $additionalData = [];

    /**
     * @var OptionSourceInterface
     */
    private $optionSource;

    /**
     * @param UrlInterface $urlBuilder
     * @param OptionSourceInterface $optionSource
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        OptionSourceInterface $optionSource,
        array $data = []
    ) {
        $this->data = $data;
        $this->urlBuilder = $urlBuilder;
        $this->optionSource = $optionSource;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->options === null) {
            $optionSourceArray = $this->optionSource->toOptionArray();
            $this->prepareData();

            foreach ($optionSourceArray as $key => $option) {
                if ($this->urlPath && $this->paramName) {
                    $this->options[$key] = [
                        'label' => $option['label'],
                        'type' => $this->paramName . '_' . $option['value'],
                        'url' => $this->urlBuilder->getUrl(
                            $this->urlPath,
                            [$this->paramName => $option['value']]
                        )
                    ];
                }
            }
        }

        return $this->options;
    }

	/**
	 * 2021-04-25 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * «Class argument is invalid: Aheadworks\AdvancedReviews\Ui\Component\Review\Listing\MassAction\Statuses»:
	 * https://github.com/canadasatellite-ca/site/issues/80
	 * @override
	 * @see \Magento\Framework\Data\OptionSourceInterface::toOptionArray()
	 * @return array
	 */
    function toOptionArray() {return $this->optionSource->toOptionArray();}

    /**
     * Prepare addition data for subactions
     *
     * @return void
     */
    private function prepareData()
    {
        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}
