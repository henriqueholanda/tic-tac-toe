<?php
namespace App\Controller;

use App\Domain\Model\Game;
use App\Domain\Move\BotMove;
use App\Domain\Storage;
use App\Domain\Board;
use App\Domain\Player;
use App\Domain\Position;
use App\Exception\GameNotFoundException;
use App\Exception\GameOverException;
use App\Exception\InvalidMoveException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;

class MoveController
{
    /** @var Storage */
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @OA\Post(
     *     path="/v1/games/{gameId}/move",
     *     description="This resource will register the human move and generate a new Bot move.",
     *     @OA\Parameter(name="gameId",
     *         in="path",
     *         required=true,
     *         description="The ID of the game",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Return the information when game is over"),
     *     @OA\Response(response="201", description="Move saved and bot move was generated"),
     *     @OA\Response(response="400", description="Error when human is trying to move to a filled position"),
     *     @OA\Response(response="422", description="Error on the body request because is missing the move")
     * )
     */
    public function create(Request $request, string $gameId) : Response
    {
        $body = json_decode($request->getContent());

        $humanPlayer = new Player(Player::O_TEAM);
        $game = new Game(
            new Board(),
            $humanPlayer,
            new BotMove()
        );
        $game->setId($gameId);

        try {
            $gameStr = $this->storage->get($game);
            $game = $game->unserialize($gameStr);
            $game->setId($gameId);

            if (!isset($body->move) || !is_array($body->move)) {
                return new JsonResponse(
                    [
                        'error' => 'Body content must have the array field `move` with the position.'
                    ],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                );
            }

            $move = current($body->move);
            $position = new Position($move->row, $move->col);

            $game->move(
                $humanPlayer,
                $position
            );

            $this->storage->save($game);

            return new JsonResponse(
                [
                    'row' => $game->getNextBotMove()->getRow(),
                    'col' => $game->getNextBotMove()->getColumn(),
                ],
                JsonResponse::HTTP_CREATED
            );
        } catch (GameNotFoundException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage()
                ],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (GameOverException $e) {
            $response = [
                'status'    => Game::GAME_OVER_STATUS,
                'message'   => $e->getMessage(),
            ];

            if ($game->getNextBotMove() instanceof Position) {
                $response['row'] = $game->getNextBotMove()->getRow();
                $response['col'] = $game->getNextBotMove()->getColumn();
            }
            return new JsonResponse($response);
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
