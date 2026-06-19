<template>
  <div class="register-container">
    <div class="card">
      <div class="logo">
        <img src="../assets/Logo.svg" alt="logo">
        <h2>Cardáp.io</h2>
      </div>

      <h1>Criar nova conta</h1>

      <p class="subtitle">
        Comece a gerenciar seu cardápio digital agora[cite: 1]
      </p>a

      <!-- Mensagens de Feedback de erro ou sucesso -->
      <p v-if="mensagemFeedback" :class="['feedback-msg', statusSucesso ? 'msg-sucesso' : 'msg-erro']">
        {{ mensagemFeedback }}
      </p>

      <!-- Adicionado o gatilho de submit com prevent para não recarregar a página -->
      <form @submit.prevent="cadastrarUsuario">

        <div class="input-group">
          <label>Nome do restaurante *</label>
          <input
            v-model="formulario.nome"
            type="text"
            placeholder="Smash House"
            required
          >
        </div>

        <div class="input-group">
          <label>E-mail *</label>
          <input
            v-model="formulario.email"
            type="email"
            placeholder="contato@smashhouse.com"
            required
          >
        </div>

        <div class="input-group">
          <label>Senha *</label>
          <input
            v-model="formulario.senha"
            type="password"
            placeholder="Crie uma senha segura"
            required
          />

          <p v-if="formulario.senha" class="status">
            {{ statusText }}
          </p>

          <div class="password-strength">
            <div
              v-for="n in 4"
              :key="n"
              class="bar"
              :class="{
                active: n <= strength,
                weak: strength <= 1,
                medium: strength > 1 && strength < 4,
                strong: strength === 4
              }"
            />
          </div>
        </div>

        <div class="input-group">
          <label>Confirmar senha *</label>
          <input
            v-model="confirmarSenha"
            type="password"
            placeholder="Repita sua senha"
            required
          >
        </div>

        <!-- Desabilita o botão se estiver carregando a requisição -->
        <button type="submit" :disabled="carregando">
          {{ carregando ? 'Criando conta...' : 'Criar conta grátis' }}
        </button>

        <p class="login-link">
          Já tem uma conta?[cite: 1]
          <router-link to="/login">
            Fazer login[cite: 1]
          </router-link>
        </p>

      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const carregando = ref(false)
const mensagemFeedback = ref('')
const statusSucesso = ref(false)

const formulario = ref({
  nome: '',
  email: '',
  usuario: '', 
  senha: ''
})

const confirmarSenha = ref('')

const strength = computed(() => {
  if (!formulario.value.senha) return 0

  let score = 0
  if (formulario.value.senha.length >= 6) score++
  if (/[a-zA-Z]/.test(formulario.value.senha) && /[0-9]/.test(formulario.value.senha)) score++
  if (/[A-Z]/.test(formulario.value.senha)) score++
  if (/[!@#$%^&*(),.?":{}|<>]/.test(formulario.value.senha)) score++

  return Math.min(score, 4)
})

const statusText = computed(() => {
  if (strength.value <= 1) return 'Senha fraca'
  if (strength.value <= 3) return 'Senha média'
  return 'Senha forte'
})

// Função que conecta o Front com o seu Back
const cadastrarUsuario = async () => {
  if (formulario.value.senha !== confirmarSenha.value) {
    statusSucesso.value = false
    mensagemFeedback.value = 'As senhas não coincidem!'
    return
  }

  formulario.value.usuario = formulario.value.email

  carregando.value = true
  mensagemFeedback.value = ''

  try {
    // AJUSTE AQUI: Mude para a URL real onde está rodando o seu script cadastrar.php
    const resposta = await fetch('/cardapio/back-end/cadastrar.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(formulario.value)
    })

    const dados = await resposta.json()

    if (dados.sucesso) {
      statusSucesso.value = true
      mensagemFeedback.value = dados.mensagem

      // Limpa os campos e joga o usuário para o login após 2 segundos
      setTimeout(() => {
        router.push('/login')
      }, 2000)
    } else {
      statusSucesso.value = false
      mensagemFeedback.value = dados.mensagem
    }
  } catch (erro) {
    statusSucesso.value = false
    mensagemFeedback.value = 'Não foi possível conectar ao servidor backend.'
    console.error('Erro na requisição:', erro)
  } finally {
    carregando.value = false
  }
}
</script>

<style scoped>
/* Seus estilos originais mantidos integralmente abaixo */
.register-container{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background: linear-gradient(135deg, #FDDFE1 0%, #f5f5f5 50%, #D3E2FB 100%);
}
.card{
    width:420px;
    background:white;
    padding:35px;
    border-radius:20px;
    box-shadow:0 0 20px rgba(0,0,0,.08);
}
.logo{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    margin-bottom:20px;
}
.logo img{
    width:40px;
}
h1{
    text-align:center;
    margin-bottom:8px;
}
.subtitle{
    text-align:center;
    color:gray;
    font-size:14px;
    margin-bottom:30px;
}
.input-group{
    display:flex;
    flex-direction:column;
    margin-bottom:18px;
}
label{
    margin-bottom:6px;
    font-size:14px;
}
input{
    padding:13px;
    border:1px solid #ddd;
    border-radius:12px;
    outline:none;
}
input:focus{
    border:1px solid #ea2323;
}
button{
    width:100%;
    padding:14px;
    background:#ea2323;
    color:white;
    border:none;
    border-radius:12px;
    cursor:pointer;
    margin-top:15px;
}
button:disabled {
    background: #ccc;
    cursor: not-allowed;
}
.login-link{
    text-align:center;
    margin-top:15px;
}
a{
    color:#ea2323;
    text-decoration:none;
}
.password-strength {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}
.bar {
  flex: 1;
  height: 10px;
  background: #ddd;
  border-radius: 20px;
}
.active.weak {
  background: #d62d2d;
}
.active.medium {
  background: orange;
}
.active.strong {
  background: #28a745;
}
.status {
  color:#d62d2d;
  font-size:14px;
  margin-top:8px;
}

/* Novos estilos para a mensagem de feedback */
.feedback-msg {
  text-align: center;
  padding: 10px;
  border-radius: 8px;
  font-size: 14px;
  margin-bottom: 15px;
}
.msg-sucesso {
  background: #d4edda;
  color: #155724;
}
.msg-erro {
  background: #f8d7da;
  color: #721c24;
}
</style>