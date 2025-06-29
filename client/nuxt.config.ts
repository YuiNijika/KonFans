// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from "@tailwindcss/vite";
const baseUrl = 'https://kon-server.x-x.work/api';
// const baseUrl = 'https://deer-server-dev.x-x.work/api';
export default defineNuxtConfig({
  compatibilityDate: '2025-05-15',
  devtools: { enabled: true },
  future: {
    compatibilityVersion: 4
  },
  modules: [
    '@nuxtjs/seo',
    '@nuxtjs/tailwindcss'
  ],
  tailwindcss: {
    configPath: '~/tailwind.config.js',
    exposeConfig: false,
    injectPosition: 0,
    viewer: true,
  },
  build: {
    transpile: ['isotope-layout']
  },
  nitro: {
    devProxy: {
      "/apiService": {
        target: baseUrl,
        changeOrigin: true,
        prependPath: true,
      },
    },
    routeRules: {
      '/apiService/**': {
        proxy: `${baseUrl}/**`
      }
    }
  },
  vite: {
    plugins: [tailwindcss()],
  },
  app: {
    head: {
      htmlAttrs: {
        lang: 'zh-CN',
      },
      title: '轻音部',
      titleTemplate: '%s - KonFans(轻音小窝)',
      link: [
        { rel: 'icon', type: 'image/icon', href: '/favicon.ico' },
      ],
    }
  },
  css: [
    "~/assets/app.css"
  ],
})