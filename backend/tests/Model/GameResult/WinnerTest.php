<?php
namespace App\Tests\Model\GameResult;

use App\Model\Board;
use App\Model\Game;
use App\Model\GameResult\Draw;
use App\Model\GameResult\Winner;
use PHPUnit\Framework\TestCase;

class WinnerTest extends TestCase
{
    /**
     * @dataProvider boardWithWinnerProvider
     */
    public function testBoardWithGameWinner($board)
    {
        $this->assertTrue(Winner::check($this->mockGame($board)));
    }

    /**
     * @dataProvider boardWithoutWinnerProvider
     */
    public function testBoardWithoutGameWinner($board)
    {
        $this->assertFalse(Winner::check($this->mockGame($board)));
    }

    private function mockBoardContent(array $content) : Board
    {
        $mock = $this->getMockBuilder(Board::class)
            ->setMethods(['getContent'])
            ->getMock();
        $mock->expects($this->any())
            ->method('getContent')
            ->willReturn($content);
        return $mock;
    }

    private function mockGame(array $content) : Game
    {
        $mock = $this->getMockBuilder(Game::class)
            ->disableOriginalConstructor()
            ->setMethods(['getBoard'])
            ->getMock();
        $mock->expects($this->any())
            ->method('getBoard')
            ->willReturn($this->mockBoardContent($content));
        return $mock;
    }

    public function boardWithoutWinnerProvider()
    {
        return [
            'game not finished' => [
                [
                    ['X', '', ''],
                    ['', 'O', ''],
                    ['', 'X', 'O'],
                ]
            ],
            'draw game' => [
                [
                    ['X', 'X', 'O'],
                    ['O', 'X', 'X'],
                    ['X', 'O', 'O'],
                ]
            ]
        ];
    }

    public function boardWithWinnerProvider()
    {
        return [
            'winner on first row' => [
                [
                    ['X', 'X', 'X'],
                    ['O', 'O', 'X'],
                    ['X', 'O', 'O'],
                ]
            ],
            'winner on second row' => [
                [
                    ['O', 'X', 'O'],
                    ['X', 'X', 'X'],
                    ['X', 'O', 'O'],
                ]
            ],
            'winner on third row' => [
                [
                    ['O', 'X', 'O'],
                    ['X', 'O', 'O'],
                    ['X', 'X', 'X'],
                ]
            ],
            'winner on left diagonal' => [
                [
                    ['O', 'X', 'X'],
                    ['X', 'O', 'O'],
                    ['X', 'X', 'O'],
                ]
            ],
            'winner on right diagonal' => [
                [
                    ['O', 'X', 'O'],
                    ['X', 'O', 'O'],
                    ['O', 'X', 'X'],
                ]
            ],
            'winner on first column' => [
                [
                    ['O', 'X', 'O'],
                    ['O', 'O', 'X'],
                    ['O', 'X', 'X'],
                ]
            ],
            'winner on second column' => [
                [
                    ['O', 'X', 'O'],
                    ['X', 'X', 'O'],
                    ['O', 'X', 'X'],
                ]
            ],
            'winner on third column' => [
                [
                    ['X', 'X', 'O'],
                    ['X', 'O', 'O'],
                    ['O', 'X', 'O'],
                ]
            ],
        ];
    }
}
