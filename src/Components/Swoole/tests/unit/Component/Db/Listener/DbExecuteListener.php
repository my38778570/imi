<?php

namespace Imi\Swoole\Test\Component\Db\Listener;

use Imi\App;
use Imi\Bean\Annotation\Listener;
use Imi\Db\Event\Param\DbExecuteEventParam;
use Imi\Event\EventParam;
use Imi\Event\IEventListener;
use Imi\Log\Log;

/**
 * @Listener("IMI.DB.EXECUTE")
 */
class DbExecuteListener implements IEventListener
{
    /**
     * 事件处理方法.
     *
     * @param DbExecuteEventParam $e
     *
     * @return void
     */
    public function handle(EventParam $e)
    {
        if (false !== App::get('DB_LOG'))
        {
            Log::info(sprintf('[%ss] %s', round($e->time, 3), $e->sql));
        }
    }
}