{config_load file="lang.txt" section="$lang"}
<!-- Header -->
<form name="{$app}_{$object}" id="{$app}_{$object}" action="?" method="post">
{#index_test#} <input type="text" name="{$app}_{$object}_name" />
<input type="submit" name="{$app}_{$object}_submit" />
</form>
<!-- End Header -->