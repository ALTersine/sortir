<?php

namespace App\Mapper;

use App\DTO\PlaceGoogleDTO;

class PlaceGoogleMapper
{
    public function map(array $place): PlaceGoogleDTO {
        return new PlaceGoogleDTO(
            $place["displayName"]["text"],
            $place["formattedAddress"],
            $place["location"]["latitude"],
            $place["location"]["longitude"],
        );
    }

}
