<?php

namespace ReenExe\Fixtures\Source;

class ParameterDefaultValue
{
    public function string($name = 'value')
    {

    }

    public function index($id = 1)
    {

    }

    public function matrix(array $array = [])
    {

    }

    public function const($const = \PHP_INT_MAX)
    {

    }
}
