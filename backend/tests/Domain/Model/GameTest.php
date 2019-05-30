<?php
namespace App\Tests\Domain\Model;

use App\Domain\Board;
use App\Domain\Move;
use App\Domain\Player;
use App\Domain\Position;
use App\Exception\GameOverException;
use App\Exception\InvalidMoveException;
use App\Domain\Model\Game;
use App\Domain\Move\BotMove;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public function testNewGameWithEmptyBoardState()
    {
        $game = new Game(
            new Board(),
            new Player(Player::O_TEAM),
            new BotMove()
        );

        $gameId = $game->start();
        $this->assertNotEmpty($gameId);
        $this->assertEquals($gameId, $game->getId());
    }

    public function testMakeHumanMoveWithSuccessGenerateNewBotMove()
    {
        $humanPlayer = new Player(Player::O_TEAM);

        $game = new Game(
            new Board(),
            $humanPlayer,
            new BotMove()
        );

        $position = new Position(1, 2);

        $botMove = $game->move($humanPlayer, $position);

        $this->assertNotEmpty($game->getBoard()->getState());
        $this->assertNotEmpty($botMove);
        $this->assertInstanceOf(Position::class, $botMove);
    }

    public function testMakeHumanMoveToFilledSpaceThrowException()
    {
        $humanPlayer = new Player(Player::O_TEAM);

        $moves = [
            new Move($humanPlayer, new Position(0, 2)),
            new Move($humanPlayer, new Position(1, 1)),
            new Move($humanPlayer, new Position(2, 0)),
        ];

        $game = new Game(
            new Board($moves),
            $humanPlayer,
            new BotMove()
        );

        $position = new Position(0, 2);

        $this->expectException(InvalidMoveException::class);
        $this->expectExceptionMessage('The position [0,2] is already filled');

        $game->move($humanPlayer, $position);
    }

    public function testGameIsOverWithHumanWinnerThrowException()
    {
        $humanPlayer = new Player(Player::O_TEAM);
        $botPlayer = new Player($humanPlayer->getOpponent());
        $moves = [
            new Move($humanPlayer, new Position(0, 0)),
            new Move($botPlayer, new Position(2, 1)),
            new Move($humanPlayer, new Position(1, 1)),
            new Move($botPlayer, new Position(1, 0)),
            new Move($humanPlayer, new Position(2, 2)),
        ];

        $game = new Game(
            new Board($moves),
            $humanPlayer,
            new BotMove()
        );

        $this->expectException(GameOverException::class);
        $this->expectExceptionMessage('The O team is the winner!');

        $game->checkGameResult($humanPlayer);
    }

    public function testGameIsOverWithBotWinnerThrowException()
    {
        $humanPlayer = new Player(Player::O_TEAM);
        $botPlayer = new Player($humanPlayer->getOpponent());
        $moves = [
            new Move($humanPlayer, new Position(0, 0)),
            new Move($botPlayer, new Position(0, 1)),
            new Move($humanPlayer, new Position(2, 2)),
            new Move($botPlayer, new Position(2, 1)),
            new Move($humanPlayer, new Position(2, 0)),
            new Move($botPlayer, new Position(1, 1)),
        ];

        $game = new Game(
            new Board($moves),
            $humanPlayer,
            new BotMove()
        );

        $this->expectException(GameOverException::class);
        $this->expectExceptionMessage('The X team is the winner!');

        $game->checkGameResult($botPlayer);
    }

    public function testGameIsOverWithDrawThrowException()
    {
        $humanPlayer = new Player(Player::O_TEAM);
        $botPlayer = new Player($humanPlayer->getOpponent());
        $moves = [
            new Move($humanPlayer, new Position(0, 0)),
            new Move($botPlayer, new Position(0, 1)),
            new Move($humanPlayer, new Position(1, 0)),
            new Move($botPlayer, new Position(1, 2)),
            new Move($humanPlayer, new Position(0, 2)),
            new Move($botPlayer, new Position(1, 1)),
            new Move($humanPlayer, new Position(2, 1)),
            new Move($botPlayer, new Position(2, 0)),
            new Move($humanPlayer, new Position(2, 2)),
        ];

        $game = new Game(
            new Board($moves),
            $humanPlayer,
            new BotMove()
        );

        $this->expectException(GameOverException::class);
        $this->expectExceptionMessage('Draw game!');

        $game->checkGameResult($botPlayer);
    }

    public function mockBotMove(array $botMove)
    {
        $mock = $this->getMockBuilder(BotMove::class)
            ->disableOriginalConstructor()
            ->setMethods(['makeMove'])
            ->getMock();
        $mock->expects($this->any())
            ->method('makeMove')
            ->willReturn($botMove);
        return $mock;
    }
}
