<?php
namespace App\Tests\Model\Move;

use App\Model\Move\BotMove;
use App\Model\Randomizer;
use PHPUnit\Framework\TestCase;

class BotMoveTest extends TestCase
{

    /**
     * @dataProvider boardToBotBlockHumanProvider
     */
    public function testBotBlockHuman($board, $row, $column, $playerUnit)
    {
        $expected = [$row, $column, $playerUnit];
        $randomizer = new Randomizer();
        $botMove = new BotMove($randomizer);

        $this->assertEquals($expected, $botMove->makeMove($board, $playerUnit));
    }

    /**
     * @dataProvider boardToWinGameProvider
     */
    public function testBotMoveToWinGame($board, $row, $column, $playerUnit)
    {
        $expected = [$row, $column, $playerUnit];
        $botMove = new BotMove($this->mockRandomizer(0));

        $this->assertEquals($expected, $botMove->makeMove($board, $playerUnit));
    }

    public function testBotMoveToAleatoryPosition()
    {
        $boardState = [
            ['X', 'O', 'X'],
            ['', '', ''],
            ['', '', ''],
        ];
        $expected = [1, 0, 'O'];
        $botMove = new BotMove($this->mockRandomizer(0));

        $this->assertEquals($expected, $botMove->makeMove($boardState, 'O'));
    }

    public function boardToWinGameProvider()
    {
        return [
            'bot win on first row' => [
                [
                    ['', 'X', 'X'],
                    ['', 'O', ''],
                    ['O', '', 'O'],
                ],
                0,
                0,
                'X'
            ],
            'bot win on second row' => [
                [
                    ['', 'O', ''],
                    ['X', '', 'X'],
                    ['O', '', 'O'],
                ],
                1,
                1,
                'X'
            ],
            'bot win on third row' => [
                [
                    ['', 'O', ''],
                    ['O', '', 'O'],
                    ['', 'X', 'X'],
                ],
                2,
                0,
                'X'
            ],
            'bot win on first column' => [
                [
                    ['O', 'X', 'X'],
                    ['', 'X', 'O'],
                    ['O', '', ''],
                ],
                1,
                0,
                'O'
            ],
            'bot win on second column' => [
                [
                    ['', '', ''],
                    ['X', 'O', 'X'],
                    ['O', 'O', 'X'],
                ],
                0,
                1,
                'O'
            ],
            'bot win on third column' => [
                [
                    ['', '', ''],
                    ['O', 'X', 'O'],
                    ['X', 'X', 'O'],
                ],
                0,
                2,
                'O'
            ],
            'bot win on left diagonal on left corner' => [
                [
                    ['', 'X', 'X'],
                    ['', 'O', ''],
                    ['', '', 'O'],
                ],
                0,
                0,
                'O'
            ],
            'bot win on left diagonal on right corner' => [
                [
                    ['O', 'X', 'X'],
                    ['', 'O', ''],
                    ['', '', ''],
                ],
                2,
                2,
                'O'
            ],
            'bot win on left diagonal on middle' => [
                [
                    ['O', 'X', 'X'],
                    ['', '', ''],
                    ['', '', 'O'],
                ],
                1,
                1,
                'O'
            ],
            'bot win on right diagonal on right corner' => [
                [
                    ['O', '', ''],
                    ['X', 'X', 'O'],
                    ['X', 'O', ''],
                ],
                0,
                2,
                'X'
            ],
            'bot block on right diagonal on left corner' => [
                [
                    ['O', '', 'X'],
                    ['X', 'X', 'O'],
                    ['', 'O', ''],
                ],
                2,
                0,
                'X'
            ],
            'bot block on right diagonal on center' => [
                [
                    ['O', '', 'X'],
                    ['X', '', 'O'],
                    ['X', 'O', ''],
                ],
                1,
                1,
                'X'
            ],
        ];
    }

    public function boardToBotBlockHumanProvider()
    {
        return [
            'bot block on first column' => [
                [
                    ['O', 'X', 'X'],
                    ['', 'O', ''],
                    ['O', '', ''],
                ],
                1,
                0,
                'X'
            ],
            'bot block on second column' => [
                [
                    ['O', 'O', 'X'],
                    ['X', '', ''],
                    ['', 'O', ''],
                ],
                1,
                1,
                'X'
            ],
            'bot block on third column' => [
                [
                    ['O', 'O', 'X'],
                    ['X', '', ''],
                    ['O', '', 'X'],
                ],
                1,
                2,
                'O'
            ],
            'bot block on first row' => [
                [
                    ['', 'X', 'X'],
                    ['', 'O', ''],
                    ['O', '', ''],
                ],
                0,
                0,
                'O'
            ],
            'bot block on second row' => [
                [
                    ['O', 'O', 'X'],
                    ['X', 'X', ''],
                    ['O', '', ''],
                ],
                1,
                2,
                'O'
            ],
            'bot block on third row' => [
                [
                    ['O', '', 'X'],
                    ['X', '', ''],
                    ['O', 'O', ''],
                ],
                2,
                2,
                'X'
            ],
            'bot block on left diagonal on left corner' => [
                [
                    ['', 'X', ''],
                    ['', 'O', ''],
                    ['X', '', 'O'],
                ],
                0,
                0,
                'X'
            ],
            'bot block on left diagonal on right corner' => [
                [
                    ['O', 'X', 'X'],
                    ['', 'O', ''],
                    ['', '', ''],
                ],
                2,
                2,
                'X'
            ],
            'bot block on left diagonal on middle' => [
                [
                    ['O', 'X', 'X'],
                    ['', '', ''],
                    ['', '', 'O'],
                ],
                1,
                1,
                'X'
            ],
            'bot block on right diagonal on right corner' => [
                [
                    ['O', '', ''],
                    ['X', 'X', 'O'],
                    ['X', 'O', ''],
                ],
                0,
                2,
                'O'
            ],
            'bot block on right diagonal on left corner' => [
                [
                    ['O', '', 'X'],
                    ['X', 'X', 'O'],
                    ['', 'O', ''],
                ],
                2,
                0,
                'O'
            ],
            'bot block on right diagonal on center' => [
                [
                    ['O', '', 'X'],
                    ['X', '', 'O'],
                    ['X', 'O', ''],
                ],
                1,
                1,
                'O'
            ],
        ];
    }

    public function mockRandomizer(int $value)
    {
        $mock = $this->createMock(Randomizer::class);
        $mock->expects($this->any())->method('randomizeIntInterval')->willReturn($value);
        return $mock;
    }
}
