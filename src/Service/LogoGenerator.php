<?php

namespace App\Service;

class LogoGenerator
{
    public function selectOneLogoForTheApp() : string {
        return 'logo'.rand(1,6).'.png';
    }

}
