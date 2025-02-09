<?php

declare(strict_types=1);

namespace Imi\Workerman\Process\Annotation;

use Imi\Bean\Annotation\Base;
use Imi\Bean\Annotation\Parser;

/**
 * 进程注解.
 *
 * @Annotation
 *
 * @Target("CLASS")
 *
 * @Parser("Imi\Workerman\Process\Parser\ProcessParser")
 *
 * @property string $name 进程名称
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Process extends Base
{
    /**
     * {@inheritDoc}
     */
    protected ?string $defaultFieldName = 'name';

    public function __construct(?array $__data = null, string $name = '')
    {
        parent::__construct(...\func_get_args());
    }
}
