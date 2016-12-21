<!-- Aqui se valida cuando se tenga el tiempo actual y compararlo con la fecha y hora que sale santa de su casa en el polo norte!-->
{if !$intime }
    <h1>{$message}</h1>
    {img width="100%" src="{$image}"}

{else}
    <h1>{$message}</h1>
    {img width="100%" src="{$image}"}
    <!-- Aqui se valida para que salga la provincia a la que debe llegar ,segun la lat y long -->

{/if}
