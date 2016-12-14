    <h1>Santa Tracker</h1>

    {space10}

    {if $province==null}
        Debe insertar una provincia
    <!-- Aqui se valida cuando se tenga el tiempo actual y compararlo con la fecha y hora que sale santa de su casa en el polo norte!-->
    {elseif $province->time_zone <= 5}
         Santa no ha partido de su casa en el Polo Norte.
        <!-- Aqui se valida para que salga la provincia a la que debe llegar ,segun la lat y long -->
    {elseif $province->lat ==1111112 && $province->long ==444444443}
        Santa llegar&aacute; a {$province->name}.
        <!-- Aqui se valida para que salga la provincia a la que debe llegar ,la hora en que llega!-->
    {else}
        Santa llegar&aacute; a {$province->name} a las {$province->time_zone} horas.
    <br>
        Le faltan (Hay que calcular el tiempo que le falta x llegar ).
        <p><strong>MAPA (IMG)</strong></p>
        {/if}




    <!--<p>IF (hay provincia) Santa llegara a TU_PROVINCIA en 1:34</p>
    <p>ELSE Agregue [su provincia] para ver tiempos</p>

    <p>Lugares ya visitados
    Array de Ciudades y provincias cuya hora < CURRENT_TIME</p>

    <p>Lugares por visitar
    Array de Ciudades y provincias cuya hora > CURRENT_TIME</p>
    !-->





