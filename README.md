# hubkat.event
Parse and authenticate github webhooks
```php
<?php

use Hubkat\Event\EventParser;
use Hubkat\Event\EventValidator;

$queue->middleware(new EventParser);
$queue->middleware(new EventValidator($myRepoSecret));

$queue->run($request, $response);

// Parsed will set parsed body to an Event object;
$event = $request->getParsedBody();
```
