<template>
  <div class="page-wrapper">
    <div class="alert-card">
      <div class="alert-icon"><img src="../assets/Correto.svg" alt="Correto"></div>
      <div class="alert-content">
        <h3>Cardápio publicado</h3>
        <p>Seu cardápio está ativo e acessível para os clientes</p>
        <div class="alert-buttons">
          <button class="btn-secondary">Ver cardápio público</button>
          <button class="btn-secondary">Baixar QR Code</button>
        </div>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon blue"><img src="../assets/Pastas.svg" alt="pasta"></div>
        <div class="stat-info">
          <p class="stat-label">Categorias</p>
          <p class="stat-number">{{ stats.categorias }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon red"><img src="../assets/Frame.svg" alt="garfo"></div>
        <div class="stat-info">
          <p class="stat-label">Pratos Totais</p>
          <p class="stat-number">{{ stats.pratosTotais }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green"><img src="../assets/Olho.svg" alt="olho"></div>
        <div class="stat-info">
          <p class="stat-label">Pratos Ativos</p>
          <p class="stat-number">{{ stats.pratosAtivos }}</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon gray"><img src="../assets/OlhoRisco.svg" alt="risco"></div>
        <div class="stat-info">
          <p class="stat-label">Pratos Inativos</p>
          <p class="stat-number">{{ stats.pratosInativos }}</p>
        </div>
      </div>
    </div>

    <div class="content-grid">
      <div class="left-column">
        <div class="card">
          <div class="card-header">
            <h3>Últimos pratos editados</h3>
            <a href="#" class="link">Ver todos</a>
          </div>
          <div class="card-body">
            <div class="dish-item" v-for="dish in recentDishes" :key="dish.id">
              <div class="dish-info">
                <p class="dish-name">{{ dish.name }}</p>
                <p class="dish-category">{{ dish.category }}</p>
              </div>
              <p class="dish-time">{{ dish.time }}</p>
            </div>
            <div v-if="recentDishes.length === 0" style="padding: 10px 0; color: #999; font-size: 14px;">
              Nenhum prato recente.
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3>Categorias e status</h3>
            <a href="#" class="link">Gerenciar</a>
          </div>
          <div class="card-body">
            <div class="category-item" v-for="cat in categories" :key="cat.id">
              <div class="category-info">
                <p class="category-name">{{ cat.name }}</p>
                <p class="category-count">{{ cat.count }} prato{{ cat.count > 1 ? 's' : '' }}</p>
              </div>
              <span :class="['status-badge', cat.status.toLowerCase()]">{{ cat.status }}</span>
            </div>
            <div v-if="categories.length === 0" style="padding: 10px 0; color: #999; font-size: 14px;">
              Nenhuma categoria cadastrada.
            </div>
          </div>
        </div>
      </div>

      <div class="right-column">
        <div class="card">
          <h3>Checklist de publicação</h3>
          <div class="checklist">
            <div class="checkbox-item">
              <input type="checkbox" id="check1" v-model="checklist.logo" />
              <label for="check1">Adicionar logo do restaurante</label>
            </div>
            <div class="checkbox-item">
              <input type="checkbox" id="check2" v-model="checklist.colors" />
              <label for="check2">Configurar cores da marca</label>
            </div>
            <div class="checkbox-item">
              <input type="checkbox" id="check3" v-model="checklist.dishes" />
              <label for="check3">Adicionar pelo menos 10 pratos</label>
            </div>
            <div class="checkbox-item">
              <input type="checkbox" id="check4" v-model="checklist.banner" />
              <label for="check4">Criar banner personalizado</label>
            </div>
            <div class="checkbox-item">
              <input type="checkbox" id="check5" v-model="checklist.mobile" />
              <label for="check5">Testar preview mobile</label>
            </div>
          </div>
          <div class="progress-container">
            <p class="progress-label">Progresso</p>
            <div class="progress-bar">
              <div class="progress-fill" :style="{ width: progressPercentage + '%' }"></div>
            </div>
            <p class="progress-text">{{ completedItems }}/5 completos</p>
          </div>
        </div>

        <div class="card">
          <h3>Identidade visual</h3>
          <p class="card-subtitle">Logo do restaurante</p>
          <div class="upload-area">
            <p>📤</p>
            <p class="upload-text">Clique para fazer upload</p>
          </div>
          <div class="colors-section">
            <p class="colors-title">Cores</p>
            <div class="colors">
              <div class="color-item">
                <div class="color-box primary"></div>
                <p>Primária</p>
              </div>
              <div class="color-item">
                <div class="color-box secondary"></div>
                <p>Secundária</p>
              </div>
            </div>
          </div>
          <a href="#" class="link">Editar identidade visual</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DashboardPage',
  data() {
    return {
      idRestaurante: null,
      stats: {
        categorias: 0,
        pratosTotais: 0,
        pratosAtivos: 0,
        pratosInativos: 0
      },
      recentDishes: [],
      categories: [],
      checklist: {
        logo: true,
        colors: true,
        dishes: true,
        banner: false,
        mobile: false
      }
    }
  },
  mounted() {
    // Pega o ID do restaurante logado no navegador
    this.idRestaurante = localStorage.getItem('id_restaurante');
    
    if (!this.idRestaurante) {
      this.$router.push('/login');
      return;
    }
    
    // Chama a função para buscar os dados do PHP assim que a tela abre
    this.carregarDashboard();
  },
  computed: {
    completedItems() {
      return Object.values(this.checklist).filter(v => v).length
    },
    progressPercentage() {
      return (this.completedItems / 5) * 100
    }
  },
  methods: {
    carregarDashboard() {
      // Usando o formato de URL que você pediu
      const urlBackend = `/cardapio/back-end/dashboard.php?id_restaurante=${this.idRestaurante}`;

      fetch(urlBackend)
        .then(res => res.json())
        .then(data => {
          // Alimenta as variáveis do front com a resposta do seu back-end
          if (data.sucesso || data.success) {
            this.stats.categorias = data.stats.categorias || 0;
            this.stats.pratosTotais = data.stats.pratosTotais || 0;
            this.stats.pratosAtivos = data.stats.pratosAtivos || 0;
            this.stats.pratosInativos = data.stats.pratosInativos || 0;
            
            this.recentDishes = data.recentDishes || [];
            this.categories = data.categories || [];
          }
        })
        .catch(err => {
          console.error('Erro ao carregar dados do dashboard:', err);
        });
    }
  }
}
</script>

<style scoped>
/* TODO O SEU CSS FOI MANTIDO 100% INTACTO AQUI PARA BAIXO */
.page-wrapper {
  padding: 40px;
  overflow-y: auto;
}

/* ALERT CARD */
.alert-card {
  background-color: #dc2626;
  color: white;
  padding: 24px;
  border-radius: 12px;
  margin-bottom: 30px;
  display: flex;
  gap: 16px;
  align-items: flex-start;
}

.alert-icon {
  font-size: 24px;
  min-width: 32px;
  text-align: center;
}

.alert-content h3 {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 4px;
}

.alert-content p {
  font-size: 14px;
  margin-bottom: 12px;
}

.alert-buttons {
  display: flex;
  gap: 12px;
}

.btn-secondary {
  background-color: rgba(255, 255, 255, 0.2);
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: background-color 0.3s;
}

.btn-secondary:hover {
  background-color: rgba(255, 255, 255, 0.3);
}

/* STATS GRID */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 16px;
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}

.stat-icon.blue {
  background-color: #dbeafe;
}

.stat-icon.red {
  background-color: #fee2e2;
}

.stat-icon.green {
  background-color: #dcfce7;
}

.stat-icon.gray {
  background-color: #f3f4f6;
}

.stat-label {
  font-size: 14px;
  color: #999;
  margin-bottom: 4px;
}

.stat-number {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
}

/* CONTENT GRID */
.content-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
}

@media (max-width: 1200px) {
  .content-grid {
    grid-template-columns: 1fr;
  }
}

/* CARDS */
.card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.card-header h3 {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
}

.link {
  color: #666;
  text-decoration: none;
  font-size: 14px;
  transition: color 0.3s;
}

.link:hover {
  color: #1a1a1a;
}

.card h3 {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 16px;
}

.card-subtitle {
  font-size: 14px;
  color: #666;
  margin-bottom: 12px;
}

/* DISHES */
.dish-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
}

.dish-item:last-child {
  border-bottom: none;
}

.dish-name {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
}

.dish-category {
  font-size: 12px;
  color: #999;
}

.dish-time {
  font-size: 12px;
  color: #ccc;
}

/* CATEGORIES */
.category-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f0f0f0;
}

.category-item:last-child {
  border-bottom: none;
}

.category-name {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
}

.category-count {
  font-size: 12px;
  color: #999;
}

.status-badge {
  font-size: 12px;
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 600;
}

.status-badge.ativo {
  background-color: #dcfce7;
  color: #16a34a;
}

.status-badge.inativo {
  background-color: #e5e7eb;
  color: #6b7280;
}

/* CHECKLIST */
.checklist {
  margin-bottom: 20px;
}

.checkbox-item {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
  cursor: pointer;
}

.checkbox-item input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #16a34a;
}

.checkbox-item label {
  font-size: 14px;
  color: #666;
  cursor: pointer;
  margin: 0;
}

.progress-container {
  margin-top: 20px;
}

.progress-label {
  font-size: 12px;
  color: #999;
  margin-bottom: 8px;
}

.progress-bar {
  width: 100%;
  height: 8px;
  background-color: #f0f0f0;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-fill {
  height: 100%;
  background-color: #16a34a;
  transition: width 0.3s;
}

.progress-text {
  font-size: 12px;
  color: #666;
  text-align: right;
}

/* UPLOAD AREA */
.upload-area {
  border: 2px dashed #ddd;
  border-radius: 8px;
  padding: 40px 20px;
  text-align: center;
  margin-bottom: 20px;
  cursor: pointer;
  transition: border-color 0.3s;
}

.upload-area:hover {
  border-color: #999;
}

.upload-area p:first-child {
  font-size: 32px;
  margin-bottom: 8px;
}

.upload-text {
  font-size: 14px;
  color: #999;
}

/* COLORS */
.colors-section {
  margin-bottom: 16px;
}

.colors-title {
  font-size: 14px;
  color: #666;
  margin-bottom: 12px;
}

.colors {
  display: flex;
  gap: 16px;
}

.color-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.color-box {
  width: 48px;
  height: 48px;
  border-radius: 6px;
}

.color-box.primary {
  background-color: #fcd34d;
}

.color-box.secondary {
  background-color: #dc2626;
}

.color-item p {
  font-size: 12px;
  color: #666;
}

/* RESPONSIVE */
@media (max-width: 768px) {
  .page-wrapper {
    padding: 20px;
  }

  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>