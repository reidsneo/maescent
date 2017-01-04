{literal}

<script type="text/javascript">
function hide (elements) {
  elements = elements.length ? elements : [elements];
  for (var index = 0; index < elements.length; index++) {
    elements[index].style.display = 'none';
  }
}
hide(document.getElementById('noty_bottomRight_layout_container'));
</script>

<style type="text/css">
    .panel {
        box-shadow: 0 2px 0 rgba(0,0,0,0.075);
        border-radius: 0;
        border: 0;
        margin-bottom: 24px;
    }
    .panel .panel-heading, .panel>:first-child {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    .panel-heading {
        position: relative;
        height: 50px;
        padding: 0;
        border-bottom:1px solid #eee;
    }
    .panel-control {
        height: 100%;
        position: relative;
        float: right;
        padding: 0 15px;
    }
    .panel-title {
        font-weight: normal;
        padding: 0 20px 0 20px;
        font-size: 1.416em;
        line-height: 50px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .panel-control>.btn:last-child, .panel-control>.btn-group:last-child>.btn:first-child {
        border-bottom-right-radius: 0;
    }
    .panel-control .btn, .panel-control .dropdown-toggle.btn {
        border: 0;
    }
    .nano {
        position: relative;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    .nano>.nano-content {
        position: absolute;
        overflow: scroll;
        overflow-x: hidden;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }
    .pad-all {
        padding: 15px;
    }
    .mar-btm {
        margin-bottom: 15px;
    }
    .media-block .media-left {
        display: block;
        float: left;
    }
    .img-sm {
        width: 46px;
        height: 46px;
    }
    .media-block .media-body {
        display: block;
        overflow: hidden;
        width: auto;
    }
    .pad-hor {
        padding-left: 15px;
        padding-right: 15px;
    }
    .speech {
        position: relative;
        background: #b7dcfe;
        color: #317787;
        display: inline-block;
        border-radius: 0;
        padding: 12px 20px;
    }
    .speech:before {
        content: "";
        display: block;
        position: absolute;
        width: 0;
        height: 0;
        left: 0;
        top: 0;
        border-top: 7px solid transparent;
        border-bottom: 7px solid transparent;
        border-right: 7px solid #b7dcfe;
        margin: 15px 0 0 -6px;
    }
    .speech-right>.speech:before {
        left: auto;
        right: 0;
        border-top: 7px solid transparent;
        border-bottom: 7px solid transparent;
        border-left: 7px solid #ffdc91;
        border-right: 0;
        margin: 15px -6px 0 0;
    }
    .speech .media-heading {
        font-size: 1.2em;
        color: #317787;
        display: block;
        border-bottom: 1px solid rgba(0,0,0,0.1);
        margin-bottom: 10px;
        padding-bottom: 5px;
        font-weight: 300;
    }
    .speech-time {
        margin-top: 20px;
        margin-bottom: 0;
        font-size: .8em;
        font-weight: 300;
    }
    .media-block .media-right {
        float: right;
    }
    .speech-right {
        text-align: right;
    }
    .pad-hor {
        padding-left: 15px;
        padding-right: 15px;
    }
    .speech-right>.speech {
        background: #ffda87;
        color: #a07617;
        text-align: right;
    }
    .speech-right>.speech .media-heading {
        color: #a07617;
    }
    .btn-primary, .btn-primary:focus, .btn-hover-primary:hover, .btn-hover-primary:active, .btn-hover-primary.active, .btn.btn-active-primary:active, .btn.btn-active-primary.active, .dropdown.open>.btn.btn-active-primary, .btn-group.open .dropdown-toggle.btn.btn-active-primary {
        background-color: #579ddb;
        border-color: #5fa2dd;
        color: #fff !important;
    }
    .btn {
        cursor: pointer;
        /* background-color: transparent; */
        color: inherit;
        padding: 6px 12px;
        border-radius: 0;
        border: 1px solid 0;
        font-size: 11px;
        line-height: 1.42857;
        vertical-align: middle;
        -webkit-transition: all .25s;
        transition: all .25s;
    }
    .form-control {
        font-size: 11px;
        height: 100%;
        border-radius: 0;
        box-shadow: none;
        border: 1px solid #e9e9e9;
        transition-duration: .5s;
    }
    .nano>.nano-pane {
        background-color: rgba(0,0,0,0.1);
        position: absolute;
        width: 5px;
        right: 0;
        top: 0;
        bottom: 0;
        opacity: 0;
        -webkit-transition: all .7s;
        transition: all .7s;
    }
    #leftcol {
        float: left;
        width: 600px;
    }

    #rightcol {
        float: left;
        width: 450px;
    }

    #centercol {
        float: left;
        width: 450px;
    }
    .msgalert{
       font-size: 12px;
       font-weight: bold;
   }
   .msgdesc{
       font-size: 13px;
       font-weight: bold;
   }
   .msgtext{
       font-size: 13px;
   }
   .bold{
       font-weight: bold;
   }
   .fullspech{
       width: 408px;/*edit*/
   }
    .speech-rightdis>.speech:before {
        left: auto;
        right: 0;
        border-top: 7px solid transparent;
        border-bottom: 7px solid transparent;
        border-left: 7px solid #898989;
        border-right: 0;
        margin: 15px -6px 0 0;
    }
   .speech-rightdis {
        text-align: right;
    }
    .speech-rightdis>.speech {
        background: #898989;
        color:#898989;
        text-align: right;
    }
    .speech-rightdis>.speech .media-heading{
        color: white;
        background-color: #898989;
    }
    .speech-rightdis>.speech p{
        color: white;
    }
    .speech-leftdis>.speech:before {
     content: "";
        display: block;
        position: absolute;
        width: 0;
        height: 0;
        left: 0;
        top: 0;
        border-top: 7px solid transparent;
        border-bottom: 7px solid transparent;
        border-right: 7px solid #898989;
        margin: 15px 0 0 -6px;
    }
   .speech-leftdis {
        text-align: left;
    }
    .speech-leftdis>.speech {
        background: #898989;
        color:#898989;
        text-align: left;
    }
    .speech-leftdis>.speech .media-heading{
        color: white;
        background-color: #898989;
    }
    .speech-leftdis>.speech p{
        color: white;
    }
    .grayscale{
        filter: grayscale(100%); /* Current draft standard */
    -webkit-filter: grayscale(100%); /* New WebKit */
    -moz-filter: grayscale(100%);
    -ms-filter: grayscale(100%); 
    -o-filter: grayscale(100%); /* Not yet supported in Gecko, Opera or IE */ 
    filter: url(resources.svg#desaturate); /* Gecko */
    filter: gray; /* IE */
    -webkit-filter: grayscale(1); /* Old WebKit */
    }
</style>
{/literal}

<div id="container">
    <div id="leftcol">
    	<div class="panel">
    		<div class="panel-heading">
    			<h3 class="panel-title">Issue List Bus ID {$busid} : [<b>{$issuecnt}</b>]</h3></div>
    			<div id="demo-chat-body" class="collapse in" aria-expanded="true" style="">
    				<div class="nano has-scrollbar" style="height:530px">
    					<div class="nano-content pad-all" tabindex="0" style="right: -17px;">
    						<ul class="list-unstyled media-block">
    							{assign var=val value=0}
    							{foreach from=$busdat key=index item=i}
    							{assign var=val value=$val+1}
    							{if $val>1}
    							<li class="mar-btm">
    								<div class="media-right"> <img src="http://www.iconshock.com/img_jpg/BETA/networking/jpg/256/log_icon.jpg" class="img-circle img-sm" alt="Profile Picture"></div>
    								<div class="media-body pad-hor speech-right">
    									<div class="speech fullspech"> <a class="media-heading">{$i.lastcheck} - <b class='bold'>{$i.id_alert}</b></a>
    										<p><span id='alertmsg' class='msgalert'>{$i.msg_alert}</span></p>
    										<p><textarea style="width: 405px; height: 89px;" class='msgtext'></textarea></p>
    									</div>
    								</div>
    							</li>
    							{else}
    							<li class="mar-btm">
    								<div class="media-left"> <img src="http://www.freeiconspng.com/uploads/alert-storm-warning-weather-icon--icon-search-engine-0.png" class="img-circle img-sm" alt="Profile Picture"></div>
    								<div class="media-body pad-hor">
    									<div class="speech fullspech"><a class="media-heading"><b class='bold'>{$i.id_alert}</b> - {$i.lastcheck}</a>
    										<p><span class='msgdesc'>Alert</span><span id='alertmsg' class='msgalert'> : {$i.msg_alert}</span></p>
    										<p><span class='msgdesc'>Description</span> :<br><textarea style="width: 405px; height: 89px;" class='msgtext'></textarea></p>
    									</div>
    								</div>
    							</li>
    							{/if}
    							{/foreach}
    						</ul>
    					</div>
                     <div class="nano-pane"><div class="nano-slider" style="height: 141px; transform: translate(0px, 0px);"></div></div></div>
                 </div>
             </div>
         </div>
         <div id="rightcol">
            <div class="panel">
               <div class="panel-heading">
                  <h3 class="panel-title">Syteline Accident on Bus ID {$busid} : [<b>{$acccnt}</b>]</h3></div>
                  <div id="demo-chat-body" class="collapse in" aria-expanded="true" style="">
                  <div class="nano has-scrollbar" style="height:530px">
                      <div class="nano-content pad-all" tabindex="0" style="right: -17px;">
                    <ul class="list-unstyled media-block">
                        {assign var=val value=0}
                        {foreach from=$buserp key=index item=i}
                        <li class="mar-btm">
                            <div class="{if $i.StatCode!="Open" AND $i.StatCode!="OPEN" AND $i.StatCode!="assigned"}media-right{else}media-left{/if}">
                            <img src="https://www.xax.de/wp-content/uploads/2015/11/infor-logo_20mm-150x150.png" class="{if $i.StatCode!="Open" AND $i.StatCode!="OPEN" AND $i.StatCode!="assigned"}grayscale{/if} img-circle img-sm" alt="Profile Picture"></div>
                            <div class="media-body pad-hor {if $i.StatCode!="Open" AND $i.StatCode!="OPEN" AND $i.StatCode!="assigned"}speech-rightdis{/if}">                            
                                {php}
                                if(!isset($i)) $i=-1;
                                $i=$i+1;
                                $test = $this->get_template_vars($buserp);
                                $this->assign('dateinc',date('Y-m-d H:m:s',strtotime($test['buserp'][$i]['IncDate'])));
                                if(strpos($test['buserp'][$i]['SSR'], 'SD') !== false) {
                                    $ssr="Service Desk";
                                }else if(strpos($test['buserp'][$i]['SSR'], 'CXX') !== false) {
                                    $ssr="Connextion";
                                    }
                                $this->assign('ssr',$ssr);
                                $busso = CentreonSoap::getClient()->LoadDataSet(array('strSessionToken'=>CentreonSoap::token(),'strIDOName'=>'FSIncReasons','strPropertyList'=>'ResolutionNotes,ReasonSpec','strFilter'=>"IncNum='".$test['buserp'][$i]['IncNum']."'",'strOrderBy'=>'IncNum desc','strPostQueryMethod'=>'','iRecordCap'=>'1','s'=>''));
                                $busdat=CentreonSoap::parsexml($busso->LoadDataSetResult->any);
                                $this->assign('resnote',$busdat['FSIncReasons']['IDO']['ResolutionNotes']);
                                $this->assign('reasspec',$busdat['FSIncReasons']['IDO']['ReasonSpec']);
                                {/php}
                                <div class="speech"><a class="media-heading"><b class='bold'>{$i.IncNum}</b> - {$dateinc}</a>
                                    <p><span class='msgdesc'>SSR</span><span id='alertmsg' class='msgalert'> : {$ssr}</span></p>
                                    <p><span class='msgdesc'>Status</span><span id='alertmsg' class='msgalert'> : {$i.StatCode}</span></p>
                                    <p><span class='msgdesc'>Code</span><span id='alertmsg' class='msgalert'> : {$reasspec}</span></p>
                                    <p><span class='msgdesc'>Priority</span><span id='alertmsg' class='msgalert'> : {$i.PriorCode}</span></p>
                                    <p><span class='msgdesc'>Resolution Notes</span><span id='alertmsg' class='msgalert'> : {$resnote}</span></p>
                                </div>
                            </div>
                        </li>
                        {/foreach}
                    </ul>
                    </div>
              <div class="nano-pane"><div class="nano-slider" style="height: 141px; transform: translate(0px, 0px);"></div></div></div>
          </div>
      </div>
  </div>
<div id="centercol">
<div class="panel-heading"><h3 class="panel-title">Help Desk Activity Log</h3></div>
        <div class="nano has-scrollbar" style="height:530px">
            <div class="nano-content pad-all" tabindex="0" style="right: -17px;">
                <ul class="list-unstyled media-block">
    <li class="mar-btm">
        <div class="media-right"> <img src="http://bootdey.com/img/Content/avatar/avatar2.png" class="img-circle img-sm" alt="Profile Picture"></div>
        <div class="media-body pad-hor speech-right">
            <div class="speech"> <a href="#" class="media-heading">Helpdesk A</a>
                <p>Helpdesk A is Remoting to bus 5519</p>
                <p class="speech-time"> <i class="fa fa-clock-o fa-fw"></i> 09:28</p>
            </div>
        </div>
    </li>
    <li class="mar-btm">
        <div class="media-left"> <img src="http://bootdey.com/img/Content/avatar/avatar3.png" class="img-circle img-sm" alt="Profile Picture"></div>
        <div class="media-body pad-hor speech-leftdis">
            <div class="speech"> <a href="#" class="media-heading">Helpdesk B</a>
                <p>Helpdesk B has fixed bus 3100 by remote</p>
                <p class="speech-time"> <i class="fa fa-clock-o fa-fw"></i> 09:28</p>
            </div>
        </div>
    </li>
    <li class="mar-btm">
        <div class="media-left"> <img src="http://bootdey.com/img/Content/avatar/avatar4.png" class="img-circle img-sm" alt="Profile Picture"></div>
        <div class="media-body pad-hor speech-leftdis">
            <div class="speech"> <a href="#" class="media-heading">Helpdesk C</a>
                <p>Helpdesk C has fixed bus 3100 by remote</p>
                <p class="speech-time"> <i class="fa fa-clock-o fa-fw"></i> 09:28</p>
            </div>
        </div>
    </li>
                </ul>
            </div>
            <div class="nano-pane"><div class="nano-slider" style="height: 141px; transform: translate(0px, 0px);"></div></div></div>
        </div>
</div>
</div>
</div>
</div>  
</div>
</div>
