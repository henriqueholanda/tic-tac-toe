<?php
namespace App\Tests\Model\Move;

use App\Exception\InvalidMoveException;
use App\Model\Move\BotMove;
use PHPUnit\Framework\TestCase;

class BotMoveTest extends TestCase
{
    /** @var BotMove */
    private $botMove;

    public function setUp() : void
    {
        $this->botMove = new BotMove();
    }

    public function testBotMakeFirstMove()
    {
        $boardState = [
            ['', '', ''],
            ['', '', ''],
            ['', '', ''],
        ];

        $expected = [0, 0, 'X'];

        $this->assertEquals($expected, $this->botMove->makeMove($boardState, 'X'));
    }

    public function testBotMakeMove()
    {
        $boardState = [
            ['X', 'O', 'X'],
            ['', '', ''],
            ['', '', ''],
        ];

        $expected = [1, 0, 'O'];

        $this->assertEquals($expected, $this->botMove->makeMove($boardState, 'O'));
    }

    public function testBotCanNotMakeMoveWhenGameIsOver()
    {
        $boardState = [
            ['O', 'X', 'X'],
            ['X', 'O', 'X'],
            ['O', 'X', 'O'],
        ];

        $this->expectException(InvalidMoveException::class);
        $this->botMove->makeMove($boardState, 'O');
    }
}
