<template>
  <div class="success-page">
    <div class="success-card">

      <div class="icon-success">
        ✓
      </div>

      <h1>E-mail enviado com sucesso!</h1>

      <p>
        Enviamos um link de recuperação para
        <strong>{{ email }}</strong>.
        <br>
        Verifique sua caixa de entrada e spam.
      </p>

      <button @click="$router.push('/login')">
        Voltar para o login
      </button>

    </div>
  </div>
</template>

<script>
export default {
  name: 'EmailEnviadoPage',

  data() {
    return {
      email: ''
    }
  },

  mounted() {
    // Tenta ler o e-mail real da URL. Se não vier nada, exibe o e-mail padrão de testes.
    this.email = this.$route.query.email || 'cardapio@teste.com';
  }
}
</script>

<style scoped>
.success-page {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(
    135deg,
    #ffe8e8 0%,
    #ffffff 50%,
    #e7f1ff 100%
  );
}

.success-card {
  width: 750px;
  min-height: 420px;
  background: rgba(255,255,255,.55);
  border: 1px solid #bdbdbd;
  border-radius: 28px;

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.icon-success {
  width: 96px;
  height: 96px;
  border-radius: 50%;
  background: #c9f2d2;

  display: flex;
  align-items: center;
  justify-content: center;

  color: #16a34a;
  font-size: 54px;
  font-weight: bold;

  margin-bottom: 30px;
}

h1 {
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 20px;
  color: #111;
}

p {
  text-align: center;
  color: #64748b;
  font-size: 16px;
  line-height: 1.5;
  margin-bottom: 60px;
}

button {
  background: #ef2020;
  color: white;
  border: none;
  border-radius: 12px;

  padding: 16px 34px;

  font-size: 18px;
  font-weight: 600;
  cursor: pointer;

  transition: .2s;
}

button:hover {
  background: #d81b1b;
}
</style>