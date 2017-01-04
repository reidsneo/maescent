    <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/ax5ui/ax5ui-menu/master/dist/ax5menu.css" />
    <script type="text/javascript" src="https://cdn.rawgit.com/ax5ui/ax5core/master/dist/ax5core.min.js"></script>
    <script type="text/javascript" src="https://cdn.rawgit.com/ax5ui/ax5ui-menu/master/dist/ax5menu.min.js"></script>
    <script type="text/javascript" src="include/common/javascript/changetab.js"></script>
    {literal}
    <script type="text/javascript">
      jQuery(document).ready(function( $ ) {
        var searchdiv="";
        $(document).on("click","#btnsrc",function() {
          var idsearch="#"+$('#bussrc').val();
          searchdiv=$('#bussrc').val();
          if($(idsearch).length>0){
            $(".blink").removeClass("blink");
            $(idsearch).addClass("blink");
            scroll_to(idsearch);
            $('#bussrc').val("");
          }else{
            alert("host not found!");
          }
        });

        $('#bussrc').keydown(function (e){
          if(e.keyCode == 13){
            var idsearch="#"+$('#bussrc').val();
            searchdiv=$('#bussrc').val();
            if($(idsearch).length>0){
              $(".blink").removeClass("blink");
              $(idsearch).addClass("blink");
              scroll_to(idsearch);
              $('#bussrc').val("");
            }else{
              alert("host not found!!");
            }
          }
        })

        function scroll_to(div){
          $('html, body').animate({
            scrollTop: ($(div).offset().top-200)
          },1000);
        } 

        var ajax="";
        $(document).on({
          mouseenter: function(e){
            $(".draggable.tip .tipkiri").css('display','none');
            $(".draggable.tip .tipkanan").css('display','none');
            if(this.id==searchdiv){
              $(".blink").removeClass("blink");
            }

            if($(".ax5-ui-menu").length){
            }else{
              e = e || window.event;
              var pageX = e.pageX;
              var pageY = e.pageY;
              if (pageX === undefined) {
                pageX = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
                pageY = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
              }
            //console.log([pageX, pageY]);
            $(".tipbody").html("<center>Loading...</center>");
            ajax=$.ajax({
              type: 'GET',
              //url: 'lib/libssh/fetchbushw.php',
              url: 'include/Mumsys/Busmap/hover_busmap.php',
              //data: {'busip':$(this).attr('net')},
              data: {'busid':$(this).attr('id')},
              success: function(result){
                $(".tipbody").html(result);
              }
            });
            if(pageX>1000){
              $("#"+this.id+"> .tipdata").addClass("tipkanan");
              $("#"+this.id+"> .tipdata").removeClass("tipkiri");
              $("#"+this.id+"> .tipkanan ").css('display','block');
            }else{
              $("#"+this.id+"> .tipdata").removeClass("tipkanan");
              $("#"+this.id+"> .tipdata").addClass("tipkiri");
              $("#"+this.id+"> .tipkiri ").css('display','block');
            }
          }
        },
        mouseleave: function(){
          $(".draggable.tip .tipkiri").css('display','none');
          $(".draggable.tip .tipkanan").css('display','none');
          if(ajax){ 
            ajax.abort();
          }
        }
      }, '.draggable');

        $(document).on("hover","body",function() {
 // $(".draggable.tip .tipkiri").css('display','none');
 // $(".draggable.tip .tipkanan").css('display','none');
});

        $(document).on("click","body",function() {
//  $(".draggable.tip .tipkiri").css('display','none');
//  $(".draggable.tip .tipkanan").css('display','none');
});
        $(document).on("click","#btnaddnewhost",function(){
          loaddialog("/centreon/main.php?p=8&act=a&min=1");
        });

        $('#btnsortgrid').on('click', function() {        
          jQuery.ajax({
            type: 'GET',
            url: 'include/Mumsys/Busmap/act_busmap.php',
            data: {'method':'sortgridasc','groupid':$('#selbusgroup').val()},
            success: function(sort){
              loadbusmap();
              alert("Grid has been sorted!, Please wait a moment...");
            }
          });
        });

        $(document).on("click","#addnewhost",function(){
          $("#hostmanage").html("");
          $("#hostmanage").html('<iframe style="border: 0px; " src="/centreon/main.php?p=8&act=a&min=1" width="100%" height="100%"></iframe>');
        });

        $(document).on("click","#listhost",function(){
          $("#hostmanage").html("");
          $("#hostmanage").html('<iframe style="border: 0px; " src="/centreon/main.php?p=601&min=1" width="100%" height="100%"></iframe>');
        });


              function loadconfig(busid,objtype,cfgtype){
                  jQuery.ajax({
                    type: 'GET',
                    url: 'include/Mumsys/Busmap/act_busmap.php',
                    data: {'method':'loadcfg','busid':busid,'type':objtype,'cfgtype':cfgtype},
                    success: function(loadcfg){
                    $("#cfgbusid").val("");
                    $("#cfgobjecttype").val("");
                    $("#cfgmethod").val("");
                      if(cfgtype=="ssh"){
                        $("#loadcfghtml").html("<tr><td>Username : </td><td><input type='text' id='unamecfg'></td></tr><tr><td>Password : </td><td><input type='text' id='pwdcfg'></td></tr><tr><td>Port : </td><td><input type='text' id='portcfg'></td></tr>");                        
                        var dtcfg = JSON.parse(loadcfg);
                        $("#unamecfg").val(dtcfg.u);
                        $("#pwdcfg").val(dtcfg.pw);
                        $("#portcfg").val(dtcfg.p);
                        $("#cfgbusid").val(busid);
                        $("#cfgobjecttype").val(objtype);
                        $("#cfgdialog").attr('title','SSH Configuration');
                        $("#ui-dialog-title-cfgdialog").html('SSH Configuration');
                        $("#cfgmethod").val("ssh");
                      }else if(cfgtype=="ftp"){
                        $("#loadcfghtml").html("<tr><td>Username : </td><td><input type='text' id='unamecfg'></td></tr><tr><td>Password : </td><td><input type='text' id='pwdcfg'></td></tr><tr><td>Port : </td><td><input type='text' id='portcfg'></td></tr>");                        
                        var dtcfg = JSON.parse(loadcfg);
                        $("#unamecfg").val(dtcfg.u);
                        $("#pwdcfg").val(dtcfg.pw);
                        $("#portcfg").val(dtcfg.p);
                        $("#cfgbusid").val(busid);
                        $("#cfgobjecttype").val(objtype);
                        $("#cfgdialog").attr('title','FTP Configuration');
                        $("#ui-dialog-title-cfgdialog").html('FTP Configuration');
                        $("#cfgmethod").val("ftp");
                      }else if(cfgtype=="wb"){
                        $("#cfgdialog").attr('title','Winbox Configuration');
                        $("#ui-dialog-title-cfgdialog").html('Winbox Configuration');
                        $("#loadcfghtml").html("<tr><td>Username : </td><td><input type='text' id='unamecfg'></td></tr><tr><td>Password : </td><td><input type='text' id='pwdcfg'></td></tr>");                        
                        var dtcfg = JSON.parse(loadcfg);
                        $("#unamecfg").val(dtcfg.u);
                        $("#pwdcfg").val(dtcfg.pw);
                        $("#cfgbusid").val(busid);
                        $("#cfgobjecttype").val(objtype);
                        $("#cfgmethod").val("wb");
                      }
                      $('#cfgdialog').dialog();
                      return false;
                    }
                  });
              }

        function initpopmenu()
        {
          var menu = new ax5.ui.menu({
            position: "absolute",
            icons: {
            'arrow': '▸'
          },
                items: [{
                  html: function () {
                    return '<div style="text-align: center;" id="buspoptit"></div>';
                  }
                }
                ,{divide: true},
                {
                    label: "Tools",
                    items: [
                        {label: "Info Detail"},
                        {label: "Ping"},
                        {label: "SSH"},
                        {label: "Telnet"},
                        {label: "Winbox"},
                        {type: 1, label: "FileZilla Hda6 mcom"},
                        {label: "FileZilla Check SWF Hang"},
                        {label: "FileZilla IS_FILES"},
                        {label: "FileZilla CPU"},
                        {label: "Live Video"},
                        {label: "Live IPCam 1"},
                        {label: "Live IPCam 2"},
                        {label: "Live IPCam 3"},
                        {label: "Live IPCam 4"},
                        {label: "IPCam 1"},
                        {label: "IPCam 2"},
                        {label: "IPCam 3"},
                        {label: "IPCam 4"},
                        {label: "IPCam 5"},
                        {label: "IPCam 6"}
                    ]
                },
                {
                    label: "Config",
                    items: [
                        {label: "host"},
                        {label: "type"},
                        {label: "ssh"},
                        {label: "ftp"},
                        {label: "winbox"}
                    ]
                }
            ]
        });
                var parentid=0;
                var ssh="";
                var ftp="";
                var wb="";
                var iptool="";
                var method="";
                var ipnet="";
                var idty="";
                $('.draggable:not(.draggable>.at,.draggable>.tu,.draggable>.tipdata,.draggable>.tipkiri,.draggable>.tipkanan)').on('click', function(e) {
                  $("#opttype>#idpar").val($(this).attr('hostnm'));
                  $(".draggable.tip .tipkiri").css('display','none');
                  $(".draggable.tip .tipkanan").css('display','none');
                  if($("#selbusgroup").val()=="78" || $("#selbusgroup").val()=="79"){
                    menu.popup(e);
                  }else{
                    menu.popup(e, {filter: function () {return this.type != 1;}});
                  }
                  ax5.util.stopEvent(e);
                  method="r";
                  parentid=$(this).attr('hostnm');
                  idty=$(this).attr('idty');
                  ipnet=$(this).attr('net');
                  hostid=$(this).attr('hostid');
                  hostnm=$(this).attr('hostnm');
                  iptool=$(this).attr('net');
                  $("#buspoptit").html($(this).attr('ty')+" "+parentid+"<br>"+iptool);
                  ssh=$(this).attr('ssh').split('|');
                  ftp=$(this).attr('ftp').split('|');
                  wb=$(this).attr('wb').split('|');
                });

                $('.draggable>.at').on('click', function(e) {
                  $("#opttype>#idpar").val($(this).attr('hostnm'));
                  $(".draggable.tip .tipkiri").css('display','none');
                  $(".draggable.tip .tipkanan").css('display','none');
                  ssh=$(this).attr('ssh').split('|');
                  ftp=$(this).attr('ftp').split('|');
                  wb=$(this).attr('wb').split('|');
                  idty=$(this).attr('idty');
                  iptool=$("#"+parentid).attr('aton');
                  ipnet=$("#"+parentid).attr('net');
                  hostid=$(this).attr('hostid');
                  hostnm=$(this).attr('hostnm');
                  method="a";
                  if (iptool == 0){
                    alert("This bus didn't have Aton!");
                    return false;
                  }else{                    
                      if($("#selbusgroup").val()=="78" || $("#selbusgroup").val()=="79"){
                        menu.popup(e);
                      }else{
                        menu.popup(e, {filter: function () {return this.type != 1;}});
                      }
                    ax5.util.stopEvent(e);
                    $("#buspoptit").html("Bus Aton "+parentid+"<br>"+iptool);              
                  }
                  console.log(parentid+"at");
                });

                $('.draggable>.tu').on('click', function(e) {
                  $("#opttype>#idpar").val($(this).attr('hostnm'));
                  $(".draggable.tip .tipkiri").css('display','none');
                  $(".draggable.tip .tipkanan").css('display','none');
                  ssh=$(this).attr('ssh').split('|');
                  ftp=$(this).attr('ftp').split('|');
                  wb=$(this).attr('wb').split('|');
                  idty=$(this).attr('idty');
                  iptool=$("#"+parentid).attr('tunnel');
                  ipnet=$("#"+parentid).attr('net');
                  hostid=$(this).attr('hostid');
                  hostnm=$(this).attr('hostnm');
                  method="t";            
                  if (iptool == 0){
                    alert("This bus didn't have Tunnel!");
                    return false;
                  }else{                    
                    if($("#selbusgroup").val()=="78" || $("#selbusgroup").val()=="79"){
                      menu.popup(e);
                    }else{
                      menu.popup(e, {filter: function () {return this.type != 1;}});
                    }
                    ax5.util.stopEvent(e);
                    $("#buspoptit").html("Bus Tunnel "+parentid+"<br>"+iptool);              
                  }
                  console.log(parentid+"tu");
                });

                menu.onClick = function () {
                 $(".draggable.tip .tipkiri").css('display','none');
                 $(".draggable.tip .tipkanan").css('display','none');
                 console.log(this.label);
                 if(this.label=="SSH"){
                  //
                  if (iptool == 0){
                    if(method=="a"){
                      alert("This bus didn't have Aton!");
                      return false;
                    }else if(method=="t"){
                      alert("This bus didn't have Tunnel!");
                      return false;
                    }
                  }else{
                    //console.log('apprun:putty/'+iptool+'/'+ssh[2]+'/'+ssh[0]+'/'+ssh[1]);
                    window.location = 'apprun:putty/'+iptool+'/'+ssh[2]+'/'+ssh[0]+'/'+ssh[1];
                  }
                }else if(this.label=="Live Video"){
                  window.location = 'apprun:live/'+ipnet;
                }else if(this.label=="Telnet"){
                  console.log('apprun:telnet/'+iptool);
                  window.location = 'apprun:telnet/'+iptool;
                }else if(this.label=="Winbox"){
                  console.log('apprun:winbox/'+iptool+'/'+wb[0]+'/'+wb[1]);
                  window.location = 'apprun:winbox/'+iptool+'/'+wb[0]+'/'+wb[1];
                }else if(this.label=="FileZilla Check SWF Hang"){
                  window.location = 'apprun:filezillaswf/'+iptool+'/'+ftp[2]+'/'+ftp[0]+'/'+ftp[1];
                  console.log('apprun:filezillaswf/'+iptool+'/'+ftp[2]+'/'+ftp[0]+'/'+ftp[1]);
                }else if(this.label=="FileZilla IS_FILES"){
                  console.log('apprun:filezillafiles/'+iptool);
                  window.location = 'apprun:filezillafiles/'+iptool+'/'+ftp[2]+'/'+ftp[0]+'/'+ftp[1];
                }else if(this.label=="FileZilla CPU"){
                  console.log('apprun:filezillacpu/'+iptool);
                  window.location = 'apprun:filezillacpu/'+iptool+'/'+ftp[2]+'/'+ftp[0]+'/'+ftp[1];
                }else if(this.label=="Live IPCam 1"){
                  console.log('apprun:liveipcam1/'+ipnet);
                  window.location = 'apprun:liveipcam1/'+ipnet+'/81/1';
                }else if(this.label=="Live IPCam 2"){
                  console.log('apprun:liveipcam2/'+ipnet);
                  window.location = 'apprun:liveipcam2/'+ipnet+'/81/2';
                }else if(this.label=="Live IPCam 3"){
                  console.log('apprun:liveipcam3/'+ipnet);
                  window.location = 'apprun:liveipcam3/'+ipnet+'/81/3';
                }else if(this.label=="Live IPCam 4"){
                  console.log('apprun:liveipcam4/'+ipnet);
                  window.location = 'apprun:liveipcam4/'+ipnet+'/81/4';
                }else if(this.label=="IPCam 1"){
                  console.log('apprun:ipcam1/'+ipnet);
                  window.location = 'apprun:ipcam1/'+ipnet+'/3001/1';
                }else if(this.label=="IPCam 2"){
                  console.log('apprun:ipcam2/'+ipnet);
                  window.location = 'apprun:ipcam2/'+ipnet+'/3002/1';
                }else if(this.label=="IPCam 3"){
                  console.log('apprun:ipcam3/'+ipnet);
                  window.location = 'apprun:ipcam3/'+ipnet+'/3003/1';
                }else if(this.label=="IPCam 4"){
                  console.log('apprun:ipcam4/'+ipnet);
                  window.location = 'apprun:ipcam4/'+ipnet+'/3004/1';
                }else if(this.label=="IPCam 5"){
                  console.log('apprun:ipcam5/'+ipnet);
                  window.location = 'apprun:ipcam5/'+ipnet+'/3005/1';
                }else if(this.label=="IPCam 6"){
                  console.log('apprun:ipcam6/'+ipnet);
                  window.location = 'apprun:ipcam6/'+ipnet+'/3006/1';
                }else if(this.label=="FileZilla Hda6 mcom"){
                  console.log('apprun:filezillahda6/'+iptool);
                  window.location = 'apprun:filezillahda6/'+iptool+'/'+ftp[2]+'/'+ftp[0]+'/'+ftp[1];
                }else if(this.label=="Info Detail"){
                  detailbus(parentid,iptool);
                }else if(this.label=="Ping"){
                  console.log('apprun:ping/'+iptool);
                  window.location = 'apprun:ping/'+iptool;
                /*
                CONFIGURATION
                */
                }else if(this.label=="host"){
                  loaddialog("/centreon/main.php?p=8&min=1&act=c&hostid="+hostid);
                }else if(this.label=="type"){
                  typedialog(idty);
                }else if(this.label=="ssh"){
                  loadconfig(parentid,method,"ssh");
                }else if(this.label=="ftp"){
                  loadconfig(parentid,method,"ftp");
                }else if(this.label=="winbox"){
                  loadconfig(parentid,method,"wb");
                }
              };
        }

        function detailbus(parentid,busip)
        {
          var $dialog = $('#dialogdetail').dialog({
                           autoOpen: false,
                           modal: true,
                           height: 655.167,
                           width: 750.167,
                           title:"Host Detail Information"
                       });
                       $dialog.dialog('open');

          $.ajax({
            type: 'GET',
              url: 'include/Mumsys/Busmap/act_busmap.php?method=lastdailyalert&busid='+parentid,
              success: function(res){
                //$("#dtllstalert").html(res);
                $("#busidnum").html(parentid);
                var arralertdtl = JSON.parse(res);
                if (arralertdtl.length > 0) {
                  for (var i = 0; i < arralertdtl.length; i += 1) {
                    $("#dtlrec").html(arralertdtl[i].is_recording);
                    $("#dtlnvs").html(arralertdtl[i].is_nvs_offline);
                    $("#dtlssd").html(arralertdtl[i].is_ssd);
                    $("#dtllux").html(arralertdtl[i].is_pftlux);
                    $("#dtltemp").html(arralertdtl[i].is_pfttemp);
                  }
                }
                return false;
              }
            });

                $("#datahw").html("");
                $("#dataapps").html("");
                $("#datamod").html("");
                $("#dataconf").html("");
          $.ajax({
            type: 'GET',
              url: 'include/Mumsys/Busmap/act_busmap.php?method=hardwareinfo&busid='+parentid,
              success: function(res){
                var hardwareinfo = JSON.parse(res);
                var swinfo=hardwareinfo.general[0].swinfo_list;
                var uname=hardwareinfo.general[0].uname;
                var camlist=hardwareinfo.general[0].cam_list;
                var camaddr=hardwareinfo.general[0].cam_addr;
                var camtype=hardwareinfo.general[0].cam_type;
                var tftnum=hardwareinfo.general[0].num_tft;
                var tfttype=hardwareinfo.general[0].tft_type;
                var tftlist=hardwareinfo.general[0].tft_lst;
                var taginfo=hardwareinfo.taginfo[0].sn;
                var ipcparam=hardwareinfo.general[0].ipc_param;
                var mfmrecstop=hardwareinfo.general[0].mfm_rec_stop;

                var confrec=hardwareinfo.general[0].cam_rec;
                var confreclen=hardwareinfo.general[0].rec_length;
                var confstrlive=hardwareinfo.general[0].cam_str_live;
                var confstrremote=hardwareinfo.general[0].cam_str_remote;
                var confslidetoplay=hardwareinfo.general[0].slide_toplay;
                var conflayout=hardwareinfo.general[0].layout;
                var confbrand=hardwareinfo.general[0].brand;
                var confpftsts=hardwareinfo.general[0].pft_stat;
                var confpftpavport=hardwareinfo.general[0].pft_pavport;
                var confntpaddr=hardwareinfo.general[0].ntp_addr;
                var conffbcap=hardwareinfo.general[0].fb_capture;
                var confsnmpsrv=hardwareinfo.general[0].snmp_srver;
                var modinfo="";
                 for (var i = 0; i < hardwareinfo.modinfo.length; i += 1) {
                    $("#datamod").append("Slot ["+hardwareinfo.modinfo[i].slot+"] "+hardwareinfo.modinfo[i].func_id.replace(/^0+/, "")+"-"+hardwareinfo.modinfo[i].mod_id.replace(/^0+/, ""));
                    if(i < hardwareinfo.modinfo.length){
                      $("#datamod").append("<br>");
                    }
                 }
                 $("#datahw").append("Camera List : "+camlist);
                 $("#datahw").append("<br>Camera Address : "+camaddr);
                 $("#datahw").append("<br>IPC_param : "+ipcparam);
                 $("#datahw").append("<br>MFM_record_stop : "+mfmrecstop);
                 $("#datahw").append("<br>Camera Type : "+camtype);
                 $("#datahw").append("<br>TFT Num : "+tftnum);
                 $("#datahw").append("<br>TFT List : "+tftlist);
                 $("#datahw").append("<br>TFT Type : "+tfttype);

                 $("#dataconf").append("<table>");
                 $("#dataconf").append("<tr><td><b>Configuration Name</b></td><td><b>Value</b></td></tr>");
                 $("#dataconf").append("<tr><td>Camera Rec</td><td>"+confrec+"</td></tr>");
                 $("#dataconf").append("<tr><td>Camera Rec Len</td><td>"+confreclen+"</td></tr>");
                 $("#dataconf").append("<tr><td>Camera Streaming</td><td>"+confstrlive+"</td></tr>");
                 $("#dataconf").append("<tr><td>Camera Remote</td><td>"+confstrremote+"</td></tr>");
                 $("#dataconf").append("<tr><td>Camera Slide to Play</td><td>"+confslidetoplay+"</td></tr>");
                 $("#dataconf").append("<tr><td>Layout</td><td>"+conflayout+"</td></tr>");
                 $("#dataconf").append("<tr><td>Brand</td><td>"+confbrand+"</td></tr>");
                 $("#dataconf").append("<tr><td>PFT Status</td><td>"+confpftsts+"</td></tr>");
                 $("#dataconf").append("<tr><td>PFT Pav Port</td><td>"+confpftpavport+"</td></tr>");
                 $("#dataconf").append("<tr><td>NTP Address</td><td>"+confntpaddr+"</td></tr>");
                 $("#dataconf").append("<tr><td>Fb Capture</td><td>"+conffbcap+"</td></tr>");
                 $("#dataconf").append("<tr><td>SNMP Server</td><td>"+confsnmpsrv+"</td></tr>");
                 $("#dataconf").append("<tr></table>");

                 var sw = swinfo.split(",");
                 $("#dataapps").append("<table id='swapptable'>");
                 $("#dataapps").append("<tr><td>Application Name</td><td>Version</td><td>Application Name</td><td>Version</td></tr>");
                 for (var i = 0; i < sw.length; i++) {
                  var swdat = sw[i].split("v");
                    if(i % 2 === 0){
                      $("#dataapps").append("<tr>");
                    }
                    $("#dataapps").append("<td>"+swdat[0].replace("_", " ").replace("-", " ")+"</td><td>"+swdat[1]+"</td>");
                    if(i % 2 === 0){
                      $("#dataapps").append("</tr>");
                    }
                 }
                 $("#dataapps").append("</table>");
                return false;
              }
            });

        }

      function loaddialog(urlnya)
      {
        var page = urlnya;
          var $dialog = $('<div></div>')
                       .html('<input type="button" value="Add Host" id="addnewhost"><input type="button" value="List Host" id="listhost"><div id="hostmanage"><iframe style="border: 0px; " src="' + page + '" width="100%" height="100%"></div></iframe>')
                       .dialog({
                           autoOpen: false,
                           modal: true,
                           height: 535.167,
                           width: 750.167,
                       });
                       $dialog.dialog('open');
      }

     $(document).on("change","#opttype>select",function(){
        $("img[name=image-swap]").attr("src",$('option:selected', this).attr('images'));
      });

     $(document).on("click","#btnsavetype",function(){
      var prev=$("#"+$("#idpar").val()).attr("idty");
      if($("#idpar").val()==0){
        alert("Please select host to edit!");
        return false;
      }

      $.ajax({
        type: 'GET',
        url: 'include/Mumsys/Busmap/act_busmap.php',
        data: {'method':'savetype','busid':$("#idpar").val(),'groupid':$("#selbusgroup").val(),'type':$("#opttype>select").val()},
        success: function(result){
         $("#"+$("#idpar").val()+">center>.imgicon").attr("src",$('option:selected',"#seltype").attr('images'));
         $("#"+$("#idpar").val()).attr("ty",$('#seltype option:selected').text());
         $("#"+$("#idpar").val()).attr("idty",$("#opttype>select").val());
         if(($("#opttype>select").val()==0) || (prev==0))
         {
          $.ajax({
            type: 'GET',
              url: 'include/Mumsys/Busmap/act_busmap.php?method=getbusdata&act=singlebus&hostid='+$("#idpar").val()+'&group_id='+$('#selbusgroup').val(),
              data: {},
              success: function(res){
               $("#"+$("#idpar").val()).replaceWith(res);
              }
            });
         }
           

         $("#opttype").dialog('close');
         alert("Change host "+$("#idpar").val()+" type success!");
       }
     });
    });

      function typedialog(idty)
      {
        var $dialog = $('#opttype').dialog({
          autoOpen: false,
          modal: true,
          height: 205.167,
          width: 370.167,
        });
        $dialog.dialog('open');
        $("#seltype").html("");
        $.ajax({
          type: 'GET',
          url: 'include/Mumsys/Busmap/act_busmap.php',
          data: {'method':'listtype'},
          success: function(result){
            var typearray = JSON.parse(result);        
            if (typearray.length > 0) {
              if(idty==0){
                  sel="selected='selected'";
                }else{
                  sel="";
                }
              $("<option value='0' images='' "+sel+">bus</option>").appendTo("#opttype>select");
              for (var i = 0; i < typearray.length; i += 1) {
                if(typearray[i].id_type==idty){
                  sel="selected='selected'";
                }else{
                  sel="";
                }
                $("<option value="+typearray[i].id_type+" images='img/typeicon/"+typearray[i].img_name+"'"+sel+">"+typearray[i].nm_type+"</option>").appendTo("#opttype>select");
              }
              $("img[name=image-swap]").attr("src",$('#opttype>select>option:selected').attr('images'));
            }

          }
        });

      }

        function loadbusmap(){
              $('#bussrc').val("");
              $.get('include/Mumsys/Busmap/act_busmap.php?method=getbusdata&group_id='+$('#selbusgroup').val(), function(data, status){
                $('#containment-wrapper').html(data);
                $(".draggable").draggable({
                  containment: "#containment-wrapper",
                  containment: "#containment-wrapper",
                  scroll: false,
                  accept: '.draggable',
                  stop: function (event, ui) {
                    var elem = $(this),
                    id = elem.attr('id'),
                    pos = elem.position(),
                    newleft = pos.left,
                    newtop = pos.top;
                    var offset = elem.offset();
                    var xPos = offset.left;
                    var yPos = offset.top;
                         // Get mouse position relative to drop target: 
                         var dropPositionX = (newleft + xPos);
                         var dropPositionY = (newtop -yPos);
                      //var currentPos = ui.helper.position();
                      //console.log(JSON.stringify(ui));
                      //console.log(JSON.stringify(ui));
                      $.ajax({
                        type: 'GET',
                        url: 'include/Mumsys/Busmap/act_busmap.php',
                        data: {'method':'savepos','id':id,'groupid':$('#selbusgroup').val(), 'x':newleft, 'y':newtop},
                        success: function(result){
                            //console.log(result);
                          }
                        });
                    }

                  });

                //free area
                initpopmenu();


              $('#savecfg').on('click', function() {
                  jQuery.ajax({
                    type: 'GET',
                    url: 'include/Mumsys/Busmap/act_busmap.php',
                    data: {'method':'savecfg','uname':$("#unamecfg").val(),'pwd':$("#pwdcfg").val(),'port':$("#portcfg").val(),'busid':$("#cfgbusid").val(),'type':$("#cfgobjecttype").val(),'cfgtype':$("#cfgmethod").val()},
                    success: function(savecfg){
                      alert("Configuration saved!");
                      $("#"+$("#cfgbusid").val()).attr($("#cfgmethod").val(),$("#unamecfg").val()+"|"+$("#pwdcfg").val()+"|"+$("#portcfg").val())
                      $("#cfgbusid").val("");
                      $("#cfgobjecttype").val("");
                      $("#cfgmethod").val("");
                      $('#cfgdialog').dialog("close");

                    }
                  });
              });

              $('.btncancel').on('click', function() {
                $('#cfgdialog').dialog("close");
                return false;
              });

            });
}

        $('#selbusgroup').on('change', function() {
             loadbusmap(); 
        });

  function checkisalive(){
      jQuery.ajax({
        type: 'GET',
        url: 'include/Mumsys/Busmap/act_busmap.php',
        data: {'method':'isalivecheck','groupid':$('#selbusgroup').val()},
        success: function(isalive){
          var isalivearray = JSON.parse(isalive);
          if (isalivearray.length > 0) {
            for (var i = 0; i < isalivearray.length; i += 1) {

              if(isalivearray[i].cntalert>0){
                $('.al-'+isalivearray[i].id_bus).addClass("alertcnt");
                $('.al-'+isalivearray[i].id_bus).html(isalivearray[i].cntalert);
              }else{
                $('.al-'+isalivearray[i].id_bus).removeClass("alertcnt");
              }

            }
          }
         setTimeout(function(){checkisalive()}, 5000);
        }
      });
 }


 function checkismodulealive(){
      jQuery.ajax({
        type: 'GET',
        url: 'include/Mumsys/Busmap/act_busmap.php',
        data: {'method':'ismodulealive','groupid':$('#selbusgroup').val()},
        success: function(ismodalive){
          var ismodalivearray = JSON.parse(ismodalive);
          if (ismodalivearray.length > 0) {
            for (var i = 0; i < ismodalivearray.length; i += 1) {

              if(ismodalivearray[i].rut_state=="OK"){
                $('#'+ismodalivearray[i].bus_id).removeClass("bggray bgred").addClass("bggreen");
              }else{
                $('#'+ismodalivearray[i].bus_id).removeClass("bggray bggreen").addClass("bgred");
              }

              if(ismodalivearray[i].aton_state=="OK"){
                $('#'+ismodalivearray[i].bus_id+">.at").removeClass("bggray bgred").addClass("bggreen");
              }else if(ismodalivearray[i].aton_state=="NOK"){
                $('#'+ismodalivearray[i].bus_id+">.at").removeClass("bggray bggreen").addClass("bgred");
              }

              if(ismodalivearray[i].tun_state=="OK"){
                $('#'+ismodalivearray[i].bus_id+">.tu").removeClass("bggray bgred").addClass("bggreen");
              }else if(ismodalivearray[i].tun_state=="NOK"){
                $('#'+ismodalivearray[i].bus_id+">.tu").removeClass("bggray bggreen").addClass("bgred");
              }
            }
          }
         setTimeout(function(){checkismodulealive()}, 5000);
        }
      });
 }

function checkiswifimon(){
      jQuery.ajax({
        type: 'GET',
        url: 'include/Mumsys/Busmap/act_busmap.php',
        data: {'method':'iswifimon','groupid':$('#selbusgroup').val()},
        success: function(ismodalive){
          var ismodalivearray = JSON.parse(ismodalive);
          if (ismodalivearray.length > 0) {
            for (var i = 0; i < ismodalivearray.length; i += 1) {
              $('#'+ismodalivearray[i].id_bus+">.wifiicon").attr("src","img/wifiok.png");
            }
          }
         setTimeout(function(){checkiswifimon()}, 10000);
        }
      });
 }


checkisalive();
checkismodulealive();
checkiswifimon();

  });
</script>
{/literal}

{literal}
<style type="text/css">
  .bgred{
    background-color: red;
    color:white;
  }
  .bggreen{
    background-color: #88b917;
    color:white;
  }
  .bggray{
    background-color: gray;
    color:white;
  }
  .draggable {
    height:90px;
    width: 90px;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none
    user-select: none;
    cursor:pointer;
    /*   padding: 0.5em;
    margin: 0 10px 10px 0;*/
    /* cursor:move;*/
    /*position: relative;*/
  }

  .draggable,.draggable a {
   /*  cursor:move;*/
 }
 #draggable, #draggable2 {
   /* margin-bottom:20px;*/
   /*  cursor:move;*/
 }
 #draggable {
   /*  cursor: move;*/
 }
 #draggable2 {
  cursor: e-resize;
}
#containment-wrapper {
  width: 100%;
  height:1500px;
  /* border:2px solid #ccc;
 padding: 10px;*/
}
h3 {
  clear: left;
}

.mnpver {
    left: 1px;
    position: absolute;
    top: 40px;
    background-color: black;
    width: 45px;
}
.wifiicon {
    left: 3px;
    position: absolute;
    top: 68px;
}
.nm{
  font-size: 14px;
  font-weight: bold;
  padding:5px;    
  position: absolute;
  left: 0;
  top: 10px;
}
.at{
  font-size: 14px;
  font-weight: bold;
  padding:8px;    
  border: 1px solid black;
  position: absolute;
  right: 1px;
  top: 1px;
  display: block;
}
.tu{
  display: block;
  border: 1px solid black;
  font-size: 14px;
  font-weight: bold;
  padding: 8px 9px 8px 8px;
  position: absolute;
  right: 1px;
  top: 37px;
}
.alertcnt{    
  background-color: gold;
  border-radius: 10px;
  color: #434343;
  font-size: 11px;
  font-weight: bold;
  left: 25px;
  padding: 1px 4px;
  position: absolute;
  top: 70px;
}

.draggable.tip {
  position: relative;
}

@keyframes showNav {
  from {opacity: 0;}
  to {opacity: 1;}
}
.draggable.tip .tipkiri:after{    
  border-color: transparent rgba(0, 0, 0, 0.89) transparent transparent;
  border-style: solid;
  border-width: 24px;
  content: "";
  height: 0;
  left: -48px;
  position: absolute;
  top: 10px;
  width: 0;
}
.draggable.tip .tipkanan:after{    
  border-color: transparent transparent transparent rgba(0, 0, 0, 0.89);
  border-style: solid;
  border-width: 24px;
  content: "";
  height: 0;
  left: 569px;
  position: absolute;
  top: 10px;
  width: 0;
}
#bussrc{
  width:100px;
}
.draggable.tip .tipkiri {    
  background: rgba(0, 0, 0, 0.89) none repeat scroll 0 0 !important;
  border-radius: 0 3.2px 3.2px 0;
  color: rgb(255, 255, 255);
  display: none;
  font-size: 22px;
  line-height: 48px;
  padding: 0 11px 0 8px;
  position: absolute;
  text-align: center;
  text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.6);
  min-width: 170px;
  max-width: 550px;
  margin-left: 101px;
  z-index: 100;animation: showNav 500ms ease-in-out both;
}
.draggable.tip .tipkanan {    
  background: rgba(0, 0, 0, 0.89) none repeat scroll 0 0 !important;
  border-radius: 0 3.2px 3.2px 0;
  color: rgb(255, 255, 255);
  display: none;
  font-size: 22px;
  line-height: 48px;
  padding: 0 11px 0 8px;
  position: absolute;
  text-align: center;
  text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.6);
  width: 550px;
  margin-left: -605px;
  z-index: 100;animation: showNav 500ms ease-in-out both;
}
.tipkanan > .tipbody >img{
  margin-left: 0px;
}

.draggable:hover.tip {
  font-size: 99%; /* this is just for IE */
}
.tipdata{
  display: none;
}
.tipbody{    
  display: inherit;
  font-size: 12px;
  height: auto;
  line-height: 1;
  min-height: 30px;
  vertical-align: top;
  width: 100%;
  word-wrap: break-word;
  padding-bottom: 15px;
  text-shadow: transparent;
  text-align: left;
}
.tiphead{    
  font-size: 14px;
  height: auto;
  margin: 0 0 -5px;
  padding: 0;
}
.draggable:hover.tip span {
  /*  display: block;*/
}
.ax5-ui-menu{
  z-index: 1000;
}
.blink{
  -webkit-animation: blink 700ms infinite alternate;
  -moz-animation: blink 700ms infinite alternate;
  -o-animation: blink 700ms infinite alternate;
  animation: blink 700ms infinite alternate;
}
@-webkit-keyframes blink {
  from { opacity:1; }
  to { opacity:0; }
}
@-o-keyframes blink {
  from { opacity:1; }
  to { opacity:0; }
}
@-moz-keyframes blink {
  from { opacity:1; }
  to { opacity:0; }
}
@keyframes blink {
  from { opacity:1; }
  to { opacity:0; }
};
#cfgsshform >tbody>tr{
  margin-bottom: 3px;
}
.btnactcfg{
  margin-left: 10px;
  margin-top: 10px;
}
#hostmanage{
 height: 465px;
 width: 100%
}
.imgicon{    
   margin-top: 34px;
    max-width: 60px;
    width: 50px;
    height: 55px;
}
.cfgdialog{
      height: 126px;
    min-height: 0;
    width: 273px;
}


#dtldtbus{
    width: 350px;
    height: 300px;
    display: inline-block;
    float: left;
}
#dtllive {
    width: 350px;
    height: 300px;
    display: inline-block;
}

#dtlwifi {
    width: 350px;
    display: inline-block;
}

#dtlhw {
    width: 350px;
    height: 300px;
    display: inline-block;
}

#dtldthw > div {
    font-size: 16px;
    font-weight: bold;
}
#dataapps > tr > td{
  padding-left: 5px;
  padding-right: 5px;
}
#dataconf > tr > td{
  padding-left: 5px;
  padding-right: 5px;
}
</style>
{/literal}

{*style="transform: translate(272px, -131px);" data-x="272" data-y="-131"*}
{*{if isset($i.x) }style='transform: translate({$i.x}px, {$i.y}px);" data-x="{$i.x}" data-y="{$i.y}"'{/if}*}

<select id='selbusgroup'>
  <option value='0'>-Please Select Concession-</option>
  {foreach from=$hostlist key=gid item=hl}
  <option value='{$hl.hg_id}'>{$hl.hg_name}</option>
  {/foreach}
</select>
<input type='text' name='bussrc' name='bussrc' id='bussrc' placeholder="Search hosts"><input type='button' id='btnsrc' value='Go'>
<input type='button' id='btnsortgrid' value='Sorting Grid'>
<input type="button" name="btnaddnewhost" id='btnaddnewhost' value='Manage Host'>
<a href='apprun.zip' class='button primary'>APPRUN ADDON V1.0.7</a> 
<div id="containment-wrapper">
 {* <div id="'.$bus['hostname'].'" net="'.$cntnet.'" aton="'.$aton.'" tunnel="'.$tunnel.'" class="ui-widget-content draggable ui-draggable" style="position:absolute; left: '.$x.'px; top: '.$y.'px;"><div class="nm">9313</div><div class="at">A</div><div class="tu">T</div><div class="alertcnt">0</div></div>
 *}
</div>
<div id='message'></div>


<div id="cfgdialog" title="" style="display:none;">
<input type="hidden" id='cfgbusid' value=''>
<input type="hidden" id='cfgobjecttype' value=''>
<input type="hidden" id='cfgmethod' value=''>
<table>
<div id='loadcfghtml'></div>
  <tr><td colspan='2'><input type='button' value='Save' id='savecfg' class='btnactcfg'><input type='button' value='Cancel' id='canceldialog' class='btncancel btnactcfg'></td></tr>
</table>
</div>

 <div id="addhost">

 </div>

<div id="opttype" style="display:none;"><input id="idpar" type="hidden" value="0"><img src="" name="image-swap" width="60px"><br><select id="seltype"></select>&nbsp;&nbsp;<input type="button" value="Save" id="btnsavetype"></div>

<div id="dialogdetail" style="display:none;">
  <div id="dtlbus">
    <div id="dtldtbus">
      <h2>Bus ID <span id="busidnum"></span></h2>

      <div id='grphwinfo'>
        <div id='lblmodulinfo'><span><h2>Module Info</h2></span>
        <div id='datamod'></div>
        </div>
      </div>

      <div id='grphwinfo'>
        <div id='lblmodulinfo'><span><h2>Hardware Info</h2></span>
        <div id='datahw'></div>
        </div>
      </div>

      <div id='grphwinfo'>
        <div id='lblmodulinfo'><span><h2>App Info</h2></span>
        <div id="dataapps" style="overflow-y: scroll; height:125px;">
        </div>
      </div>

      <div id='grphwinfo'>
        <div id='lblmodulinfo'><span><h2>Master Config Info</h2></span>
        <div id='dataconf' style="overflow-y: scroll; height:130px;"></div>
        </div>
      </div>

    </div>
  </div>
  <div id="dtllive">
    <div id="dtldtlive">
      <h2>Live Camera</h2><br><img src='sample.jpg' width="350" height="300">
    </div>
  </div>
  <div id="dtlwifi">
  </div>
  <div id="dtlhw">
    <div id="dtldthw">
      <h2>Alert Information</h2>
      <div id="dtllstalert">
        <br>Recording : <span id="dtlrec"></span>
        <br>NVS-Offline : <span id="dtlnvs"></span>
        <br>SSD : <span id="dtlssd"></span>
        <br>PFTLux : <span id="dtllux"></span>
        <br>PFTTemp: <span id="dtltemp"></span>
      </div>
    </div>
  </div>
</div>