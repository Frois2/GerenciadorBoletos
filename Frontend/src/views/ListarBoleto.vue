<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { api } from '@/services/api'

type Cliente = { id: number; nome: string; cpf_cnpj: string }
type Boleto = { id: number; cliente_id: number; desc: string; valor: string; vencimento: string; status: 'pendente' | 'pago'; cliente: Cliente }
const boletos = ref<Boleto[]>([])
const clientes = ref<Cliente[]>([])
const erro = ref('')
const editandoId = ref<number | null>(null)
const exibirFormulario = ref(false)
const buscaCliente = ref('')
const mostrarClientes = ref(false)
const busca = ref('')
const filtroStatus = ref<'todos' | 'pendente' | 'pago' | 'vencidos'>('todos')
const boleto = reactive({ cliente_id: '', desc: '', valor: '', vencimento: '', status: 'pendente' })

const clientesFiltrados = computed(() => {
  const busca = buscaCliente.value.trim().toLocaleLowerCase()
  return clientes.value.filter((cliente) => !busca || cliente.nome.toLocaleLowerCase().includes(busca) || cliente.cpf_cnpj.includes(busca)).slice(0, 6)
})

const boletosFiltrados = computed(() => {
  const termo = busca.value.trim().toLocaleLowerCase()
  return boletos.value.filter((item) => {
    const correspondeBusca = !termo || item.cliente?.nome.toLocaleLowerCase().includes(termo) || item.desc.toLocaleLowerCase().includes(termo)
    const correspondeFiltro = filtroStatus.value === 'todos'
      || (filtroStatus.value === 'vencidos' && diasParaVencimento(item.vencimento) < 0)
      || item.status === filtroStatus.value
    return correspondeBusca && correspondeFiltro
  })
})

function mascaraData(event: Event) {
  const digitos = (event.target as HTMLInputElement).value.replace(/\D/g, '').slice(0, 8)
  boleto.vencimento = digitos.replace(/^(\d{2})(\d)/, '$1/$2').replace(/^(\d{2}\/\d{2})(\d)/, '$1/$2')
}

function dataParaApi(data: string) {
  const partes = data.split('/')
  if (partes.length !== 3 || partes.some((parte, indice) => parte.length !== (indice === 2 ? 4 : 2))) return null
  return `${partes[2]}-${partes[1]}-${partes[0]}`
}

function dataParaExibicao(data: string) {
  const [ano, mes, dia] = data.split('-')
  return `${dia}/${mes}/${ano}`
}

function formatarDataBrasileira(data: string) {
  const [ano, mes, dia] = data.split('-')
  return ano && mes && dia ? `${dia}/${mes}/${ano}` : data
}

function diasParaVencimento(data: string) {
  const vencimento = new Date(`${data}T00:00:00`)
  const hoje = new Date()
  hoje.setHours(0, 0, 0, 0)
  return Math.round((vencimento.getTime() - hoje.getTime()) / 86_400_000)
}

function textoVencimento(data: string) {
  const dias = diasParaVencimento(data)
  if (dias < 0) return 'Vencido'
  if (dias === 0) return 'Vence hoje'
  if (dias === 1) return 'Vence amanhã'
  if (dias <= 7) return `Vence em ${dias} dias`
  return 'Em dia'
}

function classeVencimento(data: string) {
  const dias = diasParaVencimento(data)
  if (dias < 0) return 'vencido'
  if (dias <= 1) return 'urgente'
  if (dias <= 7) return 'proximo'
  return 'em-dia'
}

function mascaraValor(event: Event) {
  const digitado = (event.target as HTMLInputElement).value.replace(/[^\d,.]/g, '').replace(/\./g, ',')
  const [inteiro = '', ...decimais] = digitado.split(',')
  boleto.valor = decimais.length ? `${inteiro || '0'},${decimais.join('').slice(0, 2)}` : inteiro
}

async function carregar() {
  try { boletos.value = await api<Boleto[]>('/boletos') }
  catch (e) { erro.value = e instanceof Error ? e.message : 'Erro ao carregar boletos.' }
}

async function novo() {
  editandoId.value = null
  Object.assign(boleto, { cliente_id: '', desc: '', valor: '', vencimento: '', status: 'pendente' })
  buscaCliente.value = ''
  erro.value = ''
  try { clientes.value = await api<Cliente[]>('/clientes'); exibirFormulario.value = true }
  catch (e) { erro.value = e instanceof Error ? e.message : 'Cadastre um cliente antes de criar um boleto.' }
}

async function editar(item: Boleto) {
  editandoId.value = item.id
  Object.assign(boleto, {
    ...item,
    cliente_id: String(item.cliente_id),
    valor: Number(item.valor).toFixed(2).replace('.', ','),
    vencimento: dataParaExibicao(item.vencimento),
  })
  erro.value = ''
  try {
    clientes.value = await api<Cliente[]>('/clientes')
    buscaCliente.value = clientes.value.find((cliente) => cliente.id === item.cliente_id)?.nome ?? ''
    exibirFormulario.value = true
  }
  catch (e) { erro.value = e instanceof Error ? e.message : 'Erro ao carregar clientes.' }
}

async function salvar() {
  erro.value = ''
  try {
    const vencimento = dataParaApi(boleto.vencimento)
    const valor = Number(boleto.valor.replace(',', '.'))
    if (!boleto.cliente_id) throw new Error('Selecione um cliente.')
    if (!vencimento) throw new Error('Digite o vencimento no formato dd/mm/aaaa.')
    if (!Number.isFinite(valor) || valor <= 0) throw new Error('Digite um valor decimal maior que zero.')
    const url = editandoId.value ? `/boletos/${editandoId.value}` : '/boletos'
    await api(url, { method: editandoId.value ? 'PUT' : 'POST', body: JSON.stringify({ ...boleto, cliente_id: Number(boleto.cliente_id), valor, vencimento }) })
    exibirFormulario.value = false
    await carregar()
  } catch (e) { erro.value = e instanceof Error ? e.message : 'Erro ao salvar boleto.' }
}

function selecionarCliente(cliente: Cliente) {
  boleto.cliente_id = String(cliente.id)
  buscaCliente.value = cliente.nome
  mostrarClientes.value = false
}

async function excluir(item: Boleto) {
  if (!window.confirm(`Excluir o boleto “${item.desc}”?`)) return
  try { await api(`/boletos/${item.id}`, { method: 'DELETE' }); await carregar() }
  catch (e) { erro.value = e instanceof Error ? e.message : 'Erro ao excluir boleto.' }
}

async function alternarStatus(item: Boleto) {
  erro.value = ''
  const status = item.status === 'pendente' ? 'pago' : 'pendente'
  try {
    await api(`/boletos/${item.id}`, {
      method: 'PUT',
      body: JSON.stringify({
        cliente_id: item.cliente_id,
        desc: item.desc,
        valor: Number(item.valor),
        vencimento: item.vencimento,
        status,
      }),
    })
    await carregar()
  } catch (e) { erro.value = e instanceof Error ? e.message : 'Erro ao alterar status.' }
}

onMounted(carregar)
</script>

<template>
  <section>
    <div class="cabecalho-pagina"><div><span class="subtitulo">Financeiro</span><h2>Boletos</h2></div><button class="botao-cadastro" @click="novo">+ Cadastrar boleto</button></div>
    <div v-if="exibirFormulario" class="modal-fundo" @click.self="exibirFormulario = false">
      <form class="formulario modal" @submit.prevent="salvar">
        <div class="modal-cabecalho"><h3>{{ editandoId ? 'Editar boleto' : 'Cadastrar boleto' }}</h3><button type="button" class="fechar" @click="exibirFormulario = false">×</button></div>
        <label>Cliente
          <div class="cliente-busca">
            <button type="button" class="cliente-selecionado" @click="mostrarClientes = !mostrarClientes">{{ buscaCliente || 'Selecionar cliente' }} <span>⌄</span></button>
            <div v-if="mostrarClientes" class="resultados-clientes">
              <input v-model="buscaCliente" placeholder="Pesquise por nome ou CPF/CNPJ" autocomplete="off" @input="boleto.cliente_id = ''" />
              <button v-for="cliente in clientesFiltrados" :key="cliente.id" type="button" @mousedown.prevent="selecionarCliente(cliente)">{{ cliente.nome }} <small>{{ cliente.cpf_cnpj }}</small></button>
              <span v-if="!clientesFiltrados.length">Nenhum cliente encontrado.</span>
            </div>
          </div>
        </label>
        <label>Descrição <input v-model="boleto.desc" maxlength="255" required /></label>
        <label>Valor <input :value="boleto.valor" inputmode="decimal" placeholder="0,00" required @input="mascaraValor" /></label>
        <label>Vencimento <input :value="boleto.vencimento" inputmode="numeric" maxlength="10" placeholder="dd/mm/aaaa" required @input="mascaraData" /></label>
        <label>Status
          <button type="button" class="status-toggle" :class="boleto.status" @click="boleto.status = boleto.status === 'pendente' ? 'pago' : 'pendente'">
            <span class="status-indicador"></span>{{ boleto.status === 'pendente' ? 'Pendente' : 'Pago' }}<small>clique para alterar</small>
          </button>
        </label>
        <p v-if="erro" class="erro linha-inteira">{{ erro }}</p>
        <div class="acoes linha-inteira"><button>{{ editandoId ? 'Salvar alterações' : 'Cadastrar' }}</button><button type="button" class="perigo" @click="exibirFormulario = false">Cancelar</button></div>
      </form>
    </div>
    <p v-else-if="erro" class="erro">{{ erro }}</p>
    <div class="filtros">
      <input v-model="busca" placeholder="Buscar por cliente ou descrição" />
      <div class="filtro-status">
        <button :class="{ ativo: filtroStatus === 'todos' }" @click="filtroStatus = 'todos'">Todos</button>
        <button :class="{ ativo: filtroStatus === 'pendente' }" @click="filtroStatus = 'pendente'">Pendentes</button>
        <button :class="{ ativo: filtroStatus === 'pago' }" @click="filtroStatus = 'pago'">Pagos</button>
        <button :class="{ ativo: filtroStatus === 'vencidos' }" @click="filtroStatus = 'vencidos'">Vencidos</button>
      </div>
    </div>
    <table>
      <thead><tr><th>Cliente</th><th>Descrição</th><th>Valor</th><th>Vencimento</th><th>Status</th><th aria-label="Opções"></th></tr></thead>
      <tbody>
        <tr v-for="item in boletosFiltrados" :key="item.id">
          <td>{{ item.cliente?.nome }}</td><td>{{ item.desc }}</td><td>R$ {{ Number(item.valor).toFixed(2) }}</td><td class="vencimento"><span>{{ formatarDataBrasileira(item.vencimento) }}</span><small class="indicador-vencimento" :class="classeVencimento(item.vencimento)">{{ textoVencimento(item.vencimento) }}</small></td><td>{{ item.status === 'pendente' ? 'Pendente' : 'Pago' }}</td>
          
          <td class="acoes"><button @click="alternarStatus(item)">{{ item.status === 'pendente' ? 'Pago' : 'Pendente' }}</button><button @click="editar(item)">Editar</button><button class="perigo" @click="excluir(item)">Excluir</button></td>
        </tr>
        <tr v-if="!boletosFiltrados.length"><td colspan="6" class="sem-resultados">Nenhum boleto encontrado.</td></tr>
      </tbody>
    </table>
  </section>
</template>
