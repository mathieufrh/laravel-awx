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

use AwxV2\Adapter\AdapterInterface;
use AwxV2\AwxV2;
use Sdwru\Awx\Adapter\ConnectionFactory;
use Sdwru\Awx\AwxFactory;
use Sdwru\Awx\AwxManager;
use GrahamCampbell\TestBench\AbstractTestCase as AbstractTestBenchTestCase;
use Mockery;

/**
 * This is the awx factory test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class AwxFactoryTest extends AbstractTestBenchTestCase
{
    public function testMake()
    {
        $config = ['driver' => 'buzz', 'token'  => 'your-token'];

        $manager = Mockery::mock(AwxManager::class);

        $factory = $this->getMockedFactory($config, $manager);

        $return = $factory->make($config, $manager);

        $this->assertInstanceOf(AwxV2::class, $return);
    }

    public function testAdapter()
    {
        $factory = $this->getAwxFactory();

        $config = ['driver' => 'guzzlehttp', 'token'  => 'your-token'];

        $factory->getAdapter()->shouldReceive('make')->once()
            ->with($config)->andReturn(Mockery::mock(AdapterInterface::class));

        $return = $factory->createAdapter($config);

        $this->assertInstanceOf(AdapterInterface::class, $return);
    }

    protected function getAwxFactory()
    {
        $adapter = Mockery::mock(ConnectionFactory::class);

        return new AwxFactory($adapter);
    }

    protected function getMockedFactory($config, $manager)
    {
        $adapter = Mockery::mock(ConnectionFactory::class);

        $mock = Mockery::mock(AwxFactory::class.'[createAdapter]', [$adapter]);

        $mock->shouldReceive('createAdapter')->once()
            ->with($config)->andReturn(Mockery::mock(AdapterInterface::class));

        return $mock;
    }
}
