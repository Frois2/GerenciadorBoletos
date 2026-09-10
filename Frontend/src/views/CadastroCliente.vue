<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '@/services/api'

const router = useRouter()
const erro = ref('')
const cliente = reactive({ nome: '', cpf_cnpj: '', email: '' })

async function salvar() {
  erro.value = ''
  try {
    await api('/clientes', { method: 'POST', body: JSON.stringify(cliente) })
    router.push('/clientes')
  } catch (e) {
    erro.value = e instanceof Error ? e.message : 'Erro ao cadastrar cliente.'
  }
}
</script>

<template>
  <section>
    <h2>Novo cliente</h2>
    <form class="formulario" @submit.prevent="salvar">
      <label>Nome <input v-model="cliente.nome" maxlength="150" required /></label>
      <label>CPF/CNPJ <input v-model="cliente.cpf_cnpj" maxlength="20" required /></label>
      <label>E-mail <input v-model="cliente.email" type="email" maxlength="150" /></label>
      <p v-if="erro" class="erro">{{ erro }}</p>
      <button>Salvar cliente</button>
    </form>
  </section>
</template>
