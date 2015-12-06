<?php

namespace ReenExe\Fixtures\Source\Adapter;

interface PagerAdapter
{
    public function getCount(): int;

    public function getSlice(int $offset, int $limit): array;
}
