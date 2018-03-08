<?php
middleware(function(&$req, &$res, $next) {
    if ($res['dataType'] == 'json') {
        $res['body']['middleware-1'] = 'ok';
    }
    $next();
});

middleware(function(&$req, &$res, $next) {
    if ($res['dataType'] == 'json') {
        $res['body']['middleware-2'] = 'ok';
    }
    $next();
});

middleware(function(&$req, &$res, $next) {
    header('X-TEST: ok');
    $next();
});
?>