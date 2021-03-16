<?php

declare(strict_types=1);

namespace Imi\Server\TcpServer\Message;

use Imi\RequestContext;
use Imi\Server\DataParser\DataParser;

class ReceiveData implements IReceiveData
{
    /**
     * 客户端连接的标识符.
     */
    protected int $fd = 0;

    /**
     * 接收到的数据.
     */
    protected string $data = '';

    /**
     * 接收到的数据.
     *
     * @var mixed
     */
    protected $formatData;

    public function __construct(int $fd, string $data)
    {
        $this->fd = $fd;
        $this->data = $data;
        $this->formatData = RequestContext::getServerBean(DataParser::class)->decode($data);
    }

    /**
     * 获取客户端的socket id.
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * 数据内容，可以是文本内容也可以是二进制数据，可以通过opcode的值来判断.
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * 获取格式化后的数据，一般是数组或对象
     *
     * @return mixed
     */
    public function getFormatData()
    {
        return $this->formatData;
    }
}