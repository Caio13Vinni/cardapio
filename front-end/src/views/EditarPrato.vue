<template>
  <div class="editar-prato-page">
    <div class="page-header">
      <h1>Editar prato</h1>
    </div>

    <form class="form-prato" @submit.prevent="salvarPrato">
      <section class="card">
        <h3>Informações básicas</h3>

        <div class="upload-area">
          <label class="preview-img">
            <input type="file" hidden accept="image/png, image/jpeg, image/webp" @change="handleImageUpload" />
            <img v-if="imagemPreview" :src="imagemPreview" alt="Imagem do prato" />
            <div v-else class="upload-box">
              <span>📷</span>
            </div>
          </label>

          <div>
            <label class="btn-upload">
              Escolher imagem
              <input type="file" hidden accept="image/png, image/jpeg, image/webp" @change="handleImageUpload" />
            </label>
            <p>PNG, JPG ou WEBP até 5MB</p>
          </div>
        </div>

        <div class="form-group">
          <label>Nome do prato</label>
          <input v-model="form.nome" type="text" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Categoria</label>
            <input v-model="form.categoria" type="text" />
          </div>

          <div class="form-group">
            <label>Preço</label>
            <input v-model="form.preco" type="text" placeholder="Ex: 29.90" required />
          </div>
        </div>

        <div class="form-group">
          <label>Descrição</label>
          <textarea v-model="form.descricao" maxlength="500"></textarea>
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
            <input type="checkbox" v-model="form.ativo" />
            <span></span>
          </label>
        </div>

        <div class="config-item">
          <div>
            <h4>Prato em destaque</h4>
            <p>Aparece com badge especial no cardápio</p>
          </div>

          <label class="switch">
            <input type="checkbox" v-model="form.destaque" />
            <span></span>
          </label>
        </div>

        <div class="form-group">
          <label>Ordem de exibição</label>
          <input v-model="form.ordem" type="number" />
          <small>Pratos com menor número aparecem primeiro</small>
        </div>

        <div class="form-group">
          <label>Observações internas</label>
          <input
            v-model="form.observacoes"
            type="text"
            placeholder="Notas que não aparecem no cardápio público"
          />
        </div>
      </section>

      <div class="actions">
        <button type="button" class="btn-cancelar" @click="$router.push('/dashboard/pratos')">
          Cancelar
        </button>

        <button type="submit" class="btn-salvar" :disabled="loading">
          <img src="../assets/Salvar.svg" alt="Salvar" />
          <span>{{ loading ? 'Salvando...' : 'Salvar' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'EditarPratoPage',

  data() {
    return {
      idRestaurante: null,
      pratoId: null,
      loading: false,
      
      imagemPreview: '',
      arquivoImagem: null, // Guarda o arquivo físico para enviar pro PHP

      form: {
        nome: '',
        categoria: '', 
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

    // Pega o ID do prato a partir dos parâmetros da URL (ex: /editar/5)
    this.pratoId = this.$route.params.id;
    if (!this.pratoId) {
      this.$router.push('/dashboard/pratos');
      return;
    }

    this.carregarPrato();
  },

  methods: {
    carregarPrato() {
      const urlBackend = `/cardapio/back-end/pratos_acoes.php?id=${this.pratoId}&id_restaurante=${this.idRestaurante}`;
      
      fetch(urlBackend)
        .then(res => res.json())
        .then(data => {
          // Se sua API retornar uma lista, pega o primeiro item. Se retornar objeto direto, usa a própria data.
          const prato = Array.isArray(data) ? data[0] : data;

          if (prato) {
            this.form.nome = prato.nome || '';
            this.form.categoria = prato.categoria || ''; 
            this.form.preco = prato.preco || '';
            this.form.descricao = prato.descricao || '';
            this.form.ativo = parseInt(prato.ativo) !== 0; // Padrão true a menos que seja explicitamente 0
            this.form.destaque = parseInt(prato.destaque) === 1;
            this.form.ordem = prato.ordem || 1;
            this.form.observacoes = prato.observacoes || '';

            if (prato.imagem) {
              // Se o PHP retornar o caminho da imagem, monta a preview
              this.imagemPreview = prato.imagem.includes('http') ? prato.imagem : `/cardapio/back-end/${prato.imagem}`;
            }
          }
        })
        .catch(err => console.error("Erro ao carregar prato:", err));
    },

    handleImageUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      this.arquivoImagem = file;
      // Cria uma URL temporária para mostrar a foto na tela antes de enviar
      this.imagemPreview = URL.createObjectURL(file);
    },

    salvarPrato() {
      this.loading = true;
      const urlBackend = '/cardapio/back-end/pratos_acoes.php';

      // Usando FormData para suportar envio de imagem (upload)
      const formData = new FormData();
      formData.append('id', this.pratoId);
      formData.append('id_restaurante', this.idRestaurante);
      formData.append('nome', this.form.nome);
      formData.append('categoria', this.form.categoria);
      formData.append('preco', this.form.preco);
      formData.append('descricao', this.form.descricao);
      formData.append('ativo', this.form.ativo ? 1 : 0);
      formData.append('destaque', this.form.destaque ? 1 : 0);
      formData.append('ordem', this.form.ordem);
      formData.append('observacoes', this.form.observacoes);

      // Se o usuário selecionou uma imagem nova, envia pro backend
      if (this.arquivoImagem) {
        formData.append('imagem', this.arquivoImagem);
      }

      fetch(urlBackend, {
        method: 'POST',
        body: formData // Não precisa de header Content-Type, o fetch gerencia isso pro FormData
      })
      .then(res => res.json())
      .then(data => {
        if (data.sucesso || data.success) {
          alert('Prato atualizado com sucesso!');
          this.$router.push('/dashboard/pratos');
        } else {
          alert(data.mensagem || 'Erro ao salvar o prato.');
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
/* TODO O SEU CSS MANTIDO IGUAL AO DO DAVI */
.novo-prato-page {
  max-width: 760px;
  margin: 50px auto;
}

.page-header h1 {
  font-size: 40px;
  margin-bottom: 4px;
  padding: 20px;
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

@media (max-width: 768px) {
  .novo-prato-page {
    margin: 30px 20px;
  }

  .form-row {
    grid-template-columns: 1fr;
    gap: 0;
  }
}

.preview-img {
  width: 76px;
  height: 76px;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  display: block;
}

.preview-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.btn-salvar {
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

.btn-salvar img {
  width: 20px;
  height: 20px;
}
.btn-salvar:disabled {
  background-color: #fca5a5;
  cursor: not-allowed;
}
</style>