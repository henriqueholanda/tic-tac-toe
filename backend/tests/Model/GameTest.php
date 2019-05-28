<?php
namespace App\Tests\Model;

use App\Entity\Player;
use App\Entity\Position;
use App\Exception\GameNotFoundException;
use App\Exception\GameOverException;
use App\Exception\InvalidMoveException;
use App\Model\Board;
use App\Model\Game;
use App\Model\GameResult\Draw;
use App\Model\GameResult\Winner;
use App\Model\Move\BotMove;
use App\Model\Randomizer;
use App\Service\CacheService;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public function testNewGameWithEmptyBoardContent()
    {
        $expectedBoardContent = [
            ['', '', ''],
            ['', '', ''],
            ['', '', ''],
        ];

        $game = new Game(
            new BotMove(new Randomizer()),
            $this->mockCacheService(json_encode($expectedBoardContent))
        );

        $gameId = $game->start(new Player('X'));
        $this->assertEquals($expectedBoardContent, $game->getBoard()->getContent());
        $this->assertNotEmpty($gameId);
    }

    public function testGetStatusOfGameNotFoundThrowException()
    {
        $game = new Game(
            new BotMove(new Randomizer()),
            $this->mockCacheService('')
        );

        $gameId = '66273ujd22';
        $this->expectException(GameNotFoundException::class);
        $game->status($gameId);
    }

    public function testMakeMoveWithSuccess()
    {
        $cached = [
            'board' => [
                ['', '', ''],
                ['', 'X', ''],
                ['', '', ''],
            ],
            'botPlayer' => 'O',
            'humanPlayer' => 'X',
            'isOver' => false,
            'winner' => ''
        ];

        $game = new Game(
            $this->mockBotMove([0, 0, 'O']),
            $this->mockCacheService(json_encode($cached))
        );

        $expectedBoard = [
            ['O', '', ''],
            ['', 'X', ''],
            ['', '', ''],
        ];

        $game->moveBot(
            '1ab423ff2'
        );

        $this->assertEquals($expectedBoard, $game->getBoard()->getContent());
    }

    public function testBothPlayersMakeMovesWithBotWinner()
    {
        $cached = [
            'board' => [
                ['X', 'O', 'O'],
                ['O', 'X', 'X'],
                ['', 'X', ''],
            ],
            'botPlayer' => 'X',
            'humanPlayer' => 'O',
            'isOver' => false,
            'winner' => ''
        ];

        $game = new Game(
            $this->mockBotMove([2, 2, 'X']),
            $this->mockCacheService(json_encode($cached))
        );

        $expected = [
            'row' => 2,
            'col' => 2,
            'isOver' => true,
            'message' => 'The X team is the winner!'
        ];

        $gameId = '32jj344j';

        $this->mockWinner(true);

        $response = $game->moveBot($gameId);
        $this->assertEquals($expected, $response);
    }

    public function testTryToMakeMoveThrowsAnInvalidMoveExceptionWhenMoveToSamePosition()
    {
        $cached = [
            'board' => [
                ['X', '', 'O'],
                ['O', 'X', 'X'],
                ['', 'X', ''],
            ],
            'botPlayer' => 'O',
            'humanPlayer' => 'X',
            'isOver' => false,
            'winner' => ''
        ];

        $game = new Game(
            new BotMove(new Randomizer()),
            $this->mockCacheService(json_encode($cached))
        );

        $gameId = '54jj2b3';
        $playerO = new Player(Player::O_TEAM);


        $this->expectException(InvalidMoveException::class);
        $this->expectExceptionMessage('The position [1,1] is already filled by `X` team');

        $game->moveHuman($gameId, $playerO, new Position(1, 1));
    }

    public function testTryToMakeMoveThrowsAnGameOverExceptionWhenGameAlreadyOver()
    {
        $cached = [
            'board' => [
                ['X', 'O', 'O'],
                ['O', 'X', 'X'],
                ['X', 'X', 'O'],
            ],
            'botPlayer' => 'O',
            'humanPlayer' => 'X',
            'isOver' => true,
            'winner' => ''
        ];

        $game = new Game(
            new BotMove(new Randomizer()),
            $this->mockCacheService(json_encode($cached))
        );

        $gameId = '4256jjnnb4';

        $playerX = new Player(Player::X_TEAM);
        $playerO = new Player(Player::O_TEAM);

        try {
            $game->moveHuman($gameId, $playerX, new Position(0, 0));
        } catch (GameOverException $e) {
        }

        $this->expectException(GameOverException::class);
        $this->expectExceptionMessage('Sorry, you can\'t make the given movement, the game has already over.');
        $game->moveHuman($gameId, $playerO, new Position(0, 0));
    }

    public function testDrawGameThrowGameOverException()
    {
        $cached = [
            'board' => [
                ['X', 'O', 'O'],
                ['O', 'X', 'X'],
                ['X', '', 'O'],
            ],
            'botPlayer' => 'O',
            'humanPlayer' => 'X',
            'isOver' => false,
            'winner' => ''
        ];

        $game = new Game(
            new BotMove(new Randomizer()),
            $this->mockCacheService(json_encode($cached))
        );

        $gameId = '994jhhcw2';
        $playerX = new Player(Player::X_TEAM);
        $this->mockDraw(true);
        $expectedBoard = [
            ['X', 'O', 'O'],
            ['O', 'X', 'X'],
            ['X', 'X', 'O'],
        ];

        try {
            $game->moveHuman($gameId, $playerX, new Position(2, 1));
        } catch (GameOverException $e) {
            $this->assertInstanceOf(GameOverException::class, $e);
            $this->assertEquals('Draw game!', $e->getMessage());
            $this->assertEquals($expectedBoard, $game->getBoard()->getContent());
            $this->assertEmpty($game->getWinner()->getTeam());
        }
    }

    public function mockCacheService(string $cacheResponse)
    {
        $mock = $this->getMockBuilder(CacheService::class)
            ->disableOriginalConstructor()
            ->setMethods(['save', 'get'])
            ->getMock();
        $mock->expects($this->any())
            ->method('get')
            ->willReturn($cacheResponse);
        return $mock;
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

    public function mockWinner(bool $winner)
    {
        $mock = $this->getMockBuilder(Winner::class)
            ->disableOriginalConstructor()
            ->setMethods(['check'])
            ->getMock();
        $mock->expects($this->any())
            ->method('check')
            ->willReturn($winner);
        return $mock;
    }

    public function mockDraw(bool $winner)
    {
        $mock = $this->getMockBuilder(Draw::class)
            ->disableOriginalConstructor()
            ->setMethods(['check'])
            ->getMock();
        $mock->expects($this->any())
            ->method('check')
            ->willReturn($winner);
        return $mock;
    }
}
