<?php
namespace App\Domain\Model;

use App\Domain\Player;
use App\Domain\Position;
use App\Domain\Board;
use App\Exception\GameOverException;
use App\Domain\GameResult\Draw;
use App\Domain\GameResult\Winner;
use App\Domain\Move\MoveInterface;
use Serializable;

class Game implements Serializable
{
    const GAME_OVER_STATUS = 'gameover';

    /** @var string */
    private $gameId;

    /** @var Board */
    private $board;

    /** @var Player */
    private $humanPlayer;

    /** @var MoveInterface */
    private $botMoveStrategy;

    /** @var Position */
    private $nexBotMove;

    public function __construct(
        Board $board,
        Player $humanPlayer,
        MoveInterface $botMoveStrategy
    ) {
        $this->board = $board;
        $this->humanPlayer = $humanPlayer;
        $this->botMoveStrategy = $botMoveStrategy;
    }

    /**
     * @return void
     */
    private function generateId() : void
    {
        $this->gameId = uniqid();
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->gameId;
    }

    /**
     * @param string $id
     *
     * @return void
     */
    public function setId(string $id) : void
    {
        $this->gameId = $id;
    }

    /**
     * @return Board
     */
    public function getBoard() : Board
    {
        return $this->board;
    }

    public function getNextBotMove() : ?Position
    {
        return $this->nexBotMove;
    }

    /**
     * @return string
     */
    public function start() : string
    {
        $this->generateId();
        return $this->gameId;
    }

    /**
     * @param Player $player
     * @param Position $position
     *
     * @return void
     * @throws GameOverException
     */
    public function move(Player $player, Position $position) : void
    {
        $this->board->markSpace($player, $position);
        $this->checkGameResult($player);

        $botMove = $this->botMoveStrategy->makeMove($this->board->getState(), $player->getTeam());
        $this->nexBotMove = new Position($botMove[0], $botMove[1]);

        $botPlayer = new Player($player->getOpponent());
        $this->board->markSpace($botPlayer, $this->nexBotMove);

        $this->checkGameResult($botPlayer);
    }

    /**
     * String representation of object
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        $data = [
            'board'             => $this->getBoard()->serialize(),
            'humanPlayer'       => $this->humanPlayer->serialize(),
            'botMoveStrategy'   => $this->botMoveStrategy
        ];
        return serialize($data);
    }

    /**
     * Constructs the object
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized) : self
    {
        $data = unserialize($serialized);

        return new self(
            (new Board())->unserialize($data['board']),
            (new Player(''))->unserialize($data['humanPlayer']),
            $data['botMoveStrategy']
        );
    }

    /**
     * @param Player $player
     *
     * @throws GameOverException
     */
    public function checkGameResult(Player $player): void
    {
        if (Winner::check($this)) {
            throw new GameOverException(
                sprintf('The %s team is the winner!', $player->getTeam())
            );
        }

        if (Draw::check($this)) {
            throw new GameOverException('Draw game!');
        }
    }
}
