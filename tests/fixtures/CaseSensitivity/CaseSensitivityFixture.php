<?php


class CaseSensitivityFixture
{
    public function returnnoreference()
    {
        return 'x';
    }

    public function assignByReference()
    {
        $x = &$this->returnNoReference();
    }
}
