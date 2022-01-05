{$startform}
	<div class="pageoverflow">
		<p class="pagetext">*{$gwcodetext}:</p>
		<p class="pageinput">{$inputgwcode}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$gwdesctext}:</p>
		<p class="pageinput">{$inputgwdesc}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$activetext}:</p>
		<p class="pageinput">{$inputactive}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$hidden}{$submit}{$cancel}</p>
	</div>
	<br>
	<table cellspacing="0" class="pagetable">
		<thead>
			<tr>
				<th>{if isset($gwattrparmtext)}{$gwattrparmtext}{/if}</th>
				<th>{if isset($gwattrdesctext)}{$gwattrdesctext}{/if}</th>
				<th>{if isset($gwattrvaluetext)}{$gwattrvaluetext}{/if}</th>
				<th>{if isset($gwattrlocktext)}{$gwattrlocktext}{/if}</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
			<tr>
				<td>{if isset($inputgwattrparm)}{$inputgwattrparm}{/if}</td>
				<td>{if isset($inputgwattrdesc)}{$inputgwattrdesc}{/if}</td>
				<td>{if isset($inputgwattrvalue)}{$inputgwattrvalue}{/if}</td>
				<td>{if isset($inputattrlock)}{$inputattrlock}{/if}</td>
				<td>{if isset($hiddenattrid)}{$hiddenattrid}{/if}{if isset($submitattrid)}{$submitattrid}{/if}</td>
			</tr>
	{if isset($gwedit) && $gwedit != '0' }
		{foreach from=$items item=entry}
			<tr class="{$entry->rowclass}" onmouseover="this.className='{$entry->rowclass}hover';" onmouseout="this.className='{$entry->rowclass}';">
				<td>{$entry->parm}</td>
				<td>{$entry->description}</td>
				<td>{$entry->value}</td>
				<td>{$entry->editlink}</td>
				<td>{if empty($entry->lock)}{$entry->deletelink}{/if}</td>
			</tr>
		{/foreach}
		</tbody>
	{else}
		<tr class="{cycle values="row1,row2"}">
			<td colspan='5' align='center'>{if isset($noattributesavailable)}{$noattributesavailable}{/if}</td>
		</tr>
	{/if}
	</table>
{$endform}