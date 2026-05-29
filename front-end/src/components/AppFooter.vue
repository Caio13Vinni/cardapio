<template>
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-section">
        <h4>Cardáp.io</h4>
        <p>Cardápios digitais modernos para restaurantes que querem autonomia.</p>
      </div>
      <div class="footer-section">
        <h5>Produto</h5>
        <ul>
          <li><a href="#recursos">Recursos</a></li>
          <li><a href="#precos">Preços</a></li>
          <li><a href="#demo">Demonstração</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h5>Empresa</h5>
        <ul>
          <li><a href="#sobre">Sobre</a></li>
          <li><a href="#contato">Contato</a></li>
          <li><a href="#blog">Blog</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h5>Suporte</h5>
        <ul>
          <li><a href="#central">Central de Ajuda</a></li>
          <li><a href="#status">Status</a></li>
          <li><a href="#privacidade">Privacidade</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2026 Cardápio.io. Todos os direitos reservados.</p>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'AppFooter'
}
</script>

<style scoped>
.footer {
  background-color: #0F172A;
  color: white;
  padding: 60px 40px 20px;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 40px;
  margin-bottom: 40px;
}

.footer-section h4,
.footer-section h5 {
  margin-bottom: 15px;
  font-size: 16px;
}

.footer-section p {
  font-size: 13px;
  color: #999;
  line-height: 1.6;
}

.footer-section ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-section li {
  margin-bottom: 10px;
}

.footer-section a {
  text-decoration: none;
  color: #999;
  font-size: 13px;
  transition: color 0.3s;
}

.footer-section a:hover {
  color: #e74c3c;
}

.footer-bottom {
  text-align: center;
  padding-top: 20px;
  border-top: 1px solid #333;
  font-size: 13px;
  color: #666;
}

@media (max-width: 768px) {
  .footer-container {
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
}
</style>