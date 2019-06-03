<?php
namespace App\Domain\GameResult;

use App\Domain\Model\Game;

interface ResultInterface
{
    /**
     * Check if the game have the current result
     * @param Game $game
     *
     * @return bool
     */
    public static function check(Game $game) : bool;
}
