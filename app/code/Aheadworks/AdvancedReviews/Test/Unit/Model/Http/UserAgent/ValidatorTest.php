<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Http\UserAgent;

use Aheadworks\AdvancedReviews\Model\Http\UserAgent\Validator;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Http\UserAgent\Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->validator = $objectManager->getObject(
            Validator::class,
            []
        );
    }

    /**
     * Test isBot method
     *
     * @param string $userAgent
     * @param bool $result
     * @dataProvider isBotDataProvider
     */
    public function testIsBot($userAgent, $result)
    {
        $this->assertEquals($result, $this->validator->isBot($userAgent));
    }

    /**
     * @return array
     */
    public function isBotDataProvider()
    {
        return [
            [
                'userAgent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
                'result' => true,
            ],
            [
                'userAgent' => 'Mozilla/5.0',
                'result' => false,
            ],
            [
                'userAgent' => '',
                'result' => false,
            ],
        ];
    }
}
