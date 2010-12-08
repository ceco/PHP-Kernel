{config_load file="lang.txt" section="$lang"}
<!-- Soap Client -->
<form name="{$prefix}" id="{$prefix}" action="?object={$object}" method="post">
Host: <input type="text" name="hostname" value="{$params.hostname}" />
Url: <input type="text" name="url" value="{$params.url}" />
app: <input type="text" name="appname" value="{$params.appname}" />
Object: <input type="text" name="objectname" value="{$params.objectname}" />
<input type="submit" name="submit" value="Send" />
</form>
<!-- End Soap Client -->