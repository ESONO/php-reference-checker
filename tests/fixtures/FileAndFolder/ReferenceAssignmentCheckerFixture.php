<?php


class ReferenceAssignmentCheckerFixture
{
    public function returnNoReference()
    {
        return 'x';
    }

    public function assignByReference()
    {
        $x =& $this->returnNoReference();
    }
}