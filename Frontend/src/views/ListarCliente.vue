<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { api } from '@/services/api'

type Cliente = { id: number; nome: string; cpf_cnpj: string; email: string }
const clientes = ref<Cliente[]>([])
const erro = ref('')
const editandoId = ref<number | null>(null)
const exibirFormulario = ref(false)
const busca = ref('')
const cliente = reactive({ nome: '', cpf_cnpj: '', email: '' })

const clientesFiltrados = computed(() => {
  const termo = busca.value.trim().toLocaleLowerCase()
  return clientes.value.filter((item) => !termo || item.nome.toLocaleLowerCase().includes(termo) || item.cpf_cnpj.includes(termo) || item.email.toLocaleLowerCase().includes(termo))
})

async function carregar() {
  try { clientes.value = await api<Cliente[]>('/clientes') }
  catch (e) { erro.value = e instanceof Error ? e.message : 'Erro ao carregar clientes.' }
}

function novo() {
  editandoId.value = null
  Object.assign(cliente, { nome: '', cpf_cnpj: '', email: '' })
  erro.value = ''
  exibirFormulario.value = true
}

function editar(item: Cliente) {
  editandoId.value = item.id
  Object.assign(cliente, item)
  erro.value = ''
  exibirFormulario.value = true
}

function mascaraCpfCnpj(event: Event) {
  const digitos = (event.target as HTMLInputElement).value.replace(/\D/g, '').slice(0, 14)

  cliente.cpf_cnpj = digitos.length <= 11
    ? digitos.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2')
    : digitos.replace(/(\d{2})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1/$2').replace(/(\d{4})(\d{1,2})$/, '$1-$2')
}

async function salvar() {
  erro.value = ''
  try {
    const url = editandoId.value ? `/clientes/${editandoId.value}` : '/clientes'
    await api(url, { method: editandoId.value ? 'PUT' : 'POST', body: JSON.stringify(cliente) })
    exibirFormulario.value = false
    await carregar()
  } catch (e) { erro.value = e instanceof Error ? e.message : 'Erro ao salvar cliente.' }
}

async function excluir(item: Cliente) {
  if (!window.confirm(`Excluir ${item.nome}? Os boletos vinculados também serão excluídos.`)) return
  try { await api(`/clientes/${item.id}`, { method: 'DELETE' }); await carregar() }
  catch (e) { erro.value = e instanceof Error ? e.message : 'Erro ao excluir cliente.' }
}

onMounted(carregar)
</script>

<template>
  <section>
    <div class="cabecalho-pagina"><div><span class="subtitulo">Cadastros</span><h2>Clientes</h2></div><button class="botao-cadastro" @click="novo">+ Cadastrar cliente</button></div>
    <div v-if="exibirFormulario" class="modal-fundo" @click.self="exibirFormulario = false">
      <form class="formulario modal" @submit.prevent="salvar">
        <div class="modal-cabecalho"><h3>{{ editandoId ? 'Editar cliente' : 'Cadastrar cliente' }}</h3><button type="button" class="fechar" @click="exibirFormulario = false">×</button></div>
        <label>Nome <input v-model="cliente.nome" maxlength="150" required /></label>
        <label>CPF/CNPJ <input :value="cliente.cpf_cnpj" inputmode="numeric" maxlength="18" placeholder="000.000.000-00" required @input="mascaraCpfCnpj" /></label>
        <label class="linha-inteira">E-mail (opcional) <input v-model="cliente.email" type="email" maxlength="150" /></label>
        <p v-if="erro" class="erro linha-inteira">{{ erro }}</p>
        <div class="acoes linha-inteira"><button>{{ editandoId ? 'Salvar alterações' : 'Cadastrar' }}</button><button type="button" class="perigo" @click="exibirFormulario = false">Cancelar</button></div>
      </form>
    </div>
    <p v-else-if="erro" class="erro">{{ erro }}</p>
    <div class="filtros"><input v-model="busca" placeholder="Buscar por nome, CPF/CNPJ ou e-mail" /></div>
    <table>
      <thead><tr><th>Nome</th><th>CPF/CNPJ</th><th>E-mail</th><th aria-label="Opções"></th></tr></thead>
      <tbody>
        <tr v-for="item in clientesFiltrados" :key="item.id">
          <td>{{ item.nome || '—' }}</td><td>{{ item.cpf_cnpj }}</td><td>{{ item.email || '—' }}</td>
          <td class="acoes"><button @click="editar(item)">Editar</button><button class="perigo" @click="excluir(item)">Excluir</button></td>
        </tr>
        <tr v-if="!clientesFiltrados.length"><td colspan="4" class="sem-resultados">Nenhum cliente encontrado.</td></tr>
      </tbody>
    </table>
  </section>
</template>
