<?php
namespace App\Model\Move\Strategy;

use App\Entity\Player;
use App\Entity\Position;

class MoveToWin extends BotMoveStrategy implements MoveStrategyInterface
{
    /** @var array */
    private $boardState;

    /** @var Player */
    private $player;

    public function __construct(array $boardState, string $playerUnit)
    {
        $this->boardState = $boardState;
        $this->player = new Player($playerUnit);
    }

    /**
     * @return Position|null
     */
    public function move() : ?Position
    {
        $position = $this->getPositionOnRow($this->boardState, $this->player->getTeam());
        if (is_array($position)) {
            return new Position($position[0], $position[1]);
        }

        $position = $this->getPositionOnColumn($this->boardState, $this->player->getTeam());
        if (is_array($position)) {
            return new Position($position[0], $position[1]);
        }

        $position = $this->getPositionOnLeftDiagonal($this->boardState, $this->player->getTeam());
        if (is_array($position)) {
            return new Position($position[0], $position[1]);
        }

        $position = $this->getPositionOnRightDiagonal($this->boardState, $this->player->getTeam());
        if (is_array($position)) {
            return new Position($position[0], $position[1]);
        }

        return null;
    }
}
