{$startform}
	<div class="pageoverflow">
		<p class="pagetext">{$gwcodetext}:</p>
		<p class="pagetext">{$inputgwcode}</p>
	</div>
	{if $inputattrlock == 0}
		<div class="pageoverflow">
			<p class="pagetext">*{$gwattrparmtext}:</p>
			<p class="pageinput">{$inputgwattrparm}</p>
		</div>
	{/if}
	<div class="pageoverflow">
		<p class="pagetext">*{$gwattrdesctext}:</p>
		<p class="pageinput">{$inputgwattrdesc}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">*{$gwattrvaluetext}:</p>
		<p class="pageinput">{$inputgwattrvalue}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">{$gwattrlocktext}:</p>
		<p class="pageinput">{$inputattrlock}</p>
	</div>
	<div class="pageoverflow">
		<p class="pagetext">&nbsp;</p>
		<p class="pageinput">{$hidden}{$hiddenattrid}{$submit}{$cancel}</p>
	</div>
{$endform}