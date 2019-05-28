<?php
namespace App\Tests\Model\GameResult;

use App\Model\Board;
use App\Model\Game;
use App\Model\GameResult\Draw;
use PHPUnit\Framework\TestCase;

class DrawTest extends TestCase
{
    public function testIsDrawGame()
    {
        $board = [
            ['X', 'X', 'O'],
            ['O', 'X', 'X'],
            ['X', 'O', 'O'],
        ];
        $this->assertTrue(Draw::check($this->mockGame($board)));
    }

    public function testIsNotDrawGame()
    {
        $board = [
            ['O', 'O', 'X'],
            ['X', '', 'X'],
            ['X', 'O', 'O'],
        ];
        $this->assertFalse(Draw::check($this->mockGame($board)));
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
}
