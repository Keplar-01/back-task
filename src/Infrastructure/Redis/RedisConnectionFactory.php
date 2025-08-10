<?php
namespace Raketa\BackendTestTask\Infrastructure\Redis;

use Redis;
use Psr\Log\LoggerInterface;

class RedisConnectionFactory
{
    public function __construct(
        private readonly string $host,
        private readonly int $port=6379,
        private readonly ?string $password=null,
        private readonly ?int $dbindex=0,
        private readonly LoggerInterface $logger
    )
    {}

    public function createConnection(): Redis
    {
        $redis = new Redis();

        try {
            if (!$redis->isConnected()) {
                $redis->connect($this->host, $this->port);
            }

            if ($this->password) {
                $redis->auth($this->password);
            }

            if ($this->dbindex !== null) {
                $redis->select($this->dbindex);
            }
        } catch (\RedisException $ex) {
            $this->logger->error('Redis connection error: ' . $ex->getMessage());
            throw $ex;
        }

        return $redis;
    }
}