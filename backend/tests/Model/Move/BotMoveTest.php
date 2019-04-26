<?php
namespace App\Tests\Model\Move;

use App\Exception\InvalidMoveException;
use App\Model\Move\BotMove;
use App\Model\Randomizer;
use PHPUnit\Framework\TestCase;

class BotMoveTest extends TestCase
{

    public function testBotMakeFirstMove()
    {
        $boardState = [
            ['', '', ''],
            ['', '', ''],
            ['', '', ''],
        ];

        $expected = [1, 2, 'X'];
        $randomizer = $this->mockRandomizer(1, 2);
        $botMove = new BotMove($randomizer);

        $this->assertEquals($expected, $botMove->makeMove($boardState, 'X'));
    }

    public function testBotMakeMove()
    {
        $boardState = [
            ['X', 'O', 'X'],
            ['', '', ''],
            ['', '', ''],
        ];

        $expected = [2, 1, 'O'];
        $randomizer = $this->mockRandomizer(2, 1);
        $botMove = new BotMove($randomizer);

        $this->assertEquals($expected, $botMove->makeMove($boardState, 'O'));
    }

    public function testBotCanNotMakeMoveWhenGameIsOver()
    {
        $boardState = [
            ['O', 'X', 'X'],
            ['X', 'O', 'X'],
            ['O', 'X', 'O'],
        ];

        $this->expectException(InvalidMoveException::class);
        $botMove = new BotMove(new Randomizer());
        $botMove->makeMove($boardState, 'O');
    }

    public function mockRandomizer(int $row, int $column)
    {
        $mock = $this->createMock(Randomizer::class);
        $mock->expects($this->any())->method('randomizeIndex')->willReturn($row);
        $mock->expects($this->any())->method('randomizeValue')->willReturn($column);
        return $mock;
    }
}
