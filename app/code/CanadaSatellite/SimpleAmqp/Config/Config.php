<?php

namespace CanadaSatellite\SimpleAmqp\Config;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\CacheInterface;

class Config extends \Magento\Framework\Config\Data
{
	const SIMPLE_AMQP_CONFIG = 'simple_amqp';

	const CONNECTION_STRING_HOST_PATH = 'host';
	const CONNECTION_STRING_PORT_PATH = 'port';
	const CONNECTION_STRING_USERNAME_PATH = 'username';
	const CONNECTION_STRING_PASSWORD_PATH = 'password';
	const CONNECTION_STRING_VIRTUALHOST_PATH = 'virtualhost';

	/**
	 * @var \Magento\Framework\App\DeploymentConfig
	 */
	private $config;

	/**
     * @var ObjectManagerInterface
     */
	private $objectManager;

	public function __construct(
		DeploymentConfig $config,
		ObjectManagerInterface $objectManager,
		Reader $reader,
        CacheInterface $cache,
        $cacheId = 'simple_amqp_config'
	) {
		$this->config = $config;
		$this->objectManager = $objectManager;

		parent::__construct($reader, $cache, $cacheId);
	}

	/**
	 * @return CanadaSatellite\SimpleAmqp\Config\Config
	 */
	public function getConnectionString()
	{
		$config = $this->config->getConfigData(self::SIMPLE_AMQP_CONFIG) ?: array();

		$host = $this->getConfigValue($config, self::CONNECTION_STRING_HOST_PATH);
		$port = $this->getConfigValue($config, self::CONNECTION_STRING_PORT_PATH);
		$username = $this->getConfigValue($config, self::CONNECTION_STRING_USERNAME_PATH);
		$password = $this->getConfigValue($config, self::CONNECTION_STRING_PASSWORD_PATH);
		$virtualhost = $this->getConfigValue($config, self::CONNECTION_STRING_VIRTUALHOST_PATH);

		if ($host === null
			|| $port === null
			|| $username === null
			|| $password === null
		) {
			throw new \Exception("Invalid connection string. Host / Port / Username / Password cannot be omitted.");
		}

		return new ConnectionString($host, $port, $username, $password, $virtualhost);
	}

	/**
	 * @return CanadaSatellite\SimpleAmqp\Api\BatchConsumerInterface|null
	 */
	public function getQueueBatchConsumerInstance($queueName)
	{
		$config = $this->getItemByProperty('queues', $queueName);
        
        return $this->objectManager->create(
            $config['consumerInterface']
        );
	}

    /**
     * @return \CanadaSatellite\AstIntegration\LogicProcessors\AstQueueProcessor
     */
    public function getAstQueueProcessorInstance() {
        return $this->objectManager->create('CanadaSatellite\AstIntegration\LogicProcessors\AstQueueProcessor');
    }

	/**
	 * @param string $key
	 * @return string|null
	 */
	private function getConfigValue($config, $key)
	{
		if (!array_key_exists($key, $config)) {
			return null;
		}

		return $config[$key];
	}

	/**
     * Return a configuration item given a property value
     */
    protected function getItemByProperty($path, $value, $prop = 'name')
    {
        $items = $this->get($path, array());

        foreach($items as $item) {
            if($item[$prop] !== $value) {
                continue;
            }
            
            return $item;
        }
        
        // Throw exception if a value is not found
        throw new \Exception(sprintf(
            'Element with %s "%s" not found in %s list',
            $prop,
            $value,
            $path
        ));
    }
}
