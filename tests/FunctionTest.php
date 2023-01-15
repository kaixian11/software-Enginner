<?php

class FunctionTest extends \PHPUnit\Framework\TestCase {
    public function testadd() {

        $function = new \function_test;
        $result = $function->add(20,5);

        $this->assertEquals(25, $result);

    }

}

?>