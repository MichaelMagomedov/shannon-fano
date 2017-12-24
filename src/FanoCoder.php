<?php

use Nodes\HuffmanNode;
use Utils\BinaryOpertions;
use Utils\Entr;
use Nodes\Node;
use Utils\Math;

class FanoCoder
{
    /** @var  string $path */
    protected $path;

    /** @var array $codes */
    protected $codes;

    /**
     * HuffmanCoder constructor.
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @param int $charCount
     * @return array
     * @throws Exception
     */
    public function encode(int $charCount):string
    {
        $nodes = $this->getNodes($charCount); // Получаем ноды
        $codes = [];  // Коды символов

        $queue = [array_values($nodes)];

        while (!empty($queue)) {

            $item = array_pop($queue); // Берем 1 элемент удаляя его из очереди

            if (sizeof($item) == 1) {
                continue;
            }


            $bestDiff = PHP_INT_MAX;

            for ($i = 1; $i < sizeof($item); $i++) {

                $firstPartCount = $this->sum($this->subArray(0, $i, $item));
                $secondPartCount = $this->sum($this->subArray($i, sizeof($item), $item));

                $diff = abs($firstPartCount - $secondPartCount);

                if ($diff < $bestDiff) {
                    $bestDiff = $diff;
                } else {
                    break;
                }
            }

            $i -= 1;

            $left = $this->subArray(0, $i, $item);
            $right = $this->subArray($i, sizeof($item), $item);

            foreach ($left as $node) {

                if (isset($codes[$node->symbol])) {
                    $codes[$node->symbol] .= '1';
                } else {
                    $codes[$node->symbol] = "1";
                }

            }

            foreach ($right as $node) {

                if (isset($codes[$node->symbol])) {
                    $codes[$node->symbol] .= '0';
                } else {
                    $codes[$node->symbol] .= '0';
                }

            }


            array_push($queue, array_values($left));
            array_push($queue, array_values($right));
        }

        $this->codes = $codes;

        return $this->codeFile($charCount, $codes);
    }

    /**
     * @param int $charCount
     * @param array $codes
     * @return bool
     */
    protected function codeFile(int $charCount, array $codes):bool
    {
        $text = file_get_contents($this->path);
        $encode = [];
        $syll = null;

        for ($start = 0; $start <= strlen($text) - $charCount; $start += $charCount) {
            $syll = substr($text, $start + $charCount - 1, $charCount);    // Считываем указаное кол-во символов (1 или 2)
            array_push($encode, $codes[$syll]); // Заносим в список код 1 или 2 символов
        }

        $bytes = BinaryOpertions::convertBitSetToByteStr($encode);
        BinaryOpertions::fwriteByteStream($this->path . ".huf", $bytes);

        return true;
    }


    public function decodeFile(string $path, array $codes = null):string
    {
        if (empty($codes) && empty($this->codes)) {
            throw new \Exception("codes table not found");
        } elseif (empty($codes) && !empty($this->codes)) {
            $codes = $this->codes;
        }

        $bits = BinaryOpertions::readBitsDataFromFile($path);
        $buff = "";
        $resultStr = "";

        for ($i = 0; $i < strlen($bits); $i++) {
            $buff .= $bits[$i];
            foreach ($codes as $char => $code) {
                if ($code === $buff) {
                    $buff = "";
                    $resultStr .= $char;
                }
            }
        }
        return $resultStr;
    }


    /**
     * @param int $charCount
     * @return array
     * @throws Exception
     */
    protected function getNodes(int $charCount):array
    {
        $nodes = [];
        $entrEngine = new Entr($this->path);

        switch ($charCount) {
            case 1:

                $entrMap = $entrEngine->getCharProbabilityMap();
                foreach ($entrMap as $char => $prob)
                    array_push($nodes, new Node($char, $prob));

                break;

            case 2:

                $entrMap = $entrEngine->getSLogProbabilityMap();
                foreach ($entrMap as $char => $prob)
                    array_push($nodes, new Node($char, $prob));


                break;

            default:
                throw new \Exception("неизвестная форма кодирования");
                break;
        }

        return $nodes;
    }

    /**
     * @param string $text
     * @param int $size
     * @return array
     */
    protected function split(string $text, int $size):array
    {
        $buf = [];
        for ($i = 0; $i < strlen($text); $i += $size) {
            array_push($buf, substr($text, $i, Math::min($size, strlen($text))));
        }

        return $buf;
    }

    /**
     * @param array $nodes
     * @return float
     */
    protected function sum(array $nodes):float
    {
        $sum = 0;
        foreach ($nodes as $node) {
            $sum += $node->probability;
        }
        return $sum;
    }

    protected function subArray(int $first, int $last, array $array):array
    {
        $result = [];

        if ($last == $first) {
            return [$array[$first]];
        }

        for ($i = $first; $i < (sizeof($array)) && $i < ($last); $i++) {
            $result[$i] = $array[$i];
        }

        return $result;
    }

}