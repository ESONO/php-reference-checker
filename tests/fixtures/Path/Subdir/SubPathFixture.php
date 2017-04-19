<?php


class SubPathFixture
{
    public function returnNoReference()
    {
        return 'x';
    }

    public function assignByReference()
    {
        $x = &$this->returnNoReference();
    }
}
