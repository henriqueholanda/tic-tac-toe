<?php
namespace App\Tests\Controller;

use App\Model\Randomizer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MoveControllerTest extends WebTestCase
{
    public function testRequestBotMoveReturnPosition()
    {
        $client = $this->createClient();
        $boardTurns = [
            "board" => [
                ["O", "", "X"],
                ["", "X", ""],
                ["O", "O", ""]
            ]
        ];

        $client->request(
            'POST',
            '/v1/bot/X/move',
            [],
            [],
            ['Content-Type' => 'application/json'],
            json_encode($boardTurns)
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'row',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'column',
            $client->getResponse()->getContent()
        );
    }

    public function testRequestBotMoveWhenGameOverReturnError()
    {
        $client = $this->createClient();
        $boardTurns = [
            "board" => [
                ["O", "O", "X"],
                ["X", "X", "O"],
                ["O", "O", "X"]
            ]
        ];

        $client->request(
            'POST',
            '/v1/bot/X/move',
            [],
            [],
            ['Content-Type' => 'application/json'],
            json_encode($boardTurns)
        );

        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals($response->error, 'Bot can\'t make a movement.');
    }
}
