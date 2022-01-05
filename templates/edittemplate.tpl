<div class="pageoverflow">
<h3>{if isset($title)}{$title}{/if}</h3>
</div>
{$startform}
<div class="pageoverflow">
  <p class="pagetext">{$prompt_templatename}:</p>
  <p class="pageinput">{$templatename}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$prompt_template}:</p>
  <p class="pageoptions">{$template}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageoptions">{$hidden}{$submit}{$cancel}</p>
</div>
{$endform}
