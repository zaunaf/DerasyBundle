<?php

namespace Derasy\DerasyBundle\Event;

/**
 * Created by PhpStorm.
 * User: Abah
 * Date: 08/10/2018
 * Time: 11.32
 */
final class DerasyEvents
{
    /**
     * Called directly before the Lorem Ipsum API data is returned.
     *
     * Listeners have the opportunity to change that data.
     *
     * @Event("Derasy\DerasyBundle\Event\FilterApiResponseEvent")
     */
    const FILTER_API = 'derasy.filter_api';

}