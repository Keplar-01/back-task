<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure\Redis;

use Raketa\BackendTestTask\Domain\Cart;
use Redis;
use RedisException;

class Connector
{
    private const TTL = 86400;

    public function __construct(private readonly Redis $redis){}

    /**
     * @throws ConnectorException
     */
    public function get(string $key) : ?Cart
    {
        try {
            $data = $this->redis->get($key);
            return $data !== false ? unserialize($this->redis->get($key)) : null;
        } catch (RedisException $e) {
            throw new ConnectorException('Connector error', $e->getCode(), $e);
        }
    }

    /**
     * @throws ConnectorException
     */
    public function set(string $key, Cart $value) : void
    {
        try {
            $this->redis->setex($key, self::TTL, serialize($value));
        } catch (RedisException $e) {
            throw new ConnectorException('Connector error', $e->getCode(), $e);
        }
    }

    public function has(string $key): bool
    {
        return $this->redis->exists($key);
    }
}
