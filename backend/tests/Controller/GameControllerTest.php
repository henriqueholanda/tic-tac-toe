<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    public function testSendInvalidBoardReturnAnError()
    {
        $client = $this->createClient();
        $boardTurns = [
            "boardTurns" => ''
        ];

        $client->request(
            'POST',
            '/v1/game/status',
            [],
            [],
            ['Content-Type' => 'application/json'],
            json_encode($boardTurns)
        );

        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(422, $client->getResponse()->getStatusCode());
        $this->assertEquals($response->error, 'Body content must have an array field `boardTurns`.');
    }

    public function testSendCurrentBoardWithInvalidRowPositionReturnAnError()
    {
        $client = $this->createClient();
        $boardTurns = [
            "boardTurns" => [
                [
                    "team" => "X",
                    "row" => 4,
                    "column" => 0,
                ],
                [
                    "team" => "O",
                    "row" => 1,
                    "column" => 1
                ],
            ]
        ];

        $client->request(
            'POST',
            '/v1/game/status',
            [],
            [],
            ['Content-Type' => 'application/json'],
            json_encode($boardTurns)
        );

        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(422, $client->getResponse()->getStatusCode());
        $this->assertEquals($response->error, 'Invalid row! Expected values are [ 0, 1 or 2]. Got: 4');
    }

    public function testSendCurrentBoardWithInvalidColumnPositionReturnAnError()
    {
        $client = $this->createClient();
        $boardTurns = [
            "boardTurns" => [
                [
                    "team" => "X",
                    "row" => 1,
                    "column" => 0,
                ],
                [
                    "team" => "O",
                    "row" => 1,
                    "column" => 3
                ],
            ]
        ];

        $client->request(
            'POST',
            '/v1/game/status',
            [],
            [],
            ['Content-Type' => 'application/json'],
            json_encode($boardTurns)
        );

        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals(422, $client->getResponse()->getStatusCode());
        $this->assertEquals($response->error, 'Invalid column! Expected values are [ 0, 1 or 2]. Got: 3');
    }

    public function testSendCurrentBoardTurnsReturnNoContent()
    {
        $client = $this->createClient();
        $boardTurns = [
            "boardTurns" => [
                [
                    "team" => "X",
                    "row" => 1,
                    "column" => 0,
                ],
                [
                    "team" => "O",
                    "row" => 1,
                    "column" => 1
                ],
            ]
        ];

        $client->request(
            'POST',
            '/v1/game/status',
            [],
            [],
            ['Content-Type' => 'application/json'],
            json_encode($boardTurns)
        );

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $this->assertEmpty($client->getResponse()->getContent());
    }

    public function testSendCurrentBoardTurnsReturnGameOverWithWinner()
    {
        $client = $this->createClient();
        $boardTurns = [
            "boardTurns" => [
                [
                    "team" => "X",
                    "row" => 0,
                    "column" => 0,
                ],
                [
                    "team" => "O",
                    "row" => 1,
                    "column" => 1
                ],
                [
                    "team" => "X",
                    "row" => 0,
                    "column" => 1,
                ],
                [
                    "team" => "O",
                    "row" => 1,
                    "column" => 2
                ],
                [
                    "team" => "X",
                    "row" => 0,
                    "column" => 2,
                ]
            ]
        ];

        $client->request(
            'POST',
            '/v1/game/status',
            [],
            [],
            ['Content-Type' => 'application/json'],
            json_encode($boardTurns)
        );

        $expectedBoardResponse = [["X","X","X"],["","O","O"],["","",""]];

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals($response->status, 'gameover');
        $this->assertEquals($response->message, 'The X team is the winner!');
        $this->assertEquals($response->board, $expectedBoardResponse);
    }

    public function testSendCurrentBoardTurnsReturnGameOverDraw()
    {
        $client = $this->createClient();
        $boardTurns = [
            "boardTurns" => [
                [
                    "team" => "O",
                    "row" => 0,
                    "column" => 0,
                ],
                [
                    "team" => "X",
                    "row" => 0,
                    "column" => 1
                ],
                [
                    "team" => "O",
                    "row" => 0,
                    "column" => 2,
                ],
                [
                    "team" => "X",
                    "row" => 1,
                    "column" => 1
                ],
                [
                    "team" => "O",
                    "row" => 1,
                    "column" => 0,
                ],
                [
                    "team" => "X",
                    "row" => 2,
                    "column" => 0,
                ],
                [
                    "team" => "O",
                    "row" => 1,
                    "column" => 2,
                ],
                [
                    "team" => "X",
                    "row" => 2,
                    "column" => 2,
                ],
                [
                    "team" => "O",
                    "row" => 2,
                    "column" => 1,
                ]
            ]
        ];

        $client->request(
            'POST',
            '/v1/game/status',
            [],
            [],
            ['Content-Type' => 'application/json'],
            json_encode($boardTurns)
        );

        $expectedBoardResponse = [
            ["O","X","O"],
            ["O","X","O"],
            ["X","O","X"]
        ];

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $response = json_decode($client->getResponse()->getContent());
        $this->assertEquals($response->status, 'gameover');
        $this->assertEquals($response->message, 'Draw game!');
        $this->assertEquals($response->board, $expectedBoardResponse);
    }
}