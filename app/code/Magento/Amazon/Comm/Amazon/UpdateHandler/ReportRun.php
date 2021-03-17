<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\Amazon\AccountRepository;

class ReportRun implements HandlerInterface
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        if (!$account->getReportRun()) {
            $account->setReportRun(true);
            $this->accountRepository->save($account);
        }
        return array_keys($updates);
    }
}
