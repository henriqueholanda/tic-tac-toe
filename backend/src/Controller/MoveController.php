<?php
namespace App\Controller;

use App\Entity\Player;
use App\Entity\Position;
use App\Exception\GameNotFoundException;
use App\Exception\GameOverException;
use App\Exception\InvalidMoveException;
use App\Model\Game;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;

class MoveController
{
    /** @var Game */
    private $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    /**
     * @OA\Get(
     *     path="/v1/games/{gameId}/move/bot",
     *     description="This resource will give you the next BOT movement based on game board.",
     *     @OA\Parameter(name="gameId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="New bot movement and staus if game is over"),
     *     @OA\Response(response="404", description="The game informed not exists"),
     *     @OA\Response(response="400", description="Error when bot try to move")
     * )
     */
    public function moveBot(Request $request, string $gameId) : Response
    {
        try {
            $move = $this->game->moveBot($gameId);

            if (isset($move['isOver']) && $move['isOver'] == true) {
                return new JsonResponse(
                    [
                        'row' => $move['row'],
                        'col' => $move['col'],
                        'status'    => 'gameover',
                        'message'   => $move['message'],
                        'board'     => $this->game->getBoard()->getContent()
                    ]
                );
            }
            return new JsonResponse(
                [
                    'row' => $move['row'],
                    'col' => $move['col']
                ]
            );
        } catch (GameNotFoundException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage()
                ],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (InvalidMoveException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage()
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/v1/games/{gameId}/move/human",
     *     description="This resource will save the informed move of human player.",
     *     @OA\Parameter(name="gameId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Return the information when game is over"),
     *     @OA\Response(response="204", description="Move saved and game not over yet"),
     *     @OA\Response(response="400", description="Error when human is trying to move to a filled position"),
     *     @OA\Response(response="422", description="Error on the body request because is missing the move")
     * )
     */
    public function moveHuman(Request $request, string $gameId) : Response
    {
        $body = json_decode($request->getContent());

        try {
            if (!isset($body->move) || !is_array($body->move)) {
                return new JsonResponse(
                    [
                        'error' => 'Body content must have the array field `move` with the position.'
                    ],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $game = $this->game->status($gameId);
            $player = new Player($game['humanPlayer']);
            $move = current($body->move);
            $position = new Position($move->row, $move->col);

            $this->game->moveHuman(
                $gameId,
                $player,
                $position
            );
            return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (GameNotFoundException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage()
                ],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (GameOverException $e) {
            return new JsonResponse(
                [
                    'status'    => 'gameover',
                    'message'   => $e->getMessage(),
                    'board'     => $this->game->getBoard()->getContent()
                ]
            );
        } catch (InvalidMoveException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage()
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
    }
}
