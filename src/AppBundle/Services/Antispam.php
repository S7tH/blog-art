<?php

namespace AppBundle\Services;

class Antispam
{
      /**
       * Vérifie si le texte est un spam ou non
       *
       * @param string $text
       * @return bool
       */
      public function isSpam($text)
      {
        return strlen($text) < 5;
      }
}
