<?php
namespace App\Tests\Model;

use App\Entity\Player;
use App\Entity\Position;
use App\Exception\GameOverException;
use App\Exception\InvalidMoveException;
use App\Model\Game;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public function testNewGameWithEmptyBoardContent()
    {
        $game = new Game();

        $expectedBoardContent = [
            ['', '', ''],
            ['', '', ''],
            ['', '', ''],
        ];

        $this->assertEquals($expectedBoardContent, $game->getBoard()->getContent());
    }

    public function testMakeMoveWithSuccess()
    {
        $game = new Game();

        $expectedBoardContent = [
            ['', '', ''],
            ['', 'X', ''],
            ['', '', ''],
        ];

        $game->move(
            new Player(Player::X_TEAM),
            new Position(1, 1)
        );

        $this->assertEquals($expectedBoardContent, $game->getBoard()->getContent());
    }

    public function testBothPlayersMakeMovesWithOneWinner()
    {
        $game = new Game();

        $expectedBoardContent = [
            ['X', 'O', 'O'],
            ['X', '', ''],
            ['X', '', ''],
        ];

        $playerX = new Player(Player::X_TEAM);
        $playerO = new Player(Player::O_TEAM);

        $game->move($playerX, new Position(0, 0));
        $game->move($playerO, new Position(0, 1));
        $game->move($playerX, new Position(1, 0));
        $game->move($playerO, new Position(0, 2));

        $this->expectException(GameOverException::class);
        $game->move($playerX, new Position(2, 0));
        $this->assertEquals($expectedBoardContent, $game->getBoard()->getContent());
        $this->assertEquals($playerX, $game->getWinner());
    }

    public function testTryToMakeMoveThrowsAnInvalidMoveExceptionWhenMoveToSamePosition()
    {
        $game = new Game();
        $playerX = new Player(Player::X_TEAM);
        $playerO = new Player(Player::O_TEAM);

        $game->move($playerX, new Position(1, 1));

        $this->expectException(InvalidMoveException::class);
        $this->expectExceptionMessage('The position [1,1] is already filled by `X` team');

        $game->move($playerO, new Position(1, 1));
    }

    public function testTryToMakeMoveThrowsAnGameOverExceptionWhenGameAlreadyOver()
    {
        $game = new Game();
        $playerX = new Player(Player::X_TEAM);
        $playerO = new Player(Player::O_TEAM);

        $game->move($playerX, new Position(2, 2));
        $game->move($playerO, new Position(0, 2));
        $game->move($playerX, new Position(1, 1));
        $game->move($playerO, new Position(0, 1));

        try {
            $game->move($playerX, new Position(0, 0));
        } catch (GameOverException $e) {}

        $this->expectException(GameOverException::class);
        $this->expectExceptionMessage('Sorry, you can\'t make the given movement, the game has already over.');
        $game->move($playerO, new Position(0, 0));
    }

    public function testDrawGameThrowGameOverException()
    {
        $game = new Game();
        $playerX = new Player(Player::X_TEAM);
        $playerO = new Player(Player::O_TEAM);

        $expectedBoard = [
            ['X', 'O', 'O'],
            ['O', 'X', 'X'],
            ['X', 'X', 'O'],
        ];

        $game->move($playerX, new Position(0, 0));
        $game->move($playerO, new Position(0, 1));
        $game->move($playerX, new Position(1, 1));
        $game->move($playerO, new Position(0, 2));
        $game->move($playerX, new Position(1, 2));
        $game->move($playerO, new Position(1, 0));
        $game->move($playerX, new Position(2, 0));
        $game->move($playerO, new Position(2, 2));

        try {
            $game->move($playerX, new Position(2, 1));
        } catch (GameOverException $e) {
            $this->assertInstanceOf(GameOverException::class, $e);
            $this->assertEquals('Draw game!', $e->getMessage());
            $this->assertEquals($expectedBoard, $game->getBoard()->getContent());
            $this->assertEmpty($game->getWinner()->getTeam());
        }
    }
}