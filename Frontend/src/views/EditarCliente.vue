<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { api } from '@/services/api'

type Cliente = { id: number; nome: string; cpf_cnpj: string; email: string }
const route = useRoute()
const router = useRouter()
const erro = ref('')
const cliente = reactive({ nome: '', cpf_cnpj: '', email: '' })

onMounted(async () => {
  try {
    Object.assign(cliente, await api<Cliente>(`/clientes/${route.params.id}`))
  } catch (e) { erro.value = e instanceof Error ? e.message : 'Erro ao carregar cliente.' }
})

async function salvar() {
  erro.value = ''
  try {
    await api(`/clientes/${route.params.id}`, { method: 'PUT', body: JSON.stringify(cliente) })
    router.push('/clientes')
  } catch (e) { erro.value = e instanceof Error ? e.message : 'Erro ao editar cliente.' }
}
</script>

<template>
  <section>
    <h2>Editar cliente</h2>
    <form class="formulario" @submit.prevent="salvar">
      <label>Nome <input v-model="cliente.nome" maxlength="150" required /></label>
      <label>CPF/CNPJ <input v-model="cliente.cpf_cnpj" maxlength="20" required /></label>
      <label>E-mail <input v-model="cliente.email" type="email" maxlength="150" required /></label>
      <p v-if="erro" class="erro">{{ erro }}</p>
      <button>Salvar alterações</button>
    </form>
  </section>
</template>
