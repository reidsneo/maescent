<script type="text/javascript" src="include/common/javascript/changetab.js"></script>
<script type="text/javascript" src="include/common/javascript/ajaxreq.js"></script>
<link rel="stylesheet" type="text/css" href="include/common/datatable.css"/>
<script type="text/javascript" src="include/common/datatable.js"></script>
<script type="text/javascript" src="./include/common/javascript/highchart.js"></script>

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
    .option{border: 1px solid gray;
    border-radius: 10px;
    color: gray;
    display: block;
    float: left;
    margin: 1px;
    padding: 7px;
    text-align: center;
    text-decoration: none;
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
            { "data": "id_alert" },
            { "data": "bus_id" },
            { "data": "bus_locate" },
            { "data": "msg_alert" },
            { "data": "statecheck" },
            { "data": "lastcheck" }
        ],
        "order": [[1, 'asc']],
        "columnDefs": [
        { "width": "1px", "targets": 0 },
	      { "width": "1px", "targets": 1 },
	      { "width": "1px", "targets": 2 },
	      { "width": "200px", "targets": 3 },
          { "width": "10px", "targets": 4 },
          { "width": "10px", "targets": 5 }
    	]
    } );
     
    // Add event listener for opening and closing details
    $('#example tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
   }

   loadtable("include/Mumsys/Realtimereport/datajson.php");

    $('#example tfoot th').each( function (i) {
        if (i>0){            
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
    
    //create a variable so we can pass the value dynamically
        var chartype = 'column';

        //On page load call the function setDynamicChart
        setDynamicChart(chartype);
        
        //jQuery part - On Click call the function setDynamicChart(dynval) and pass the chart type
        $('.option').click(function(){
            //get the value from 'a' tag
            var chartype = $(this).attr('id');
            setDynamicChart(chartype);
        });
        
        
        //function is created so we pass the value dynamically and be able to refresh the HighCharts on every click
        
        function setDynamicChart(chartype){
            $('#progresschart').highcharts({
                chart: {
                    type: chartype
                },
                title: {
                    text: 'Daily Bus Alert {/literal}{$datenow}{literal}'
                },
                xAxis: {
                    categories: [{/literal}{$busid}{literal}]
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Total Alert {/literal}{$datenow}{literal}'
                    }
                },
                plotOptions: {
                    //this need only for pie chart
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer'
                    }
                },
                series: [{
                    name: 'Alert Flag',
                    data: [
                    {/literal}{$busdata}{literal}
                    ]
                }]
            });
        }


    });
</script>
{/literal}
{$form.javascript}{$javascript}
<div id="rule_error" style="color: red"></div>
<form id="RuleForm" action="?p={$page}">
<input type="hidden" id="rule_id" name="rule_id" value="{$rule_id}">
<div class="headerTabContainer">
	<ul id="mainnav">
		<li class="a" id='c1'><a href="#" onclick="javascript:montre('1');">Dashboard</a></li>
		<li class="b" id='c2'><a href="#" onclick="javascript:montre('2');">Data Management</a></li>
	</ul>
</div>
<div id="tab1" class="tab">
<div style="margin: 0 auto; height: 50px; text-align:center;">
    <a href="javascript:void(0);" class="option" id="line">Line Chart</a>
    <a href="javascript:void(0);" class="option" id="bar">Bar Chart</a>
    <a href="javascript:void(0);" class="option" id="column">Column Chart</a>
    <a href="javascript:void(0);" class="option" id="pie">Pie Chart</a>
</div>
	<div id="progresschart" style="width:900px; height: 100%; margin: 0 auto"></div>
</div>
<div id="tab2" class="tab">
    <table id="example" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Bus ID</th>
                <th>Bus Locate</th>
                <th>Bus Alert</th>
                <th>State Check</th>
                <th>Last Check</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>No</th>
                <th>Bus ID</th>
                <th>Bus Locate</th>
                <th>Bus Alert</th>
                <th>State Check</th>
                <th>Last Check</th>
            </tr>
        </tfoot>
    </table>
</div>


</form>