<?php /* Smarty version 2.6.26, created on 2010-03-07 14:44:15
         compiled from header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'config_load', 'header.tpl', 1, false),)), $this); ?>
<?php echo smarty_function_config_load(array('file' => "./../../../lang/lang.txt",'section' => ($this->_tpl_vars['lang'])), $this);?>

<?php echo smarty_function_config_load(array('file' => "lang.txt",'section' => ($this->_tpl_vars['lang'])), $this);?>

<html>
<head> 
    <script language="javascript" src="js/www/kernel.class.js" type="text/javascript"></script>
    <script language="javascript" src="js/www/main.class.js" type="text/javascript"></script>
</head>
<body>
<!-- Header -->
<table width="100%">
<tr>
    <td>Kernel: <a href="?">Home</a>|  <a href="?app=testcase&object=frontend">Active frontend kernel test</a> | 
<a href="?app=testcase">Backend kernel test</a> |
    </td>
    <td align="right">
    [ <a href="../kernel/">Kernel</a> ] |
    <a href="../applications/">Applications</a> |
    <a href="../applications_server/">Applications server</a> |
    <a href="../builder/">Builder</a> |
    <a href="../maintainer/">Maintaniner</a>
    </td>
    </td>
</tr>
</table>
<hr />
<!-- End Header -->
<div id="contents">