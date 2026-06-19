<template>
  <div class="nova-categoria">

    <button class="btn-voltar" @click="$router.push('/dashboard/categorias')">
      <img src="../assets/Seta.svg" alt="SetaIcon"> Voltar para as categorias
    </button>

    <div class="header">
      <h1>Nova categoria</h1>
      <p>Crie uma nova categoria para organizar seus pratos</p>
    </div>

    <div class="card">

      <h2>Informações da categoria</h2>

      <div class="form-group">
        <label>Nome da categoria</label>
        <input
          v-model="categoria.nome"
          type="text"
          placeholder="Ex: Massas, Entradas, Carnes..."
        >
      </div>

      <div class="form-group">
        <label>Descrição (opcional)</label>
        <textarea
          v-model="categoria.descricao"
          rows="6"
          placeholder="Adicione uma descrição para esta categoria"
        />
      </div>

      <div class="divider"></div>

      <div class="switch-area">

        <label class="switch">
          <input
            type="checkbox"
            v-model="categoria.ativa"
          >
          <span class="slider"></span>
        </label>

        <div>
          <strong>Categoria ativa</strong>
          <p>
            Categorias inativas não aparecem no cardápio público
          </p>
        </div>

      </div>

    </div>

    <div class="actions">
      <button class="btn-cancelar" @click="$router.push('/dashboard/categorias')">
        Cancelar
      </button>

      <button
        class="btn-salvar"
        @click="salvarCategoria"
        :disabled="loading"
      >
        <img src="../assets/Salvar.svg" alt="SalveIcon"> 
        {{ loading ? 'Salvando...' : 'Criar categoria' }}
      </button>
    </div>

  </div>
</template>

<script>
export default {
  name: 'NovaCategoriaPage',

  data() {
    return {
      idRestaurante: null,
      loading: false,
      categoria: {
        nome: '',
        descricao: '',
        ativa: true
      }
    }
  },

  mounted() {
    // Garante que a página saiba a qual restaurante a categoria pertence
    this.idRestaurante = localStorage.getItem('id_restaurante');
    if (!this.idRestaurante) {
      this.$router.push('/login');
    }
  },

  methods: {
    salvarCategoria() {
      // Validação básica antes de enviar para o PHP
      if (!this.categoria.nome.trim()) {
        alert('Por favor, informe o nome da categoria.');
        return;
      }

      this.loading = true;
      const urlBackend = '/cardapio/back-end/categorias_acoes.php';

      fetch(urlBackend, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          id_restaurante: this.idRestaurante,
          nome: this.categoria.nome,
          descricao: this.categoria.descricao,
          // Converte o true/false do Vue para o formato 1/0 do MySQL
          ativo: this.categoria.ativa ? 1 : 0 
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.sucesso || data.success) {
          alert('Categoria criada com sucesso!');
          this.$router.push('/dashboard/categorias');
        } else {
          alert(data.mensagem || 'Erro ao criar a categoria.');
        }
      })
      .catch(err => {
        console.error('Erro:', err);
        alert('Erro de comunicação com o servidor.');
      })
      .finally(() => {
        this.loading = false;
      });
    }
  }
}
</script>

<style scoped>
/* TODO O CSS MANTIDO IGUAL AO DO DAVI */
.nova-categoria {
  padding: 30px;
  width: 100%;
}

.btn-voltar {
  background: none;
  border: none;
  cursor: pointer;
  color: #64748b;
  margin-bottom: 24px;
  font-size: 15px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.header h1 {
  font-size: 44px;
  margin-bottom: 8px;
}

.header p {
  color: #888;
  margin-bottom: 30px;
}

.card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 20px;
  padding: 32px;
}

.card h2 {
  margin-bottom: 28px;
}

.form-group {
  margin-bottom: 25px;
}

.form-group label {
  display: block;
  margin-bottom: 10px;
  font-weight: 600;
}

input,
textarea {
  width: 100%;
  border: 1px solid #d1d5db;
  border-radius: 12px;
  padding: 14px;
  font-size: 15px;
}

.divider {
  height: 1px;
  background: #e5e7eb;
  margin: 30px 0;
}

.switch-area {
  display: flex;
  gap: 20px;
  align-items: center;
}

.switch {
  position: relative;
  width: 60px;
  height: 32px;
}

.switch input {
  display: none;
}

.slider {
  position: absolute;
  inset: 0;
  background: #ccc;
  border-radius: 50px;
  cursor: pointer;
}

.slider::before {
  content: '';
  position: absolute;
  width: 24px;
  height: 24px;
  background: white;
  left: 4px;
  top: 4px;
  border-radius: 50%;
  transition: .3s;
}

.switch input:checked + .slider {
  background: #39c24a;
}

.switch input:checked + .slider::before {
  transform: translateX(28px);
}

.actions {
  margin-top: 30px;
  display: flex;
  justify-content: flex-end;
  gap: 16px;
}

.btn-cancelar {
  background: transparent;
  border: none;
  cursor: pointer;
  font-size: 15px;
}

.btn-salvar {
  background: #ef2020;
  color: white;
  border: none;
  border-radius: 12px;
  padding: 14px 22px;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-salvar:disabled {
  background: #fca5a5;
  cursor: not-allowed;
}
</style>