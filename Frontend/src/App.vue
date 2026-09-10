<template>
  <header class="topo">
    <h1>Gerenciador de Boletos</h1>
    <nav>
      <RouterLink to="/clientes">Clientes</RouterLink>
      <RouterLink to="/boletos">Boletos</RouterLink>
    </nav>
  </header>

  <main class="conteudo">
    <RouterView />
  </main>
</template>

<style>
* { box-sizing: border-box; }
body {
  min-height: 100vh; margin: 0; color: #292929;
  font-family: Inter, ui-sans-serif, system-ui, sans-serif;
  background: #d5d5d5;
}
.topo { max-width: 1100px; margin: 1.5rem auto 0; padding: 1.1rem 1.4rem; border: 1px solid rgba(255,255,255,.65); border-radius: 14px; background: rgba(255,255,255,.28); box-shadow: 0 10px 28px rgba(0,0,0,.09); backdrop-filter: blur(18px); }
.topo h1 { margin: 0 0 .7rem; font-size: 1.35rem; letter-spacing: .04em; }
.topo nav { display: flex; gap: .5rem; }
.topo a { padding: .45rem .7rem; color: #4a4a4a; text-decoration: none; border-radius: 8px; }
.topo a.router-link-active { background: rgba(255,255,255,.65); color: #111; }
.conteudo { max-width: 1100px; padding: 2rem 0; margin: auto; }
h2 { margin: .1rem 0 0; font-size: 1.6rem; }
.formulario { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; max-width: 700px; margin: 1rem 0; padding: 1.25rem; border: 1px solid rgba(255,255,255,.32); border-radius: 18px; background: rgba(255,255,255,.16); box-shadow: 0 18px 45px rgba(0,0,0,.25); backdrop-filter: blur(20px); }
label { display: grid; gap: .4rem; font-size: .88rem; font-weight: 600; color: #393939; }
input, select { width: 100%; padding: .7rem .75rem; border: 1px solid rgba(0,0,0,.13); border-radius: 8px; outline: 0; background: rgba(255,255,255,.42); color: #202020; font: inherit; }
input:focus, select:focus { border-color: #777; box-shadow: 0 0 0 3px rgba(0,0,0,.08); }
button, .botao { border: 0; border-radius: 8px; padding: .62rem .9rem; cursor: pointer; background: #282828; color: #fff; font: inherit; box-shadow: none; }
button:hover { filter: brightness(1.12); }
.perigo { background: #6b3030; color: #fff; box-shadow: none; }
.acoes { display: flex; gap: .5rem; align-items: center; }
table { width: 100%; overflow: hidden; border-spacing: 0; border: 1px solid rgba(255,255,255,.7); border-radius: 14px; background: rgba(255,255,255,.28); box-shadow: 0 16px 40px rgba(0,0,0,.1); backdrop-filter: blur(20px); color: #252525; }
th, td { padding: .9rem 1rem; border-bottom: 1px solid rgba(0,0,0,.08); text-align: left; }
th { color: #555; font-size: .76rem; letter-spacing: .08em; text-transform: uppercase; }
tr:last-child td { border-bottom: 0; }
tr:hover td { background: rgba(255,255,255,.30); }
.erro { color: #8d2525; }
.linha-inteira { grid-column: 1 / -1; }
.cabecalho-pagina { display: flex; justify-content: space-between; align-items: end; gap: 1rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(0,0,0,.14); }
.subtitulo { color: #666; font-size: .76rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
.botao-cadastro { padding: .72rem 1rem; white-space: nowrap; }
.modal-fundo { position: fixed; z-index: 10; inset: 0; display: grid; place-items: center; padding: 1rem; background: rgba(25,25,25,.42); backdrop-filter: blur(4px); }
.modal { position: relative; width: min(700px, 100%); max-height: calc(100vh - 2rem); overflow-y: auto; margin: 0; }
.modal-cabecalho { display: flex; justify-content: space-between; align-items: center; grid-column: 1 / -1; }
.modal-cabecalho h3 { margin: 0; }
.fechar { width: 2rem; height: 2rem; padding: 0; border-radius: 50%; font-size: 1.2rem; }
.cliente-busca { position: relative; }
.cliente-selecionado { display: flex; width: 100%; justify-content: space-between; align-items: center; background: rgba(255,255,255,.42); color: #202020; border: 1px solid rgba(0,0,0,.13); box-shadow: none; text-align: left; }
.resultados-clientes { position: absolute; z-index: 2; top: calc(100% + .35rem); right: 0; left: 0; overflow: hidden; border: 1px solid rgba(0,0,0,.12); border-radius: 9px; background: #f3f3f3; box-shadow: 0 12px 25px rgba(0,0,0,.16); }
.resultados-clientes input { border: 0; border-bottom: 1px solid rgba(0,0,0,.1); border-radius: 0; background: transparent; }
.resultados-clientes button { display: flex; width: 100%; justify-content: space-between; padding: .65rem .75rem; border: 0; border-radius: 0; background: transparent; color: #242424; box-shadow: none; text-align: left; }
.resultados-clientes button:hover { background: #dedede; }
.resultados-clientes small { color: #666; }
.resultados-clientes span { display: block; padding: .7rem; color: #666; }
.status-toggle { display: flex; width: 100%; align-items: center; gap: .55rem; border: 1px solid rgba(0,0,0,.12); background: rgba(255,255,255,.42); color: #303030; box-shadow: none; text-align: left; }
.status-toggle small { margin-left: auto; color: #777; font-weight: 400; }
.status-indicador { width: .65rem; height: .65rem; border-radius: 50%; background: #888; }
.status-toggle.pago .status-indicador { background: #3d7251; }
.filtros { display: flex; justify-content: space-between; gap: 1rem; margin: 1rem 0; }
.filtros > input { max-width: 340px; background: rgba(255,255,255,.5); }
.filtro-status { display: flex; gap: .35rem; }
.filtro-status button { background: rgba(255,255,255,.32); color: #333; box-shadow: none; }
.filtro-status button.ativo { background: #282828; color: #fff; }
.vencimento { white-space: nowrap; }
.indicador-vencimento { display: inline-block; margin-left: .45rem; padding: .17rem .4rem; border-radius: 999px; background: rgba(0,0,0,.08); color: #555; font-size: .72rem; }
.indicador-vencimento.urgente, .indicador-vencimento.vencido { background: #f3d3d3; color: #8d2525; }
.indicador-vencimento.proximo { background: #eee6c7; color: #685518; }
.indicador-vencimento.em-dia { background: #d9e9dc; color: #355d3e; }
.sem-resultados { color: #666; text-align: center; }
@media (max-width: 1150px) { .topo, .conteudo { margin-left: 1rem; margin-right: 1rem; } }
@media (max-width: 650px) { .conteudo { padding: 1.5rem 0; } .formulario { grid-template-columns: 1fr; } .filtros { flex-direction: column; } .filtros > input { max-width: none; } table { display: block; overflow-x: auto; white-space: nowrap; } }
</style>
