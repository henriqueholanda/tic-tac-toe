<?php
namespace App\Controller;

use App\Exception\InvalidMoveException;
use App\Model\Move\MoveInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MoveController
{
    /** @var MoveInterface */
    private $move;

    public function __construct(MoveInterface $move)
    {
        $this->move = $move;
    }

    /**
     * @param Request $request
     * @param string $team
     *
     * @return Response
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
