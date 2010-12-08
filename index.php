<?
    /**
    * @name index.php
    * @date 2009/12/13
    * @author Tsvetan Filev <tsvetan.filev@gmail.com>
    */

    // Include configuration
    require("private/index/etc/config.php");
    // Include kernel
    require("kernel.class.php");

    ///////////// MAIN //////////////////////

    // create main object
    $kernel = new kernel();

    $kernel->statistics_save_time();

    // run main project
    $kernel->main();

    echo $kernel->statistics_calc_time();
?>
