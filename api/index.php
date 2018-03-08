<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/middlewares.php';

$baseVerb = getenv('BASE_VERB');

// Default index page
router('GET', '^/' . $baseVerb . '/$', function() {
    html('<h3>Hoy!</h3>');
});

// A dummy endpoint
router('GET', '^/' . $baseVerb . '/dummy/(?<query>(.*))$', function($params) {
    $output = [];
    $output['verb'] = 'dummy';
    $output['query'] = $params['query'];

    json($output);
});

// Route with pagination
router('GET', '^/' . $baseVerb . '/dummies/(?<start>\d+)-(?<end>\d+)$', function($params) {
    $output = [];
    $output['verb'] = 'dummies';
    $output['start'] = $params['start'];
    $output['end'] = $params['end'];

    json($output);
});

// Route constructed with the helper
router('GET', entry('hi', '(?<name>(.*))'), function($params) {
    html("hello {$params['name']}");
});

// In the worst case...
error('404 Not Found');

?>