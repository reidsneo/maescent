		<style type="text/css">			
			.example {
				float: left;
				margin: 15px;
			}			
			.demo {
				width: 400px;
				height: 400px;
				border-top: solid 1px #BBB;
				border-left: solid 1px #BBB;
				border-bottom: solid 1px #FFF;
				border-right: solid 1px #FFF;
				background: #FFF;
				overflow: scroll;
				padding: 5px;
			}	
			#readertext{
   background: #fff none repeat scroll 0 0;
    border-color: #bbb #fff #fff #bbb;
    border-style: solid;
    border-width: 1px;
    height: 400px;
    margin: 15px;
    overflow-y: scroll;
    padding: 5px;
    width: 800px;
			}			
		</style>
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="include/common/jquery.timepicker.min.js"></script>
		<link href="include/common/jquery.timepicker.css" rel="stylesheet" type="text/css" media="screen" />
		<script src="include/common/jquery.easing.js" type="text/javascript"></script>
		<script src="include/common/jqueryFileTree.js" type="text/javascript"></script>
		<link href="include/common/jqueryFileTree.css" rel="stylesheet" type="text/css" media="screen" />
		
		<script type="text/javascript">
			
			jQuery(document).ready(function($) {
				var filename=""		;
				$('#fileTreeDemo_3').fileTree({ root: '/var/lib/MNPLog/', script: 'include/Mumsys/BusLog/connectors/jqueryFileTree.php', folderEvent: 'click', expandSpeed: 750, collapseSpeed: 750, expandEasing: 'easeOutBounce', collapseEasing: 'easeOutBounce', loadMessage: 'Un momento...', multiFolder: false }, function(file) {
					$("#readertext").html("Loading....");
					filename=file;
					 $.post("include/Mumsys/BusLog/filereader.php",
			          {
			          	filepath:file
			          },
			          function(data, status){
			          	$("#readertext").html(data);
			          });
				});
				$("#datestart").datepicker();
				$("#dateend").datepicker();
				$('#timestart').timepicker({ 'timeFormat': 'H:i:s' });
				$('#timeend').timepicker({ 'timeFormat': 'H:i:s' });
				$(document).on("click","#btnsortdate",function(){
					if($("#datestart").val()=="" || $("#dateend").val()==""){
						alert("Please fill all data!");
						return false;
					}else{
						var splstart=$("#datestart").val().split("/");
						var datestart=parseInt(splstart[2]+splstart[0]+splstart[1]);
						var splend=$("#dateend").val().split("/");
						var dateend=parseInt(splend[2]+splend[0]+splend[1]);
						$( "li.ext_log" ).each(function( index ) {
							var logdata=$(this).text().split("_");
							var logdate=parseInt(logdata[0]);
							var isvalid="";
							if(logdate>=datestart && logdate<=dateend){
								//isvalid="valid";
							}else{
								//isvalid="notvalid";
								$(".ext_log").remove(":contains('"+logdate+"')");
							}
							//console.log( index + ": " + logdata[0]+isvalid);
						});
					}
				});
				$(document).on("click","#btnsorttime",function(){					
					 $.post("include/Mumsys/BusLog/filereader.php",
			          {
			          	filepath:filename,
			          	starttime:$("#timestart").val(),
			          	endtime:$("#timeend").val(),
			          },
			          function(data, status){
			          	$("#readertext").html(data);
			          });
				});
			});
		</script>
		<div class="example">
			<div id="fileTreeDemo_3" class="demo"></div>
			Start : <input type="text" id="datestart">
			End : <input type="text" id="dateend">
			<input type="button" name="btnsortdate" id="btnsortdate" value="GO!">
		</div>
		<div id='readertext'></div>
		Start : <input type="text" id="timestart">
		End : <input type="text" id="timeend">
		<input type="button" name="btnsorttime" id="btnsorttime" value="GO!">