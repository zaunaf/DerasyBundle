<?php

namespace Derasy\DerasyBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Created by PhpStorm.
 * User: Abah
 * Date: 08/10/2018
 * Time: 11.06
 */
class FilterApiResponseEvent extends Event
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }
}