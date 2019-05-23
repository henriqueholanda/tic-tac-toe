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
     *     path="/v1/game/status",
     *     description="This resource will give you the game status based on game turns.",
     *     @OA\Response(response="200", description="Game is over and have a winner or is a draw"),
     *     @OA\Response(response="204", description="Game is not over"),
     *     @OA\Response(response="422", description="Validation error")
     * )
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
