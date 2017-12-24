<?php

namespace Utils;

class Math
{
    public static function min(int $a, int $b):int
    {
        return ($a < $b) ? $a : $b;
    }
}