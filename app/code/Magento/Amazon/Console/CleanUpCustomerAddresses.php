<?php

declare(strict_types=1);

namespace Magento\Amazon\Console;

use Magento\Amazon\Model\DeadlockRetriesTrait;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\DB\Select;
use Symfony\Component\Console\Input\InputOption;

class CleanUpCustomerAddresses extends \Symfony\Component\Console\Command\Command
{
    use DeadlockRetriesTrait;

    private const ADDRESSES_LIMIT = 2000;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Address\CollectionFactory
     */
    private $addressCollectionFactory;

    public function __construct(
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Model\ResourceModel\Address\CollectionFactory $addressCollectionFactory
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->addressRepository = $addressRepository;
        $this->addressCollectionFactory = $addressCollectionFactory;
        parent::__construct(null);
    }

    protected function configure()
    {
        $this->setName('channel:amazon:customer-address-cleanup');
        $this->setDescription('Delete addresses for Amazon customer accounts with more than 2 addresses');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force deleting');
        parent::configure();
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $customersFound = $this->getCustomersWithTooManyAddresses();
        $customerIds = array_column($customersFound, 'customer_id');
        if (!$customerIds) {
            $output->writeln('No customers that needs to be cleaned up');
            return 0;
        }
        $totalAddressesFound = array_sum(array_column($customersFound, 'addresses'));
        $totalCustomersFound = count($customerIds);
        $output->writeln(
            'Addresses to be deleted: ' . $totalAddressesFound . '. They belong to ' . $totalCustomersFound . ' customers.'
        );
        if (!($input->getOption('force'))) {
            $output->writeln(
                'Command execution aborted. Please add --force flag if you want to delete these addresses.'
            );
            return 1;
        }

        $this->deleteAddresses($customerIds, $totalAddressesFound, $output);
    }

    /**
     * @return array
     */
    private function getCustomersWithTooManyAddresses(): array
    {
        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $customerCollection */
        $customerCollection = $this->customerCollectionFactory->create();
        $customerCollection->removeAllFieldsFromSelect();
        $customerCollection->addFieldToFilter('email', ['like' => '%@marketplace.amazon.com']);
        $resource = $customerCollection->getResource();
        $select = $customerCollection->getSelect();
        $select->joinLeft(
            ['address' => $resource->getTable('customer_address_entity')],
            'address.parent_id = e.entity_id',
            []
        );
        $select->reset(Select::COLUMNS);
        $select->columns(
            [
                'customer_id' => 'e.entity_id',
                'addresses' => 'count(*)'
            ]
        );
        $select->having('addresses > 2');
        $select->group('customer_id');
        return $customerCollection->getData();
    }

    /**
     * @param array address ids
     */
    private function getAddressIds(array $customerIds): array
    {
        /** @var \Magento\Customer\Model\ResourceModel\Address\Collection $addressCollection */
        $addressCollection = $this->addressCollectionFactory->create();
        $select = $addressCollection->getSelect();
        $select->reset(Select::COLUMNS);
        $select->columns(
            [
                'address_id' => 'e.entity_id',
            ]
        );
        $select->limit(self::ADDRESSES_LIMIT);
        $select->where('parent_id IN (?)', $customerIds);
        $addressIds = $addressCollection->getData();
        return array_column($addressIds, 'address_id');
    }

    /**
     * @param array $customerIds
     * @param int $totalAddressesFound
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function deleteAddresses(
        array $customerIds,
        int $totalAddressesFound,
        \Symfony\Component\Console\Output\OutputInterface $output
    ): void {
        $i = 1;
        do {
            $addressIds = $this->getAddressIds($customerIds);
            if (!$addressIds) {
                $output->writeln('All addresses cleaned up.');
                break;
            }
            foreach ($addressIds as $addressId) {
                $output->writeln(
                    sprintf(
                        'Deleting address %d of %s (%s%%)',
                        $i,
                        $totalAddressesFound,
                        round($i / $totalAddressesFound * 100)
                    )
                );
                $this->doWithDeadlockRetries(
                    function () use ($addressId) {
                        $this->addressRepository->deleteById($addressId);
                    }
                );
                $i++;
            }
        } while (count($addressIds) === self::ADDRESSES_LIMIT);
    }
}
