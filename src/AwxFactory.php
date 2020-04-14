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

use AwxV2\AwxV2;
use Sdwru\Awx\Adapter\ConnectionFactory as AdapterFactory;

/**
 * This is the awx factory class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class AwxFactory
{
    /**
     * The adapter factory instance.
     *
     * @var \Sdwru\Awx\Adapter\ConnectionFactory
     */
    protected $adapter;

    /**
     * Create a new filesystem factory instance.
     *
     * @param \Sdwru\Awx\Adapter\ConnectionFactory $adapter
     *
     * @return void
     */
    public function __construct(AdapterFactory $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Make a new awx client.
     *
     * @param string[] $config
     *
     * @return \AwxV2\AwxV2
     */
    public function make(array $config)
    {
        $adapter = $this->createAdapter($config);
         $baseApiUrl = $config['apiUrl'];

        return new AwxV2($adapter, $baseApiUrl);
    }

    /**
     * Establish an adapter connection.
     *
     * @param array $config
     *
     * @return \AwxV2\Adapter\AdapterInterface
     */
    public function createAdapter(array $config)
    {
        return $this->adapter->make($config);
    }

    /**
     * Get the adapter factory instance.
     *
     * @return \Sdwru\Awx\Adapter\ConnectionFactory
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
}
