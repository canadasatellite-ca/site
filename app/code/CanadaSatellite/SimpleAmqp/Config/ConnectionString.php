<?php

namespace CanadaSatellite\SimpleAmqp\Config;

class ConnectionString
{
	/**
	 * @var string
	 */
	private $host;

	/**
	 * @var string
	 */
	private $port;

	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var string
	 */
	private $virtualhost;

	public function __construct($host, $port, $username, $password, $virtualhost = '/')
	{
		$this->host = $host;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
		$this->virtualhost = $virtualhost;
	}

	public function getHost()
	{
		return $this->host;
	}

	public function getPort()
	{
		return $this->port;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function getVirtualhost()
	{
		return $this->virtualhost;
	}
}
