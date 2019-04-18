<?php
namespace App\Tests\Model\GameResult;

use App\Model\Board;
use App\Model\Game;
use App\Model\GameResult\Draw;
use App\Model\GameResult\Winner;
use PHPUnit\Framework\TestCase;

class WinnerTest extends TestCase
{
    public function testHasWinnerOnFirstRow()
    {
        $board = [
            ['X', 'X', 'X'],
            ['O', 'O', 'X'],
            ['X', 'O', 'O'],
        ];
        $this->assertTrue(Winner::check($this->mockGame($board)));
    }

    public function testHasWinnerOnSecondRow()
    {
        $board = [
            ['O', 'X', 'O'],
            ['X', 'X', 'X'],
            ['X', 'O', 'O'],
        ];
        $this->assertTrue(Winner::check($this->mockGame($board)));
    }

    public function testHasWinnerOnThirdRow()
    {
        $board = [
            ['O', 'X', 'O'],
            ['X', 'O', 'O'],
            ['X', 'X', 'X'],
        ];
        $this->assertTrue(Winner::check($this->mockGame($board)));
    }

    public function testHasWinnerOnLeftDiagonal()
    {
        $board = [
            ['O', 'X', 'X'],
            ['X', 'O', 'O'],
            ['X', 'X', 'O'],
        ];
        $this->assertTrue(Winner::check($this->mockGame($board)));
    }

    public function testHasWinnerOnRightDiagonal()
    {
        $board = [
            ['O', 'X', 'O'],
            ['X', 'O', 'O'],
            ['O', 'X', 'X'],
        ];
        $this->assertTrue(Winner::check($this->mockGame($board)));
    }

    public function testHasWinnerOnFirstColumn()
    {
        $board = [
            ['O', 'X', 'O'],
            ['O', 'O', 'X'],
            ['O', 'X', 'X'],
        ];
        $this->assertTrue(Winner::check($this->mockGame($board)));
    }

    public function testHasWinnerOnSecondColumn()
    {
        $board = [
            ['O', 'X', 'O'],
            ['X', 'X', 'O'],
            ['O', 'X', 'X'],
        ];
        $this->assertTrue(Winner::check($this->mockGame($board)));
    }

    public function testHasWinnerOnThirdColumn()
    {
        $board = [
            ['X', 'X', 'O'],
            ['X', 'O', 'O'],
            ['O', 'X', 'O'],
        ];
        $this->assertTrue(Winner::check($this->mockGame($board)));
    }

    public function testHasNoWinner()
    {
        $board = [
            ['X', '', ''],
            ['', 'O', ''],
            ['', 'X', 'O'],
        ];
        $this->assertFalse(Winner::check($this->mockGame($board)));
    }

    public function testIsDrawGame()
    {
        $board = [
            ['X', 'X', 'O'],
            ['O', 'X', 'X'],
            ['X', 'O', 'O'],
        ];
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
            ->setMethods(['getBoard'])
            ->getMock();
        $mock->expects($this->any())
            ->method('getBoard')
            ->willReturn($this->mockBoardContent($content));
        return $mock;
    }
}
