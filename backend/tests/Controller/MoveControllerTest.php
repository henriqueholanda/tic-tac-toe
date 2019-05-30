<?php
namespace App\Tests\Controller;

use App\Domain\Model\Game;
use App\Domain\Move\BotMove;
use App\Domain\Board;
use App\Domain\Move;
use App\Domain\Player;
use App\Domain\Position;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MoveControllerTest extends WebTestCase
{
    private function insertDataToTest(Game $game)
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $storage = static::$kernel->getContainer()
            ->get('App\Domain\Storage');

        $storage->save($game);
    }

    public function testSendHumanInvalidMoveReturnError()
    {
        $gameId = 'test123';

        $client = $this->createClient();

        $client->request(
            'POST',
            '/v1/games/' . $gameId . '/move',
            [],
            [],
            ['Content-Type' => 'application/json'],
            '{"move":""}',
        );


        $this->assertEquals(422, $client->getResponse()->getStatusCode());
    }

    public function testSendHumanMoveReturnBotMove()
    {
        $humanPlayer = new Player(Player::O_TEAM);
        $botPlayer = new Player($humanPlayer->getOpponent());
        $moves = [
            new Move($humanPlayer, new Position(0, 1)),
            new Move($botPlayer, new Position(0, 0)),
            new Move($humanPlayer, new Position(0, 2)),
            new Move($botPlayer, new Position(2, 0)),
        ];

        $game = new Game(
            new Board($moves),
            $humanPlayer,
            new BotMove()
        );
        $gameId = 'test123';
        $game->setId($gameId);

        $this->insertDataToTest($game);

        $client = $this->createClient();

        $client->request(
            'POST',
            '/v1/games/' . $gameId . '/move',
            [],
            [],
            ['Content-Type' => 'application/json'],
            '{"move":[{"row":2,"col":2}]}',
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertNotEmpty($client->getResponse()->getContent());
        $this->assertStringContainsString(
            'row',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'col',
            $client->getResponse()->getContent()
        );
    }

    public function testSendHumanMoveWithAnInvalidGameIdReturnError()
    {
        $gameId = 'abc66543';
        $client = $this->createClient();

        $client->request(
            'POST',
            '/v1/games/' . $gameId . '/move',
            [],
            [],
            ['Content-Type' => 'application/json'],
            '{"move":[{"row":2,"col":2}]}',
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testSendHumanMoveWhenGameOverReturnInformations()
    {
        $humanPlayer = new Player(Player::O_TEAM);
        $botPlayer = new Player($humanPlayer->getOpponent());
        $moves = [
            new Move($humanPlayer, new Position(0, 1)),
            new Move($botPlayer, new Position(1, 0)),
            new Move($humanPlayer, new Position(0, 2)),
            new Move($botPlayer, new Position(2, 0)),
        ];

        $game = new Game(
            new Board($moves),
            $humanPlayer,
            new BotMove()
        );
        $gameId = 'test123';
        $game->setId($gameId);

        $this->insertDataToTest($game);

        $client = $this->createClient();

        $client->request(
            'POST',
            '/v1/games/' . $gameId . '/move',
            [],
            [],
            ['Content-Type' => 'application/json'],
            '{"move":[{"row":0,"col":0}]}',
        );


        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('gameover', $response->status);
        $this->assertStringContainsString(
            'message',
            $client->getResponse()->getContent()
        );
    }

    public function testSendHumanMoveToFilledPositionReturnError()
    {
        $humanPlayer = new Player(Player::O_TEAM);
        $botPlayer = new Player($humanPlayer->getOpponent());
        $moves = [
            new Move($humanPlayer, new Position(0, 1)),
            new Move($botPlayer, new Position(1, 0)),
            new Move($humanPlayer, new Position(0, 2)),
            new Move($botPlayer, new Position(2, 0)),
        ];

        $game = new Game(
            new Board($moves),
            $humanPlayer,
            new BotMove()
        );
        $gameId = 'test123';
        $game->setId($gameId);

        $this->insertDataToTest($game);

        $client = $this->createClient();

        $client->request(
            'POST',
            '/v1/games/' . $gameId . '/move',
            [],
            [],
            ['Content-Type' => 'application/json'],
            '{"move":[{"row":1,"col":0}]}',
        );


        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals('The position [1,0] is already filled', $response->error);
    }
}
