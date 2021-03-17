<?php

declare(strict_types=1);

namespace Magento\Amazon\Notification;

use Magento\Amazon\Cache\StoresWithOrdersThatCannotBeImported;
use Magento\Amazon\Ui\FrontendUrl;

class CannotCreateSalesOrders implements \Magento\Framework\Notification\MessageInterface
{
    /**
     * @var StoresWithOrdersThatCannotBeImported
     */
    private $storesWithOrdersThatCannotBeImported;
    /**
     * @var FrontendUrl
     */
    private $frontendUrl;
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    private $acl;

    public function __construct(
        StoresWithOrdersThatCannotBeImported $storesWithOrdersThatCannotBeImported,
        FrontendUrl $frontendUrl,
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->storesWithOrdersThatCannotBeImported = $storesWithOrdersThatCannotBeImported;
        $this->frontendUrl = $frontendUrl;
        $this->acl = $authorization;
    }

    public function getIdentity()
    {
        $key = 'asc-stores' . implode(':', $this->storesWithOrdersThatCannotBeImported->get());
        return hash('sha256', $key);
    }

    public function isDisplayed()
    {
        return !empty($this->storesWithOrdersThatCannotBeImported->get())
            && $this->acl->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    public function getText()
    {
        $storeUrls = [];
        foreach ($this->storesWithOrdersThatCannotBeImported->get() as $uuid => $name) {
            $storeUrls[] = sprintf(
                '<a href="%s">%s</a>',
                $this->frontendUrl->getOrdersGridUrlByUuid($uuid),
                $name
            );
        }

        return __(
            'Your Amazon store(s) has orders that cannot be imported into Magento.'
            . ' See Recent Orders in the store dashboard(s): %1',
            implode(', ', $storeUrls)
        )->render();
    }

    public function getSeverity()
    {
        return \Magento\Framework\Notification\MessageInterface::SEVERITY_CRITICAL;
    }
}
