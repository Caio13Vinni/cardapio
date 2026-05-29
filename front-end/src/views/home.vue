<template>
  <div class="home">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <h1>Crie e gerencie o cardápio digital do seu restaurante com autonomia</h1>
        <p>Atualize pratos, categorias, identidade visual e publique seu menu por link e QR Code sem depender de programador.</p>
        <button class="btn-primary">Criar conta grátis</button>
      </div>
      
      <div class="hero-images">
        <img src="../assets/notebook.svg" alt="Cardápio Digital">
        <img src="../assets/celular.svg" alt="Cardápio Digital Mobile">
        <p class="hero-subtitle">Sem cartão de crédito • Comece em menos de 2 minutos</p>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
      <div class="stat-card">
        <h3>1.500+</h3>
        <p>Restaurantes ativos</p>
      </div>
      <div class="stat-card">
        <h3>50k+</h3>
        <p>Cardápios visualizados</p>
      </div>
      <div class="stat-card">
        <h3>98%</h3>
        <p>Satisfação</p>
      </div>
      <div class="stat-card">
        <h3>24/7</h3>
        <p>Disponibilidade</p>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features">
      <h2>Como funciona</h2>
      <p class="features-subtitle">Crie seu cardápio digital em 3 passos simples</p>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-number">1</div>
          <h3>Cadastre seu restaurante</h3>
          <p>Crie sua conta em menos de 2 minutos e configure as informações básicas do seu estabelecimento.</p>
        </div>
        <div class="feature-card">
          <div class="feature-number">2</div>
          <h3>Adicione seus pratos</h3>
          <p>Organize por categorias, adicione fotos, descrições e preços. Tudo de forma simples e intuitiva.</p>
        </div>
        <div class="feature-card">
          <div class="feature-number">3</div>
          <h3>Publique e compartilhe</h3>
          <p>Gere QR Code e link públicos. Seus clientes acessam o cardápio digital.</p>
        </div>
      </div>
    </section>

    <!-- Resources Section -->
    <section class="resources">
      <h2>Todos os recursos que você precisa</h2>
      <p class="resources-subtitle">Ferramental completo para gerenciar seu cardápio digital</p>
      <div class="resources-grid">
        <div class="resource-card">
          <div class="resource-icon">1</div>
          <h3>Gestão de Categorias</h3>
          <p>Organize seus pratos em categorias, personalize a ordem, quantidade e muito mais.</p>
        </div>
        <div class="resource-card">
          <div class="resource-icon">2</div>
          <h3>Design Responsivo</h3>
          <p>Cardápio otimizado para mobile, tablet e desktop. Perfeito em qualquer dispositivo.</p>
        </div>
        <div class="resource-card">
          <div class="resource-icon">3</div>
          <h3>QR Code Integrado</h3>
          <p>Gere QR Codes personalizados para hospedar nas suas mesas, porta, etc.</p>
        </div>
        <div class="resource-card">
          <div class="resource-icon">4</div>
          <h3>Personalização Visual</h3>
          <p>Configure cores, logo, banner para deixar a identidade da sua marca.</p>
        </div>
        <div class="resource-card">
          <div class="resource-icon">5</div>
          <h3>Preview em Tempo Real</h3>
          <p>Visualize como ficará seu cardápio antes de publicar para os clientes.</p>
        </div>
        <div class="resource-card">
          <div class="resource-icon">6</div>
          <h3>Destaque de Pratos</h3>
          <p>Marque pratos especiais e organize por ordem de edição.</p>
        </div>
      </div>
    </section>

    <!-- Video/Demo Section -->
    <section class="demo">
      <h2>Veja o Cardápio.io em ação</h2>
      <p>Interface moderna e fácil de usar</p>
      <div class="demo-content">
        <img src="../assets/imagemPessoas.svg" alt="Demo do Cardápio.io">
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
      <h2>O que dizem nossos clientes</h2>
      <div class="testimonials-grid">
        <div class="testimonial-card">
          <div class="stars">★★★★★</div>
          <p>"Lorem ipsum dolor sit amet, consectetur. Agora posso atualizar o menu com agilidade!"</p>
          <h4>Carlos Silva</h4>
          <span>Pizzaria Silva</span>
        </div>
        <div class="testimonial-card">
          <div class="stars">★★★★★</div>
          <p>"Interface super intuitiva e suporte fantástico. Recomendo para qualquer restaurante!"</p>
          <h4>Marina Santos</h4>
          <span>Restaurante Marina</span>
        </div>
        <div class="testimonial-card">
          <div class="stars">★★★★★</div>
          <p>"Melhor investimento que fiz! Aumentei muito meu cardápio de clientes com o QR Code!"</p>
          <h4>Roberto Oliveira</h4>
          <span>Churrascaria Roberto</span>
        </div>
      </div>
    </section>

    <!-- Final CTA Section -->
    <section class="cta">
      <h2>Pronto para modernizar seu restaurante?</h2>
      <p>Junte-se a mais de 1.500 restaurantes que já usam o Cardápio.io</p>
      <button class="btn-primary">Juntar-se</button>
    </section>
  </div>
</template>

<script>
export default {
  name: 'HomePage'
}
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.home {
  width: 100%;
}

/* HERO SECTION */
.hero {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 80px 100px;  
  background: linear-gradient(135deg, #fff5f7 0%, #f0f9ff 100%);
  gap: 80px;
}

.hero-content {
  flex: 0 1 500px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.hero-content h1 {
  font-size: 48px;
  font-weight: bold;
  margin-bottom: 20px;
  color: #1a1a1a;
  line-height: 1.2;
}

.hero-content p {
  font-size: 16px;
  color: #666;
  margin-bottom: 30px;
  line-height: 1.6;
}

.hero-images {
  flex: 0 1 auto;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  gap: 0;  /* ← ZERO de espaço - totalmente coladas */
  position: relative;
}

.hero-images img {
  height: auto;
  filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.15));
}

.hero-images img:first-child {
  max-width: 500px;
}

.hero-images img:last-child {
  max-width: 180px;
}

.hero-subtitle {
  position: absolute;
  bottom: -50px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 13px;
  color: #999;
  white-space: nowrap;
  width: 100%;
  text-align: center;
}

.btn-primary {
  background-color: #e74c3c;
  color: white;
  padding: 12px 30px;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
  font-weight: bold;
  width: fit-content;
  margin: 0 auto;
}

.btn-primary:hover {
  background-color: #c0392b;
}

/* STATS SECTION */
.stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  padding: 60px 40px;
  background-color: #ffffff;
  text-align: center;
}

.stat-card h3 {
  font-size: 36px;
  font-weight: bold;
  color: #e74c3c;
  margin-bottom: 10px;
}

.stat-card p {
  font-size: 14px;
  color: #999;
}

/* FEATURES SECTION */
.features {
  padding: 80px 40px;
  background: linear-gradient(135deg, #FDDFE1 0%, #f5f5f5 50%, #D3E2FB 100%);
  text-align: center;
}

.features h2 {
  font-size: 36px;
  margin-bottom: 10px;
  color: #1a1a1a;
}

.features-subtitle {
  font-size: 16px;
  color: #999;
  margin-bottom: 50px;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
}

.feature-card {
  padding: 30px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  text-align: left;
}

.feature-number {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background-color: #f0f0f0;
  border-radius: 50%;
  font-weight: bold;
  color: #999;
  margin-bottom: 15px;
}

.feature-card h3 {
  font-size: 18px;
  margin-bottom: 10px;
  color: #1a1a1a;
}

.feature-card p {
  color: #666;
  font-size: 14px;
  line-height: 1.6;
}

/* RESOURCES SECTION */
.resources {
  padding: 80px 40px;
  background-color: #ffffff;
  text-align: center;
}

.resources h2 {
  font-size: 36px;
  margin-bottom: 10px;
  color: #1a1a1a;
}

.resources-subtitle {
  font-size: 16px;
  color: #999;
  margin-bottom: 50px;
}

.resources-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
}

.resource-card {
  padding: 30px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  text-align: left;
}

.resource-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background-color: #f0f0f0;
  border-radius: 50%;
  font-weight: bold;
  color: #999;
  margin-bottom: 15px;
}

.resource-card h3 {
  font-size: 16px;
  margin-bottom: 10px;
  color: #1a1a1a;
  font-weight: bold;
}

.resource-card p {
  color: #666;
  font-size: 13px;
  line-height: 1.6;
}

/* DEMO SECTION */
.demo {
  padding: 80px 40px;
  text-align: center;
  background: linear-gradient(135deg, #FDDFE1 0%, #f5f5f5 50%, #D3E2FB 100%);
}

.demo h2 {
  font-size: 36px;
  margin-bottom: 10px;
  color: #1a1a1a;
}

.demo > p {
  font-size: 16px;
  color: #999;
  margin-bottom: 50px;
}

.demo-content {
  display: flex;
  align-items: center;
  gap: 40px;
  justify-content: center;
}

.demo-content img {
  max-width: 900px;
  width: 100%;
  height: auto;
  border-radius: 8px;
}

.demo-benefits {
  flex: 0 1 300px;
  text-align: left;
  background-color: #e74c3c;
  padding: 40px;
  border-radius: 8px;
  color: white;
}

.benefit {
  display: flex;
  align-items: flex-start;
  gap: 15px;
  margin-bottom: 25px;
}

.benefit-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  background-color: rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  font-size: 18px;
  flex-shrink: 0;
}

.benefit p {
  font-size: 14px;
  line-height: 1.5;
  margin: 0;
}

/* TESTIMONIALS SECTION */
.testimonials {
  padding: 80px 40px;
  text-align: center;
}

.testimonials h2 {
  font-size: 36px;
  margin-bottom: 50px;
  color: #1a1a1a;
}

.testimonials-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
}

.testimonial-card {
  padding: 30px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  text-align: left;
}

.stars {
  color: #ffc107;
  font-size: 18px;
  margin-bottom: 15px;
}

.testimonial-card p {
  color: #666;
  font-size: 14px;
  margin-bottom: 20px;
  line-height: 1.6;
}

.testimonial-card h4 {
  color: #1a1a1a;
  font-size: 16px;
  margin: 0 0 5px 0;
}

.testimonial-card span {
  color: #999;
  font-size: 13px;
}

/* CTA SECTION */
.cta {
  padding: 80px 40px;
  background: linear-gradient(135deg, #FDDFE1 0%, #f5f5f5 50%, #D3E2FB 100%);
  text-align: center;
}

.cta h2 {
  font-size: 36px;
  margin-bottom: 10px;
  color: #1a1a1a;
}

.cta p {
  font-size: 16px;
  color: #999;
  margin-bottom: 30px;
}

/* RESPONSIVO */
@media (max-width: 1024px) {
  .hero {
    gap: 40px;
    padding: 60px 40px;
  }

  .hero-content h1 {
    font-size: 40px;
  }

  .hero-images img:first-child {
    max-width: 400px;
  }

  .features-grid,
  .resources-grid,
  .testimonials-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .hero {
    flex-direction: column;
    padding: 40px 20px;
    gap: 40px;
  }

  .hero-content h1 {
    font-size: 32px;
  }

  .hero-images {
    width: 100%;
  }

  .hero-images img:first-child {
    max-width: 100%;
  }

  .hero-images img:last-child {
    max-width: 120px;
  }

  .stats {
    grid-template-columns: repeat(2, 1fr);
  }

  .features-grid,
  .resources-grid,
  .testimonials-grid {
    grid-template-columns: 1fr;
  }

  .demo-content {
    flex-direction: column;
    gap: 20px;
  }

  .demo-benefits {
    width: 100%;
  }
}
</style>