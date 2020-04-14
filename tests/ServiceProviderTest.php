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

namespace GrahamCampbell\Tests\Awx;

use AwxV2\AwxV2;
use Sdwru\Awx\Adapter\ConnectionFactory as AdapterFactory;
use Sdwru\Awx\AwxFactory;
use Sdwru\Awx\AwxManager;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

/**
 * This is the service provider test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ServiceProviderTest extends AbstractTestCase
{
    use ServiceProviderTrait;

    public function testAdapterFactoryIsInjectable()
    {
        $this->assertIsInjectable(AdapterFactory::class);
    }

    public function testAwxFactoryIsInjectable()
    {
        $this->assertIsInjectable(AwxFactory::class);
    }

    public function testAwxManagerIsInjectable()
    {
        $this->assertIsInjectable(AwxManager::class);
    }

    public function testBindings()
    {
        $this->assertIsInjectable(AwxV2::class);

        $original = $this->app['awx.connection'];
        $this->app['awx']->reconnect();
        $new = $this->app['awx.connection'];

        $this->assertNotSame($original, $new);
        $this->assertEquals($original, $new);
    }
}
