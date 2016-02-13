<?php

require_once 'vendor/autoload.php';

use Symfony\Component\Filesystem\Filesystem;

define('FIXTURE_RESULT_PATH', __DIR__ . '/fixtures/Result');
$fs = new Filesystem();
$fs->remove(FIXTURE_RESULT_PATH);
foreach (['Decorator', 'Adapter', 'Composite'] as $type) {
    $fs->mkdir(FIXTURE_RESULT_PATH . '/' . $type, 0777);
}