<template>
  <div class="page-wrapper">
    <div class="categories-header">
      <div class="categories-title">
        <h2>Categorias</h2>
        <p>Organize seus pratos em categorias</p>
      </div>
      <button class="btn-nova-categoria" @click="$router.push('/dashboard/categorias/nova')">
        + Nova categoria
      </button>
    </div>

    <div class="categories-list">
      <div class="category-item" v-for="category in categories" :key="category.id">
        <div class="category-drag">
          <span class="drag-icon">⋮⋮</span>
        </div>

        <div class="category-info">
          <div class="category-name-section">
            <h3>{{ category.name }}</h3>
            <span v-if="category.status" class="status-badge">{{ category.status }}</span>
          </div>
          <p class="category-count">
            <span class="icon"><img src="../assets/Garfo.svg" alt="Garfo"></span>
            {{ category.count }} Prato{{ category.count > 1 ? 's' : '' }}
          </p>
        </div>

        <div class="category-actions">
          <div class="toggle-container">
            <label class="switch">
              <input 
                type="checkbox" 
                v-model="category.active"
                @change="toggleCategory(category.id)"
              />
              <span class="slider"></span>
            </label>
          </div>

          <button class="btn-icon" @click="$router.push(`/dashboard/categorias/editar?id=${category.id}`)">
            <span><img src="../assets/lapis.svg" alt="lapis"></span>
          </button>

          <button class="btn-icon btn-delete" @click="deleteCategory(category.id)" title="Deletar">
            <span><img src="../assets/lixo.svg" alt="lixo"></span>
          </button>
        </div>
      </div>
      
      <div v-if="categories.length === 0" style="text-align: center; color: #999; padding: 20px;">
        Nenhuma categoria cadastrada ainda.
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CategoriasPage',
  data() {
    return {
      idRestaurante: null,
      categories: []
    }
  },
  mounted() {
    this.idRestaurante = localStorage.getItem('id_restaurante');
    
    if (!this.idRestaurante) {
      this.$router.push('/login');
      return;
    }
    
    this.fetchCategories();
  },
  methods: {
    fetchCategories() {
      // Padrão de URL estabelecido
      const urlBackend = `/cardapio/back-end/categorias_acoes.php?id_restaurante=${this.idRestaurante}`;
      
      fetch(urlBackend)
        .then(res => res.json())
        .then(data => {
          if (Array.isArray(data)) {
            // Mapeia o retorno do banco de dados (ex: 'nome', 'total_pratos') 
            // para as variáveis que o HTML do Davi usa ('name', 'count')
            this.categories = data.map(cat => ({
              id: cat.id,
              name: cat.nome,
              count: cat.total_pratos || 0,
              active: parseInt(cat.ativo) === 1,
              status: parseInt(cat.ativo) === 1 ? null : 'Inativo'
            }));
          }
        })
        .catch(err => console.error('Erro ao buscar categorias:', err));
    },

    deleteCategory(id) {
      if (confirm('Tem certeza que deseja deletar esta categoria?')) {
        const urlBackend = `/cardapio/back-end/categorias_acoes.php?id=${id}`;
        
        fetch(urlBackend, { method: 'DELETE' })
          .then(res => res.json())
          .then(data => {
            if (data.sucesso || data.success) {
              // Remove a categoria da tela sem precisar recarregar a página
              this.categories = this.categories.filter(cat => cat.id !== id);
            } else {
              alert(data.mensagem || 'Erro ao deletar categoria.');
            }
          })
          .catch(err => console.error('Erro:', err));
      }
    },

    toggleCategory(id) {
      const category = this.categories.find(cat => cat.id === id);
      if (!category) return;

      // Altera o badge visual na hora
      category.status = category.active ? null : 'Inativo';

      const urlBackend = '/cardapio/back-end/categorias_acoes.php';
      const novoStatus = category.active ? 1 : 0;

      fetch(urlBackend, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: category.id,
          id_restaurante: this.idRestaurante,
          nome: category.name,
          ativo: novoStatus
        })
      })
      .then(res => res.json())
      .then(data => {
        if (!data.sucesso && !data.success) {
          // Se der erro no PHP, desfaz o clique visualmente
          category.active = !category.active;
          category.status = category.active ? null : 'Inativo';
          alert('Erro ao alterar status da categoria no banco de dados.');
        }
      })
      .catch(err => {
        console.error('Erro:', err);
        category.active = !category.active;
        category.status = category.active ? null : 'Inativo';
      });
    }
  }
}
</script>

<style scoped>
/* CSS do Davi 100% mantido */
.page-wrapper {
  padding: 40px;
  overflow-y: auto;
}

/* CATEGORIES HEADER */
.categories-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 40px;
}

.categories-title h2 {
  font-size: 32px;
  color: #1a1a1a;
  font-weight: 700;
  margin-bottom: 8px;
}

.categories-title p {
  font-size: 14px;
  color: #999;
}

.btn-nova-categoria {
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

.btn-nova-categoria:hover {
  background-color: #d91a1a;
}

/* CATEGORIES LIST */
.categories-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.category-item {
  background: white;
  border: 1px solid #e8e8e8;
  border-radius: 8px;
  padding: 20px 24px;
  display: flex;
  align-items: center;
  gap: 20px;
  transition: box-shadow 0.3s;
}

.category-item:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.category-drag {
  display: flex;
  align-items: center;
  cursor: grab;
  color: #ccc;
  font-size: 16px;
}

.drag-icon {
  letter-spacing: 2px;
}

.category-info {
  flex: 1;
}

.category-name-section {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
}

.category-name-section h3 {
  font-size: 16px;
  font-weight: 600;
  color: #1a1a1a;
}

.status-badge {
  background-color: #e5e7eb;
  color: #6b7280;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.category-count {
  font-size: 13px;
  color: #999;
  display: flex;
  align-items: center;
  gap: 6px;
}

.category-count .icon {
  font-size: 14px;
}

.category-actions {
  display: flex;
  align-items: center;
  gap: 16px;
}

/* TOGGLE SWITCH */
.toggle-container {
  display: flex;
  align-items: center;
}

.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 28px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #e8e8e8;
  transition: 0.4s;
  border-radius: 28px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 22px;
  width: 22px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #22c55e;
}

input:checked + .slider:before {
  transform: translateX(22px);
}

/* ACTION BUTTONS */
.btn-icon {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  transition: transform 0.3s;
  padding: 8px;
  border-radius: 6px;
}

.btn-icon:hover {
  transform: scale(1.1);
  background-color: #f5f5f5;
}

.btn-icon.btn-delete:hover {
  background-color: #fee2e2;
}

/* RESPONSIVE */
@media (max-width: 1024px) {
  .categories-header {
    flex-direction: column;
    gap: 20px;
  }

  .category-item {
    flex-wrap: wrap;
  }
}

@media (max-width: 768px) {
  .page-wrapper {
    padding: 20px;
  }

  .category-item {
    gap: 12px;
  }

  .category-name-section {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>