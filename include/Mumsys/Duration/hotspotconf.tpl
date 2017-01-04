<h1>Hotspot Monitoring Configuration</h1>
{literal}
<style>
.formLayout
    {
        background-color: #f3f3f3;
        border: solid 1px #a1a1a1;
        padding: 10px;
        width: 300px;
    }
    .activate,.notactivated{
    	display: block;
        width: 140px;
        float: left;
        margin-bottom: 10px;
    }
    .hotintl{
    	font-size: 15px;
    	width: 70px;
    }
    .formLayout label
    {
        display: block;
        width: 120px;
        float: left;
        margin-bottom: 10px;
    }
 
    .formLayout label
    {
        text-align: right;
        padding-right: 20px;  margin-top: 10px;
    }
    .activate{
    	background-color: #45a049;
    	cursor: pointer;
    }
    .notactivated{
    	background-color: #e00b3d;
    	cursor: pointer;
    }
    #savehotcfg{
    	cursor: pointer;display: inline-block;
    	margin-left: 10px;
    }
    .inpintv{
    	font-size: 15px;
    }
    br
    {
        clear: left;
    }
</style>
{/literal}
{literal}
<script type="text/javascript">
jQuery( document ).ready(function( $ ) {
	$(document).on("click",".hotmon",function(){
		if (confirm('Are you sure want to enable / disable hotspot monitoring service?')) {
		      $.post("include/Mumsys/Hotspot/hotspotact.php",
	          {
	          	method:"hotmonitor"
	          },
	          function(data, status){
	           if(data=="enabled"){
	           	 $('.hotmon').val("ON");
	           	  $('#hotmon').removeClass('notactivated');
	           	  	$('#hotmon').addClass('activate');
	           }else{
	           	$('.hotmon').val("OFF");
	           	  $('#hotmon').removeClass('activate');
	           	  	$('#hotmon').addClass('notactivated');
	           }
	          });
		}
	});

	$(document).on("click","#savehotcfg",function(){
		$.post("include/Mumsys/Hotspot/hotspotact.php",
	          {
	          	method:"edtintval",
	          	value:$('#inpintv').val()
	          },function(data, status){
	          	if(data=="saved"){
	          		alert("Interval has been saved! & applied on next hotpost service start")
	          	}

	          });
	});

	$(document).on("click",".hotoffbus",function(){
		$.post("include/Mumsys/Hotspot/hotspotact.php",
	          {
	          	method:"hotoffbus"
	          },function(data, status){
	          	if(data=="enabled"){
	           	 $('.hotoffbus').val("ON");
	           	  $('#hotoffbus').removeClass('notactivated');
	           	  	$('#hotoffbus').addClass('activate');
	           }else{
	           	$('.hotoffbus').val("OFF");
	           	  $('#hotoffbus').removeClass('activate');
	           	  	$('#hotoffbus').addClass('notactivated');
	           }
	          });
	});
});
</script>
{/literal}


<div class='formLayout'>
        <label>Monitoring Service</label>
        {if $hotspotmon=='1'}
        	<input type="button" value='ON' class='hotmon activate' id='hotmon'><br>
        {else}
        	<input type="button" value='OFF' class='hotmon notactivated' id='hotmon'><br>
        {/if}
        <label>Monitoring Interval</label>
        <input type="text" value='{$hotspotinterval}' id='inpintv' class='hotintl'><input type='button' value='Save' id='savehotcfg' class='btc bt_info'><br>
        <label>Save Offline Bus</label>
        {if $saveofflinebus=='1'}
        <input type="button" value='ON' class='hotoffbus activate' id='hotoffbus'><br>
        {else}
        <input type="button" value='OFF' class='hotoffbus notactivated' id='hotoffbus'><br>
        {/if}

        
</div>