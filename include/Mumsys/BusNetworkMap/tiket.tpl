<script type="text/javascript" src="include/common/javascript/changetab.js"></script>
<script type="text/javascript" src="include/common/javascript/ajaxreq.js"></script>

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
		<li class="a" id='c1'><a href="#" onclick="javascript:montre('1');">{$sort1}</a></li>
		<li class="b" id='c2'><a href="#" onclick="javascript:montre('2');">{$sort2}</a></li>
	</ul>
</div>
<div id="tab1" class="tab">
	<table id="OTcontainer1" class="formTable table">
	 	<tr class="ListHeader">
	 		<td class="FormHeader" colspan="2">
	 			<h3>Ticket View</h3>
	 		</td>
	 	</tr>
	 	<tr class="list_lvl_1">
	 		<td class="ListColLvl1_name" colspan="2">
	 			<h4>General {$header.general}</h4>
	 		</td>
	 	</tr>
		<tr class="list_one">
			<td class="FormRowField">A;oas
				{$form.rule_alias.label}
			</td>
			<td class="FormRowValue">Alias
				{$form.rule_alias.html}
			</td>
		</tr>
	</table>
	<div id="validForm">
	    <p class="oreonbutton">
			<input id="OTSave" class="btc bt_success" type="button" value="Ssave" onClick="saveForm()" />
		</p>
	</div>
</div>
<div id="tab2" class="tab">
fds
</div>


</form>