<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Traits;

/**
 * Class Additional
 * @package Aitoc\OrdersExportImport\Traits
 */
trait Additional
{
    /**
     * @param $name
     * @return string
     */
    private function toCamelCase($name)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
    }

    /**
     * @param $collection
     * @return \Generator
     */
    public function partCollection($collection)
    {
        foreach ($collection as $element) {
            yield $element;
        }
    }

    /**
     * @param $model
     * @param $callback
     * @param array $args
     * @return mixed
     */
    public function walk($model, $callback, $object)
    {
        return call_user_func([$model, $callback], $object);
    }

    /**
     * @param $minutes
     * @return mixed
     */
    public function addDate($minutes)
    {
        $dateTime = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magento\Framework\Stdlib\DateTime\Timezone');
        $date     = $dateTime->date();
        if ($minutes > 0) {
            $date->add(new \DateInterval('PT' . $minutes . 'M'));
        }

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @param $minutes
     * @return mixed
     */
    public function subDate($minutes)
    {
        $dateTime = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magento\Framework\Stdlib\DateTime\Timezone');
        $date     = $dateTime->date();
        if ($minutes > 0) {
            $date->sub(new \DateInterval('PT' . $minutes . 'M'));
        }

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @param $element
     * @return int
     */
    public function isStore($element)
    {
        if (count($element)) {
            $params = $this->getParams();
            $stores = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Store\Model\Store')
                ->getCollection()
                ->getAllIds();
            if (isset($element['store_id'])) {
                if (!$params['try_storeviews']) {
                    $element['store_id'] = $params['store_id'];
                } else {
                    if (!in_array($element['store_id'], $stores)) {
                        $element['store_id'] = $params['store_id'];
                    }
                }
            } else {
                $element['store_id'] = $params['store_id'];
            }
        }

        return $element;
    }

    /**
     * @param $model
     * @param $params
     * @return mixed
     */
    public function setModel($model, $params, $replace = 0, $order = 0)
    {
        $object = $this->objectManager->create($model);
        if ($order) {
            $object->load($params['increment_id'], 'increment_id');
            if ($object->getId() && !$replace) {
                return null;
            }
        }
        if ($order && $replace) {
            if ($object->getId()) {
                $params = $this->deleteNode($params, 'increment_id');
                $data   = $object->getData();
                $params = array_merge($data, $params);
            }
        }
        $object->setData($params);
     
        return $object;
    }

    /**
     * @param $element
     * @param $field
     * @return mixed
     */
    public function deleteNode($element, $field)
    {
        if (isset($element[$field])) {
            unset($element[$field]);
        }

        return $element;
    }

    /**
     * @param $key
     * @return bool
     */
    public function isItems($key)
    {
        return in_array($key, ['items', 'comments', 'trackingsinformation']);
    }

    /**
     * @param $element
     * @return mixed
     */
    public function addCustomer($element, $order)
    {
        if ($order->getCustomerId()) {
            $params     = $this->getParams();
            $customer   = $this->objectManager->create('Magento\Customer\Model\Customer');
            $collection = $customer->getCollection()->addFilter('email', $order->getCustomerEmail());
            if ($params['customers_autocreate']) {
                if ($collection->count() > 0) {
                    $tempCustomer = $collection->getFirstItem();
                    $customerId   = $tempCustomer->getId();
                    if ($tempCustomer->getAddressCollection()->count() < 2) {
                        $tempCustomer->addAddress($this->addAddress($element));
                        $tempCustomer->save();
                    }
                } else {
                    $data          = [];
                    $data['email'] = $order->getCustomerEmail();
                    foreach ($element as $key => $value) {
                        if (in_array($key, ['firstname', 'lastname', 'middlename'])) {
                            $data[$key] = $value;
                        }
                    }
                    $customer->setData($data);
                    
                    $customer->addAddress($this->addAddress($element));
                    \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')
                    ->debug(json_encode($customer->getData()));
                    
                    $customer->save();
                    $customerId = $customer->getId();
                }
               
                $element['customer_id'] = $customerId;
                $order->setCustomerIsGuest(0);
            } else {
                if ($collection->count() > 0) {
                    $tempCustomer = $collection->getFirstItem();
                    $customerId   = $tempCustomer->getId();
                    $order->setCustomerIsGuest(0);
                } else {
                    $customerId = null;
                    $order->setCustomerIsGuest(1);
                }
                $element['customer_id'] = $customerId;
            }
        } else {
            $element['customer_id'] = null;
            $order->setCustomerIsGuest(1);
        }
        
        return $element;
    }

    /**
     * @param $element
     * @return mixed
     */
    public function addAddress($element)
    {
        $address = $this->objectManager->create('Magento\Customer\Model\Address');
        foreach ($element as $key => $value) {
            if (in_array($key, [
                'firstname',
                'lastname',
                'middlename',
                'city',
                'region_id',
                'region',
                'postcode',
                'street',
                'telephone',
                'country_id',
                'company'
            ])) {
                $data[$key] = $value;
            }
        };
        $address->setData($data);

        return $address;
    }

    /**
     * @param $element
     * @return mixed
     */
    public function addAdditionalInfo($element)
    {
        if (!isset($element['additional_information'])
            || (!$element['additional_information'])
            || ($element['additional_information'] == 'a:0:{}')
        ) {
            $element['additional_information'] = ['method_title' => null,
                'payable_to' => null,
                'mailing_address' => null
            ];
        }

        return $element;
    }

    /**
     * @param $keyOrd
     * @param $value
     * @param $class
     * @param $scope
     * @param $order
     */
    public function isPaymentTransaction($keyOrd, $value, $class, $scope, $order)
    {
        if (in_array($keyOrd, ['paymentstransaction'])) {
            foreach ($value as $element) {
                $object = $this->setModel($class[$keyOrd]['model'], $element, 0);
                $object->setOrderId($scope['ordersId']);
                $object->setPaymentId($order->getPayment()->getId());
                $object->save();
            }
        }
    }

    /**
     * @param $value
     * @return mixed
     */
    public function changeItem($value)
    {
        if (isset($value['name'])) {
            $product = $this->objectManager->create('Magento\Customer\Model\Address');
            if ($product->load($value['name'], 'name')) {
                $value['product_id'] = $product->getId();

                return $value;
            }
        }

        $value['product_id'] = null;

        return $value;
    }
}
