<template>
  <div class="preview-page">

    <div class="header">
      <h1>Preview do cardápio</h1>
      <p>Visualize como seu cardápio aparece para os clientes</p>
    </div>

    <div class="preview-container">

      <div class="restaurant-card" :style="{ borderTop: `8px solid ${aparencia.cor_primaria || '#ef2020'}`, backgroundImage: aparencia.banner ? `url(${aparencia.banner})` : 'none', backgroundSize: 'cover', backgroundPosition: 'center' }">
        <img class="logo" :src="aparencia.logo || require('../assets/imgRestaurante.svg')" alt="Logo">

        <h2>{{ restaurante.nome || 'Restaurante Don Giovanni' }}</h2>
        <p class="subtitle">{{ restaurante.descricao || 'Cozinha italiana tradicional com toques contemporâneos' }}</p>

        <div class="info">
          <span>{{ restaurante.endereco ? `${restaurante.endereco} - ${restaurante.bairro} ${restaurante.cidade} - ${restaurante.estado}` : 'Rua dos pinheiros, 123 - Pinheiros São Paulo - SP' }}</span>
          <span>{{ restaurante.telefone || '(11) 3456-4673' }}</span>
          <span>{{ restaurante.horarios || 'Ter-Dom: 12h-15h e 19h-23h' }}</span>
        </div>

        <hr>

        <div class="info">
          <div class="item" v-if="restaurante.instagram || !carregado">
            <img src="../assets/instagram.svg" alt="Insta">
            <span>{{ restaurante.instagram || '@dongiovanni' }}</span>
          </div>
          <div class="item" v-if="restaurante.facebook || !carregado">
            <img src="../assets/facebook.svg" alt="Face">
            <span>{{ restaurante.facebook || 'dongiovanni' }}</span>
          </div>
          <div class="item" v-if="restaurante.website || !carregado">
            <span>{{ restaurante.website || 'www.dongiovanni.com.br' }}</span>
          </div>
        </div>
      </div>

      <div class="categories" v-if="categorias.length > 0">
        <button
          v-for="categoria in categorias"
          :key="categoria"
          @click="categoriaSelecionada = categoria"
          :class="{ active: categoriaSelecionada === categoria }"
          :style="categoriaSelecionada === categoria ? { backgroundColor: aparencia.cor_primaria || '#ef2020' } : {}"
        >
          {{ categoria }}
        </button>
      </div>

      <h3 v-if="categorias.length > 0">{{ categoriaSelecionada }}</h3>

      <div class="pratos-grid">
        <div class="prato-card" v-for="prato in pratosFiltrados" :key="prato.id">
          <img :src="prato.imagem" :alt="prato.nome">
          <div class="content">
            <h4 :style="{ color: aparencia.cor_secundaria || '#1a1a1a' }">{{ prato.nome }}</h4>
            <span class="preco" :style="{ color: aparencia.cor_primaria || '#ef2020' }">R$ {{ prato.preco }}</span>
          </div>
        </div>
      </div>

      <div v-if="carregado && pratos.length === 0" style="text-align: center; margin-top: 40px; color: #999;">
        Nenhum prato cadastrado ou ativo para exibir no cardápio público.
      </div>

    </div>
  </div>
</template>

<script>
export default {
  name: 'PreviewPage',

  data() {
    return {
      idRestaurante: null,
      carregado: false,
      restaurante: {},
      aparencia: {},
      categoriaSelecionada: '',
      categorias: [],
      pratos: []
    }
  },

  computed: {
    pratosFiltrados() {
      return this.pratos.filter(prato => prato.categoria === this.categoriaSelecionada)
    }
  },

  mounted() {
    this.idRestaurante = localStorage.getItem('id_restaurante');
    if (!this.idRestaurante) {
      this.$router.push('/login');
      return;
    }
    this.carregarDadosDoPreview();
  },

  methods: {
    async carregarDadosDoPreview() {
      try {
        const [resDados, resAparencia, resCategorias, resPratos] = await Promise.all([
          fetch(`/cardapio/back-end/dados_restaurante.php?id_restaurante=${this.idRestaurante}`).then(r => r.json()),
          fetch(`/cardapio/back-end/aparencia.php?id_restaurante=${this.idRestaurante}`).then(r => r.json()),
          fetch(`/cardapio/back-end/categorias_acoes.php?id_restaurante=${this.idRestaurante}`).then(r => r.json()),
          fetch(`/cardapio/back-end/pratos_acoes.php?id_restaurante=${this.idRestaurante}`).then(r => r.json())
        ]);

        // 1. Dados do restaurante
        if (!resDados.erro) this.restaurante = resDados;

        // 2. Aparência — o PHP retorna { sucesso, aparencia: {...} }
        if (resAparencia.sucesso) {
          this.aparencia = resAparencia.aparencia || {};
          if (this.aparencia.logo && !this.aparencia.logo.includes('http')) {
            this.aparencia.logo = `/cardapio/back-end/${this.aparencia.logo}`;
          }
          if (this.aparencia.banner && !this.aparencia.banner.includes('http')) {
            this.aparencia.banner = `/cardapio/back-end/${this.aparencia.banner}`;
          }
        }

        // 3. Categorias
        if (Array.isArray(resCategorias)) {
          this.categorias = resCategorias
            .filter(c => parseInt(c.ativo) === 1)
            .map(c => c.nome);
          if (this.categorias.length > 0) {
            this.categoriaSelecionada = this.categorias[0];
          }
        }

        // 4. Pratos
        if (Array.isArray(resPratos)) {
          this.pratos = resPratos
            .filter(p => parseInt(p.ativo) === 1)
            .map(p => {
              let imgUrl = 'https://picsum.photos/400/300?grayscale';
              if (p.imagem) {
                imgUrl = p.imagem.includes('http') ? p.imagem : `/cardapio/back-end/${p.imagem}`;
              }
              return {
                id: p.id,
                categoria: p.categoria || 'Sem categoria',
                nome: p.nome,
                preco: parseFloat(p.preco || 0).toFixed(2).replace('.', ','),
                imagem: imgUrl
              };
            });
        }

      } catch (error) {
        console.error("Erro ao carregar preview:", error);
      } finally {
        this.carregado = true;
      }
    }
  }
}
</script>

<style scoped>
.preview-page { padding: 30px; }
.preview-container { background: #eef0ff; padding: 40px; border-radius: 30px; }
.banner-container { width: 100%; margin-bottom: 20px; border-radius: 14px; overflow: hidden; }
.banner { width: 100%; height: 250px; object-fit: cover; display: block; }
.restaurant-card { background: white; border-radius: 20px; padding: 30px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,.1); }
.logo { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; }
.restaurant-card h2 { margin-top: 15px; font-size: 42px; }
.subtitle { color: #666; }
.info { display: flex; justify-content: center; gap: 100px; margin-top: 20px; padding: 10px 30px; font-family: sans-serif; }
.info .item { display: flex; align-items: center; gap: 8px; }
.info .item img { width: 18px; height: 18px; }
.categories { margin-top: 25px; background: white; border-radius: 20px; padding: 10px; display: flex; gap: 10px; justify-content: space-between; }
.categories button { border: none; background: transparent; padding: 15px 100px; border-radius: 15px; cursor: pointer; font-weight: 600; font-size: 30px; transition: all 0.3s; }
.categories button.active { background: #ef2020; color: white; }
.pratos-grid { margin-top: 25px; display: grid; grid-template-columns: repeat(auto-fill,minmax(320px,1fr)); gap: 20px; }
.prato-card { background: white; border-radius: 15px; overflow: hidden; }
.prato-card img { width: 100%; height: 250px; object-fit: cover; }
.content { padding: 20px; }
.content h4 { font-size: 28px; margin-bottom: 10px; }
.preco { color: #ef2020; font-size: 28px; font-weight: bold; }
hr { display: flex; width: 800px; margin: auto; margin-top: 10px; }
</style>