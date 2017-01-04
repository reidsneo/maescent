<script type="text/javascript" src="include/common/javascript/changetab.js"></script>
<link rel="stylesheet" type="text/css" href="include/common/datatable.css"/>
<style type="text/css">
	#tbhwscan > tbody > tr > td{
		padding-left:2px;padding-right:2px;
	}
</style>
<script type="text/javascript" src="include/common/datatable.js"></script>
		<script type="text/javascript">
			jQuery(document).ready(function($) {				
				$(document).on("change","#selconces",function(){					
					 $.post("include/Mumsys/Toolkits/act_toolkits.php",
			          {
			          	method:"selbusid",
			          	hgroup:$("#selconces").val(),
			          },
			          function(data, status){
			          	$('#selbusid').html("<option>-Select BUS ID-</option>");
			          	$('#selbusid').append(data);
			          });
				});

				$(document).on("click","#btngo",function(){
				$("#btngo").attr('value', 'Please Wait...');
				$("#btngo").attr('disabled','disabled');
					 $.post("include/Mumsys/Toolkits/act_toolkits.php",
			          {
			          	method:"exescan",
			          	busid:$("#selbusid option:selected").text(),
			          	busip:$("#selbusid option:selected").val(),
			          	hgroup:$("#selconces option:selected").val(),
			          },
			          function(data, status){
			          	$('#example').DataTable().ajax.reload();
			          	alert(data);
			          	$("#btngo").removeAttr('disabled');
			          	$("#btngo").attr('value', 'Hardware Scan!');
			          });
				});


				 function loadtable(ajaxurl){
			   	 var table = $('#example').DataTable( {
			   	 	"bRetrieve" : true,
			    	dom: 'Bfrtip',
			        buttons: [
			            'copyHtml5',
			            'excelHtml5',
			            'csvHtml5'
			        ],
			        "ajax": ajaxurl+"?loadmeth=hardwarecheckload",
			        "columns": [
			            {
			                "orderable":      false,
			                "data":           null,
			                "defaultContent": ''
			            },
			            { "data": "id_log" },
			            { "data": "busid" },
			            { "data": "groupid" },
			            { "data": "status" },
			            { "data": "date" }
			        ],
			        "order": [[1, 'desc']],
			        "columnDefs": [
			        { "width": "5px", "targets": 0 },
				      { "width": "1px", "targets": 1 },
				      { "width": "1px", "targets": 2 },
				      { "width": "1px", "targets": 3 },
				      { "width": "1px", "targets": 4 }
			    	],
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
            if (aData['status'] == "NOK"){$('td:eq(4)', nRow).css('color', 'Red');}
            if (aData['status'] == "OK"){$('td:eq(4)', nRow).css('color', 'green');}
        }

			    } );
			     
			   }

			   loadtable("include/Mumsys/Toolkits/act_toolkits.php");
			     $('#example tfoot th').each( function (i) {
			        if (i>1){            
			            var title = $(this).text();
			            $(this).html( '<input class="searchfoot" type="text" placeholder="Search here" />' );
			        }
			    } );
			 
			    // DataTable
			    var table = $('#example').DataTable();
			    // Apply the search
			    table.columns().every( function () {
			        var that = this;
			        $( 'input', this.footer() ).on( 'keyup change', function () {
			            if ( that.search() !== this.value ) {
			                that
			                    .search( this.value )
			                    .draw();
			            }
			        });
			    });

			});
		</script>

<table id='tbhwscan'>
	<tr>
		<td>
			<select id="selconces">
				<option>-Select Concession-</option>
				<?php				
					require_once "/usr/share/centreon/www/include/common/common-Func.php";
					require_once("/usr/share/centreon/www/class/centreonDB.class.php");
					$DBStorage = new CentreonDB("centreon");
					$conceslist=$DBStorage->getAll("SELECT hg_name FROM `centreon`.`hostgroup` WHERE hg_name LIKE '%Racks%'");
					foreach ($conceslist as $key => $val) {
						echo "<option value='".$val['hg_name']."'>".str_replace("-Racks","",$val['hg_name'])."</option>";
					}
				 ?>
			</select>
		</td>
		<td>
			<select id="selbusid">
				<option>-Select BUS ID-</option>
			</select>
		</td>
		<td><input type="button" name="btngo" id="btngo" value="Hardware Scan!"></td>
	</tr>
</table>
<br><br>
<form id="RuleForm" action="?p={$page}">
<input type="hidden" id="rule_id" name="rule_id" value="{$rule_id}">
<div class="headerTabContainer">
	<ul id="mainnav">
		<li class="a" id='c1'><a href="#" onclick="javascript:montre('1');">Data Log</a></li>
	</ul>
</div>
<div id="tab1" class="tab">
	 <table id="example" class="display" cellspacing="0" width="60%">
        <thead>
            <tr>
            	<th></th>
                <th>No</th>
                <th>Bus ID</th>
                <th>Concession</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
            	<th></th>
                <th>No</th>
                <th>Bus ID</th>
                <th>Concession</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </tfoot>
    </table>
</div>
</form>