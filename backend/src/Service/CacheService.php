<?php
namespace App\Service;

use Predis\Client as RedisClient;

class CacheService
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var integer
     */
    private $port;

    /**
     * @var string
     */
    private $prefix;

    /** @var RedisClient */
    private $instance;

    public function __construct(string $host, string $port, string $prefix)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @return RedisClient
     */
    private function getInstance() : RedisClient
    {
        if (!$this->instance instanceof RedisClient) {
            $this->instance = new RedisClient(
                [
                    "host" => $this->host,
                    "port" => $this->port
                ]
            );
            $this->instance->connect();
        }
        return $this->instance;
    }

    public function save(string $key, string $data)
    {
        $this->getInstance()->set($key, $data);
    }

    public function get(string $key) : ?string
    {
        return $this->getInstance()->get($key);
    }
}
