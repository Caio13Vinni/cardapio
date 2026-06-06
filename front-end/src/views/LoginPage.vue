<template>
  <div class="login-container">
    <div class="login-card">
      <div class="logo-area">
        <img src="../assets/Logo.svg" alt="Logo">
        <h2>Cardáp.io</h2>
      </div>

      <h1>Entrar no sistema</h1>
      <p>Acesse sua conta para gerenciar seu cardápio</p>

      <form @submit.prevent="handleLogin">

        <label>E-mail *</label>
        <input
          v-model="email"
          type="email"
          placeholder="restaurante@exemplo.com"
          required
        />

        <label>Senha *</label>
        <input
          v-model="senha"
          type="password"
          placeholder="••••••••••"
          required
        />

        <div class="options">
          <div>
            <input type="checkbox">
            <span>Lembrar de mim</span>
          </div>
          <a href="#">Esqueceu a senha?</a>
        </div>

        <button type="submit" :disabled="carregando">
          {{ carregando ? 'Aguarde...' : 'Entrar no painel' }}
        </button>

        <p v-if="erro" class="error-msg">{{ erro }}</p>

        <p class="register">
          Não tem uma conta?
          <router-link to="/cadastro">Criar Conta</router-link>
        </p>

      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const email = ref('');
const senha = ref('');
const erro = ref('');
const carregando = ref(false);
const router = useRouter();

const handleLogin = async () => {
  carregando.value = true;
  erro.value = '';

  try {
    // IMPORTANTE: MUDAR ESSA PORRA QUANDO HOSPEDAR NA AWS!! CONFIGURAR A .ENV DISSO
    const urlBackend = '/cardapio/back-end/valida.php';

    const response = await fetch(urlBackend, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        cardapioLogin: true,
        email: email.value,
        senha: senha.value
      })
    });

    const data = await response.json();

    if (data.success) {

     localStorage.setItem('token_cardapio', data.user.id);

      router.push('/dashboard');
    } else {
      erro.value = data.message;
    }
  } catch (e) {
    erro.value = "Erro ao conectar com o servidor.";
    console.error(e);
  } finally {
    carregando.value = false;
  }
};
</script>

<style scoped>

.error-msg {
  color: #ef4444;
  font-weight: bold;
  margin-top: 15px;
  font-size: 14px;
}

button:disabled {
  background: #94a3b8;
  cursor: not-allowed;
}


*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial,sans-serif;
}

.login-container{
height:100vh;
display:flex;
justify-content:center;
align-items:center;
background: linear-gradient(135deg, #FDDFE1 0%, #f5f5f5 50%, #D3E2FB 100%);
}

.login-card{
width:420px;
background:white;
padding:40px;
border-radius:20px;
box-shadow: 0 10px 30px rgba(0,0,0,.08);
border:1px solid #e8e8e8;
}

.logo-area{
display:flex;
justify-content:center;
align-items:center;
gap:10px;
margin-bottom:25px;
}

.logo-area img{ width:40px; }
.logo-area h2{ font-size:28px; }

h1{ text-align:center; font-size:34px; color:#1e293b; margin-bottom:10px; }
p{ text-align:center; color:#64748b; margin-bottom:30px; }

label{ display:block; margin-bottom:8px; font-weight:600; margin-top:15px; }

input[type=email],
input[type=password]{
width:100%;
padding:14px;
border-radius:12px;
border:2px solid #ef4444;
outline:none;
margin-bottom:10px;
}

.options{
display:flex;
justify-content:space-between;
font-size:13px;
margin-top:10px;
margin-bottom:25px;
}

.options div{ display:flex; align-items:center; gap:5px; }
.options a{ color:red; text-decoration:none; }

button{
width:100%;
padding:15px;
background:#ef2020;
border:none;
border-radius:12px;
color:white;
font-size:16px;
cursor:pointer;
transition:.3s;
}

button:hover{ transform:translateY(-2px); }

.register{ margin-top:20px; font-size:14px; }
.register a{ color:red; text-decoration:none; font-weight:bold; }
</style>