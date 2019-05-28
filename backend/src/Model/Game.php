<?php
namespace App\Model;

use App\Entity\Player;
use App\Entity\Position;
use App\Exception\GameNotFoundException;
use App\Exception\GameOverException;
use App\Model\GameResult\Draw;
use App\Model\GameResult\Winner;
use App\Model\Move\MoveInterface;
use App\Service\CacheService;

class Game
{
    /** @var Board */
    private $board;

    /** @var bool  */
    private $isOver = false;

    /** @var Player  */
    private $winner = null;

    /** @var MoveInterface */
    private $move;

    /** @var CacheService */
    private $cacheService;

    public function __construct(MoveInterface $move, CacheService $cacheService)
    {
        $this->board = new Board();
        $this->move = $move;
        $this->cacheService = $cacheService;
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
     * @param Player $botPlayer
     *
     * @return string
     */
    public function start(Player $botPlayer) : string
    {
        $gameId = uniqid();
        $this->saveCurrentStatus($gameId, $botPlayer->getTeam(), $botPlayer->getOpponent());
        return $gameId;
    }

    /**
     * @param string $gameId
     *
     * @return array
     * @throws GameNotFoundException
     */
    public function status(string $gameId) : array
    {
        $game = json_decode($this->cacheService->get($gameId));
        if (empty($game)) {
            throw new GameNotFoundException('A game with id `'. $gameId .'` does not exists');
        }

        $this->board->setContent($game->board);
        $this->isOver = $game->isOver;

        return [
            'board' => $game->board,
            'botPlayer' => $game->botPlayer,
            'humanPlayer' => $game->humanPlayer
        ];
    }

    /**
     * @param string $gameId
     * @param Player $player
     * @param Position $position
     *
     * @return void
     *
     * @throws GameOverException
     * @throws GameNotFoundException
     */
    public function moveHuman(string $gameId, Player $player, Position $position) : void
    {
        $this->status($gameId);
        $this->checkGameIsOver();

        $this->board->checkPositionAvailable($position);
        $this->board->movePlayerToPosition($player, $position);

        $this->saveCurrentStatus($gameId, $player->getOpponent(), $player->getTeam());

        $this->checkGameResult($player);
    }

    /**
     * @param string $gameId
     *
     * @return array
     * @throws GameNotFoundException
     */
    public function moveBot(string $gameId) : array
    {
        $position = null;
        $player = null;
        try {
            $status = $this->status($gameId);

            $player = new Player($status['botPlayer']);

            $move = $this->move->makeMove($this->board->getContent(), $player->getTeam());
            $position = new Position($move[0], $move[1]);

            $this->board->movePlayerToPosition($player, $position);
            $this->checkGameIsOver();
            $this->saveCurrentStatus($gameId, $player->getTeam(), $player->getOpponent());
            $this->checkGameResult($player);

            return [
                'row' => $position->getRow(),
                'col' => $position->getColumn(),
            ];
        } catch (GameOverException $e) {
            $this->saveCurrentStatus($gameId, $player->getTeam(), $player->getOpponent());
            return [
                'row' => $position->getRow(),
                'col' => $position->getColumn(),
                'isOver' => $this->isOver,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @param string $gameId
     * @param string $bot
     * @param string $human
     *
     * @return void
     */
    private function saveCurrentStatus(string $gameId, string $bot, string $human) : void
    {
        $content = [
            'board' => $this->board->getContent(),
            'botPlayer' => $bot,
            'humanPlayer' => $human,
            'isOver' => $this->isOver,
            'winner' => $this->winner
        ];
        $this->cacheService->save($gameId, json_encode($content));
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
