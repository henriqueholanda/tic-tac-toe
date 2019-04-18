<?php
namespace App\Entity;

use InvalidArgumentException;

class Position
{
    /** @var int */
    private $row;

    /** @var int */
    private $column;

    /**
     * @param int $row
     * @param int $column
     */
    public function __construct(int $row, int $column)
    {
        $this->setRow($row);
        $this->setColumn($column);
    }

    /**
     * @param int $row
     *
     * @throws InvalidArgumentException
     */
    private function setRow(int $row)
    {
        if (!$this->isValid($row)) {
            throw new InvalidArgumentException(
                sprintf('Invalid row! Expected values are [ 0, 1 or 2]. Got: %d', $row)
            );
        }

        $this->row = $row;
    }

    /**
     * @param int $column
     *
     * @throws InvalidArgumentException
     */
    private function setColumn(int $column) : void
    {
        if (!$this->isValid($column)) {
            throw new InvalidArgumentException(
                sprintf('Invalid column! Expected values are [ 0, 1 or 2]. Got: %d', $column)
            );
        }

        $this->column = $column;
    }

    /**
     * @param int $position
     *
     * @return boolean
     */
    private function isValid(int $position) : bool
    {
        return ($position > -1 && $position < 3);
    }

    /**
     * @return int
     */
    public function getRow() : int
    {
        return $this->row;
    }

    /**
     * @return int
     */
    public function getColumn() : int
    {
        return $this->column;
    }
}
