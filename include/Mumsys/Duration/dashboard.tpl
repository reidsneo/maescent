<script type="text/javascript" src="include/common/javascript/changetab.js"></script>
<script type="text/javascript" src="include/common/javascript/ajaxreq.js"></script>
<link rel="stylesheet" type="text/css" href="include/common/datatable.css"/>
<script type="text/javascript" src="include/common/datatable.js"></script>

{literal}
<style type="text/css">
	.headerTabContainer {
    	font-size: 13px;
	}
	.dataTables_wrapper {
	    font-size: 12px;
	}
	td.details-control {
	    background: url('img/details_open.png') no-repeat center center;
	    cursor: pointer;
	}
	tr.shown td.details-control {
	    background: url('img/details_close.png') no-repeat center center;
	}
    .searchfoot{
        width:60px;
    }
    #example>tbody>tr>td, th { text-align: center }
    .offlinemsg{
        background-color: #a5545b;
        border: 1px solid #a5545b;
        border-radius: 10px;
        color: white;
        font-size: 15px;
        font-weight: bold;
        padding: 5px;
        text-align: center;
    }
</style>
{/literal}

{literal}
<script type="text/javascript">
$(document).ready(function() {
	 function loadtable(ajaxurl){
   	 var table = $('#example').DataTable( {
   	 	"bRetrieve" : true,
    	dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5'
        ],
        "ajax": ajaxurl,
        "columns": [
            {
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            { "data": "bus_id" },
            { "data": "rut_updur" },
            { "data": "rut_downdur" },
            { "data": "aton_updur" },
            { "data": "aton_downdur" },
            { "data": "tun_updur" },
            { "data": "tun_downdur" },
            { "data": "wifi_updur" },
            { "data": "date" }
        ],
        "order": [[1, 'asc']],
        "columnDefs": [
        { "width": "5px", "targets": 0 },
	      { "width": "1px", "targets": 1 },
	      { "width": "1px", "targets": 2 },
	      { "width": "1px", "targets": 3 },
	      { "width": "1px", "targets": 4 },
	      { "width": "1px", "targets": 5 },
	      { "width": "1px", "targets": 6 },
	      { "width": "1px", "targets": 7 },
	      { "width": "1px", "targets": 8 }
    	]

    } );
     
   }

   loadtable("include/Mumsys/Duration/datajson.php");
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
{/literal}

{*
{literal}
<script type="text/javascript">
jQuery( document ).ready(function( $ ) {
	alert("tes");
 makeRequest('http://api.bf4stats.com/api/playerInfo?plat=xone&name=davetherave2010&output=json','POST', function(battlefieldData){
alert(battlefieldData);
})
});

</script>
{/literal}
*}


{$form.javascript}{$javascript}
<div id="rule_error" style="color: red"></div>
<form id="RuleForm" action="?p={$page}">
<input type="hidden" id="rule_id" name="rule_id" value="{$rule_id}">
<div class="headerTabContainer">
	<ul id="mainnav">
		<li class="a" id='c1'><a href="#" onclick="javascript:montre('1');">Dashboard</a></li>
		<!--<li class="b" id='c2'><a href="#" onclick="javascript:montre('2');">Data Management</a></li>!-->
	</ul>
</div>
<div id="tab1" class="tab">
	 <table id="example" class="display" cellspacing="0" width="60%">
        <thead>
            <tr>
            	<th></th>
                <th>Bus ID</th>
                <th>RouterUp Duration</th>
                <th>RouterDown Duration</th>
                <th>AtonUp Duration</th>
                <th>AtonDown Duration</th>
                <th>TunnelUp Duration</th>
                <th>TunnelDown Duration</th>
                <th>WifiUP Duration</th>
                <th>Date</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
            	<th></th>
                <th>Bus ID</th>
                <th>RouterUp Duration</th>
                <th>RouterDown Duration</th>
                <th>AtonUp Duration</th>
                <th>AtonDown Duration</th>
                <th>TunnelUp Duration</th>
                <th>TunnelDown Duration</th>
                <th>WifiUP Duration</th>
                <th>Date</th>
            </tr>
        </tfoot>
    </table>
</div>
<div id="tab2" class="tab">
	<div id="realploting" style="min-width: 310px; height: 400px; margin: 0 auto"></div>   
</div>


</form>