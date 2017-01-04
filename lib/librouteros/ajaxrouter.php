<!DOCTYPE html>
<html>
<head>
	<title>Ajax Queue</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	
</head>
<body>
<?php
require_once "/usr/share/centreon/www/include/Mumsys/common.php";
?>
<script type="text/javascript">$(document).ready(function() {
	$(document).on("click","#clear",function() {
		$("#message").html("");
	});
    $(document).on("click","#proses",function() {
<?php
	foreach ($busdata as $key => $val) {
?>
			$.post("example.php",
		    {
		    	id: "<?php echo $val['id'];?>",
		    	ip: "<?php echo $val['ip'];?>",
		    	type: "<?php echo $val['type'];?>"
		    },
		    function(data, status){
		    	$('#message').append(data);
		    });
<?php
	}
?>
		    


    });
});
	</script>
<input type='button' id='proses' name='proses' class='button' value="proses!">
<input type='button' id='clear' name='clear' class='button' value="clear!">
<div id='message'></div>
</body>
</html>