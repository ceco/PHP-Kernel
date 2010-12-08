{config_load file="./../../../lang/lang.txt" section="$lang"}
{config_load file="lang.txt" section="en"}
<html>
<head> 
    <script language="javascript" src="js/www/application.class.js" type="text/javascript"></script>
    <script language="javascript" src="js/www/main.class.js" type="text/javascript"></script>
</head>
<body>
<!-- Header -->
Admin mode:
<a href="?">Home</a>|
<a href="?app=test">Test</a>|
<a href="?app=test&object=ajax">Ajax</a>|
<a href="?app=contacts">Contacts</a>|
<a href="?object=soap_client">Soap client</a>|
<a href="?app=acl">Permissions</a>|
<a href="?app=custom">Visual construction</a>
<hr />
<!-- End Header -->
<div id="contents">