<?php
namespace App\Model;

class Randomizer
{
    public function randomizeIndex(array $data) : int
    {
        return array_rand($data, 1);
    }

    public function randomizeValue(array $data) : int
    {
        $value = array_rand($data, 1);
        return $data[$value];
    }
}
