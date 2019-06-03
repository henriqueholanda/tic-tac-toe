<?php
namespace App\Controller;

use App\Domain\Move\BotMove;
use App\Domain\Storage;
use App\Domain\Board;
use App\Domain\Player;
use App\Domain\Model\Game;
use Predis\PredisException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Tic Tac Toe",
 *     version="0.1",
 *     description="This is a functional implementation of Tic Tac Toe game.",
 *     @OA\Contact(
 *         email="contato@henriqueholanda.com.br"
 *     ),
 * )
 */
class GameController
{
    /** @var Storage */
    private $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @OA\Post(
     *     path="/v1/games",
     *     description="This resource will start a new game and generate an ID to the game.",
     *     @OA\Response(response="200", description="Game started and ID was generated"),
     *     @OA\Response(response="422", description="Validation error because bot player is missing"),
     *     @OA\Response(response="500", description="Internal error")
     * )
     */
    public function create(Request $request) : Response
    {
        $game = new Game(
            new Board(),
            new Player(Player::O_TEAM),
            new BotMove()
        );

        try {
            $gameId = $game->start();
            $this->storage->save($game);
            return new JsonResponse(
                [
                    'gameId' => $gameId
                ],
                JsonResponse::HTTP_OK
            );
        } catch (PredisException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage()
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
