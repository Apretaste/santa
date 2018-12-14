<h2>&iquest;Qu&eacute; hace Santa?</h2>

{space5}

<center>
	<i>{$message}</i>
	{space5}
	{img src="{$image}" alt="santa" width="100%"}
	{space5}
	<table cellspacing="0" cellpadding="10" border="0" width="100%">
        {foreach from=$top50 item=person}
			<tr {if $person@iteration is odd}style="background-color:#F2F2F2;"{/if}>
				<td width="120">{link href="PERFIL {$person->username}" caption="{$person->first_name} {$person->last_name} (@{$person->username})"}</td>
				<td>{$person->total}</td>
			</tr>
        {/foreach}
	</table>
    {space5}
	{button href="SANTA" caption="Saber de Santa" size="medium"}
</center>
