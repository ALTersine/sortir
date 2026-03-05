<?php

namespace App\Exception;

use Exception;

class SortieIllegalUpdate extends Exception
{
    public function __construct(
        string $message = 'Cette sortie ne peut pas être mise à jour',
        int $code = 403,
    ){
        parent::__construct($message, $code);
    }

}
