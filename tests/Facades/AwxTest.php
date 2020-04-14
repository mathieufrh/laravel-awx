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

namespace GrahamCampbell\Tests\Awx\Facades;

use Sdwru\Awx\AwxManager;
use Sdwru\Awx\Facades\Awx;
use GrahamCampbell\TestBenchCore\FacadeTrait;
use GrahamCampbell\Tests\Awx\AbstractTestCase;

/**
 * This is the awx facade test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class AwxTest extends AbstractTestCase
{
    use FacadeTrait;

    /**
     * Get the facade accessor.
     *
     * @return string
     */
    protected function getFacadeAccessor()
    {
        return 'awx';
    }

    /**
     * Get the facade class.
     *
     * @return string
     */
    protected function getFacadeClass()
    {
        return Awx::class;
    }

    /**
     * Get the facade root.
     *
     * @return string
     */
    protected function getFacadeRoot()
    {
        return AwxManager::class;
    }
}
