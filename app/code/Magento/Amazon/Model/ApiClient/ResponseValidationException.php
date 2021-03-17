<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\ApiClient;

use Magento\Framework\Phrase;
use Psr\Http\Message\ResponseInterface;

/**
 * Exception used to pass the errors related to response validation,
 * e.g. message body contains error, a field is missing, etc.
 */
class ResponseValidationException extends ApiException
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @param Phrase $message
     * @param ResponseInterface $response
     * @param \Exception|null $previous
     */
    public function __construct(
        Phrase $message,
        ResponseInterface $response,
        \Exception $previous = null
    ) {
        $this->response = $response;
        parent::__construct($message, $previous);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
