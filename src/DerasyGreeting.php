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
    private $salamProviders;
    private $salamList;

    public function __construct(
            array $salamProviders,
            // SalamProviderInterface $salamProvider,
            bool $isDebug = true,
            string $who = "Ebeh")
    {
        $this->who = $who;
        // $this->salamProvider = $salamProvider;
        $this->salamProviders = $salamProviders;

    }

    public function getGreeting() {
        return "Haaaiii {$this->who} !! ". implode(", ", $this->getSalamList());
    }

    public function getSalamList(){

        if (null === $this->salamList) {

            $salams = [];
            foreach ($this->salamProviders as $salamProvider) {
                $salams = array_merge($salams, $salamProvider->getSalamList());
            }

            if (count($salams) <= 1) {
                throw new \Exception('Jumlah salam harus lebih dari 1 kata');
            }

            $this->salamList = $salams;
        }

        // return $this->salamProvider->getSalamList();
        return $this->salamList;
    }
    
    
}