<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MoveControllerTest extends WebTestCase
{
    private function insertDataToTest(string $key, string $data)
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $cacheService = static::$kernel->getContainer()
            ->get('App\Service\CacheService');

        $cacheService->save($key, $data);
    }

    public function testRequestBotMoveReturnPosition()
    {
        $gameId = 'test123';
        $cached = [
            'board' => [
                ['X', 'O', 'O'],
                ['', '', ''],
                ['X', '', ''],
            ],
            'botPlayer' => 'O',
            'humanPlayer' => 'X',
            'isOver' => false,
            'winner' => ''
        ];
        $this->insertDataToTest($gameId, json_encode($cached));

        $client = $this->createClient();

        $client->request(
            'GET',
            '/v1/games/' . $gameId . '/move/bot',
            [],
            [],
            ['Content-Type' => 'application/json'],
        );


        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'row',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'col',
            $client->getResponse()->getContent()
        );
    }

    public function testRequestBotMoveWhenGameIsOverReturnInformations()
    {
        $gameId = 'test123';
        $cached = [
            'board' => [
                ['X', 'O', 'O'],
                ['O', 'X', 'O'],
                ['X', 'O', ''],
            ],
            'botPlayer' => 'X',
            'humanPlayer' => 'O',
            'isOver' => false,
            'winner' => ''
        ];
        $this->insertDataToTest($gameId, json_encode($cached));

        $client = $this->createClient();

        $client->request(
            'GET',
            '/v1/games/' . $gameId . '/move/bot',
            [],
            [],
            ['Content-Type' => 'application/json'],
        );


        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('gameover', $response->status);
        $this->assertStringContainsString(
            'board',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'message',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'row',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'col',
            $client->getResponse()->getContent()
        );
    }

    public function testRequestBotMoveWithAnInvalidGameIdReturnError()
    {
        $gameId = 'abc66543';
        $client = $this->createClient();

        $client->request(
            'GET',
            '/v1/games/' . $gameId . '/move/bot',
            [],
            [],
            ['Content-Type' => 'application/json'],
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testRequestBotMoveWhenBoardHaveNoAvailablePositionsReturnError()
    {
        $gameId = 'test123';
        $cached = [
            'board' => [
                ['X', 'O', 'O'],
                ['O', 'X', 'O'],
                ['X', 'O', 'X'],
            ],
            'botPlayer' => 'X',
            'humanPlayer' => 'O',
            'isOver' => true,
            'winner' => 'X'
        ];
        $this->insertDataToTest($gameId, json_encode($cached));
        $client = $this->createClient();

        $client->request(
            'GET',
            '/v1/games/' . $gameId . '/move/bot',
            [],
            [],
            ['Content-Type' => 'application/json'],
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testSendHumanInvalidMoveReturnError()
    {
        $gameId = 'test123';

        $client = $this->createClient();

        $client->request(
            'POST',
            '/v1/games/' . $gameId . '/move/human',
            [],
            [],
            ['Content-Type' => 'application/json'],
            '{"move":""}',
        );


        $this->assertEquals(422, $client->getResponse()->getStatusCode());
    }

    public function testSendHumanMoveReturnNoContent()
    {
        $gameId = 'test123';
        $cached = [
            'board' => [
                ['X', 'O', 'O'],
                ['', '', ''],
                ['X', '', ''],
            ],
            'botPlayer' => 'O',
            'humanPlayer' => 'X',
            'isOver' => false,
            'winner' => ''
        ];
        $this->insertDataToTest($gameId, json_encode($cached));

        $client = $this->createClient();

        $client->request(
            'POST',
            '/v1/games/' . $gameId . '/move/human',
            [],
            [],
            ['Content-Type' => 'application/json'],
            '{"move":[{"row":2,"col":2}]}',
        );


        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testSendHumanMoveWithAnInvalidGameIdReturnError()
    {
        $gameId = 'abc66543';
        $client = $this->createClient();

        $client->request(
            'POST',
            '/v1/games/' . $gameId . '/move/human',
            [],
            [],
            ['Content-Type' => 'application/json'],
            '{"move":[{"row":2,"col":2}]}',
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testSendHumanMoveWhenGameOverReturnInformations()
    {
        $gameId = 'test123';
        $cached = [
            'board' => [
                ['X', 'O', 'O'],
                ['O', 'X', 'O'],
                ['X', 'O', 'X'],
            ],
            'botPlayer' => 'X',
            'humanPlayer' => 'O',
            'isOver' => true,
            'winner' => 'X'
        ];
        $this->insertDataToTest($gameId, json_encode($cached));

        $client = $this->createClient();

        $client->request(
            'POST',
            '/v1/games/' . $gameId . '/move/human',
            [],
            [],
            ['Content-Type' => 'application/json'],
            '{"move":[{"row":2,"col":2}]}',
        );


        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('gameover', $response->status);
        $this->assertStringContainsString(
            'board',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'message',
            $client->getResponse()->getContent()
        );
    }

    public function testSendHumanMoveToFilledPositionReturnError()
    {
        $gameId = 'test123';
        $cached = [
            'board' => [
                ['X', '', ''],
                ['O', '', 'O'],
                ['X', 'O', ''],
            ],
            'botPlayer' => 'X',
            'humanPlayer' => 'O',
            'isOver' => false,
            'winner' => ''
        ];
        $this->insertDataToTest($gameId, json_encode($cached));

        $client = $this->createClient();

        $client->request(
            'POST',
            '/v1/games/' . $gameId . '/move/human',
            [],
            [],
            ['Content-Type' => 'application/json'],
            '{"move":[{"row":0,"col":0}]}',
        );


        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals('The position [0,0] is already filled by `X` team', $response->error);
    }
}
