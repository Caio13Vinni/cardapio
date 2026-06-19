<template>
  <aside class="sidebar">
    <div class="logo-area">
      <img src="../assets/Logo.svg" alt="Logo">
      <h2>Cardáp.io</h2>
    </div>

    <nav class="menu">
      <router-link 
        v-for="item in menuItems" 
        :key="item.path"
        :to="item.path" 
        class="menu-item"
        :class="{ active: isActive(item.path) }"
        >
        <img :src="item.icon" :alt="item.label" class="icon" />
        <span>{{ item.label }}</span>
      </router-link>
    </nav>

    <div class="sidebar-footer">
      <button class="btn-logout" @click="handleLogout">
        <img src="../assets/PortaFundos.svg" alt="Porta" class="icon" />
        <span>Sair</span>
      </button>
    </div>
  </aside>
</template>

<script>
  import { useRoute } from 'vue-router'

  import dashboardIcon from '@/assets/DashBoard.svg'
  import categoriasIcon from '@/assets/PastasCinzas.svg'
  import pratosIcon from '@/assets/Garfo.svg'
  import personalizacaoIcon from '@/assets/Pintura.svg'
  import restauranteIcon from '@/assets/Casa.svg'
  import qrIcon from '@/assets/QRcode.svg'
  import previewIcon from '@/assets/OlhoCinza.svg'

  export default {
    name: 'AppSidebar',
    setup() {
      const route = useRoute()

      const menuItems = [
        { path: '/dashboard', label: 'Dashboard', icon: dashboardIcon },
        { path: '/dashboard/categorias', label: 'Categorias', icon: categoriasIcon },
        { path: '/dashboard/pratos', label: 'Pratos', icon: pratosIcon },
        { path: '/dashboard/personalizacao', label: 'Personalizar', icon: personalizacaoIcon },
        { path: '/dashboard/dados-restaurante', label: 'Dados do Restaurante', icon: restauranteIcon },
        { path: '/dashboard/qrcode', label: 'Qr Code', icon: qrIcon },
        { path: '/dashboard/preview', label: 'Preview', icon: previewIcon }
      ]

      const isActive = (path) => route.path === path

      return {
        menuItems,
        isActive
      }
    },
    methods: {
      handleLogout() {
        // Limpa a sessão local do usuário antes de sair
        localStorage.removeItem('id_restaurante');
        localStorage.removeItem('nome_restaurante');
        
        // Redireciona para a tela de login
        this.$router.push('/login');
      }
    }
  }
</script>

<style scoped>
/* O CSS ORIGINAL MANTIDO 100% INTACTO */
.sidebar {
  width: 220px;
  background: white;
  border-right: 1px solid #e8e8e8;
  display: flex;
  flex-direction: column;
  padding: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  height: 100vh;
  position: fixed;
  left: 0;
  top: 0;
  overflow-y: auto;
}

.logo-area {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #f0f0f0;
}

.logo-area img {
  width: 32px;
}

.logo-area h2 {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
}

.menu {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 8px;
  text-decoration: none;
  color: #666;
  transition: all 0.3s;
  font-size: 14px;
  font-weight: 500;
}

.menu-item:hover {
  background-color: #f5f5f5;
  color: #1a1a1a;
}

.menu-item.active {
  background-color: #fce4e4;
  color: #ef2020;
  font-weight: 600;
}
.menu-item.active .icon {
  /* Filtro mágico que transforma cinza/preto no vermelho exato do seu projeto (#ef2020) */
  filter: invert(21%) sepia(91%) saturate(5411%) hue-rotate(352deg) brightness(96%) contrast(105%);
}

.menu-item .icon {
  width: 20px;
  height: 20px;
  object-fit: contain;
}

.sidebar-footer {
  padding-top: 20px;
  border-top: 1px solid #f0f0f0;
}

.btn-logout {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  background: white;
  border: none;
  border-radius: 8px;
  color: #ef2020;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;

}

.btn-logout {
  color: #666;
}

.btn-logout:hover {
  background-color: #fce4e4;
  color: #ef2020;
}

.btn-logout:hover .icon {
  filter: invert(21%) sepia(91%) saturate(5411%) hue-rotate(352deg) brightness(96%) contrast(105%);
}
/* RESPONSIVE */
@media (max-width: 768px) {
  .sidebar {
    width: 100%;
    height: auto;
    position: relative;
    flex-direction: row;
    border-right: none;
    border-bottom: 1px solid #e8e8e8;
  }

  .menu {
    flex-direction: row;
    flex: 1;
    overflow-x: auto;
  }

  .sidebar-footer {
    padding-top: 0;
    padding-left: 20px;
    border-top: none;
    border-left: 1px solid #f0f0f0;
  }
}
</style>