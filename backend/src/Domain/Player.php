<?php
namespace App\Domain;

use Serializable;

class Player implements Serializable
{
    const X_TEAM = 'X';
    const O_TEAM = 'O';

    /** @var string */
    private $team;

    /**
     * Player constructor.
     * @param string $team
     */
    public function __construct(string $team)
    {
        $this->team = $team;
    }

    /**
     * @return string
     */
    public function getTeam() : string
    {
        return $this->team;
    }

    /**
     * @return string
     */
    public function getOpponent() : string
    {
        if ($this->team != self::O_TEAM) {
            return self::O_TEAM;
        }
        return self::X_TEAM;
    }

    /**
     * @return string
     */
    public function serialize() : string
    {
        return serialize($this->team);
    }

    /**
     * @param string $serialized
     *
     * @return self
     */
    public function unserialize($serialized) : self
    {
        return new self(unserialize($serialized));
    }
}
