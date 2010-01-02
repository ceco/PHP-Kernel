{config_load file="lang.txt" section="$lang"}
<!-- Index -->

<div id="indicator" style="display:">Loading...</div><br />

GET: <a href="javascript:;" onClick="main.load_sync_get('?app={$params.app}&object=frontend_test','contents_test');">load_sync_get=function(url, element_name, code)</a><br />

GET:<a href="javascript:;" onClick="main.load_async_get('?app={$params.app}&object=frontend_test','contents_test');">load_async_get=function(url, element_name, code)</a><br />

<form method="post" id="test_form" action="?app={$params.app}&object=frontend_test">
A: <input type="text" name="a" id="a" value="1" />
B: <input type="text" name="b" id="b" value="2" />
C: <input type="text" name="c" id="c" value="3" />
</form>

POST: <a href="javascript:;" onClick="main.load_sync_post('test_form','contents_test');">load_sync_post=function(url, element_name, code)</a><br />

POST: <a href="javascript:;" onClick="main.load_async_post('test_form','contents_test');">load_async_post=function(url, element_name, code)</a><br />

POST: <a href="javascript:;" onClick="main.load_async_post_prefix('?app={$params.app}&object=frontend_test','contents_test','b');">load_async_post_post=function(url, element_name, prefix, code)</a><br />

GET RESULT: <a href="javascript:;" onClick="alert( main.get_result('?app={$params.app}&object=frontend_test') );">get_result=function(url, code )</a><br />

A: <a href="?app={$params.app}&object=frontend_test" onClick="return main.load_async_a(this,'contents_test');">load_async_a=function(obj, element_name, code )</a><br />

A: <a href="?app={$params.app}&object=frontend_test" onClick="return main.load_sync_a(this,'contents_test');">load_sync_a=function(obj, element_name, code )</a><br />
<br />
<a href="javascript:;" onClick="main.loadobjs('css/www/test.css','js/www/test.js');">loadobjs = function('css/www/test.css','js/www/test.js')</a><br />
<a href="javascript:;" onClick="main.unloadobjs('css/www/test.css','js/www/test.js');">unloadobjs = function()</a><br /><br />

<a href="javascript:;" onClick="main.load_async_get('?app={$params.app}&object=frontend_js','contents_test','test()');">JS Autoload 1</a>
<a href="javascript:;" onClick="main.load_async_get('?app={$params.app}&object=frontend_js','contents_test','test1()');">JS Autoload 2</a>
<br />
<br />
<a href="javascript:;" onClick="alert( ' X: ' + main.getMouseX(event) + ' Y: ' + main.getMouseY(event) ); " onKeyPress="alert( 'Key:' + main.getKeyCode(event) + ' Ctrl: ' + main.getKeyCtrl(event) + ' Alt: ' + main.getKeyAlt(event) );">Events</a></br>


<div id="contents_test">Contents</div>


<!-- End Index -->