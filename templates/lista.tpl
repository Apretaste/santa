<h1 align="center"><font size="3px"> {$titulo}</font></h1>
<p><strong>Listado de provincias con su c&oacutedigo</strong></p>
<table>
    {foreach item = prov from = $province}
    <tr >
        <td>{img src="temp/point.png"}</td>
        <td> {$prov->name}</td>
        <td align="center" width="70">{$prov->code}</td>

    </tr>
{/foreach}
</table>


