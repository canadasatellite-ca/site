<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Config\Backend\AllReviewsPage;

use Aheadworks\AdvancedReviews\Model\Config\Backend\AllReviewsPage\RequestPath;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\UrlRewrite\Helper\UrlRewrite as UrlRewriteHelper;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReviews\Model\Url\Rewrite\Service\CustomEntity as UrlRewriteCustomEntityService;
use Magento\Store\Model\Website;
use Aheadworks\AdvancedReviews\Model\Config;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Config\Backend\AllReviewsPage\RequestPath
 */
class RequestPathTest extends TestCase
{
    /**
     * Config option path
     */
    const PATH = 'aw_advanced_reviews/general/all_reviews_page_route';

    /**
     * @var RequestPath
     */
    private $configValue;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var UrlRewriteHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlRewriteHelperMock;

    /**
     * @var UrlRewriteCustomEntityService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlRewriteCustomEntityServiceMock;

    /**
     * @var ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->urlRewriteHelperMock = $this->createMock(UrlRewriteHelper::class);
        $this->urlRewriteCustomEntityServiceMock = $this->createMock(UrlRewriteCustomEntityService::class);
        $this->configMock = $this->createMock(ScopeConfigInterface::class);

        $this->configValue = $objectManager->getObject(
            RequestPath::class,
            [
                'storeManager' => $this->storeManagerMock,
                'urlRewriteHelper' => $this->urlRewriteHelperMock,
                'urlRewriteCustomEntityService' => $this->urlRewriteCustomEntityServiceMock,
                '_config' => $this->configMock,
            ]
        );

        $this->configValue->setPath(self::PATH);
    }

    /**
     * Test beforeSave method
     *
     * @param string|null $value
     * @param string|bool $expectedValue false if exception to be thrown
     * @dataProvider beforeSaveDataProvider
     */
    public function testBeforeSave($value, $expectedValue)
    {
        $this->configValue->setValue($value);

        if ($expectedValue === false) {
            $this->urlRewriteHelperMock->expects($this->once())
                ->method('validateRequestPath')
                ->with($value)
                ->willThrowException(new LocalizedException(__("Validation error!")));
            $this->expectException(LocalizedException::class);
        } else {
            $this->urlRewriteHelperMock->expects($this->once())
                ->method('validateRequestPath')
                ->with($value)
                ->willReturn(true);
        }

        $this->configValue->beforeSave();
        $this->assertEquals($expectedValue, $this->configValue->getValue());
    }

    /**
     * @return array
     */
    public function beforeSaveDataProvider()
    {
        return [
            [null, null],
            ['', ''],
            ['all-reviews', 'all-reviews'],
            ['valid-request-path', 'valid-request-path'],
            ['//invalid-request-path', false],
            ['//invalid-request#path', false],
            ['invalid-request#path', false],
        ];
    }

    /**
     * Test afterSave method when config value wasn't changed
     *
     * @param string $scope
     * @param string $scopeCode
     * @param string $value
     * @param string $oldValue
     * @dataProvider afterSaveValueNotChangedDataProvider
     */
    public function testAfterSaveValueNotChanged($scope, $scopeCode, $value, $oldValue)
    {
        $this->configValue->setScope($scope);
        $this->configValue->setScopeCode($scopeCode);
        $this->configValue->setValue($value);

        $this->configMock->expects($this->any())
            ->method('getValue')
            ->with(self::PATH, $scope, $scopeCode)
            ->willReturn($oldValue);

        $this->storeManagerMock->expects($this->never())
            ->method('getWebsite');
        $this->storeManagerMock->expects($this->never())
            ->method('getStores');

        $this->urlRewriteCustomEntityServiceMock->expects($this->never())
            ->method('deleteRewrites');
        $this->urlRewriteCustomEntityServiceMock->expects($this->never())
            ->method('addRewrites');

        $this->configValue->afterSave();
    }

    /**
     * @return array
     */
    public function afterSaveValueNotChangedDataProvider()
    {
        return [
            [
                'scope'         => 'default',
                'scopeCode'     => '',
                'value'         => '',
                'oldValue'      => '',
            ],
            [
                'scope'         => 'default',
                'scopeCode'     => '',
                'value'         => 'url-path',
                'oldValue'      => 'url-path',
            ],
        ];
    }

    /**
     * Test afterSave method
     *
     * @param string $scope
     * @param int $scopeId
     * @param string $scopeCode
     * @param string $value
     * @param string $oldValue
     * @param array $storesToProcessRewrites
     * @dataProvider afterSaveDataProvider
     */
    public function testAfterSaveValue($scope, $scopeId, $scopeCode, $value, $oldValue, $storesToProcessRewrites)
    {
        $this->configValue->setScope($scope);
        $this->configValue->setScopeId($scopeId);
        $this->configValue->setScopeCode($scopeCode);
        $this->configValue->setValue($value);

        $this->setUpStoreManagerMock();
        $this->setUpConfigMock($scope, $scopeCode, $oldValue);

        $this->urlRewriteCustomEntityServiceMock->expects($this->once())
            ->method('deleteRewrites')
            ->with($storesToProcessRewrites, $oldValue);
        $this->urlRewriteCustomEntityServiceMock->expects($this->once())
            ->method('addRewrites')
            ->with($storesToProcessRewrites, $value, Config::ALL_REVIEWS_PAGE_CANONICAL_URL_PATH);

        $this->configValue->afterSave();
    }

    /**
     * @return array
     */
    public function afterSaveDataProvider()
    {
        return [
            [
                'scope'                     => 'stores',
                'scopeId'                   => 1,
                'scopeCode'                 => 'store-1',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
                'storesToProcessRewrites'   => [1],
            ],
            [
                'scope'                     => 'stores',
                'scopeId'                   => 2,
                'scopeCode'                 => 'store-2',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
                'storesToProcessRewrites'   => [2],
            ],
            [
                'scope'                     => 'stores',
                'scopeId'                   => 3,
                'scopeCode'                 => 'store-3',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
                'storesToProcessRewrites'   => [3],
            ],
            [
                'scope'                     => 'stores',
                'scopeId'                   => 4,
                'scopeCode'                 => 'store-4',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
                'storesToProcessRewrites'   => [4],
            ],
            [
                'scope'                     => 'stores',
                'scopeId'                   => 5,
                'scopeCode'                 => 'store-5',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
                'storesToProcessRewrites'   => [5],
            ],
            [
                'scope'                     => 'stores',
                'scopeId'                   => 6,
                'scopeCode'                 => 'store-6',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
                'storesToProcessRewrites'   => [6],
            ],
            [
                'scope'                     => 'stores',
                'scopeId'                   => 7,
                'scopeCode'                 => 'store-7',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
                'storesToProcessRewrites'   => [7],
            ],
            [
                'scope'                     => 'stores',
                'scopeId'                   => 8,
                'scopeCode'                 => 'store-8',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
                'storesToProcessRewrites'   => [8],
            ],
            [
                'scope'                     => 'stores',
                'scopeId'                   => 9,
                'scopeCode'                 => 'store-9',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
                'storesToProcessRewrites'   => [9],
            ],
            [
                'scope'                     => 'websites',
                'scopeId'                   => 1,
                'scopeCode'                 => 'first_webs',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
                'storesToProcessRewrites'   => [1,2,3],
            ],
            [
                'scope'                     => 'websites',
                'scopeId'                   => 2,
                'scopeCode'                 => 'second_webs',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
                'storesToProcessRewrites'   => [4, 6],
            ],
            [
                'scope'                     => 'websites',
                'scopeId'                   => 3,
                'scopeCode'                 => 'third_webs',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
                'storesToProcessRewrites'   => [],
            ],
            [
                'scope'                     => 'default',
                'scopeId'                   => 0,
                'scopeCode'                 => '',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
                'storesToProcessRewrites'   => [1,2,3,4,6],
            ],
        ];
    }

    /**
     * Init store manager mock for tests
     *
     * @return void
     */
    private function setUpStoreManagerMock()
    {
        $firstWebsiteStores = [1 => 1, 2 => 2, 3 => 3];
        $firstWebsiteMock = $this->createMock(Website::class);
        $firstWebsiteMock->expects($this->any())
            ->method('getStoreIds')
            ->willReturn($firstWebsiteStores);
        $secondWebsiteStores = [4 => 4, 5 => 5, 6 => 6];
        $secondWebsiteMock = $this->createMock(Website::class);
        $secondWebsiteMock->expects($this->any())
            ->method('getStoreIds')
            ->willReturn($secondWebsiteStores);
        $thirdWebsiteStores = [7 => 7, 8 => 8, 9 => 9];
        $thirdWebsiteMock = $this->createMock(Website::class);
        $thirdWebsiteMock->expects($this->any())
            ->method('getStoreIds')
            ->willReturn($thirdWebsiteStores);
        $this->storeManagerMock->expects($this->any())
            ->method('getWebsite')
            ->willReturnMap(
                [
                    [
                        1,
                        $firstWebsiteMock
                    ],
                    [
                        2,
                        $secondWebsiteMock
                    ],
                    [
                        3,
                        $thirdWebsiteMock
                    ],
                ]
            );
        $this->storeManagerMock->expects($this->any())
            ->method('getStores')
            ->willReturn($firstWebsiteStores + $secondWebsiteStores + $thirdWebsiteStores);
    }

    /**
     * Init config mock for tests
     *
     * @param string $scope
     * @param string $scopeCode
     * @param string $oldValue
     * @return void
     */
    private function setUpConfigMock($scope, $scopeCode, $oldValue)
    {
        $this->configMock->expects($this->any())
            ->method('getValue')
            ->willReturnMap(
                [
                    [
                        self::PATH,
                        $scope,
                        $scopeCode,
                        $oldValue
                    ],
                    [
                        self::PATH,
                        ScopeInterface::SCOPE_STORE,
                        1,
                        $oldValue,
                    ],
                    [
                        self::PATH,
                        ScopeInterface::SCOPE_STORE,
                        2,
                        $oldValue,
                    ],
                    [
                        self::PATH,
                        ScopeInterface::SCOPE_STORE,
                        3,
                        $oldValue,
                    ],
                    [
                        self::PATH,
                        ScopeInterface::SCOPE_STORE,
                        4,
                        $oldValue,
                    ],
                    [
                        self::PATH,
                        ScopeInterface::SCOPE_STORE,
                        5,
                        'custom-value-for-store-5',
                    ],
                    [
                        self::PATH,
                        ScopeInterface::SCOPE_STORE,
                        6,
                        $oldValue,
                    ],
                    [
                        self::PATH,
                        ScopeInterface::SCOPE_STORE,
                        7,
                        'custom-value-for-store-7',
                    ],
                    [
                        self::PATH,
                        ScopeInterface::SCOPE_STORE,
                        8,
                        'custom-value-for-store-8',
                    ],
                    [
                        self::PATH,
                        ScopeInterface::SCOPE_STORE,
                        9,
                        'custom-value-for-store-9',
                    ],
                ]
            );
    }

    /**
     * Test afterSave method
     *
     * @param string $scope
     * @param int $scopeId
     * @param string $scopeCode
     * @param string $value
     * @param string $oldValue
     * @dataProvider afterSaveWithExceptionDataProvider
     */
    public function testAfterSaveValueWithException($scope, $scopeId, $scopeCode, $value, $oldValue)
    {
        $storesToProcessRewrites = [];
        $this->configValue->setScope($scope);
        $this->configValue->setScopeId($scopeId);
        $this->configValue->setScopeCode($scopeCode);
        $this->configValue->setValue($value);

        $this->storeManagerMock->expects($this->any())
            ->method('getWebsite')
            ->with($scopeId)
            ->willThrowException(new LocalizedException(__("Test error")));

        $this->configMock->expects($this->any())
            ->method('getValue')
            ->with(self::PATH, $scope, $scopeCode)
            ->willReturn($oldValue);

        $this->urlRewriteCustomEntityServiceMock->expects($this->once())
            ->method('deleteRewrites')
            ->with($storesToProcessRewrites, $oldValue);
        $this->urlRewriteCustomEntityServiceMock->expects($this->once())
            ->method('addRewrites')
            ->with($storesToProcessRewrites, $value, Config::ALL_REVIEWS_PAGE_CANONICAL_URL_PATH);

        $this->configValue->afterSave();
    }

    /**
     * @return array
     */
    public function afterSaveWithExceptionDataProvider()
    {
        return [
            [
                'scope'                     => 'websites',
                'scopeId'                   => 1,
                'scopeCode'                 => 'first_webs',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
            ],
            [
                'scope'                     => 'websites',
                'scopeId'                   => 2,
                'scopeCode'                 => 'second_webs',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
            ],
            [
                'scope'                     => 'websites',
                'scopeId'                   => 3,
                'scopeCode'                 => 'third_webs',
                'value'                     => 'old-all-reviews-request-path',
                'oldValue'                  => 'new-all-reviews-request-path',
            ],
        ];
    }
}
