<template>
  <div class="personalizar-container">
    <div class="header">
      <h1>Personalizar cardápio</h1>
      <p>Configure a identidade visual do seu cardápio</p>
    </div>

    <div class="content-grid">
      <div class="main-content">
        <div class="card">
          <h2>Logo do restaurante</h2>

          <div class="upload-area">
            <label class="upload-box" style="overflow: hidden;">
              <input type="file" accept="image/png, image/jpeg, image/svg+xml, image/webp" hidden @change="handleLogoUpload" />
              <span v-if="!logoPreview">
                <img src="../assets/Download.svg" alt="Download" />
              </span>
              <img v-else :src="logoPreview" style="width: 100%; height: 100%; object-fit: cover;" alt="Preview Logo" />
            </label>

            <div class="upload-info">
              <label class="btn-escolher">
                Escolher logo
                <input type="file" accept="image/png, image/jpeg, image/svg+xml, image/webp" hidden @change="handleLogoUpload" />
              </label>

              <p>PNG, JPG ou SVG até 2MB</p>
              <p>Recomendado: 512x512px</p>
            </div>
          </div>
        </div>

        <div class="card">
          <h2>Cores da marca</h2>

          <div class="cores-grid">
            <div>
              <label>Cor primária</label>
              <div class="color-input">
                <div class="preview" :style="{ background: corPrimaria }"></div>
                <input v-model="corPrimaria" type="text" placeholder="#HEX" />
              </div>
              <small>Usadas em botões e destaques</small>
            </div>

            <div>
              <label>Cor secundária</label>
              <div class="color-input">
                <div class="preview" :style="{ background: corSecundaria }"></div>
                <input v-model="corSecundaria" type="text" placeholder="#HEX" />
              </div>
              <small>Usadas em cabeçalhos e textos</small>
            </div>
          </div>
        </div>

        <div class="card">
          <h2>Banner do cardápio</h2>

          <label class="banner-upload" :style="bannerPreview ? 'padding: 0; gap: 0; overflow: hidden;' : ''">
            <input type="file" accept="image/jpeg, image/png, image/webp" hidden @change="handleBannerUpload" />
            <template v-if="!bannerPreview">
              <span><img src="../assets/Download.svg" alt="Upload" /></span>
              <p>Banner opcional para o topo do cardápio</p>
            </template>
            <img v-else :src="bannerPreview" style="width: 100%; height: 100%; object-fit: cover;" alt="Preview Banner" />
          </label>

          <label class="btn-banner" style="display: inline-block; margin-top: 15px;">
            Adicionar banner
            <input type="file" accept="image/jpeg, image/png, image/webp" hidden @change="handleBannerUpload" />
          </label>

          <small style="display: block; margin-top: 10px;">JPG, PNG ou WEBP até 5MB • Recomendado: 1920x400px</small>
        </div>

        <div class="footer-action">
          <button class="btn-salvar" @click="salvarPersonalizacao" :disabled="loading">
            {{ loading ? 'Salvando...' : 'Salvar alterações' }}
          </button>
        </div>
      </div>

      <div class="sidebar">
        <div class="card">
          <h2>Orientações</h2>

          <ul class="orientacoes">
            <li><img src="../assets/Corretoverde.svg" alt="Verde" /> Use cores que representem sua marca</li>
            <li><img src="../assets/Corretoverde.svg" alt="Verde" /> Logo em alta resolução garante boa visualização</li>
            <li><img src="../assets/Corretoverde.svg" alt="Verde" /> Banner é opcional mas cria uma ótima primeira impressão</li>
            <li><img src="../assets/Corretoverde.svg" alt="Verde" /> Teste no preview antes de publicar</li>
          </ul>
        </div>

        <div class="card">
          <h2>Pré-visualização de cores</h2>

          <div class="preview-card" :style="{ background: corPrimaria }">
            <strong>Cor primária</strong>
            <span>Botões e destaques</span>
          </div>

          <div class="preview-card" :style="{ background: corSecundaria }">
            <strong>Cor secundária</strong>
            <span>Cabeçalhos e textos</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const idRestaurante = ref(null)
const loading = ref(false)

const corPrimaria = ref('#ef2b2d') // Valor padrão de placeholder
const corSecundaria = ref('#1a1a1a') // Valor padrão de placeholder

const logoFile = ref(null)
const bannerFile = ref(null)
const logoPreview = ref('')
const bannerPreview = ref('')

onMounted(() => {
  idRestaurante.value = localStorage.getItem('id_restaurante')
  if (!idRestaurante.value) {
    router.push('/login')
    return
  }
  
  carregarAparencia()
})

const carregarAparencia = () => {
  const urlBackend = `/cardapio/back-end/aparencia.php?id_restaurante=${idRestaurante.value}`
  
  fetch(urlBackend)
    .then(res => res.json())
    .then(data => {
      if (!data.erro) {
        if (data.cor_primaria) corPrimaria.value = data.cor_primaria
        if (data.cor_secundaria) corSecundaria.value = data.cor_secundaria
        
        // Formata os links das imagens corretamente
        if (data.logo) {
          logoPreview.value = data.logo.includes('http') ? data.logo : `/cardapio/back-end/${data.logo}`
        }
        if (data.banner) {
          bannerPreview.value = data.banner.includes('http') ? data.banner : `/cardapio/back-end/${data.banner}`
        }
      }
    })
    .catch(err => console.error('Erro ao carregar aparência:', err))
}

function handleLogoUpload(event) {
  const file = event.target.files[0]
  if (file) {
    logoFile.value = file
    logoPreview.value = URL.createObjectURL(file)
  }
}

function handleBannerUpload(event) {
  const file = event.target.files[0]
  if (file) {
    bannerFile.value = file
    bannerPreview.value = URL.createObjectURL(file)
  }
}

const salvarPersonalizacao = () => {
  loading.value = true
  
  const formData = new FormData()
  formData.append('id_restaurante', idRestaurante.value)
  formData.append('cor_primaria', corPrimaria.value)
  formData.append('cor_secundaria', corSecundaria.value)
  
  if (logoFile.value) formData.append('logo', logoFile.value)
  if (bannerFile.value) formData.append('banner', bannerFile.value)

  const urlBackend = '/cardapio/back-end/aparencia.php'
  
  fetch(urlBackend, {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.sucesso || data.success) {
      alert('Personalização salva com sucesso!')
    } else {
      alert(data.mensagem || 'Erro ao salvar a personalização.')
    }
  })
  .catch(err => {
    console.error('Erro:', err)
    alert('Erro de conexão com o servidor.')
  })
  .finally(() => {
    loading.value = false
  })
}
</script>

<style scoped>
/* TODO O CSS MANTIDO INTACTO DO DAVI */
.personalizar-container {
  padding: 30px;
  background: #f8f8f8;
  min-height: 100vh;
}

.header h1 {
  font-size: 40px;
  margin-bottom: 8px;
}

.header p {
  color: #777;
}

.content-grid {
  margin-top: 25px;
  display: grid;
  grid-template-columns: 1fr 320px;
  gap: 24px;
}

.card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 20px;
  border: 1px solid #e6e6e6;
}

.card h2 {
  margin-bottom: 20px;
}

.upload-area {
  display: flex;
  gap: 20px;
  align-items: center;
}

.upload-box {
  width: 120px;
  height: 120px;
  border: 2px dashed #cfcfcf;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}


.upload-info button,
.btn-banner {
  border: 1px solid #ccc;
  background: white;
  padding: 10px 20px;
  border-radius: 10px;
  cursor: pointer;
}

.cores-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 25px;
}

.color-input {
  display: flex;
  gap: 10px;
  margin: 10px 0;
}

.preview {
  width: 60px;
  height: 60px;
  border-radius: 10px;
}

.color-input input {
  flex: 1;
  border: 1px solid #ccc;
  border-radius: 10px;
  padding: 12px;
}

.banner-upload {
  height: 180px;
  border: 2px dashed #cfcfcf;
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 10px;
}

.sidebar {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.orientacoes {
  list-style: none;
  padding: 0;
}

.orientacoes li {
  margin-bottom: 15px;
}

.preview-card {
  color: white;
  padding: 18px;
  border-radius: 12px;
  margin-bottom: 16px;
}

.preview-card strong {
  display: block;
}

.footer-action {
  display: flex;
  justify-content: flex-end;
}

.btn-salvar {
  background: #ef2b2d;
  color: white;
  border: none;
  padding: 14px 24px;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 600;
}

.btn-salvar:disabled {
  background-color: #fca5a5;
  cursor: not-allowed;
}

.upload-box,
.banner-upload,
.btn-escolher,
.btn-banner {
  cursor: pointer;
}

.btn-escolher {
  display: inline-block;
  border: 1px solid #ccc;
  background: white;
  padding: 10px 20px;
  border-radius: 10px;
}
</style>