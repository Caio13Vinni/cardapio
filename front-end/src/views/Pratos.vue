<template>
  <div class="page-wrapper">
    <div class="pratos-header">
      <div class="pratos-title">
        <h2>Pratos</h2>
        <p>Gerencie o cardápio do seu restaurante</p>
      </div>
      <button class="btn-novo-prato" @click="$router.push('/dashboard/pratos/novo')">
        + Novo prato
      </button>
    </div>

    <div class="pratos-filters">
      <div class="search-box">
        <input 
          type="text" 
          placeholder="Buscar pratos"
          v-model="searchQuery"
        />
      </div>
      <div class="category-filter">
        <input 
          type="text" 
          placeholder="Todas as categorias"
          v-model="categoryFilter"
        />
      </div>
    </div>

    <div class="pratos-grid-container">
      
      <div v-if="pratos.length === 0 && !loading" style="text-align: center; color: #999; padding: 40px; width: 100%;">
        Nenhum prato encontrado. Que tal cadastrar o primeiro?
      </div>

      <div v-else class="pratos-grid">
        <div class="prato-card" v-for="prato in filteredPratos" :key="prato.id">
          <div class="prato-image">
            <img :src="prato.image" :alt="prato.name" v-if="prato.image" />
            <span v-else style="color: #ccc; font-size: 14px;">Sem imagem</span>
          </div>

          <div class="prato-info">
            <h3>{{ prato.name }}</h3>
            <p class="prato-category">{{ prato.category }}</p>
            <p class="prato-price">R$ {{ prato.price }}</p>
            
            <div class="prato-actions">
              <button class="btn-editar" @click="$router.push('/dashboard/pratos/editar/' + prato.id)">
                Editar
              </button>
              <button class="btn-icon btn-delete" @click="deletePrato(prato.id)" title="Excluir">
                <img src="../assets/lixo.svg" alt="lixo">
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PratosPage',
  data() {
    return {
      idRestaurante: null,
      loading: false,
      searchQuery: '',
      categoryFilter: '',
      pratos: [] // Começa vazio e carrega do banco
    }
  },
  computed: {
    filteredPratos() {
      return this.pratos.filter(prato => {
        const matchName = prato.name.toLowerCase().includes(this.searchQuery.toLowerCase())
        const matchCategory = prato.category.toLowerCase().includes(this.categoryFilter.toLowerCase()) || this.categoryFilter === ''
        return matchName && matchCategory
      })
    }
  },
  mounted() {
    this.idRestaurante = localStorage.getItem('id_restaurante');
    if (!this.idRestaurante) {
      this.$router.push('/login');
      return;
    }
    
    this.fetchPratos();
  },
  methods: {
    deletePrato(id) {
      if (confirm('Tem certeza que deseja excluir este prato?')) {
        const urlBackend = `/cardapio/back-end/pratos_acoes.php?id=${id}`;

        fetch(urlBackend, { method: 'DELETE' })
          .then(res => res.json())
          .then(data => {
            if (data.sucesso || data.success) {
              this.pratos = this.pratos.filter(prato => prato.id !== id);
            } else {
              alert(data.mensagem || 'Erro ao excluir prato.');
            }
          })
          .catch(err => console.error('Erro:', err));
      }
    },

    fetchPratos() {
      this.loading = true;
      const urlBackend = `/cardapio/back-end/pratos_acoes.php?id_restaurante=${this.idRestaurante}`;
      
      fetch(urlBackend)
        .then(res => res.json())
        .then(data => {
          if (Array.isArray(data)) {
            // Mapeia os dados do PHP (nome, preco, etc) para as variáveis que o HTML do Davi já esperava (name, price)
            this.pratos = data.map(p => {
              // Verifica se a imagem existe e monta a URL completa
              let imgUrl = '';
              if (p.imagem) {
                imgUrl = p.imagem.includes('http') ? p.imagem : `/cardapio/back-end/${p.imagem}`;
              }

              // Formata o preço com vírgula para manter o visual BR
              const precoFormatado = parseFloat(p.preco || 0).toFixed(2).replace('.', ',');

              return {
                id: p.id,
                name: p.nome,
                category: p.categoria || 'Sem categoria', // Ajuste dependendo do nome da coluna que vem do JOIN no PHP
                price: precoFormatado,
                image: imgUrl
              };
            });
          }
        })
        .catch(err => console.error('Erro ao buscar pratos:', err))
        .finally(() => {
          this.loading = false;
        });
    }
  }
}
</script>

<style scoped>
/* TODO O CSS MANTIDO INTACTO DO DAVI */
.page-wrapper {
  padding: 40px;
  overflow-y: auto;
}

/* PRATOS HEADER */
.pratos-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 40px;
}

.pratos-title h2 {
  font-size: 32px;
  color: #1a1a1a;
  font-weight: 700;
  margin-bottom: 8px;
}

.pratos-title p {
  font-size: 14px;
  color: #999;
}

.btn-novo-prato {
  background-color: #ef2020;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  transition: background-color 0.3s;
}

.btn-novo-prato:hover {
  background-color: #d91a1a;
}

/* FILTERS */
.pratos-filters {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 40px;
}

.search-box input,
.category-filter input {
  width: 100%;
  padding: 14px 16px;
  border: 1px solid #e8e8e8;
  border-radius: 8px;
  font-size: 14px;
  color: #666;
  outline: none;
  transition: border-color 0.3s;
}

.search-box input::placeholder,
.category-filter input::placeholder {
  color: #ccc;
}

.search-box input:focus,
.category-filter input:focus {
  border-color: #ef2020;
}

/* PRATOS GRID CONTAINER */
.pratos-grid-container {
  display: flex;
  justify-content: center;
  width: 100%;
}

/* PRATOS GRID - 3 COLUNAS */
.pratos-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  max-width: 1200px;
  width: 100%;
}

.prato-card {
  background: white;
  border: 1px solid #e8e8e8;
  border-radius: 8px;
  overflow: hidden;
  transition: box-shadow 0.3s, transform 0.3s;
  display: flex;
  flex-direction: column;
}

.prato-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.prato-image {
  width: 100%;
  height: 200px;
  overflow: hidden;
  background-color: #f5f5f5;
  display: flex;
  align-items: center;
  justify-content: center;
}

.prato-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.prato-card:hover .prato-image img {
  transform: scale(1.05);
}

.prato-info {
  padding: 20px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.prato-info h3 {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 4px;
}

.prato-category {
  font-size: 12px;
  color: #999;
  margin-bottom: 12px;
}

.prato-price {
  font-size: 18px;
  font-weight: 700;
  color: #ef2020;
  margin-bottom: 16px;
}

.btn-editar {
  background: white;
  color: #666;
  border: 1px solid #e8e8e8;
  padding: 10px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-editar:hover {
  border-color: #ef2020;
  color: #ef2020;
  background-color: #fce4e4;
}

.prato-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: auto;
}

.prato-actions .btn-editar {
  flex: 1;
  margin-top: 0;
}

.btn-icon {
  background: white;
  border: 1px solid #e8e8e8;
  font-size: 18px;
  cursor: pointer;
  transition: all 0.3s;
  padding: 10px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-icon img {
  width: 16px;
  height: 16px;
}

.btn-icon.btn-delete:hover {
  border-color: #ef2020;
  background-color: #fee2e2;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .pratos-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .pratos-header {
    flex-direction: column;
    gap: 20px;
  }
}

@media (max-width: 768px) {
  .page-wrapper {
    padding: 20px;
  }

  .pratos-filters {
    grid-template-columns: 1fr;
  }

  .pratos-grid {
    grid-template-columns: 1fr;
  }
}
</style>