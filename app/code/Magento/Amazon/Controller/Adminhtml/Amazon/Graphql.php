<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Controller\Adminhtml\Amazon;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;

class Graphql extends \Magento\Backend\App\Action implements
    HttpPostActionInterface,
    HttpGetActionInterface,
    CsrfAwareActionInterface
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    private $rawResponseFactory;

    /**
     * @var \Magento\Amazon\GraphQl\GraphQl
     */
    private $graphQlApi;

    /**
     * Graphql constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $rawResponseFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Amazon\GraphQl\GraphQl $graphQlApi
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $rawResponseFactory,
        \Magento\Amazon\GraphQl\GraphQl $graphQlApi
    ) {
        parent::__construct($context);
        $this->rawResponseFactory = $rawResponseFactory;
        $this->graphQlApi = $graphQlApi;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->rawResponseFactory->create();

        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->getRequest();

        $query = $request->getParam('query');
        $variables = $request->getParam('variables');
        $operationName = $request->getParam('operationName');

        if ($request->isPost()) {
            $body = $request->getContent();
            $body = json_decode($body, true);
            $query = $body['query'] ?? null;
            $variables = $body['variables'] ?? null;
            $operationName = $body['operationName'] ?? null;
        }

        $response->setHeader('Content-Type', 'application/json');
        $response->setContents($this->graphQlApi->handle($query, $variables, $operationName));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?\Magento\Framework\App\Request\InvalidRequestException {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    public function _processUrlKeys()
    {
        $isValid = false;
        if ($this->_auth->isLoggedIn() && $this->_backendUrl->useSecretKey()) {
            $isValid = $this->_validateSecretKey();
        }
        if (!$isValid) {
            $this->getResponse()->representJson($this->graphQlApi->formatError('Authentication failed'));
        }
        return true;
    }
}
