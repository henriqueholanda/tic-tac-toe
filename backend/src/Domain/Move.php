<?php
namespace App\Domain;

use Serializable;

class Move implements Serializable
{
    /** @var Player */
    private $player;

    /** @var Position */
    private $position;

    public function __construct(Player $player, Position $position)
    {
        $this->player = $player;
        $this->position = $position;
    }

    /**
     * @return Player
     */
    public function getPlayer() : Player
    {
        return $this->player;
    }

    /**
     * @return Position
     */
    public function getPosition() : Position
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function serialize() : string
    {
        return serialize(
            [
                'player'    => $this->getPlayer()->serialize(),
                'position'  => $this->getPosition()->serialize()
            ]
        );
    }

    /**
     * @param string $serialized
     *
     * @return self
     */
    public function unserialize($serialized) : self
    {
        $data = unserialize($serialized);

        return new self(
            (new Player(''))->unserialize($data['player']),
            (new Position(0, 0))->unserialize($data['position'])
        );
    }
}
