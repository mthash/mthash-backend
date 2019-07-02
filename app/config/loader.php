<?php
/**
 * @var $config \Phalcon\Config
 */


$loader = new \Phalcon\Loader();
$namespaces = [];

// Models
foreach (recursiveDirectoryLoader($config->application->modelsDir) as $path)
{
    $ns = str_replace ($config->application->modelsDir, '', $path);
    $ns = str_replace ('/', '\\', $ns);

    $ns = explode ('\\', $ns);
    foreach ($ns as &$n)
    {
        $n = ucfirst (strtolower ($n));
    }

    $nsArray    = $ns;

    $ns = rtrim (implode ('\\', $ns), '\\');
    $namespaces['MtHash\Model\\' . $ns] = $path;
    $namespaces['MtHash\Controller\\' . $nsArray[0]]      = APP_PATH . '/controllers/' . strtolower ($nsArray[0]) . '/';
}

$namespaces['MtHash\Model']         = APP_PATH . '/models/';
$namespaces['MtHash\Controller']    = APP_PATH . '/controllers/';

$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH . '/traits/',
    ]
);

$loader->registerFiles(
    [
        APP_PATH . '/config/exceptions.php',
        APP_PATH . '/config/functions.php',
    ]
);

$loader->registerNamespaces($namespaces);
$loader->register();


/**
 * @param string $rootDirectory
 * @return array
 *
 */
function recursiveDirectoryLoader (string $rootDirectory) : array
{
    $return     = [];
    $iterator   = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootDirectory)
    );

    foreach ($iterator as $name => $item)
    {
        if (is_file ($item)) continue;

        $return[] = rtrim ($item->getPathName(), '.');
    }

    $return = array_unique ($return);
    rsort ($return);

    unset ($return[count($return)-1]);
    return $return;
}