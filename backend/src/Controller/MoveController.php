<?php
namespace App\Controller;

use App\Exception\InvalidMoveException;
use App\Model\Move\MoveInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Annotations as OA;

class MoveController
{
    /** @var MoveInterface */
    private $move;

    public function __construct(MoveInterface $move)
    {
        $this->move = $move;
    }

    /**
     * @OA\Post(
     *     path="/v1/bot/{team}/move",
     *     @OA\Parameter(name="team",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="New bot movement"),
     *     @OA\Response(response="400", description="Error when bot try to move")
     * )
     */
    public function move(Request $request, string $team) : Response
    {
        $body = json_decode($request->getContent());

        try {
            $move = $this->move->makeMove(
                $body->board,
                $team
            );
        } catch (InvalidMoveException $e) {
            return new JsonResponse(
                [
                    'error' => $e->getMessage()
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            [
                'row' => $move[0],
                'column' => $move[1]
            ]
        );
    }
}
