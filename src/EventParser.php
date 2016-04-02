<?php
// @codingStandardsIgnoreFile

namespace Hubkat\Event;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EventParser
{
    protected $eventFactory;

    public function __construct(callable $eventFactory = null)
    {
        $this->eventFactory = $eventFactory ?: [$this, 'eventFactory'];
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (! $this->isEvent($request)) {
            return $this->invalidEvent($response);
        }

        $factory = $this->eventFactory;
        $event   = $factory($request);
        $request = $request->withParsedBody($event);

        return $next($request, $response);
    }

    protected function invalidEvent(Response $response)
    {
        $response = $response->withStatus(400);
        $response->getBody()->write('Invalid Event');
        return $response;
    }

    protected function isEvent(Request $request)
    {
        $method = $request->getMethod();

        $type = trim(
            array_shift(explode(';', $request->getHeaderLine('Content-Type')))
        );

        $agent = array_shift(explode('/', $request->getHeaderLine('user-agent')));

        return (
            $method === 'POST'
            && ! empty($type)
            && 'application/json' == strtolower($type)
            && 'GitHub-Hookshot' == $agent
            && $request->hasHeader('x-github-event')
            && $request->hasHeader('x-github-delivery')
            && $request->hasHeader('x-hub-signature')
        );
    }

    /**
     * Event Factory
     *
     * @param Request $request DESCRIPTION
     *
     * @return mixed
     *
     * @access protected
     */
    protected function eventFactory(Request $request)
    {
        return new Event(
            $request->getHeaderLine('x-github-delivery'),
            $request->getHeaderLine('x-github-event'),
            $request->getHeaderLine('x-hub-signature'),
            (string) $request->getBody()
        );
    }
}
