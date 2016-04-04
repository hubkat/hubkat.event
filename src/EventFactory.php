<?php
// @codingStandardsIgnoreFile

namespace Hubkat\Event;

use Psr\Http\Message\ServerRequestInterface as Request;

class EventFactory
{
    public function newEvent($delivery, $event, $sig, $body)
    {
        return new Event($delivery, $event, $sig, $body);
    }

    protected function newEventFromRequest(Request $request)
    {
        return $this->newEvent(
            $request->getHeaderLine('x-github-delivery'),
            $request->getHeaderLine('x-github-event'),
            $request->getHeaderLine('x-hub-signature'),
            (string) $request->getBody()
        );
    }
}
