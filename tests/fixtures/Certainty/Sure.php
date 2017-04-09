<?php

class SureOne {
    public function &foo()
    {
        return 'foo';
    }
}

class SureTwo {
    public function &foo()
    {
        $foo = 'foo';
        return $foo;
    }
}

class SureCaller {
    public function caller()
    {
        $cls = new SureOne();
        $foo = '';
        $foo =& $cls->foo();
    }
}