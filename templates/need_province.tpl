<h1>Santa no sabe donde vive</h1>
<p>Para llegar a tu casa Santa necesita saber donde queda, pero no tienes puesta una provincia en tu perfil. Haz click en tu provinca y le diremos a Santa. Con esta acci&oacute;n tu perfil ser&aacute; actualizado.</p>

<ul>
	{foreach item=item key=i from=$list}
		<li>{link href="SANTA {$item}" caption ="{$item}"}</li>
	{/foreach}
</ul>
