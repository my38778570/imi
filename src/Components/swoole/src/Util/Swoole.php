<?php

declare(strict_types=1);

namespace Imi\Swoole\Util;

use Imi\Server\ServerManager;
use Imi\Swoole\Server\Contract\ISwooleServer;

class Swoole
{
    use \Imi\Util\Traits\TStaticClass;

    /**
     * 获取master进程pid.
     */
    public static function getMasterPID(): int
    {
        /** @var ISwooleServer $server */
        $server = ServerManager::getServer('main', ISwooleServer::class);

        return $server->getSwooleServer()->master_pid;
    }

    /**
     * 获取manager进程pid.
     */
    public static function getManagerPID(): int
    {
        /** @var ISwooleServer $server */
        $server = ServerManager::getServer('main', ISwooleServer::class);

        return $server->getSwooleServer()->manager_pid;
    }
}
