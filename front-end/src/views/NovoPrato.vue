<template>
  <div class="novo-prato-page">
    <div class="page-header">
      <h1>Novo prato</h1>
      <p>Adicione um novo prato ao seu cardápio</p>
    </div>

    <form class="form-prato" @submit.prevent="criarPrato">
      <section class="card">
        <h3>Informações básicas</h3>

        <div class="upload-area">
          <label class="upload-box" style="overflow: hidden;">
            <input type="file" accept="image/png, image/jpeg, image/webp" hidden @change="handleImageUpload">
            <span v-if="!imagemPreview">
              <img src="../assets/Download.svg" alt="Download" height="30px" width="30px">
            </span>
            <img v-else :src="imagemPreview" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
          </label>

          <div>
            <!-- Transformado em label para abrir o seletor de arquivos sem perder o estilo do botão -->
            <label class="btn-upload" style="display: inline-block; cursor: pointer;">
              Escolher imagem
              <input type="file" accept="image/png, image/jpeg, image/webp" hidden @change="handleImageUpload">
            </label>
            <p>PNG, JPG ou WEBP até 5MB</p>
          </div>
        </div>

        <div class="form-group">
          <label>Nome do prato</label>
          <input 
            type="text" 
            v-model="form.nome"
            placeholder="Ex: Filé Mignon ao Molho Madeira"
            required
          >
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Categoria</label>
            <!-- Select agora é dinâmico, buscando as categorias do banco -->
            <select v-model="form.id_categoria" required>
              <option value="">Selecione uma categoria</option>
              <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                {{ cat.nome }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label>Preço</label>
            <input 
              type="text" 
              v-model="form.preco"
              placeholder="Ex: 29.90"
              required
            >
          </div>
        </div>

        <div class="form-group">
          <label>Descrição</label>
          <textarea 
            v-model="form.descricao"
            maxlength="500"
          ></textarea>
          <small>Máximo 500 caracteres</small>
        </div>
      </section>

      <section class="card">
        <h2>Configurações</h2>

        <div class="config-item">
          <div>
            <h4>Status do prato</h4>
            <p>Pratos inativos não aparecem no cardápio</p>
          </div>
          <label class="switch">
            <input type="checkbox" v-model="form.ativo">
            <span></span>
          </label>
        </div>

        <div class="config-item">
          <div>
            <h4>Prato em destaque</h4>
            <p>Aparece com badge especial no cardápio</p>
          </div>
          <label class="switch">
            <input type="checkbox" v-model="form.destaque">
            <span></span>
          </label>
        </div>

        <div class="form-group">
          <label>Ordem de exibição</label>
          <input type="number" v-model="form.ordem">
          <small>Pratos com menor número aparecem primeiro</small>
        </div>

        <div class="form-group">
          <label>Observações internas</label>
          <input 
            type="text" 
            v-model="form.observacoes"
            placeholder="Notas que não aparecem no cardápio público"
          >
        </div>
      </section>

      <div class="actions">
        <button type="button" class="btn-cancelar" @click="$router.push('/dashboard/pratos')">Cancelar</button>
        <button type="submit" class="btn-criar" :disabled="loading">
          <img src="../assets/Salvar.svg" alt="Salvar"> 
          <span>{{ loading ? 'Salvando...' : 'Criar prato' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'NovoPrato',
  data() {
    return {
      idRestaurante: null,
      loading: false,
      imagemPreview: '',
      arquivoImagem: null,
      categorias: [], // Receberá as categorias do banco
      form: {
        nome: '',
        id_categoria: '',
        preco: '',
        descricao: '',
        ativo: true,
        destaque: false,
        ordem: 1,
        observacoes: ''
      }
    }
  },
  mounted() {
    this.idRestaurante = localStorage.getItem('id_restaurante');
    if (!this.idRestaurante) {
      this.$router.push('/login');
      return;
    }
    
    this.carregarCategorias();
  },
  methods: {
    carregarCategorias() {
      const urlBackend = `/cardapio/back-end/categorias_acoes.php?id_restaurante=${this.idRestaurante}`;
      fetch(urlBackend)
        .then(res => res.json())
        .then(data => {
          if (Array.isArray(data)) {
            // Guarda apenas as categorias ativas (opcional, mas recomendado) para vincular ao prato
            this.categorias = data;
          }
        })
        .catch(err => console.error('Erro ao carregar categorias:', err));
    },

    handleImageUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      this.arquivoImagem = file;
      this.imagemPreview = URL.createObjectURL(file);
    },

    criarPrato() {
      const erros = [];
      const preco = parseFloat(String(this.form.preco).replace(',', '.'));

      // RN034 - Imagem obrigatória
      if (!this.arquivoImagem) erros.push("A imagem do prato é obrigatória. (RN034)");

      // Campo nome e categoria obrigatórios
      if (!this.form.nome) erros.push("O nome do prato é obrigatório.");
      if (!this.form.id_categoria) erros.push("A categoria é obrigatória.");

      // RN030 - Preço >= 0
      if (this.form.preco === '' || isNaN(preco) || preco < 0) erros.push("O preço deve ser maior ou igual a zero. (RN030)");

      // RN031 - No máximo 2 casas decimais
      const precoStr = String(this.form.preco).replace(',', '.');
      if (/\.\d{3,}/.test(precoStr)) erros.push("O preço deve ter no máximo duas casas decimais. (RN031)");

      if (erros.length > 0) {
        alert(erros.join("\n"));
        return;
      }

      this.loading = true;
      const urlBackend = '/cardapio/back-end/pratos_acoes.php';

      // Uso de FormData para suportar envio de arquivos (imagem)
      const formData = new FormData();
      formData.append('id_restaurante', this.idRestaurante);
      formData.append('nome', this.form.nome);
      formData.append('id_categoria', this.form.id_categoria);
      
      // Troca vírgula por ponto no preço caso o usuário tenha digitado assim
      const precoFormatado = String(this.form.preco).replace(',', '.');
      formData.append('preco', precoFormatado);
      
      formData.append('descricao', this.form.descricao);
      formData.append('ativo', this.form.ativo ? 1 : 0);
      formData.append('destaque', this.form.destaque ? 1 : 0);
      formData.append('ordem', this.form.ordem);
      formData.append('observacoes', this.form.observacoes);

      if (this.arquivoImagem) {
        formData.append('imagem', this.arquivoImagem);
      }

      fetch(urlBackend, {
        method: 'POST',
        body: formData // Fetch gerencia os headers multipart automaticamente
      })
      .then(res => res.json())
      .then(data => {
        if (data.sucesso || data.success) {
          alert('Prato criado com sucesso!');
          this.$router.push('/dashboard/pratos');
        } else {
          alert(data.mensagem || 'Erro ao criar o prato.');
        }
      })
      .catch(err => {
        console.error('Erro:', err);
        alert('Erro de conexão com o servidor.');
      })
      .finally(() => {
        this.loading = false;
      });
    }
  }
}
</script>

<style scoped>
/* O CSS MANTIDO INTACTO */
.novo-prato-page {
  max-width: 760px;
  margin: 50px auto;
}

.page-header h1 {
  font-size: 28px;
  margin-bottom: 4px;
}

.page-header p {
  color: #666;
  margin-bottom: 30px;
}

.card {
  background: white;
  border: 1px solid #aaa;
  border-radius: 10px;
  padding: 18px 24px;
  margin-bottom: 28px;
}

.card h3 {
  font-size: 16px;
  font-weight: 500;
  margin-bottom: 28px;
}

.card h2 {
  font-size: 22px;
  margin-bottom: 30px;
}

.upload-area {
  display: flex;
  align-items: center;
  gap: 35px;
  margin-bottom: 35px;
}

.upload-box {
  width: 76px;
  height: 76px;
  border: 2px dashed #999;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.upload-box span {
  font-size: 24px;
  color: #777;
}

.btn-upload {
  background: white;
  border: 1px solid #999;
  border-radius: 20px;
  padding: 6px 18px;
  cursor: pointer;
}

.upload-area p {
  margin-top: 18px;
  color: #777;
  font-size: 13px;
}

.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 16px;
}

.form-group label {
  font-size: 16px;
  margin-bottom: 8px;
}

.form-group input,
.form-group select,
.form-group textarea {
  height: 46px;
  border: 1px solid #ddd;
  border-radius: 7px;
  padding: 0 16px;
  font-size: 14px;
  outline: none;
}

.form-group textarea {
  height: 135px;
  padding-top: 12px;
  resize: none;
}

.form-group small {
  margin-top: 8px;
  color: #777;
  font-size: 12px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 32px;
}

.config-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 0;
  border-bottom: 1px solid #eee;
}

.config-item h4 {
  font-size: 16px;
  font-weight: 500;
}

.config-item p {
  color: #777;
  font-size: 13px;
  margin-top: 5px;
}

.switch input {
  display: none;
}

.switch span {
  width: 42px;
  height: 24px;
  background: #aaa;
  border-radius: 20px;
  display: block;
  position: relative;
  cursor: pointer;
}

.switch span::before {
  content: '';
  width: 16px;
  height: 16px;
  background: white;
  border-radius: 50%;
  position: absolute;
  top: 4px;
  left: 4px;
  transition: .3s;
}

.switch input:checked + span {
  background: #36c24a;
}

.switch input:checked + span::before {
  transform: translateX(18px);
}

.actions {
  display: flex;
  justify-content: flex-end;
  gap: 20px;
  margin-bottom: 40px;
}

.btn-cancelar {
  background: transparent;
  border: none;
  color: #555;
  cursor: pointer;
}

.btn-criar {
  background: #ef2020;
  color: white;
  border: none;
  border-radius: 10px;
  padding: 11px 18px;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-criar img {
  width: 20px;
  height: 20px;
  object-fit: contain;
}

.btn-criar:disabled {
  background: #fca5a5;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .novo-prato-page {
    margin: 30px 20px;
  }

  .form-row {
    grid-template-columns: 1fr;
    gap: 0;
  }
}
</style>