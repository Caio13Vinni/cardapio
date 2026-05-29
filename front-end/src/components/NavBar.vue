<template>
  <nav class="navbar">
    <div class="navbar-container">
      <div class="logo">
        <img src="../assets/Logo.svg" alt="Cardápio.io">
        <span>Cardáp.io</span>
      </div>

      <ul class="nav-links">
        <li><a href="#inicio">Início</a></li>
        <li><a href="#como-funciona">Como funciona</a></li>
        <li><a href="#recursos">Recursos</a></li>
        <li><a href="#demonstracao">Demonstração</a></li>
        <li><a href="#contato">Contato</a></li>
      </ul>

      <button class="btn-entrar" @click="$router.push('/login')">Entrar</button>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'NavBar'
}
</script>

<style scoped>

.navbar {
  background-color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 100;
}

.navbar-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 15px 40px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 18px;
  font-weight: bold;
  color: #1a1a1a;
  cursor: pointer;
}

.logo img {
  height: 40px;
  width: auto;
}

.nav-links {
  border-radius: 30px;
  border: 2px solid gray;
  display: flex;
  list-style: none;
  gap: 30px;
  margin: 0;
  padding: 15px 30px;
}

.nav-links a {
  text-decoration: none;
  color: #666;
  font-size: 14px;
  transition: color 0.3s;
}

.nav-links a:hover {
  color: #e74c3c;
}

.btn-entrar {
  background-color: #e74c3c;
  color: white;
  border: none;
  padding: 10px 25px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
  transition: background-color 0.3s;
}

.btn-entrar:hover {
  background-color: #c0392b;
}

@media (max-width: 768px) {
  .navbar-container {
    padding: 15px 20px;
  }

  .nav-links {
    display: none;
  }

  .logo span {
    display: none;
  }
}
</style>