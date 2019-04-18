<?php
namespace App\Model\GameResult;

use App\Entity\Position;
use App\Model\Game;

class Winner
{
    /**
     * @return array
     */
    private static function winnerPositions() : array
    {
        return [
            [new Position(0, 0), new Position(0, 1), new Position(0, 2)],
            [new Position(1, 0), new Position(1, 1), new Position(1, 2)],
            [new Position(2, 0), new Position(2, 1), new Position(2, 2)],
            [new Position(0, 0), new Position(1, 0), new Position(2, 0)],
            [new Position(0, 1), new Position(1, 1), new Position(2, 1)],
            [new Position(0, 2), new Position(1, 2), new Position(2, 2)],
            [new Position(0, 0), new Position(1, 1), new Position(2, 2)],
            [new Position(0, 2), new Position(1, 1), new Position(2, 0)],
        ];
    }

    /**
     * Check if the game have the current result
     * @param Game $game
     *
     * @return bool
     */
    public static function check(Game $game) : bool
    {
        $board = $game->getBoard()->getContent();

        foreach (self::winnerPositions() as $position) {
            $first  = $board[$position[0]->getRow()][$position[0]->getColumn()];
            $second = $board[$position[1]->getRow()][$position[1]->getColumn()];
            $third = $board[$position[2]->getRow()][$position[2]->getColumn()];

            if (empty($first) || empty($second) || empty($third)) {
                continue;
            }

            if ($first == $second &&
                $second == $third &&
                $third == $first
            ) {
                return true;
            }
        }

        return false;
    }
}