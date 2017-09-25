<?php


namespace AppBundle\BracketCheck;


class BracketChecker
{
    public function check(string $str): bool
    {
        $bracketStack = [];

        $len = strlen($str);
        if ($len === 1) {
            return false;
        }

        for ($i = 0; $i < $len; $i++) {
            $char = substr($str, $i, 1);
            switch ($char) {
                case '(' :
                case '[' :
                case '{' :
                    $bracketStack[] = $char;
                    break;
                case ')' :
                case ']' :
                case '}' :
                    $inverted = $this->inverseBracket($char);
                    $stackSize = count($bracketStack);
                    if ($stackSize !== 0 && $bracketStack[$stackSize - 1] === $inverted) {
                        array_pop($bracketStack);
                    } else {
                        return false;
                    }
                    break;
            }
        }
        return empty($bracketStack);
    }

    private function inverseBracket($char)
    {
        switch ($char) {
            case ')':
                return '(';
            case ']':
                return '[';
            case '}':
                return '{';
        }
        throw new \RuntimeException('Use only: ), ], }');
    }
}