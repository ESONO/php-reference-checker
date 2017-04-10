<?php

class FiftyPercentOne
{
    public function foo()
    {
        return 'foo';
    }
}

class FiftyPercentTwo
{
    public function &foo()
    {
        $foo = 'foo';

        return $foo;
    }
}

class FiftyPercentCaller
{
    public function caller()
    {
        $cls = new FiftyPercentOne();
        $foo = '';
        $foo = &$cls->foo();
    }
}
