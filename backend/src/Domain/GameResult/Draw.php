<?php
namespace App\Domain\GameResult;

use App\Domain\Model\Game;

class Draw implements ResultInterface
{
    /**
     * Check if the game have the current result
     * @param Game $game
     *
     * @return bool
     */
    public static function check(Game $game) : bool
    {
        $boardContent = $game->getBoard()->getState();

        foreach ($boardContent as $row) {
            $row = array_filter($row);

            if (count($row) < 3) {
                return false;
            }
        }

        return true;
    }
}
