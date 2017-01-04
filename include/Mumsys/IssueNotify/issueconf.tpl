<h1>Daily Issue Analyst Configuration</h1>
{literal}
<style>
.formLayout
    {
        background-color: #f3f3f3;
        border: solid 1px #a1a1a1;
        padding: 10px;
        width: 600px;
    }
    .activate,.notactivated{
    	display: block;
        width: 140px;
        float: left;
        margin-bottom: 10px;
    }
    .hotintl{
    	font-size: 15px;
        width: 300px;
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
		if (confirm('Are you sure want to enable / disable daily issue monitoring service?')) {
		      $.post("include/Mumsys/IssueNotify/issueconfact.php",
	          {
	          	method:"emailcronmon"
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

	$(document).on("click","#saveemailconf",function(){
		 $.post("include/Mumsys/IssueNotify/issueconfact.php",
	          {
	          	method:"saveconf",
	          	emailcheckinterval:$('#emailcheckinterval').val(),
                emailnotifytime:$('#emailnotifytime').val(),
                emailtarget:$('#emailtarget').val(),
                emailcc:$('#emailcc').val(),
                emailbotuser:$('#emailbotuser').val(),
                emailbotpass:$('#emailbotpass').val(),
                emailtargetname:$('#emailtargetname').val(),
	          },function(data, status){
	          	if(data=="saved"){
	          		alert("Configuration has been saved")
	          	}

	          });
	});
});
</script>
{/literal}


<div class='formLayout'>
        <label>Daily Analyst Service</label>
        {if $emailcron=='1'}
        	<input type="button" value='ON' class='hotmon activate' id='hotmon'><br>
        {else}
        	<input type="button" value='OFF' class='hotmon notactivated' id='hotmon'><br>
        {/if}
        <label>Service Check Interval (sec)</label>
        <input type="text" value='{$emailcheckinterval}' id='emailcheckinterval' class='hotintl'><br>
        <label>Daily Email Notify Time</label>
        <input type="text" value='{$emailnotifytime}' id='emailnotifytime' class='hotintl'><br><hr><br>
        <label>Email Name</label>
        <input type="text" value='{$emailtargetname}' id='emailtargetname' class='hotintl'><br>
        <label>Email Receiver</label>
        <input type="text" value='{$emailtarget}' id='emailtarget' class='hotintl'><br>
        <label>Email Receiver CC</label>
        <textarea style="width: 457px; height: 90px;" id='emailcc'>{$emailcc}</textarea><br><br><hr><br>
        <label>Bot Acc Email</label>
        <input type="text" value='{$emailbotuser}' id='emailbotuser' class='hotintl'><br>
        <label>Bot Acc Password</label>
        <input type="text" value='{$emailbotpass}' id='emailbotpass' class='hotintl'><br><br><hr><br>
        <input type="button" value='Save Configuration' id='saveemailconf'><br>
</div>