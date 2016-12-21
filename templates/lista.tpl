<h1>{$titulo}</h1>
<p>Listado de provincias con su codigo</p>
{foreach item = prov from = $province}
    {$prov->name} &nbsp {$prov->code};
{/foreach}
