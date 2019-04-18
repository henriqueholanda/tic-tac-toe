<?php
namespace App\Model;

use App\Entity\Player;
use App\Entity\Position;
use App\Exception\GameOverException;
use App\Model\GameResult\Draw;
use App\Model\GameResult\Winner;

class Game
{
    /** @var Board */
    private $board;

    /** @var bool  */
    private $isOver = false;

    /** @var Player  */
    private $winner = null;

    public function __construct()
    {
        $this->board = new Board();
    }

    /**
     * @return Board
     */
    public function getBoard() : Board
    {
        return $this->board;
    }

    /**
     * @return Player
     */
    public function getWinner() : Player
    {
        return $this->winner;
    }

    /**
     * @param Player $player
     * @param Position $position
     *
     * @return void
     *
     * @throws GameOverException
     */
    public function move(Player $player, Position $position) : void
    {
        $this->checkGameIsOver();

        $this->board->checkPositionAvailable($position);
        $this->board->movePlayerToPosition($player, $position);

        $this->checkGameResult($player);
    }

    /**
     * @throws GameOverException
     */
    private function checkGameIsOver(): void
    {
        if ($this->isOver === true) {
            throw new GameOverException(
                'Sorry, you can\'t make the given movement, the game has already over.'
            );
        }
    }

    /**
     * @param Player $player
     *
     * @throws GameOverException
     */
    private function checkGameResult(Player $player): void
    {
        if (Winner::check($this)) {
            $this->isOver = true;
            $this->winner = $player;

            throw new GameOverException(
                sprintf('The %s team is the winner!', $player->getTeam())
            );
        }

        if (Draw::check($this)) {
            $this->isOver = true;
            $this->winner = new Player('');

            throw new GameOverException('Draw game!');
        }
    }
}
