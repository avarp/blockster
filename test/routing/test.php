<?php
require('../../core/routing/RoutingCompiler.php');
require('../../core/routing/Router.php');
require('../../core/functions.php');

function chooseAny($arr)
{
    return $arr[rand(0, count($arr)-1)];
}

function addUniquePart($path)
{
    static $uid = 0;
    $uid += rand(1,10);

    $path['uri'] = '/'.$uid.$path['uri'];
    $path['positiveTest'] = '/'.$uid.$path['positiveTest'];
    $path['negativeTest'] = '/'.$uid.$path['negativeTest'];
    return $path;
}

function getRandomRoute()
{
    

    $protocol_variants = [
        [
            'uri' => '//',
            'positiveTest' => 'https://',
            'negativeTest' => 'ftp://'
        ],
        [
            'uri' => 'https://',
            'positiveTest' => 'https://',
            'negativeTest' => 'http://'
        ],
        [
            'uri' => 'http://',
            'positiveTest' => 'http://',
            'negativeTest' => 'https://'
        ]
    ];

    $host_variants = [
        [
            'uri' => 'smth-fixed.org',
            'params' => [],
            'positiveTest' => 'smth-fixed.org',
            'negativeTest' => 'smth-other.org'
        ],
        [
            'uri' => '{h1}.wikipedia.org',
            'params' => [
                'h1' => ['type' => 'string', 'length' => '2-5']
            ],
            'positiveTest' => 'p2p.wikipedia.org',
            'negativeTest' => 'blabla.wikipedia.org'
        ],
        [
            'uri' => '{h1}.site-{h2}.org',
            'params' => [
                'h1' => ['type' => 'string', 'length' => '2-5', 'optional' => 'true', 'default' => 'en'],
                'h2' => ['type' => 'variants', 'variants' => ['1', '2', '3']]
            ],
            'positiveTest' => 'site-2.org',
            'negativeTest' => 'ru.site-4.org',
        ],
        [
            'uri' => '{h1}.example-{h2}.{h3}',
            'params' => [
                'h1' => ['type' => 'string', 'length' => '2-5', 'optional' => 'true', 'default' => 'en'],
                'h2' => ['type' => 'numeric', 'length' => '1'],
                'h3' => ['type' => 'string', 'variants' => ['ru', 'com', 'org']]
            ],
            'positiveTest' => 'example-2.org',
            'negativeTest' => 'ru.example-10.ru',
        ]
    ];

    $paths_variants = [
        [
            'uri' => '/fixed-path',
            'params' => [],
            'positiveTest' => '/fixed-path',
            'negativeTest' => '/other-path'
        ],
        [
            'uri' => '/admin/menu/{p1}/{p2}/{p3}',
            'params' => [
                'p1' => ['type' => 'string', 'length' => '8'],
                'p2' => ['type' => 'numeric'],
                'p3' => ['type' => 'variants', 'variants' => ['en', 'ru', 'fi']]
            ],
            'positiveTest' => '/admin/menu/menuname/17/en',
            'negativeTest' => '/admin/menu/edit/100/fi',
        ],
        [
            'uri' => '/admin/articles/{p1}/{p2}',
            'params' => [
                'p1' => ['type' => 'string', 'optional' => true, 'default' => 'default'],
                'p2' => ['type' => 'numeric'],
            ],
            'positiveTest' => '/admin/articles/12',
            'negativeTest' => '/admin/articles/new',
        ]
    ];


    $uri = '';
    $params = [];
    $positiveTest = '';
    $negativeTest = '';

    if (rand(0, 10) > 3) {
        $protocol = chooseAny($protocol_variants);
        $host = chooseAny($host_variants);
        $path = addUniquePart(chooseAny($paths_variants));

        $uri = $protocol['uri'].$host['uri'].$path['uri'];
        $params = array_merge($host['params'], $path['params']);
        $positiveTest = $protocol['positiveTest'].$host['positiveTest'].$path['positiveTest'];

        switch (rand(0, 2)) {
            case 0:
            $negativeTest = $protocol['negativeTest'].$host['positiveTest'].$path['positiveTest'];
            break;

            case 1:
            $negativeTest = $protocol['positiveTest'].$host['negativeTest'].$path['positiveTest'];
            break;

            case 2:
            $negativeTest = $protocol['positiveTest'].$host['positiveTest'].$path['negativeTest'];
            break;
        }
    } else {
        $path = addUniquePart(chooseAny($paths_variants));
        $uri = $path['uri'];
        $params = $path['params'];
        $positiveTest = 'http://example.com'.$path['positiveTest'];
        $negativeTest = 'http://example.com'.$path['negativeTest'];
    }

    $selector = compact('uri', 'params');
    $content = compact('positiveTest', 'negativeTest');

    foreach ($params as $name => $p) {
        $content[$name] = '{'.$name.'}';
    }

    return compact('selector', 'content');
}

$map = [];
for ($i=0; $i<1000; $i++) {
    $map["route-$i"] = getRandomRoute();
}

file_put_contents('map.json', jsonFmt(json_encode($map)));

$compiler = new \core\routing\RoutingCompiler;
$routes = $compiler->compile('map.json');

file_put_contents('map-compiled.json', jsonFmt(json_encode($routes)));

$router = new \core\routing\Router($routes);

$t0 = microtime(true);
$fails = 0;
ob_start();

foreach ($routes as $name => $route) {

    //positive test
    $httpQuery = array(
        'uri' => $route['content']['positiveTest'],
        'method' => 'GET',
        'status' => 200
    );
    $result = $router->findRoute($httpQuery);
    echo "---- Route $name ----\n";
    if (empty($result) || $result['name'] != $name) {
        echo "Positive test FAILURE.\n";
        echo "\tRequest: $httpQuery[uri]\n";
        echo "\tFound: ".(empty($result) ? 'nothing' : $result['name'])."\n";
        $fails++;
    } else {
        echo "Positive test OK.\n";
    }

    //negative test
    $httpQuery = array(
        'uri' => $route['content']['negativeTest'],
        'method' => 'GET',
        'status' => 200
    );
    $result = $router->findRoute($httpQuery);
    if (!empty($result) && $result['name'] == $name) {
        echo "Negative test FAILURE.\n";
        $fails++;
    } else {
        echo "Negative test OK.\n";
        echo "\tRequest: $httpQuery[uri]\n";
        echo "\tFound: ".(empty($result) ? 'nothing' : $result['name'])."\n";
    }
    echo "\n";

}
echo "\nTEST FINISHED in ".(1000*(microtime(true) - $t0))." ms. Fails $fails time(s).";

file_put_contents('test.log', ob_get_clean());
echo $fails == 0 ? "TRUE\n" : "FALSE\n" ;