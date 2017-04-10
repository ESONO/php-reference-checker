<?php

class TwentyFivePercentOne
{
    public function foo()
    {
        return 'foo';
    }
}

class TwentyFivePercentTwo
{
    public function &foo()
    {
        $foo = 'foo';

        return $foo;
    }
}

class TwentyFivePercentThree
{
    public function foo()
    {
        return 'foo';
    }
}

class TwentyFivePercentFour
{
    public function foo()
    {
        return 'foo';
    }
}

class TwentyFivePercentCaller
{
    public function caller()
    {
        $cls = new FiftyPercentOne();
        $foo = '';
        $foo = &$cls->foo();
    }
}
