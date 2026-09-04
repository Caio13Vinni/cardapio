<template>
  <div class="dados-container">

    <div class="page-header">
      <h1>Dados do restaurante</h1>
      <p>Informações básicas que aparecem no cardápio público</p>
    </div>

    <section class="card">
      <h2>Informações básicas</h2>

      <div class="form-group">
        <label>Nome do restaurante</label>
        <input
          type="text"
          placeholder="Ex: Restaurante Nazo"
          v-model="form.nome"
        >
      </div>

      <div class="form-group">
        <label>Descrição</label>
        <textarea
          rows="4"
          placeholder="Conte sobre seu restaurante"
          v-model="form.descricao"
        />
        <small>Aparece no topo do cardápio público</small>
      </div>
    </section>

    <section class="card">
      <h2><img src="../assets/Local.svg" alt="LocalIcon"> Endereço</h2>

      <div class="form-group">
        <label>Endereço</label>
        <input type="text" placeholder="Rua, Número" v-model="form.endereco">
      </div>

      <div class="row">
        <div class="form-group">
          <label>Bairro</label>
          <input type="text" placeholder="Ex: Pinheiros" v-model="form.bairro">
        </div>

        <div class="form-group">
          <label>CEP</label>
          <input type="text" placeholder="00000-00" v-model="form.cep">
        </div>
      </div>

      <div class="row">
        <div class="form-group city">
          <label>Cidade</label>
          <input type="text" placeholder="Ex: São Paulo" v-model="form.cidade">
        </div>

        <div class="form-group state">
          <label>Estado</label>
          <input type="text" placeholder="SP" v-model="form.estado">
        </div>
      </div>
    </section>

    <section class="card">
      <h2><img src="../assets/Telefone.svg" alt="TefeloneIcon"> Contato</h2>

      <div class="row">
        <div class="form-group">
          <label>Telefone</label>
          <input type="text" placeholder="(00) 0000-0000" v-model="form.telefone">
        </div>

        <div class="form-group">
          <label>E-mail</label>
          <input
            type="email"
            placeholder="contato@restaurante.com.br"
            v-model="form.email"
          >
        </div>
      </div>
    </section>

    <section class="card">
      <h2><img src="../assets/relogio.svg" alt="RelogioIcon"> Horário de funcionamento</h2>

      <div class="form-group">
        <label>Horários</label>
        <textarea
          rows="3"
          placeholder="Ex: Ter-Dom: 12h-15h e 19h-23h"
          v-model="form.horarios"
        />
      </div>
    </section>

    <section class="card">
      <h2>Redes sociais</h2>

      <div class="form-group">
        <label>Instagram</label>
        <input type="text" placeholder="@seurestaurante" v-model="form.instagram">
      </div>

      <div class="form-group">
        <label>Facebook</label>
        <input type="text" placeholder="seurestaurante" v-model="form.facebook">
      </div>

      <div class="form-group">
        <label>Website</label>
        <input
          type="text"
          placeholder="www.seurestaurante.com.br"
          v-model="form.website"
        >
      </div>
    </section>

    <div class="actions">
      <button class="btn-save" @click="salvarDados" :disabled="loading">
        <img src="../assets/Salvar.svg" alt=""> 
        {{ loading ? 'Salvando...' : 'Salvar alterações' }}
      </button>
    </div>

  </div>
</template>

<script>
export default {
  name: 'DadosRestaurantePage',
  data() {
    return {
      idRestaurante: null,
      loading: false,
      form: {
        nome: '',
        descricao: '',
        endereco: '',
        bairro: '',
        cep: '',
        cidade: '',
        estado: '',
        telefone: '',
        email: '',
        horarios: '',
        instagram: '',
        facebook: '',
        website: ''
      }
    }
  },
  mounted() {
    this.idRestaurante = localStorage.getItem('id_restaurante');
    if (!this.idRestaurante) {
      this.$router.push('/login');
      return;
    }
    
    // Busca os dados do banco assim que a tela carrega
    this.carregarDados();
  },
  methods: {
    carregarDados() {
      const urlBackend = `/cardapio/back-end/dados_restaurante.php?id_restaurante=${this.idRestaurante}`;
      
      fetch(urlBackend)
        .then(res => res.json())
        .then(data => {
          if (!data.erro) {
            // Mescla os dados recebidos com os campos em branco do form
            this.form = { ...this.form, ...data };
          }
        })
        .catch(err => console.error("Erro ao carregar dados do restaurante:", err));
    },
    
    salvarDados() {
      // Validações frontend
      const erros = [];
      const nome = (this.form.nome || '').trim();
      const email = (this.form.email || '').trim();
      const telefone = (this.form.telefone || '').trim();
      const endereco = (this.form.endereco || '').trim();
      const horarios = (this.form.horarios || '').trim();
      const instagram = (this.form.instagram || '').trim();
      const facebook = (this.form.facebook || '').trim();
      const website = (this.form.website || '').trim();

      if (!nome) erros.push("O nome do restaurante é obrigatório. (RN059)");
      else if (nome.length < 2 || nome.length > 120) erros.push("O nome deve ter entre 2 e 120 caracteres. (RN060)");
      if (telefone.length > 20) erros.push("O telefone deve ter no máximo 20 caracteres. (RN062)");
      if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) erros.push("O e-mail deve ter formato válido. (RN063)");
      if (email.length > 150) erros.push("O e-mail deve ter no máximo 150 caracteres. (RN064)");
      if (endereco.length > 255) erros.push("O endereço deve ter no máximo 255 caracteres. (RN065)");
      if (horarios.length > 255) erros.push("O horário deve ter no máximo 255 caracteres. (RN066)");
      if (instagram.length > 100) erros.push("O Instagram deve ter no máximo 100 caracteres. (RN067)");
      if (facebook.length > 100) erros.push("O Facebook deve ter no máximo 100 caracteres. (RN068)");
      if (website.length > 255) erros.push("O Website deve ter no máximo 255 caracteres. (RN069)");

      if (erros.length > 0) {
        alert(erros.join("\n"));
        return;
      }

      this.loading = true;
      const urlBackend = '/cardapio/back-end/dados_restaurante.php';
      
      fetch(urlBackend, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        // Envia todos os dados preenchidos + o ID do restaurante
        body: JSON.stringify({
          id_restaurante: this.idRestaurante,
          ...this.form
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.sucesso || data.success) {
          alert('Dados salvos com sucesso!');
        } else {
          alert(data.mensagem || 'Erro ao salvar os dados.');
        }
      })
      .catch(err => {
        console.error(err);
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
/* TODO O ESTILO MANTIDO INTACTO */
.dados-container {
  padding: 30px;
  max-width: 100%;
}

.page-header h1 {
  font-size: 38px;
  font-weight: 700;
  color: #333;
}

.page-header p {
  color: #888;
  margin-top: 6px;
  margin-bottom: 30px;
}

.card {
  background: white;
  border: 1px solid #e5e5e5;
  border-radius: 14px;
  padding: 24px;
  margin-bottom: 22px;
}

.card h2 {
  margin-bottom: 24px;
  font-size: 18px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 16px;
  flex: 1;
}

.form-group label {
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 8px;
}

input,
textarea {
  border: 1px solid #d8d8d8;
  border-radius: 10px;
  padding: 12px;
  font-size: 14px;
  outline: none;
}

input:focus,
textarea:focus {
  border-color: #ef2020;
}

small {
  color: #999;
  margin-top: 8px;
}

.row {
  display: flex;
  gap: 20px;
}

.city {
  flex: 3;
}

.state {
  flex: 1;
}

.actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
}

.btn-save {
  background: #ef2020;
  color: white;
  border: none;
  padding: 14px 22px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-save:hover {
  opacity: .9;
}

.btn-save:disabled {
  background: #94a3b8;
  cursor: not-allowed;
}
</style>