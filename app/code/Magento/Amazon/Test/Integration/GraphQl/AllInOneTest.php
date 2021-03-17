<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Test\Integration\GraphQl;

/**
 * Very generic all-in-one test which validates that we somehow fetching all the data without errors
 */
class AllInOneTest extends \Magento\Amazon\Test\Integration\GraphQl\BaseTest
{
    /**
     * @magentoDataFixture Magento_Amazon::Test/Integration/fixtures/create_stores.php
     * @magentoDataFixture Magento_Amazon::Test/Integration/fixtures/create_orders.php
     */
    public function testStores(): void
    {
        $this->markTestIncomplete('This test is not testing anything right now and should be rewritten');
        $gql = $this->getFixtureData('all_in_one.graphql');
        $result = $this->query($gql);
        $this->assertHasNoError($result);
        $stores = $this->getChildNode($result, 'data.allStores');
        $this->assertNotEmpty($stores, 'Stores should not be empty');
        $this->assertCount(count($this->getFixtureData('stores')), $stores, 'Stores should not be empty');
        $stores = $this->getChildNode($result, 'data.uuids');
        $this->assertNotEmpty($stores, 'UUIDs should not be empty');

        /* BASIC STORE WITH ORDERS */
        $storeWithOrders = $this->getChildNode($result, 'data.ordersStore');
        $this->assertNotEmpty($storeWithOrders['basicOrders']);
        $orders = $storeWithOrders['basicOrders'];
        $this->assertArrayHasKey('edges', $orders);
        $this->assertFinite($orders['totalCount']);
        $this->assertArrayHasKey('endCursor', $orders['pageInfo']);
        $this->assertArrayHasKey('hasNextPage', $orders['pageInfo']);
        $this->assertEquals(1, $storeWithOrders['reports']['lifetime']);
        $reportTotal = array_reduce($storeWithOrders['reports']['revenue'], function ($carry, $day): float {
            return $carry + $day['revenue'];
        }, 0);
        $this->assertEquals(1, $reportTotal);

        /* FILTER ORDERS BY TOTAL */
        foreach ($storeWithOrders['filterByTotal']['edges'] as $edge) {
            $this->assertGreaterThan(0.1, $edge['node']['total']);
            $this->assertLessThan(2, $edge['node']['total']);
        }

        /* LIMIT */
        $this->assertEquals(1, count($storeWithOrders['limit']['edges']));
        $this->assertEquals(3, $storeWithOrders['limit']['totalCount']);
        $this->assertEquals(true, $storeWithOrders['limit']['pageInfo']['hasNextPage']);

        /* SORTING */
        $edges = $storeWithOrders['sort']['edges'];
        $totals = array_map(function ($edge) {
            return $edge['node']['total'];
        }, $edges);
        sort($totals);
        foreach ($edges as $i => $edge) {
            $this->assertEquals($totals[$i], $edge['node']['total']);
        }
    }
}
