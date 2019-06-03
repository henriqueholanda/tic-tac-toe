<?php
namespace App\Domain;

use App\Exception\GameNotFoundException;
use App\Domain\Model\Game;
use Predis\Client as RedisClient;

class Storage
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
        $this->prefix = $prefix;
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

    /**
     * @param Game $game
     *
     * @return void
     */
    public function save(Game $game) : void
    {
        $this->getInstance()->set($game->getId(), $game->serialize());
    }

    /**
     * @param Game $game
     *
     * @return string
     * @throws GameNotFoundException
     */
    public function get(Game $game) : string
    {
        $data = $this->getInstance()->get($game->getId());
        if (empty($data)) {
            throw new GameNotFoundException('A game with id `'. $game->getId() .'` does not exists');
        }
        return $data;
    }
}
