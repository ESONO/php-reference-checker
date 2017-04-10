<?php

class BlocksOne {

    public function foo() {
        return 'bar';
    }
}

class BlocksCaller {

    public function bar($flag)
    {
        $c = 'foo';

        if (true === $flag) {
            $b = new BlocksOne();
            $c =& $b->foo();
        }

        return $c;
    }
}