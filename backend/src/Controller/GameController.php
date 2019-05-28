<?php
namespace App\Controller;

use App\Entity\Player;
use App\Exception\GameNotFoundException;
use App\Exception\GameOverException;
use App\Model\Game;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Tic Tac Toe",
 *     version="0.1",
 *     @OA\Contact(
 *         email="contato@henriqueholanda.com.br"
 *     ),
 * )
 */
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
     * @OA\Post(
     *     path="/v1/game",
     *     description="This resource will start a new game and generate an ID to the game.",
     *     @OA\Response(response="200", description="Game started and ID was generated"),
     *     @OA\Response(response="422", description="Validation error because bot player is missing"),
     *     @OA\Response(response="500", description="Internal error")
     * )
     */
    public function start(Request $request) : Response
    {
        $body = json_decode($request->getContent());

        try {
            if (!isset($body->botPlayer) || empty($body->botPlayer)) {
                return new JsonResponse(
                    [
                        'error' => 'Body content must have the field `botPlayer`.'
                    ],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            $gameId = $this->game->start(new Player($body->botPlayer));
            return new JsonResponse(
                [
                    'gameId' => $gameId
                ],
                JsonResponse::HTTP_OK
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage()
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
