/**
* Class main. Adds specific code to the application
* @name main
* @author Tsvetan Filev <tsvetan.filev@gmail.com>
* @date 2006/09/25
*/

main.prototype = new kernel();
main.prototype.constructor=main;

function main(){
    this.timerID = 0;
    this.originalColor = null;
}

main.prototype.init=function(){
    //setTimeout("mainObj.check_for_messages()", 10000 );
    //check_for_messages()
}
/*
  // Override start_loading
main.prototype.start_loading=function(){
    //msg = document.getElementById("status_msg");
    //msg.style.display = "app";
    //this.timerID  = setTimeout("mainObj.UpdateTimer()", 100);
    //this.loading_str = document.getElementById('status_msg_txt').innerHTML;
    this.changeOpac(70,'contents');
    document.getElementById("dhtmlgoodies_progressPane").style.display="app";
    this.time_start_loading = this.datetounixtime();
    document.getElementById("indicator").style.display="inline";
    this.loading = true;
} // end start_loading
*/
/*
 // Override end_loading
main.prototype.end_loading=function(){
    //msg = document.getElementById("status_msg");
    //if(this.timerID) {
    //    clearTimeout(this.timerID);
    //    this.timerID  = 0;
    //}
    //document.getElementById('status_msg_txt').innerHTML = this.loading_str;
    //msg.style.display = "none";
     this.changeOpac(100,'contents');
     document.getElementById("dhtmlgoodies_progressPane").style.display="none";
     progressBar_outer = document.getElementById('dhtmlgoodies_progressBar_outer');
     progressBar_txt = document.getElementById('dhtmlgoodies_progressBar_txt');
     progressBar_outer.style.width = 0 + 'px';
     progressBar_txt.innerHTML = 0+'%';
     document.getElementById("loaded_time").innerHTML = "Loaded for "+ (this.datetounixtime() - this.time_start_loading ).toFixed(3) + " secs";
    document.getElementById("indicator").style.display="none";
    this.loading = false;
} // end end_loading
*/
/*
// Override jsrs_on_loading
main.prototype.jsrs_on_loading=function(){

    if( !this.IE ){
        var content_length = this.xmlhttp.getResponseHeader("Content-Length");
        if( content_length ){
            if( typeof(this.xmlhttp.responseText) != "undefined" ){
                incoming_length = this.xmlhttp.responseText.length;
                if( incoming_length <= content_length ){
                    percent = incoming_length/content_length*100;
                    progressBar_outer = document.getElementById('dhtmlgoodies_progressBar_outer');
                    progressBar_txt = document.getElementById('dhtmlgoodies_progressBar_txt');
                    progressBar_outer.style.width = Math.round(percent) + 'px';
                    progressBar_txt.innerHTML = Math.round(percent)+'%';
                }
            }
        }
    }
} // end jsrs_on_loading
*/
/**
* Executes modules specific code
* @name module_specific_code
*
*/
main.prototype.module_specific_code=function(){

    //if( document.getElementById('invoices') ) document.getElementById('vendor').select();

} // end module_specific_code


/**
* Returns the key code of pressed button
* @name    keyCode
* @param   object      event object
* @return  integer     key code
*/
main.prototype.keyCode=function( event ){
    var key;
    if ( window.event ) key = window.event.keyCode;
    if ( !key && event ) key = event.which;
    return key;
}

/**
* Creates hot keys for given actions
* @name    short_cut
* @param   object      event object
* @return  boolean     accept or reject key
*/
main.prototype.short_cut=function( event ){

    var key = null;
    var mod = null;

    if ( window.event ){  key = window.event.keyCode; mod = window.event.ctrlKey; }
    if ( !key && event ){  key = event.which; mod = event.ctrlKey; }

    if( !mod ) return true;

    switch( key ) {
        case  97: // ctrl + a  add task
            this.jsrs_call_get("?module=tasks&action=tasks_add");
            return false;
        case 101: // ctrl + e  vendors
            this.jsrs_call_get("?module=vendors");
            return false;
        case 103: // ctrl + g  goods receiving
            this.jsrs_call_get("?module=goods_receiving");
            return false;
        case 104: // ctrl + h  home
            this.jsrs_call_get("?");
            return false;
            break;
        case 105: // ctrl + i  invoice
            this.jsrs_call_get("?module=invoices");
            return false;
        case 107: // ctrl + k  tasks
            this.jsrs_call_get("?module=tasks&created_by=all_tasks&open=OPEN&closed=CLOSED");
            return false;
        case 109: // ctrl + m  send chat message
            chat = document.getElementById('send_chat_message');
            chat.style.display = chat.style.display == 'none' ? 'block' : 'none';
            body = document.getElementById('chat_body');
            body.focus();
            body.select();
            return false;
        case 115: // ctrl + s  server building
            this.jsrs_call_get("?module=server_building");
            return false;
    } // switch

    return true;
} // end short_cut

/**
* Creates hot keys for given actions
* @name    form_short_cut
* @param   object      event object
* @return  boolean     accept or reject key
*/
main.prototype.form_short_cut=function( event, form_id ){

    var key = null;
    var mod = null;

    if ( window.event ){  key = window.event.keyCode; mod = window.event.ctrlKey; }
    if ( !key && event ){  key = event.which; mod = event.ctrlKey; }

    if( !mod ) return true;

    switch( key ) {
        case  100: // ctrl + d submit form
            this.jsrs_call_post(form_id);
            return false;
    } // switch

    return true;
} // end form_short_cut

main.prototype.noenter=function(event, curr_id, form_id){
    var form_obj = document.getElementById( form_id );
    var curr_obj = document.getElementById( curr_id );
    var res = (window.event && window.event.keyCode == 13);
    var next_index = 0;

    if( !res ) res = (event && event.which == 13);

    if( res ){
        for( i = 0; i < form_obj.elements.length; i++ ){
            if( form_obj.elements[i].tabIndex > curr_obj.tabIndex && form_obj.elements[i].type.toLowerCase() == "text" ){
            if( !next_index ) next_index = form_obj.elements[i].tabIndex;
            if( form_obj.elements[i].tabIndex < next_index ) next_index = form_obj.elements[i].tabIndex;
            }
        }
        for( i = 0; i < form_obj.elements.length; i++ ){
            if( form_obj.elements[i].tabIndex == next_index ){
                try {
                    form_obj.elements[i].focus();
                    form_obj.elements[i].select();
                } catch(e) {}
            }
        }
    }

    return !res;
} // end noenter

main.prototype.maximize=function(){

    document.getElementById('oMbbar').style.display = 'none';
    document.getElementById('oM_main_1_0').style.display = 'none';
    document.getElementById('oM_main_2_0').style.display = 'none';
    document.getElementById('oM_main_3_0').style.display = 'none';
    document.getElementById('oM_main_4_0').style.display = 'none';
    document.getElementById('oM_main_5_0').style.display = 'none';
    document.getElementById('oM_main_6_0').style.display = 'none';
    document.getElementById('oM_main_7_0').style.display = 'none';
    document.getElementById('oM_main_8_0').style.display = 'none';
    document.getElementById('contents').style.marginTop = '0px';
    document.getElementById('status_bar').style.display = 'none';
    document.getElementById('endgradient').style.display = 'none';
    this.originalColor = document.body.style.backgroundColor;
    document.body.style.backgroundColor = '#FFFFFF';

} // end maximize

main.prototype.minimize=function(){

    document.getElementById('oMbbar').style.display = 'block';
    document.getElementById('oM_main_1_0').style.display = 'block';
    document.getElementById('oM_main_2_0').style.display = 'block';
    document.getElementById('oM_main_3_0').style.display = 'block';
    document.getElementById('oM_main_4_0').style.display = 'block';
    document.getElementById('oM_main_5_0').style.display = 'block';
    document.getElementById('oM_main_6_0').style.display = 'block';
    document.getElementById('oM_main_7_0').style.display = 'block';
    document.getElementById('oM_main_8_0').style.display = 'block';
    document.getElementById('contents').style.marginTop = '26px';
    document.getElementById('status_bar').style.display = 'block';
    document.getElementById('endgradient').style.display = 'block';
    document.body.style.backgroundColor = this.originalColor;

} // end minimize

//change the opacity for different browsers
main.prototype.changeOpac=function(opacity, id) {
    var object = document.getElementById(id).style;
    object.opacity = (opacity / 100);
    object.MozOpacity = (opacity / 100);
    object.KhtmlOpacity = (opacity / 100);
    object.filter = "alpha(opacity=" + opacity + ")";
} // end changeOpac

main.prototype.addRow=function(id){
    var table = this.getId(id);
    var row = table.rows[table.rows.length-1];
    var default_sting = this.get_result('?app=custom&object=list_modules');

    for( i = 0; i < table.rows[0].cells.length-1; i++){
        row.cells[i].style.border = '1px solid black';
        //row.cells[i].innerHTML = "cell "+(table.rows.length)+" "+(i+1);
        str = default_sting;
        str = str.replace(/row=1/g,"row="+table.rows.length);
        str = str.replace(/col=1/g,"col="+(i+1));
        str = str.replace(/cell_1_1/g,"cell_"+table.rows.length+"_"+(i+1));
        row.cells[i].innerHTML = str;
    }
    row = table.insertRow(table.rows.length);
    for( i = 0; i <  table.rows[0].cells.length; i++){
        var cell = row.insertCell(i);
        cell.setAttribute("id", "cell_"+(table.rows.length)+"_"+(i+1));
        cell.style.border = '1px dotted black';
        //str = "cell "+(table.rows.length)+" "+(i+1);
        str = "&nbsp;";
        if( i == 0 ){ str += " <a href=\"javascript:;\" onClick=\"main.addRow('custom');\"><img src=\"images/www/more_down.gif\" border=\"0\" align=\"absmiddle\" /></a>"; }
        cell.innerHTML = str;
    }
}

main.prototype.addCol=function(id){
    var table = this.getId(id);
    var num = table.rows[0].cells.length;

    var default_sting = this.get_result('?app=custom&object=list_modules');

    for( i = 0; i <  table.rows.length-1; i++){
        var cell = table.rows[i].cells[table.rows[i].cells.length-1];
        cell.style.border = '1px solid black';
        //cell.innerHTML = "cell "+(i+1)+" "+(num);
        str = default_sting;
        str = str.replace(/row=1/g,"row="+(i+1));
        str = str.replace(/col=1/g,"col="+(num));
        str = str.replace(/cell_1_1/g,"cell_"+(i+1)+"_"+(num));
        cell.innerHTML = str;
    }
    for( i = 0; i <  table.rows.length; i++){
        var cell = table.rows[i].insertCell(num);
        cell.setAttribute("id", "cell_"+(i+1)+"_"+(num+1));
        cell.style.border = '1px dotted black';
        //str = "cell "+(i+1)+" "+(num+1);
        str = "&nbsp;";
        if( i == 0 ){ str += " <a href=\"javascript:;\" onClick=\"main.addCol('custom');\"><img src=\"images/www/more_right.gif\" border=\"0\" align=\"absmiddle\" /></a>"; }
        cell.innerHTML = str;
    }
}


// End Class main

 // Create object
main = new main();
