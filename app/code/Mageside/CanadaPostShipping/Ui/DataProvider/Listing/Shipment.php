<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Ui\DataProvider\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

/**
 * Class FilterDataProvider
 */
class Shipment extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $manifestId = $this->request->getParam('manifest_id');
        if ($manifestId) {
            $this->data['config']['params']['manifest_id'] = $manifestId;

            $this->addFilter(
                $this->filterBuilder
                    ->setField('manifest_id')
                    ->setValue($manifestId)
                    ->create()
            );
        }
        $data = parent::getData();

        return $data;
    }
}
