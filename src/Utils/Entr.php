<?php

namespace Utils;

class Entr
{

    /** @var string $path */
    protected $path;
    /** @var int $fileSize */
    protected $fileSize;

    /**
     * Entr constructor.
     * @param $filename
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->fileSize = filesize($path);
    }

    /**
     * @return array
     */
    public function getCharProbabilityMap():array
    {
        $descriptor = fopen($this->path, 'r');
        $charProbability = [];
        $textLength = 1;
        while (($char = fgetc($descriptor)) !== false) {

            if ($char !== "\n") {
                if (isset($charProbability[$char])) {
                    $charProbability[$char]++;
                } else {
                    $charProbability[$char] = 1;
                }
                $textLength++;
            }

        }

        fclose($descriptor);

        foreach ($charProbability as $key => $value) {
            $charProbability[$key] = $value / $textLength;
        }

        return $charProbability;
    }

    /**
     * @return array
     */
    public function getSLogProbabilityMap():array
    {
        $descriptor = fopen($this->path, 'r');
        $slogProbability = [];
        $textLength = 1;
        $prevChar = fgetc($descriptor);

        while (($char = fgetc($descriptor)) !== false) {

            if ($char !== "\n") {
                if (isset($slogProbability[$char . $prevChar])) {
                    $slogProbability[$prevChar . $char]++;
                } else {
                    $slogProbability[$prevChar . $char] = 1;
                }
                $prevChar = $char;
                $textLength++;
            }

        }

        fclose($descriptor);

        //энтропии в слоге
        foreach ($slogProbability as $baseKey => $baseValue) {
            $slogProbability[$baseKey] = $baseValue / $textLength;
        }

        return $slogProbability;


    }


}