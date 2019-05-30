<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    public function testStartNewGameReturnGameId()
    {
        $client = $this->createClient();
        $botPlayer = [
            "botPlayer" => 'X'
        ];

        $client->request(
            'POST',
            '/v1/games',
            [],
            [],
            ['Content-Type' => 'application/json'],
            json_encode($botPlayer)
        );

        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotEmpty($response->gameId);
    }
}
