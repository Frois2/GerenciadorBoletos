<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '@/services/api'

type Cliente = { id: number; nome: string }
const router = useRouter()
const clientes = ref<Cliente[]>([])
const erro = ref('')
const boleto = reactive({ cliente_id: '', desc: '', valor: '', vencimento: '', status: 'pendente' })

onMounted(async () => {
  try { clientes.value = await api<Cliente[]>('/clientes') }
  catch (e) { erro.value = e instanceof Error ? e.message : 'Erro ao carregar clientes.' }
})

async function salvar() {
  erro.value = ''
  try {
    await api('/boletos', { method: 'POST', body: JSON.stringify({ ...boleto, cliente_id: Number(boleto.cliente_id), valor: Number(boleto.valor) }) })
    router.push('/boletos')
  } catch (e) { erro.value = e instanceof Error ? e.message : 'Erro ao cadastrar boleto.' }
}
</script>

<template>
  <section>
    <h2>Novo boleto</h2>
    <form class="formulario" @submit.prevent="salvar">
      <label>Cliente <select v-model="boleto.cliente_id" required><option disabled value="">Selecione</option><option v-for="cliente in clientes" :key="cliente.id" :value="cliente.id">{{ cliente.nome }}</option></select></label>
      <label>Descrição <input v-model="boleto.desc" maxlength="255" required /></label>
      <label>Valor <input v-model="boleto.valor" type="number" min="0.01" step="0.01" required /></label>
      <label>Vencimento <input v-model="boleto.vencimento" type="date" required /></label>
      <label>Status <select v-model="boleto.status"><option value="pendente">Pendente</option><option value="pago">Pago</option></select></label>
      <p v-if="erro" class="erro">{{ erro }}</p><button>Salvar boleto</button>
    </form>
  </section>
</template>
