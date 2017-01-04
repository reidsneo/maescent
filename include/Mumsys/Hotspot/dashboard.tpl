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
    #edtbusid{
        width: 65px;
    }
</style>
{/literal}

{literal}
<script type="text/javascript">
function format (d) {
    var out='';
    out +='<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<tr>'+
            '<td>No</td>'+
            '<td>User Uptime</td>'+
            '<td>User MacAddr</td>'+
            '<td>User Bytein</td>'+
            '<td>User Byteout</td>'+
            '<td>User State</td>'+
        '</tr>';
    var busuptime=d.bus_useruptime.split(",");
    var busmacaddr=d.bus_usermacaddr.split(",");
    var busbytein=d.bus_usertrfin.split(",");
    var busbyteout=d.bus_usertrfout.split(",");
    var state="";
    for (var i=0;i<=(busuptime.length-1);i++){
        if(busuptime[i]=="00:00:00"){
            state="<font color='green'>Active</font>";
        }else{
            state="Inactive";
        }
        out +='<tr>'+
            '<td>'+(i+1)+'</td>'+
            '<td>'+busuptime[i]+'</td>'+
            '<td>'+busmacaddr[i]+'</td>'+
            '<td>'+busbytein[i]+'</td>'+
            '<td>'+busbyteout[i]+'</td>'+
            '<td>'+state+'</td>'+
        '</tr>';
    }
    out +='</table>';
    if(busuptime.length!=1){
        return out;     
    }else{
        return false;   
    }
}
 
$(document).ready(function() {
   loadtable("include/Mumsys/Hotspot/datajson.php");

     $(document).on("change","#hotpicker",function() {
        $("#example").dataTable().fnDestroy();
        loadtable("include/Mumsys/Hotspot/datajson.php?method=selbydate&date="+$("#hotpicker").val());

     });

     $(document).on("click","#btngobusid",function() {

        $("#example").dataTable().fnDestroy();
        loadtable("include/Mumsys/Hotspot/datajson.php?method=selbybus&busid="+$("#edtbusid").val());
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
        "ajax": ajaxurl,
        "columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            { "data": "id_mon" },
            { "data": "bus_id" },
            { "data": "bus_ip" },
            { "data": "bus_state" },
            { "data": "bus_uptime" },
            { "data": "bus_bytein" },
            { "data": "bus_byteout" },
            { "data": "bus_totdata" },
            { "data": "bus_activeuser" },
            { "data": "isinternet" },
            { "data": "ismt" },
            { "data": "bus_ver" },
            { "data": "bus_boardname" },
            { "data": "date" }
        ],
        "order": [[1, 'asc']],
        "columnDefs": [
        { "width": "5px", "targets": 0 },
          { "width": "1px", "targets": 1 },
          { "width": "5px", "targets": 2 },
          { "width": "10px", "targets": 3 },
          { "width": "10px", "targets": 4 },
          { "width": "10px", "targets": 5 },
          { "width": "10px", "targets": 6 },
          { "width": "10px", "targets": 7 },
          { "width": "10px", "targets": 8 },
          { "width": "10px", "targets": 9 },
          { "width": "1px", "targets": 10 },
          { "width": "1px", "targets": 11 },
          { "width": "10px", "targets": 12 },
          { "width": "10px", "targets": 13 },
          { "width": "150px", "targets": 14 }
        ],
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
            if (aData['isinternet'] == "disconnected"){$('td:eq(10)', nRow).css('color', 'Red');}
            if (aData['ismt'] == "disconnected"){$('td:eq(11)', nRow).css('color', 'Red');}
        }
    } );
   }

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
    //$(".dt-buttons").append("<select class='dt-button buttons-html5' id='busstatedisp'><option value='0'>Show All Bus</option><option value='1'>Show Only Online Bus</option><option value='2'>Show Only Offline Bus</option></select>");

     $(document).on("change","#busstatedisp",function() {
        alert("include/Mumsys/Hotspot/test.php?state="+$("#busstatedisp").val());
        loadtable("include/Mumsys/Hotspot/test.php?state="+$("#busstatedisp").val());
     });


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
    

    RawDataChartWithMultiYAxis();//parseInt('{/literal}{$hotspotinterval}{literal}

    function addAxes(name, visiblity, index) {    
        var chart = $('#realploting').highcharts();
        if (visiblity == "hidden") {
            chart.addAxis({
                id: name,
                title: {
                    text: name
                },            
                lineWidth: 2,
                lineColor: '#08F',
                labels: {
                    format: '{value} ' + chart.series[index].tooltipOptions.valueSuffix,
                    style: {
                        color: '#4572A7'
                    }
                },
                opposite: false
            });
        } else {
            chart.get(name).remove();
        }
    }

    function RawDataChartWithMultiYAxis() {
        var a = 0;
        new Highcharts.Chart({
            title: {
                text: 'Bus Hotspot Traffic Data Monitoring'
            },
            subtitle: {
                text: '{/literal}{$datenow}{literal}'
            },
            chart: {
                renderTo: 'realploting',
                type: 'line',
                alignTicks: true,
                animation: Highcharts.svg, // don't animate in old IE
                events: {
                    load: function () {
                        // set up the updating of the chart each second                    
                        var series = this.series[0];
                        var series1 = this.series[1];
                        var series2 = this.series[2];
                        var dbintval = (parseInt('{/literal}{$hotspotinterval}{literal}')*1000);                 
                        setInterval(function () {                       
                            var shift = series.data.length > 10;                        
                            var x = a++, // current time                                      
                                y = parseFloat(Math.random());
                            $.post("include/Mumsys/Hotspot/datajson.php?method=crontask",{},
                                function(data, status){
                                   var bustrf=data.split(",");
                                    series.addPoint([x, parseInt(bustrf[0])], true, shift);
                                    series1.addPoint([x, parseInt(bustrf[1])], true, shift);
                                    series2.addPoint([x, parseInt(bustrf[2])], true, shift);
                                    dbintval = parseInt(bustrf[3]);
                                });
                        }, dbintval);
                    }
                }
            }, plotOptions: {
                series: {
                    animation: true,
                    events: {
                        click: function () {
                        },
                        legendItemClick: function (event) {
                            var visibility = this.visible ? 'visible' : 'hidden';
                            addAxes(this.name, visibility, this.index);
                        }
                    }
                }
            },
            tooltip: {
                shared: true
            },
            yAxis: {
                title: {
                    text: 'Bytein Max'
                },
                lineWidth: 2,
                lineColor: '#F33'
            },
            legend: {
                enabled: true
            },
            series: [{
                name: 'Bytein Max',
                data: [],
                tooltip: { valueSuffix: " Byte" }
            }, {
                name: 'Byteout Max',
                data: [],
                visible: false,
                tooltip: { valueSuffix: " Byte" }
            }, {
                name: 'Total Data',
                data: [],
                visible: false,
                tooltip: { valueSuffix: " Byte" }
            }]
        });
    }

    if(parseInt('{/literal}{$hotspotcron}{literal}')!=1){
        $("#realploting").remove();
        $("#tab1").html("<div class='offlinemsg'>Hotspot Monitoring Service is OFFLINE</div>");
    }


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
        <li class="b" id='c2'><a href="#" onclick="javascript:montre('2');">Data Management</a></li>
    </ul>
</div>
<div id="tab1" class="tab">
    <div id="realploting" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
</div>
<div id="tab2" class="tab">
<div id='datepick'>
    Display from date :
    <select id='hotpicker'>
        {foreach from=$datehotspot item=label key=key}
          <option value="{$label}" {if $data.key == $key} selected="selected" {/if}>{$label}
          </option>
          {/foreach}
    </select>
</div>
<div>
    Trace All Day BUS ID : <input type="text" name="edtbusid" id='edtbusid'><input type="button" value='Go' id='btngobusid'>
</div>

    <table id="example" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th></th>
                <th>No</th>
                <th>Bus ID</th>
                <th>Bus IP</th>
                <th>Bus State</th>
                <th>Bus Uptime</th>
                <th>Bus Bytein</th>
                <th>Bus Byteout</th>
                <th>Bus TotalByte</th>
                <th>Hotspot ActiveUser</th>
                <th>Internet</th>
                <th>MT</th>
                <th>Router OS</th>
                <th>Boardname</th>
                <th>Date</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th></th>
                <th>No</th>
                <th>Bus ID</th>
                <th>Bus IP</th>
                <th>Bus State</th>
                <th>Bus Uptime</th>
                <th>Bus Bytein</th>
                <th>Bus Byteout</th>
                <th>Bus TotalByte</th>
                <th>Hotspot ActiveUser</th>
                <th>Internet</th>
                <th>MT</th>
                <th>Router OS</th>
                <th>Boardname</th>
                <th>Date</th>
            </tr>
        </tfoot>
    </table>
</div>


</form>