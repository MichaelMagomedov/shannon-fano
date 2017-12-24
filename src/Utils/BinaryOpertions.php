<?php

namespace Utils;

/**
 * Class BinaryOpertions
 * @package Utils
 */
class BinaryOpertions
{
    /**
     * @param string $file
     * @return string
     */
    public static function readBitsDataFromFile(string $file):string
    {
        $buffer = file_get_contents($file);
        $length = filesize($file);

        if (!$buffer || !$length) {
            die("Reading error\n");
        }

        $_buffer = '';
        for ($i = 0; $i < $length; $i++) {
            $_buffer .= sprintf("%08b", ord($buffer[$i]));
        }

        return $_buffer;
    }

    /**
     * @param $fp
     * @param $string
     * @return int
     */
    public static function fwriteByteStream($fp, $string)
    {
        @unlink($fp);
        $fpStream = fopen($fp, 'a+');
        for ($written = 0; $written < strlen($string); $written += $fwrite) {
            $fwrite = fwrite($fpStream, substr($string, $written));
            if ($fwrite === false) {
                return $written;
            }
        }

        chmod($fp, 777);

        return $written;
    }

    /**
     * @param array $bitset
     * @return string
     */
    public static function convertBitSetToByteStr(array $bitset):string
    {
        $byteSet = str_split(implode("", $bitset), 8);
        $chars = array_map('bindec', $byteSet);
        array_unshift($chars, 'C*');
        $bytStr = call_user_func_array('pack', $chars);
        return $bytStr;
    }

}

