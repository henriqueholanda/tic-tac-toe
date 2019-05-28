<?php
namespace App\Model;

use App\Entity\Player;
use App\Entity\Position;
use App\Exception\InvalidMoveException;

class Board
{
    /** @var array */
    private $board;

    public function __construct()
    {
        $this->board = [
            ['', '', ''],
            ['', '', ''],
            ['', '', ''],
        ];
    }

    /**
     * @param array $board
     *
     * @return void
     */
    public function setContent(array $board) : void
    {
        $this->board = $board;
    }

    /**
     * @return array
     */
    public function getContent() : array
    {
        return $this->board;
    }

    /**
     * @param Player $player
     * @param Position $position
     *
     * @return void
     */
    public function movePlayerToPosition(Player $player, Position $position) : void
    {
        $this->board[$position->getRow()][$position->getColumn()] = $player->getTeam();
    }

    /**
     * @param Position $position
     *
     * @throws InvalidMoveException
     * @return void
     */
    public function checkPositionAvailable(Position $position) : void
    {
        $positionContent = $this->getPositionContent($position);
        if (!empty($positionContent)) {
            throw new InvalidMoveException(
                sprintf(
                    'The position [%d,%d] is already filled by `%s` team',
                    $position->getRow(),
                    $position->getColumn(),
                    $positionContent
                )
            );
        }
    }

    /**
     * @param Position $position
     *
     * @return string
     */
    private function getPositionContent(Position $position) : string
    {
        return $this->board[$position->getRow()][$position->getColumn()];
    }
}
