<?php

namespace Derasy\DerasyBundle;

use Derasy\DerasyBundle\SalamProviderInterface;

class SalamUmumList implements SalamProviderInterface {

    public function getSalamList(): array
    {
        // TODO: Implement getSalamList() method.
        return [
            "Selamat pagi",
            "Selamat siang",
            "Selamat sore",
            "Selamat malam"
        ];
    }

}