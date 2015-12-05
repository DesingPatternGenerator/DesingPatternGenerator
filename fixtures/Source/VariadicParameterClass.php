<?php

namespace ReenExe\fixtures\Source;

class VariadicParameterClass
{
    public function get(... $names)
    {

    }

    public function getStringMap(string ... $names)
    {

    }

    public function getArrayMap(array ... $names)
    {

    }

    public function getChildrenMap(VariadicParameterClass ... $names)
    {

    }
}
