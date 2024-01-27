<?php

namespace App\Service;

class ProfanityFilter
{
    private array $badWords;

    public function __construct(array $badWordsList)
    {
        $this->badWords = $badWordsList;
    }

    public function filter(string $text): string
    {
        foreach ($this->badWords as $badWord) {
            if (mb_stripos($text, $badWord) !== false) {
                $text = str_ireplace($badWord, str_repeat('*', mb_strlen($badWord)), $text);
            }
        }

        return $text;
    }
}