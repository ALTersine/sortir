<?php

namespace App\DTO;

class PlaceGoogleDTO
{
    public function __construct(
        public string $name,
        public string $address,
        public string $latitude,
        public string $longitude,
    )
    {
    }

}
