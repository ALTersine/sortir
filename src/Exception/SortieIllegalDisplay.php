<?php

namespace App\Exception;

use Exception;

class SortieIllegalDisplay extends Exception
{
    public function __construct(
        string $message = 'Cette sortie ne peut plus être consultée',
        int $code = 403,
    ){
        parent::__construct($message, $code);
    }

}
