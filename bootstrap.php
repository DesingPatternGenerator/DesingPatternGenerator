<?php

require_once 'vendor/autoload.php';

use Symfony\Component\Filesystem\Filesystem;

$resultDirectory = __DIR__ . '/fixtures/Result';
$fs = new Filesystem();
$fs->remove($resultDirectory);
$fs->mkdir($resultDirectory, 0777);