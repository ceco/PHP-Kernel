/**
* @name kernel
* @author Tsvetan Filev <tsvetan.filev@gmail.com>
* @date 2008/03/10
* @version 2.0
* Provides ajax, IO, graphical and general functionality
*/

/**
* kernel constructor
* @name kernel
*
*/
function kernel(){
    /**
    * General variables
    *
    */
    this.xmlhttp = false;
    this.parent_div = null;
    this.on_load_code = new Array();
    this.on_load_code_counter = 0;
    this.requests = '';
    /**
    * Status message variables
    *
    */
    this.loading = false;
    this.time_start_loading;
    this.time_end_loading;

    this.opera = (navigator.userAgent.indexOf('Opera')!=-1)?1:0;
    this.ns6 = (document.getElementById && !document.all && !this.opera)?1:0;
    this.DOM = (document.getElementById) ? 1 : 0;
    this.NS4 = (document.layers) ? 1 : 0;
    this.Opera5 = (navigator.userAgent.indexOf('Opera 5') > -1 || navigator.userAgent.indexOf('Opera/5') > -1) ? 1 : 0;
    this.IE = (navigator.appName.indexOf('Microsoft') != -1)?1:0;

    this.loadedobjects="";

    /**
    * JSRS Code
    *
    */
    /*@cc_on @*/
    /*@if (@_jscript_version >= 5)
    // JScript gives us Conditional compilation, we can cope with old IE versions.
    // and security blocked creation of the objects.
    try {
    this.xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
    try {
    this.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (E) {
    this.xmlhttp = false;
    }
    }
    @end @*/
    if (!this.xmlhttp && typeof XMLHttpRequest!='undefined')
        this.xmlhttp = new XMLHttpRequest();

} // end kernel

//////////// AJAX functions ///////////////////////////////////

/**
* Shows the loading sign
* @name start_loading
* @global loading true or false
*
*/
kernel.prototype.start_loading=function(){
    var indicator = this.getId("indicator");
    if( indicator ) indicator.style.display="inline";
    this.time_start_loading = this.datetounixtime();
    this.loading = true;
} // end start_loading

/**
* Hides the loading sign
* @name end_loading
* @global loading true or false
*
*/
kernel.prototype.end_loading=function(){
    var indicator = this.getId("indicator");
    if( indicator ) indicator.style.display="none";
    this.time_end_loading = this.datetounixtime();
    this.loading = false;
} // end end_loading

/**
* Executes modules specific code after loading is finished
* @name module_specific_code
*
*/
kernel.prototype.module_specific_code=function(){
} // end module_specific_code

/**
* Main function for sync and async calls
* @name jsrs_call
*
*/
kernel.prototype.jsrs_call=function(action, element_name, method, async, code, return_result, prefix ){
    var thisObj = this;
    var args = null;
    var elements = new Array();

    // Initializations
    if( !action ) return false;
    if( this.loading ){ this.requests += "this.jsrs_call('"+action+"','"+element_name+"','"+method+"',"+(async?'true':'false')+",'"+(code?code.replace(/'/g,"\\'"):'')+"',"+(return_result?'true':'false')+",'"+(prefix?prefix:'')+"');|||"; return false; }
    method = method.toUpperCase();
    method = method ? method : "GET";
    async = async ? async : false;
    this.parent_div = element_name ? element_name : null;
    this.on_load_code[this.on_load_code_counter++] = code ? code : null;

    // If no jsrs act regularly
    if( !this.xmlhttp ){
        if( method == "GET" ) document.location = action;
        return true;
    }

    this.start_loading();

/*      // Asyncrounous form submition protection mechanism
        if( document.getElementById("form_id") ){
            form_id_array = new Array();
            c = 0;
            for( i = 0; i < document.forms.length; i++ )
                for( j = 0; j < document.forms[i].elements.length; j++ )
                    if( document.forms[i].elements[j].name == "form_id" && document.forms[i].elements[j].value )
                        form_id_array[c++] = document.forms[i].elements[j].value;
            _form_id = "&_form_id="+form_id_array.join(",");
        } else _form_id = null;*/
    //this.xmlhttp.open(method, action + "&content_only=1"+_form_id, async);

    // if post method selected
    if( method == "POST" ){
        var form = this.getId( action );

        if( form ){
            action = form.action;
            elements = form.elements;
        }
        if( prefix ) {
            el = document.getElementsByTagName('input');
            for( c = 0; c < el.length; c++ )
                if( el[c].id.indexOf( prefix ) == 0 )
                    elements[elements.length] = el[c];
            el = document.getElementsByTagName('select');
            for( c = 0; c < el.length; c++ )
                if( el[c].id.indexOf( prefix ) == 0 )
                    elements[elements.length] = el[c];
            el = document.getElementsByTagName('textarea');
            for( c = 0; c < el.length; c++ )
                if( el[c].id.indexOf( prefix ) == 0 )
                    elements[elements.length] = el[c];
        }

        args = "";

        for( c = 0; c < elements.length; c++ ){
                if( elements[c].tagName.toLowerCase() == 'fieldset' ) continue;
                if( elements[c].type.toLowerCase() == "checkbox" ){
                    if( elements[c].checked )
                        args += elements[c].name + "=" + elements[c].value+"&";
                } else if( elements[c].type.toLowerCase() == "radio" ){
                    if( elements[c].checked )
                        args += elements[c].name + "=" + elements[c].value+"&";
                } else if ( elements[c].type.toLowerCase() == "select-multiple"  ) {
                    for( j = 0; j < elements[c].options.length; j++ )
                    if( elements[c].options[j].selected )
                        args += elements[c].name + "=" + elements[c].options[j].value+"&";
                } else
                    args += elements[c].name + "=" + this.URLencode( elements[c].value )+"&";
        } // end for elements
    } // end if POST

    //alert( "Action: " + action + " Element: " + element_name + " Method: " + method + " Async: " + async + " Loading: " + this.loading + " args: " + args );

    this.xmlhttp.open(method, action+"&content_only=1", async);
    if( method == "POST" ) this.xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    this.xmlhttp.setRequestHeader("X-Requested-With", "XmlHttpRequest" );
    this.xmlhttp.onreadystatechange=function(){ eval( "thisObj.jsrs_handler();" ); }
    this.xmlhttp.send(args);

    if ( async == false ){

        if (this.xmlhttp.status == 200){
            if( return_result ){ this.end_loading(); return this.xmlhttp.responseText; }
            if( element_name ){
                var c = this.getId( element_name );
                if( c ) c.innerHTML = this.xmlhttp.responseText;
            }
        } else
            this.jsrs_on_error();

        this.end_loading();
        if( code ) eval( code );

    } // end if call_type SYNC

    return false;

} // end jsrs_call

kernel.prototype.load_sync_get=function(url, element_name, code){
    return this.jsrs_call( url, element_name, "get", false, code );
} // end load_sync_get

kernel.prototype.load_async_get=function(url, element_name, code){
    return this.jsrs_call( url, element_name, "get", true, code );
} // end load_async_get

kernel.prototype.call_sync_get=function(url, code){
    return this.jsrs_call( url, "", "get", false, code );
} // end call_sync_get

kernel.prototype.call_async_get=function(url, code){
    return this.jsrs_call( url, "", "get", true, code );
} // end call_async_get

kernel.prototype.load_sync_post=function(url, element_name, code){
    return this.jsrs_call( url, element_name, "post", false, code );
} // end load_sync_post

kernel.prototype.load_async_post=function(url, element_name, code){
    return this.jsrs_call( url, element_name, "post", true, code );
} // end load_async_post

kernel.prototype.load_async_post_prefix=function(url, element_name, prefix, code){
    return this.jsrs_call( url, element_name, "post", true, code, false, prefix );
} // end load_async_post

kernel.prototype.call_sync_post=function(url, code){
    return this.jsrs_call( url, "", "post", false, code );
} // end call_sync_post

kernel.prototype.call_async_post=function(url, code){
    return this.jsrs_call( url, "", "post", true, code );
} // end call_async_post

kernel.prototype.get_result=function(url, code ){
    return this.jsrs_call( url, "", "get", false, code, true );
} // end get_result

    // A href
kernel.prototype.load_async_a=function(obj, element_name, code ){
    if( !obj ) return;
    this.jsrs_call( obj.href, element_name, "get", true, code );
    return false;
} // end load_async_a

kernel.prototype.load_sync_a=function(obj, element_name, code ){
    if( !obj ) return;
    this.jsrs_call( obj.href, element_name, "get", false, code );
    return false;
} // end load_sync_a

/**
* Handles the get asynchronous call
* @name jsrs_handler_get
*
*/
kernel.prototype.jsrs_handler=function(){

    switch (this.xmlhttp.readyState) {
/*        case 1: // Initializes the call
            this.jsrs_on_start_init_loading();
            break;
        case 2: // End initializing the call
            this.jsrs_on_end_init_loadin();
            break;*/
        case 3: // Loading (interactive)
            this.jsrs_on_loading();
            break;
        case 4: // Ready
            try {
                if (this.xmlhttp.status == 200)
                    this.jsrs_on_completion();
                else {
                    this.jsrs_on_error();
                    this.end_loading();
                }
            } // end try
            catch (e) {
                this.end_loading();
            }
            break;
    } // end switch

} // end jsrs_handler_get

/**
* On loading handler
* @name jsrs_on_loading
*
*/
kernel.prototype.jsrs_on_loading=function(){
//     c = document.getElementById(this.parent_div);
//     c.innerHTML = this.xmlhttp.responseText;
} // end jsrs_on_loading

/**
* On completed loading handler
* @name jsrs_on_completion
*
*/
kernel.prototype.jsrs_on_completion=function(){

    var c = this.getId(this.parent_div);
    if( c ) c.innerHTML = this.xmlhttp.responseText;
    this.end_loading();
    this.module_specific_code();

    // Extract scripts and evaluate
    if(this.xmlhttp.responseText.indexOf("<script") != -1 ){
        str = this.xmlhttp.responseText.replace(/\n/igm,"");
        var re = new RegExp('<script[^>]*>.+?</script>', 'gi');
        var matches = str.match(re);
        if( matches && matches.length ){
            for( c = 0; c < matches.length; c++ ){
                str = matches[c].replace(/<\/script[^>]*>/, "" ).replace(/<script[^>]*>/, "");
                eval( str );
            }
        }
    } // end script

    // Exec post code
    if( this.on_load_code[--this.on_load_code_counter] ){
        eval(this.on_load_code[this.on_load_code_counter]);
        this.on_load_code[this.on_load_code_counter] = null;
    }

    // Exec queue
    if( this.requests ){
        requests = this.requests.split("|||");
        this.requests = '';
        if( requests[0] )
            eval(requests[0]);
        for(c=1; c<requests.length-1; c++)
            if( requests[c] )
                this.requests += requests[c] + "|||";
    } // end exec queue

} // end jsrs_on_completion

/**
* On error handler
* @name jsrs_on_error
*
*/
kernel.prototype.jsrs_on_error=function(){
    alert("There was a problem updating data: " + this.xmlhttp.statusText);
} // end jsrs_on_error

/**
* Refreshes numerous divs
* @name synchronize
* @param url string url to call
* @param div string div to reload
* variable args list
*
*/
kernel.prototype.synchronize=function(){
    for (var i = 0; i < arguments.length; i+=2) {
        if( arguments[i] && arguments[i+1] )
            this.load_sync_get( arguments[i], arguments[i+1] );
    }
} // end synchronize

//////////// IO functions ///////////////////////////////////

kernel.prototype.getKeyCode=function( event ){
    return window.event ? window.event.keyCode : event.which;
} // end getKeyCode

kernel.prototype.getKeyCtrl=function( event ){
    return window.event ? window.event.ctrlKey : event.ctrlKey;
} // end getKeyCtrl

kernel.prototype.getKeyAlt=function( event ){
    return window.event ? window.event.altKey : event.altKey;
} // end getKeyAlt

kernel.prototype.getMouseX=function( event ){
    if( window.event && event.clientX ) return event.clientX + document.body.scrollLeft;
    else if( event.pageX ) return event.pageX;
    return 0;
} // end getMouseX

kernel.prototype.getMouseY=function( event ){
    if( window.event && event.clientY ) return event.clientY + document.body.scrollTop;
    else if( event.pageY ) return event.pageY;
    return 0;
} // end getMouseY

//////////// Graphical functions ///////////////////////////////////

/**
* Copy content from one container to another one
* @name    c_copy
* @param   src_id
* @param   dst_id
*/
kernel.prototype.c_copy=function( src_id, dst_id ){
    var src = this.getId( src_id );
    var dst = this.getId( dst_id );
    if( src && dst ) dst.innerHTML = src.innerHTML;
} // end c_copy

/**
* Empty content of a container
* @name    c_empty
* @param   element_name
*/
kernel.prototype.c_empty=function( element_name ){
    var src = this.getId( element_name );
    if( src ) src.innerHTML = "";
} // end c_empty

/**
* Moves content from one container to another one
* @name    c_move
* @param   src_id
* @param   dst_id
*/
kernel.prototype.c_move=function( src_id, dst_id ){
    this.c_copy( src_id, dst_id );
    this.c_empty( src_id );
} // end c_move

/**
* Set container size
* @name    c_set_size
* @param   element_name
* @param   width
* @param   height
*/
kernel.prototype.c_set_size=function( element_name, width, height ){
    var elem = this.getId( element_name );
    if( !elem ) return;
    elem.style.width = width+"px";
    elem.style.height = height+"px";
} // end c_set_size

/**
* Move container to
* @name    c_move_to
* @param   element_name
* @param   left
* @param   top
*/
kernel.prototype.c_move_to=function( element_name, left, top ){
    var elem = this.getId( element_name );
    if( !elem ) return;
    elem.style.position="absolute";
    elem.style.top = left+"px";
    elem.style.left = top+"px";
} // end c_move_to

/**
* Add container in a given container
* @name    c_add
* @param   src_id
* @param   dst_id
* @param   element_name
* @param   style
*/
kernel.prototype.c_add=function( src_id, dst_id, element_type, style, contents ){
    var element_type = element_type ? element_type : "div";
    var newWindow = document.createElement(element_type);
    var src = this.getId( src_id );
    var dst = this.getId( dst_id );

    if( !src || dst ) return;

    newWindow.setAttribute("id", dst_id);
    if( style ) newWindow.setAttribute("style", style);

    //var theText1 = document.createTextNode('Test <b>window');
    //newWindow.appendChild(theText1);
    src.appendChild(newWindow);

    if( contents ) this.getId(dst_id).innerHTML = contents;
    //this.parentNode.insertBefore(newWindow,document.getElementById('contents'));
    //newWindow.onclick = function () { document.getElementById('contents').removeChild(this); };
//    main.jsrs_get_data('?', id );

} // end c_add

/**
* Remove container from a given container
* @name    c_remove
* @param   src_id
* @param   dst_id
*/
kernel.prototype.c_remove=function( src_id, dst_id ){
    var src = this.getId( src_id );
    var dst = this.getId( dst_id );

    if( !src || !dst ) return;
    src.removeChild( dst );
} // end c_remove

/**
* insertAfter
* @name    insertAfter
* @param   parent
* @param   node
* @param   referenceNode
*/
kernel.prototype.insertAfter=function(parent, node, referenceNode) {
    parent.insertBefore(node, referenceNode.nextSibling);
} // end insertAfter

/**
* Folds and unfolds given container
* @name fold
*/
kernel.prototype.fold=function(id){
    obj = this.getId( id );
    obj_img = this.getId( id + "_img" );
    if( !obj ) return;
    if( obj.style.display == 'none' ){
        obj.style.display = 'block';
        if( obj_img ) obj_img.src = 'images/www/desc.png';
    } else {
        obj.style.display = 'none';
        if( obj_img ) obj_img.src = 'images/www/asc.png';
    }
} // end fold

//////////// General functions ///////////////////////////////////

/**
* URL encodes strings
* @name URLencode
* @param string val value
*
* @return string val encoded value
*
*/
kernel.prototype.URLencode=function( val ){
    val = val.replace(/\%/g, "%25");
    val = val.replace(/\+/g,"%2b");
    val = val.replace(/\&/g, "%26");
    val = val.replace(/\#/g, "%23");
    val = val.replace(/\n/g, "%0a");
    val = val.replace(/\r/g, "%0d");

    return val;
} // end URLencode

/**
* Returns unixtime from current date
* @name    datetounixtime
* @return  integer unixtime in seconds
*/
kernel.prototype.datetounixtime=function(){
    var now = new Date; // Generic JS date object
    var unixtime_ms = now.getTime(); // Returns milliseconds since the epoch

    return unixtime_ms / 1000;
} // end datetounixtime

/**
* Gets reference to given element by id
* @name    getId
* @return  reference
*/
kernel.prototype.getId=function( id ){
    return document.getElementById( id );
} // end getId

/**
* Checks if a values is in array
* @name    in_array
* @return  boolean
*/
kernel.prototype.in_array=function( array, val ){
    if( typeof( array ) != "object" ) return false;
    for( c = 0; c < array.length; c++ )
        if( array[c] == val ) return true;
    return false;
} // end in_array

/**
* Extracts filename from full path name
* @name    basename
* @return  string base file name
*/
kernel.prototype.basename = function(file){
    var Parts = file.split('\\');
    if( Parts.length < 2 )
    Parts = file.split('/');
    return Parts[Parts.length - 1];
} // end basename

/**
* Loads and unloads js, css, etc. external files on runtime
* @name loadobjs/unloadobjs
* Usage:
* loadobjs('external.css') //load one CSS file
* loadobjs('external.css', 'external2.css', 'feature.js') //load 2 CSS files & 1 JS file
* loadobjs('feature.js', 'feature2.js', 'feature3.js') //load 3 JS files
* http://www.dynamicdrive.com/dynamicindex17/ajaxcontent.htm
*/
kernel.prototype.loadobjs = function(){
    if (!document.getElementById) return
    for (i=0; i<arguments.length; i++){
        var file=arguments[i]
        var fileref=""

        if (this.loadedobjects.indexOf(file)==-1){ //Check to see if this object has not already been added to page before proceeding
            if (file.indexOf(".js")!=-1){ //If object is a js file
            fileref=document.createElement('script')
            fileref.setAttribute("type","text/javascript");
            fileref.setAttribute("src", file);
            }
            else if (file.indexOf(".css")!=-1){ //If object is a css file
            fileref=document.createElement("link")
            fileref.setAttribute("rel", "stylesheet");
            fileref.setAttribute("type", "text/css");
            fileref.setAttribute("href", file);
            }
            fileref.setAttribute("id", file );
        }
        if (fileref!=""){
            document.getElementsByTagName("head").item(0).appendChild(fileref)
            this.loadedobjects+=file+" " //Remember this object as being already added to page
        }
    }
} // end loadobjs

kernel.prototype.unloadobjs = function(){
    if (!document.getElementById) return
    for (i=0; i<arguments.length; i++){
        var file=arguments[i]

        if (this.loadedobjects.indexOf(file)!=-1){ //Check to see if this object has not already been added to page before proceeding
            var old = document.getElementById( file );
            if (old) document.getElementsByTagName('head').item(0).removeChild(old);
            this.loadedobjects = this.loadedobjects.replace( file+" ", " " ) //Remember this object as being already added to page
        }
    }
} // end unloadobjs

// end class kernel