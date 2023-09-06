<?php

namespace Np\Contents\Classes;


class EventsHandler
{
    public function subscribe($events)
    {
        $events->listen('pages.builder.registerControls', 'Np\Contents\Classes\CustomControls@registerControls');
    }
}
