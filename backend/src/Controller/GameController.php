<?php
namespace App\Controller;

use App\Entity\Player;
use App\Entity\Position;
use App\Exception\GameOverException;
use App\Model\Game;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GameController
{
    /** @var Game */
    private $game;

    /** @var array */
    private $players = [];

    public function __construct(Game $game)
    {
        $this->game = $game;

        $this->players = [
            Player::X_TEAM => new Player(Player::X_TEAM),
            Player::O_TEAM => new Player(Player::O_TEAM),
        ];
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function status(Request $request) : Response
    {
        $body = json_decode($request->getContent());

        try {
            if (!isset($body->boardTurns) || !is_array($body->boardTurns)) {
                return new JsonResponse(
                    [
                        'error' => 'Body content must have an array field `boardTurns`.'
                    ],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            foreach ($body->boardTurns as $boardTurn) {
                $this->game->move(
                    $this->players[$boardTurn->team],
                    new Position($boardTurn->row, $boardTurn->column)
                );
            }

            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (GameOverException $e) {
            return new JsonResponse(
                [
                    'status'    => 'gameover',
                    'message'   => $e->getMessage(),
                    'board'     => $this->game->getBoard()->getContent()
                ]
            );
        } catch (InvalidArgumentException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage()
                ],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
