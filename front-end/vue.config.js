const { defineConfig } = require('@vue/cli-service')
module.exports = defineConfig({
  transpileDependencies: true,
  // O app não vive na raiz do domínio, vive em /cardapio/.
  // Sem isso, o build gera os caminhos de JS/CSS como "/js/..." em vez de
  // "/cardapio/js/...", e o navegador não encontra os arquivos (tela branca).
  publicPath: '/cardapio/'
})
