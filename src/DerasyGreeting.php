<?php

namespace  Derasy\DerasyBundle;

/**
 * Created by PhpStorm.
 * User: Abah
 * Date: 03/10/2018
 * Time: 19.58
 */

class DerasyGreeting
{
    private $who;

    public function __construct(
            SalamProviderInterface $salamProvider,
            bool $isDebug = true,
            string $who = "Ebeh")
    {
        $this->who = $who;
        $this->salamProvider = $salamProvider;
    }

    public function getGreeting() {
        return "Haaaiii {$this->who} !! ". implode(", ", $this->getSalamList());
    }

    private function getSalamList(){
        return $this->salamProvider->getSalamList();
    }
}