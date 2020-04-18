<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Awx.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdwru\Awx;

use GrahamCampbell\Manager\AbstractManager;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;

/**
 * This is the awx manager class.
 *
 * @method \AwxV2\AwxV2 connection(string|null $name)
 * @method \AwxV2\AwxV2 reconnect(string|null $name)
 * @method array<string,\AwxV2\AwxV2> getConnections(string $name)
 * @method \AwxV2\Api\Action action()
 * @method \AwxV2\Api\Image image()
 * @method \AwxV2\Api\Domain domain()
 * @method \AwxV2\Api\DomainRecord domainRecord()
 * @method \AwxV2\Api\Size size()
 * @method \AwxV2\Api\Region region()
 * @method \AwxV2\Api\Key key()
 * @method \AwxV2\Api\Droplet droplet()
 * @method \AwxV2\Api\RateLimit rateLimit()
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class AwxManager extends AbstractManager
{
    /**
     * The factory instance.
     *
     * @var \Sdwru\Awx\AwxFactory
     */
    protected $factory;
    
    /**
     * The oauth instance.
     *
     * @var \Sdwru\Awx\AwxOauth
     */
    protected $oauth;

    /**
     * Create a new awx manager instance.
     *
     * @param \Illuminate\Contracts\Config\Repository          $config
     * @param \Sdwru\Awx\AwxFactory $factory
     *
     * @return void
     */
    public function __construct(Repository $config, AwxFactory $factory, AwxOauthWrapper $oauth)
    {
        parent::__construct($config);
        $this->oauth = $oauth;
        $this->factory = $factory;
    }
    
    /**
     * Get an oauth instance.
     *
     * @param string|null $name
     *
     * @return object
     */
    public function oauthInstance()
    {
        return $this->makeOauth();
    }

    /**
     * Create the connection instance.
     *
     * @param array $config
     *
     * @return \AwxV2\AwxV2
     */
    protected function createConnection(array $config)
    {
        $config['token'] = $this->oauth->getAwxAccessToken($this->oauthInstance());
        return $this->factory->make($config);
    }
    
    /**
     * Create the oauth instance.
     *
     * @param array $config
     *
     * @return \AwxV2\AwxV2
     */
    protected function createOauth(array $config)
    {
        return $this->oauth->make($config);
    }

    /**
     * Get the configuration name.
     *
     * @return string
     */
    protected function getConfigName()
    {
        return 'awx';
    }

    /**
     * Get the factory instance.
     *
     * @return \Sdwru\Awx\AwxFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }
    
    /**
     * Get the oauth instance.
     *
     * @return \Sdwru\Awx\AwxFactory
     */
    public function getOauth()
    {
        return $this->oauth;
    }
    
    /**
     * Get the the oauth configuration.
     *
     * @param string|null $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getOauthConfig()
    {
        return $this->getNamedConfig('oauth', 'Oauth2', 'credentials');
    }
    
    /**
     * Get the the api configuration.
     *
     * @param string|null $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getApiConfig()
    {
        return $this->getNamedConfig('api', 'API', 'url');
    }
    
    /**
     * Get the the ssl configuration.
     *
     * @param string|null $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function getSslConfig()
    {
        return $this->getNamedConfig('api', 'SSL', 'ssl');
    }
    
    /**
     * Get all configuration parameters for given type.
     *
     * @param string $type
     * @param string $desc
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function getAllConfig(string $type, string $desc)
    {
        $data = $this->config->get($this->getConfigName().'.'.$type);

        if (!is_array($data) && !$data) {
            throw new InvalidArgumentException("$desc not configured.");
        }

        return $data;
    }
    
    /**
     * Make the connection instance.
     *
     * @param string $name
     *
     * @return object
     */
    protected function makeConnection(string $name)
    {
        $config = $this->getConnectionConfig($name);
        $apiUrl = $this->getApiConfig();
        $ssl = $this->getSslConfig();
        $config['apiUrl'] = $apiUrl['base'];
        $config['sslVerify'] = $ssl['verify'];
        if (isset($this->extensions[$name])) {
            return $this->extensions[$name]($config);
        }

        if ($driver = Arr::get($config, 'driver')) {
            if (isset($this->extensions[$driver])) {
                return $this->extensions[$driver]($config);
            }
        }

        return $this->createConnection($config);
    }

    /**
     * Make the oauth instance.
     *
     * @param string $name
     *
     * @return object
     */
    protected function makeOauth()
    {
        $config = $this->getOauthConfig();
        $apiUrl = $this->getApiConfig();
        $ssl = $this->getSslConfig();
        $config['apiUrl'] = $apiUrl['base'];
        $config['sslVerify'] = $ssl['verify'];
        return $this->createOauth($config);
    }
    
    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        return $this->connection()->$method(...$parameters);
    }
}
