<template>
  <div class="erro-page">
    <div class="logo-area">
      <img src="../assets/Logo.svg" alt="Cardáp.io" />
      <h2>Cardáp<span>.io</span></h2>
    </div>

    <div class="content">
      <div class="left">
        <h1>
          4<span>0</span>4
        </h1>

        <h2>Ops! Cardápio não encontrado</h2>

        <div class="line"></div>

        <p class="text">
          Não conseguimos encontrar este cardápio no momento.
          <br />
          Ele pode ter sido removido, alterado ou estar
          <br />
          temporariamente indisponível.
        </p>

        <p class="small">
          Mas não se preocupe — temos outras delícias te esperando!
        </p>

        <button @click="$router.back()">Voltar</button>
      </div>

      <div class="right">
        <img src="../assets/Erro404.svg" alt="Erro 404" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Erro404Page'
}
</script>

<style scoped>
/* TODO O CSS ORIGINAL DO DAVI MANTIDO INTACTO */
.erro-page {
  height: 100vh;
  overflow: hidden;
  background: white;
  padding: 15px 95px;
  font-family: "Poppins", sans-serif;
}

.logo-area {
  display: flex;
  align-items: center;
  gap: 22px;
}

.logo-area img {
  width: 120px;
}

.logo-area h2 {
  font-size: 38px;
  font-weight: 800;
  color: #0f172a;
}

.logo-area h2 span {
  color: red;
}

.content {
  height: calc(80vh - 180px);
  display: grid;
  grid-template-columns: 1fr 1fr;
  align-items: center;
}

.right img {
  width: 100%;
  max-width: 560px;
}

.content {
  margin-top: 95px;
  display: grid;
  grid-template-columns: 1fr 1.2fr;
  align-items: center;
}

.left h1 {
  font-size: 140px;
  line-height: 1;
  font-weight: 900;
  color: #0f172a;
}

.left h1 span {
  color: red;
}

.left h2 {
  font-size: 36px;
  margin-top: 22px;
  font-weight: 800;
}

.line {
  width: 120px;
  height: 6px;
  background: red;
  border-radius: 20px;
  margin: 28px 0 45px;
}

.text {
  font-size: 22px;
  line-height: 1.5;
  color: #111;
}

.small {
  margin-top: 28px;
  color: #666;
  font-size: 16px;
}

button {
  margin-top: 25px;
  background: #e92929;
  color: white;
  border: none;
  border-radius: 10px;
  padding: 13px 35px;
  font-size: 18px;
  font-weight: 700;
  cursor: pointer;
}

button:hover {
  background: #d91b1b;
}

.right img {
  width: 100%;
  max-width: 760px;
}

@media (max-width: 900px) {
  .erro-page {
    padding: 40px 25px;
  }

  .logo-area img {
    width: 230px;
  }

  .content {
    grid-template-columns: 1fr;
    margin-top: 50px;
  }

  .left h1 {
    font-size: 100px;
  }

  .right {
    margin-top: 40px;
  }
}
</style>