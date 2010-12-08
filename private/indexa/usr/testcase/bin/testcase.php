<?php

    require_once('/srv/www/htdocs/simpletest/unit_tester.php');
    require_once('/srv/www/htdocs/simpletest/reporter.php');
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