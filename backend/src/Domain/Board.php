<?php
namespace App\Domain;

use App\Exception\InvalidMoveException;
use Serializable;

class Board implements Serializable
{
    /** @var array */
    private $moves;

    /** @var array */
    private $state;

    public function __construct(array $moves = [])
    {
        $this->moves = $moves;
        $this->state = [
            ['', '', ''],
            ['', '', ''],
            ['', '', ''],
        ];
    }

    /**
     * @param Player $player
     * @param Position $position
     *
     * @throws InvalidMoveException
     * @return void
     */
    public function markSpace(Player $player, Position $position) : void
    {
        if (!$this->isFreeSpace($position)) {
            throw new InvalidMoveException(
                sprintf(
                    'The position [%d,%d] is already filled',
                    $position->getRow(),
                    $position->getColumn()
                )
            );
        }
        $this->moves[] = new Move($player, $position);
    }

    /**
     * @return array
     */
    public function getState() : array
    {
        array_map(function (Move $move) {
            $position = $move->getPosition();
            $this->state[$position->getRow()][$position->getColumn()] = $move->getPlayer()->getTeam();
        }, $this->moves);

        return $this->state;
    }

    /**
     * @param Position $position
     *
     * @return bool
     */
    private function isFreeSpace(Position $position) : bool
    {
        $spaces = array_filter($this->moves, function (Move $move) use ($position) {
            return (
                $move->getPosition()->getRow() === $position->getRow()
                && $move->getPosition()->getColumn() === $position->getColumn()
            );
        });

        return empty($spaces);
    }

    /**
     * @return string
     */
    public function serialize() : string
    {
        $data = array_map(function (Move $move) {
            return $move->serialize();
        }, $this->moves);

        return serialize($data);
    }

    /**
     * @param string $serialized
     *
     * @return self
     */
    public function unserialize($serialized) : self
    {
        $data = unserialize($serialized);
        $moves = array_map(function ($move) {
            return (
                (new Move(new Player(''), new Position(0, 0)))->unserialize($move)
            );
        }, $data);

        return new self($moves);
    }
}
