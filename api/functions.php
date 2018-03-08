<?php
const JSON_TYPE = 'json';
const JSON_MIME_TYPE = 'application/json';
const HTML_TYPE = 'html';
const HTML_MIME_TYPE = 'text/html';
const TEXT_TYPE = 'text';

$middlewares = [];
$middlewaresIndex = 0;

function router($httpMethods, $route, $callback, $exit = true) {
    if (!in_array($_SERVER['REQUEST_METHOD'], (array) $httpMethods)) {
        return;
    }

    $path = parse_url($_SERVER['REQUEST_URI'])['path'];

    $scriptName = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'])));
    $len = strlen($scriptName);
    if ($len > 0 && $scriptName !== '/') {
        $path = substr($path, $len);
    }
    
    $matches = null;
    $regex = '/' . str_replace('/', '\/', $route) . '/';

    if (!preg_match_all($regex, $path, $matches)) {
        return;
    }
    
    if (empty($matches)) {
        $callback();
    } else {
        $params = array();
        foreach ($matches as $k => $v) {
            if (!is_numeric($k) && !isset($v[1])) {
                $params[$k] = $v[0];
            }
        }
        $callback($params);
    }

    if ($exit) {
        exit;
    }
}

function _next($res) {
    global $middlewares, $middlewaresIndex;

    if ($middlewares[$middlewaresIndex]) {
        $middlewares[$middlewaresIndex++](
            $_SERVER,
            $res,
            function () use (&$res, &$middlewaresIndex, &$middlewares) {
                if ($middlewaresIndex == count($middlewares)) {
                    if ($res['dataType'] == JSON_TYPE) {
                        echo json_encode($res['body']);
                    } else {
                        echo $res['body'];
                    }
                } else {
                    _next($res);
                }
            }
        );
    }
}

function middleware($func) {
    global $middlewares;
    array_push($middlewares, $func);
}

function entry($endpoint, $params) {
    $baseVerb = getenv('BASE_VERB');
    return "^/{$baseVerb}/{$endpoint}/{$params}$";
}

function json($arr) {
    header('Content-Type: ' . JSON_MIME_TYPE);
    _next(
        array(
            'body' => $arr,
            'dataType' => JSON_TYPE
        )
    );
}

function html($code) {
    header('Content-Type: ' . HTML_MIME_TYPE);
    _next(
        array(
            'body' => $code,
            'dataType' => HTML_TYPE
        )
    );
}

function error($msg) {
    header('HTTP/1.0 404 Not Found');
    _next(
        array(
            'body' => $msg,
            'dataType' => TEXT_TYPE
        )
    );
}
?>