<?php

declare(strict_types=1);

namespace App\Config;

use Zend\ConfigAggregator\ArrayProvider;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;

$cacheConfig = [
    'config_cache_path' => 'data/config-cache.php',
];
$aggregator = new ConfigAggregator([
    new ArrayProvider($cacheConfig),
    new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),
    new PhpFileProvider('config/development.config.php'),
], $cacheConfig['config_cache_path']);
return $aggregator->getMergedConfig();