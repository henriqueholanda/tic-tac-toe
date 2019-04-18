<?php
namespace App\Entity;

class Player
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
}
