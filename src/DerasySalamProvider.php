<?php

namespace  Derasy\DerasyBundle;

/**
 * Created by PhpStorm.
 * User: Abah
 * Date: 05/10/2018
 * Time: 16.50
 */
class DerasySalamProvider implements SalamProviderInterface {

    public function getSalamList(): array {

        return [
            'Assalamu \'alaikum',
            'Selamat pagi',
            'Selamat sejahtera untuk kita semua'
        ];

    }
}