<?php

    require_once('unit_tester.php');
    require_once('reporter.php');
    require_once('../../etc/config.php');
    require_once('../../lib/userspace.class.php');

    class TestOfKernel extends userspace {

        function testKernel() {
            $kernel = new userpace();
            $kernel->detect_environemnt();
            $this->assertFalse($kernel->environment);
        }
    }

    $test = &new TestOfKernel();
    $test->run(new HtmlReporter());

?>