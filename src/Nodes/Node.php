<?php

namespace Nodes;

class Node
{
    /** @var  string $symbol */
    public $symbol;
    /** @var  int $probability */
    public $probability;

    /**
     * Node constructor.
     * @param string $symbol
     * @param int $probability
     */
    public function __construct($symbol, $probability)
    {
        $this->symbol = $symbol;
        $this->probability = $probability;
    }

}

