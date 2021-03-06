<?php
// @codingStandardsIgnoreFile

namespace Hubkat\Event;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Hubkat\EventInterface\EventInterface as Event;

class EventValidator
{
    protected $secret;

    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $event = $request->getParsedBody();

        if (! $event instanceof Event) {
            throw new \InvalidArgumentException(
                'Parsed body must be object of type Event'
            );
        }

        if (! $event->isValid($this->secret)) {
            return $this->invalidSecret($response);
        }

        return $next($request, $response);
    }

    protected function invalidSecret(Response $response)
    {
        $response = $response->withStatus(403);
        $response->getBody()->write('Invalid Secret');
        return $response;
    }
}
