<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Cart2Quote\Quotation\Test\Integration;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteManagement;
use Magento\Quote\Model\QuoteRepository;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class CreateOrderTest
 *
 * @package Cart2Quote\Quotation\Test\Integration
 */
class CreateOrderTest extends TestCase
{
    /**
     * @magentoConfigFixture default_store quotation_general/general/cart2quote_enable 1
     * @magentoConfigFixture default_store cart2quote_quotation/global/enable 1
     * @magentoDataFixture Magento/Sales/_files/quote_with_customer.php
     * @magentoAppIsolation enabled
     */
    public function testCreateOrder()
    {
        /** @var QuoteRepository $quoteRepository */
        $quoteRepository = Bootstrap::getObjectManager()->get(QuoteRepository::class);

        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class);

        $searchCriteria = $searchCriteriaBuilder->create();
        $searchResults = $quoteRepository->getList($searchCriteria);

        /** @var QuoteManagement $quoteManagement */
        $quoteManagement = Bootstrap::getObjectManager()->get(QuoteManagement::class);

        /** @var Quote $quote */
        foreach ($searchResults->getItems() as $quote) {
            // Set shipping method
            $quote->getShippingAddress()->setCollectShippingRates(true)
                ->collectShippingRates()
                ->setShippingMethod('flatrate_flatrate');

            $quoteManagement->submit($quote);
        }
    }
}
