<template>
  <main class="pagina-cardapio">

    <section class="info-restaurante" :style="{ borderTop: `8px solid ${visual.cor_primaria || '#ef2020'}`, backgroundImage: visual.banner ? `url(${visual.banner})` : 'none', backgroundSize: 'cover', backgroundPosition: 'center' }">
      <img :src="visual.logo || require('../assets/imgRestaurante.svg')" class="logo" alt="Logo" />
      <h1 :style="{ color: visual.cor_secundaria || '#1a1a1a' }">{{ restaurante.nome || 'Restaurante Don Giovanni' }}</h1>
      <p>{{ restaurante.descricao || 'Cozinha italiana tradicional com toques contemporâneos' }}</p>
      <div class="infos">
        <span>{{ restaurante.endereco ? `${restaurante.endereco} - ${restaurante.bairro || ''} ${restaurante.cidade || ''}` : 'Rua dos pinheiros, 123 - Pinheiros São Paulo - SP' }}</span>
        <span>{{ restaurante.telefone || '(11) 3456-4673' }}</span>
        <span>{{ restaurante.horarios || 'Ter-Dom: 12h-15h e 19h-23h' }}</span>
      </div>
      <hr />
      <div class="redes">
        <span v-if="restaurante.instagram || !carregado">📷 {{ restaurante.instagram || '@dongiovanni' }}</span>
        <span v-if="restaurante.facebook || !carregado">● {{ restaurante.facebook || 'dongiovanni' }}</span>
        <span v-if="restaurante.website || !carregado">{{ restaurante.website || 'www.dongiovanni.com.br' }}</span>
      </div>
    </section>

    <div v-if="carregando" style="text-align: center; margin: 50px 0; color: #888; font-size: 18px;">
      Carregando cardápio atualizado...
    </div>

    <template v-else>
      <nav class="categorias" v-if="categorias.length > 0">
        <button
          v-for="categoria in categorias"
          :key="categoria"
          :class="{ ativo: categoriaSelecionada === categoria }"
          :style="categoriaSelecionada === categoria ? { backgroundColor: visual.cor_primaria || 'red' } : {}"
          @click="categoriaSelecionada = categoria"
        >
          {{ categoria }}
        </button>
      </nav>

      <section class="lista-pratos">
        <div v-for="prato in pratosFiltrados" :key="prato.id" class="card-prato" @click="abrirPrato(prato)">
          <img :src="prato.imagem" :alt="prato.nome" v-if="prato.imagem" />
          <div v-else style="height: 290px; background: #eee; display: flex; align-items: center; justify-content: center; color: #999;">
            Sem foto
          </div>
          <div class="conteudo-card">
            <span v-if="prato.destaque" class="badge-destaque" :style="{ backgroundColor: visual.cor_primaria || '#e01818' }">⭐ Destaque</span>
            <h2 :style="{ color: visual.cor_secundaria || '#1a1a1a' }">{{ prato.nome }}</h2>
            <p :style="{ color: visual.cor_primaria || '#e01818' }">{{ formatarPreco(prato.preco) }}</p>
          </div>
        </div>
      </section>

      <div v-if="pratosFiltrados.length === 0 && carregado" style="text-align: center; padding: 40px; color: #999;">
        Nenhum prato disponível para esta categoria no momento.
      </div>
    </template>

    <div v-if="pratoSelecionado" class="overlay" @click="fecharPrato">
      <div class="modal-prato" @click.stop>
        <div class="modal-esquerda">
          <img :src="pratoSelecionado.imagem" :alt="pratoSelecionado.nome" v-if="pratoSelecionado.imagem" />
          <div v-else style="height: 230px; background: #eee; border-radius: 8px;"></div>
          <h2 :style="{ color: visual.cor_secundaria || '#1a1a1a' }">{{ pratoSelecionado.nome }}</h2>
          <p :style="{ color: visual.cor_primaria || '#e01818' }">{{ formatarPreco(pratoSelecionado.preco) }}</p>
        </div>
        <div class="modal-direita">
          <h2 :style="{ color: visual.cor_secundaria || '#1a1a1a' }">Descrição do prato:</h2>
          <p :style="{ color: visual.cor_secundaria || '#444' }">{{ pratoSelecionado.descricao || 'Descrição não cadastrada.' }}</p>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
export default {
  name: "CardapioCliente",

  data() {
    return {
      idRestaurante: null,
      carregando: true,
      carregado: false,
      restaurante: {},
      visual: {},
      categoriaSelecionada: "",
      categorias: [],
      pratos: [],
      pratoSelecionado: null
    };
  },

  mounted() {
    this.idRestaurante = this.$route.query.id || this.$route.params.id || localStorage.getItem("id_restaurante");
    if (!this.idRestaurante) {
      console.warn("Nenhum restaurante selecionado.");
      this.carregando = false;
      return;
    }
    this.buscarCardapioCompleto();
  },

  computed: {
    pratosFiltrados() {
      return this.pratos.filter((prato) => prato.categoria === this.categoriaSelecionada);
    }
  },

  methods: {
    async buscarCardapioCompleto() {
      try {
        const [resCardapio, resPerfil] = await Promise.all([
          fetch(`/cardapio/back-end/cardapio_publico.php?id=${this.idRestaurante}`).then(r => r.json()),
          fetch(`/cardapio/back-end/dados_restaurante.php?id_restaurante=${this.idRestaurante}`).then(r => r.json()).catch(() => ({}))
        ]);

        
        if (!resCardapio.sucesso) {
          // Direciona para a rota de erro 404 nomeada (ver router/index.js)
          this.$router.replace({ name: 'Erro404' });
          return;
        }

        if (resCardapio.sucesso) {
          this.visual = resCardapio.visual || {};
          if (this.visual.logo && !this.visual.logo.includes('http')) {
            this.visual.logo = `/cardapio/back-end/${this.visual.logo}`;
          }
          if (this.visual.banner && !this.visual.banner.includes('http')) {
            this.visual.banner = `/cardapio/back-end/${this.visual.banner}`;
          }

          this.restaurante = {
            nome: resCardapio.estabelecimento || resPerfil.nome,
            descricao: resPerfil.descricao || '',
            endereco: resPerfil.endereco || '',
            bairro: resPerfil.bairro || '',
            cidade: resPerfil.cidade || '',
            telefone: resPerfil.telefone || '',
            horarios: resPerfil.horarios || '',
            instagram: resPerfil.instagram || '',
            facebook: resPerfil.facebook || '',
            website: resPerfil.website || ''
          };

          const listaCategorias = [];
          const listaPratos = [];

          if (Array.isArray(resCardapio.cardapio)) {
            resCardapio.cardapio.forEach(grupo => {
              if (grupo.categoria) {
                listaCategorias.push(grupo.categoria);
                if (Array.isArray(grupo.itens)) {
                  grupo.itens.forEach(item => {
                    listaPratos.push({
                      id: item.id,
                      nome: item.nome,
                      descricao: item.descricao,
                      preco: item.preco,
                      imagem: item.imagem ? (item.imagem.includes('http') ? item.imagem : `/cardapio/back-end/${item.imagem}`) : '',
                      categoria: grupo.categoria,
                      destaque: item.destaque || 0
                    });
                  });
                }
              }
            });
          }

          this.categorias = listaCategorias;
          this.pratos = listaPratos;
          if (this.categorias.length > 0) {
            this.categoriaSelecionada = this.categorias[0];
          }
        }
      } catch (error) {
        console.error("Erro ao buscar dados do cardápio:", error);
      } finally {
        this.carregando = false;
        this.carregado = true;
      }
    },

    abrirPrato(prato) { this.pratoSelecionado = prato; },
    fecharPrato() { this.pratoSelecionado = null; },

    formatarPreco(preco) {
      if (!preco) return 'R$ 0,00';
      return `R$ ${parseFloat(preco).toFixed(2).replace('.', ',')}`;
    }
  }
};
</script>

<style scoped>
.pagina-cardapio { min-height: 100vh; background: #f7f7f7; padding: 35px 70px; font-family: "Poppins", sans-serif; }
.banner-container { width: 100%; margin-bottom: 20px; border-radius: 14px; overflow: hidden; }
.banner { width: 100%; height: 250px; object-fit: cover; display: block; }
.info-restaurante { background: white; border-radius: 14px; text-align: center; padding: 15px 90px 35px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.18); }
.logo { width: 130px; border-radius: 50%; max-height: 130px; object-fit: cover; }
.info-restaurante h1 { font-size: 34px; margin: 5px 0; }
.info-restaurante p, .info-restaurante span { color: #666; }
.infos, .redes { display: flex; justify-content: space-between; margin-top: 18px; font-size: 18px; }
.categorias { background: white; margin: 25px 0; border-radius: 12px; padding: 5px 90px; display: flex; justify-content: space-between; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); overflow-x: auto; gap: 10px; }
.categorias button { border: none; background: transparent; font-size: 28px; font-weight: 700; color: #666; padding: 10px 60px; border-radius: 12px; cursor: pointer; transition: background-color 0.3s, color 0.3s; white-space: nowrap; }
.categorias button.ativo { background: red; color: white; }
.lista-pratos { display: grid; grid-template-columns: repeat(3, 1fr); gap: 38px; margin-top: 25px; }
.card-prato { background: white; border: 1px solid #aaa; border-radius: 8px; overflow: hidden; cursor: pointer; transition: transform 0.2s; }
.card-prato:hover { transform: translateY(-3px); }
.card-prato img { width: 100%; height: 290px; object-fit: cover; }
.conteudo-card { padding: 25px 14px; position: relative; }
.badge-destaque { display: inline-block; color: white; font-size: 13px; font-weight: 700; padding: 3px 10px; border-radius: 20px; margin-bottom: 8px; }
.conteudo-card h2 { font-size: 28px; margin-bottom: 18px; }
.conteudo-card p { color: #e01818; font-size: 22px; font-weight: 700; }
.overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, 0.9); display: flex; align-items: center; justify-content: center; z-index: 999; }
.modal-prato { width: 850px; background: white; border-radius: 12px; padding: 45px; display: flex; gap: 45px; max-height: 90vh; overflow-y: auto; }
.modal-esquerda, .modal-direita { width: 50%; }
.modal-esquerda img { width: 100%; height: 230px; object-fit: cover; border-radius: 8px; }
.modal-esquerda h2 { font-size: 32px; margin-top: 25px; }
.modal-esquerda p { color: #e01818; font-size: 30px; font-weight: 700; }
.modal-direita h2 { font-size: 30px; }
.modal-direita p { font-size: 28px; line-height: 1.45; color: #444; }
@media (max-width: 1024px) { .lista-pratos { grid-template-columns: repeat(2, 1fr); } .modal-prato { flex-direction: column; width: 90%; } .modal-esquerda, .modal-direita { width: 100%; } }
@media (max-width: 768px) { .pagina-cardapio { padding: 15px 20px; } .lista-pratos { grid-template-columns: 1fr; } .categorias button { font-size: 18px; padding: 8px 20px; } }
</style>