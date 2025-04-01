/**
 * main.ts
 *
 * Bootstraps Vuetify and other plugins then mounts the App`
 */

// Plugins
import { registerPlugins } from '@/plugins'

// Components
import App from './App.vue'

// Composables
import { createApp } from 'vue'

// 引入 vue-lazyload
import VueLazyload from 'vue-lazyload';

const app = createApp(App)

// 使用 vue-lazyload
app.use(VueLazyload, {
    preLoad: 1.3,
    error: '/assets/error.jpg', // 错误图片路径
    loading: '/assets/load.gif', // 加载中图片路径
    attempt: 1
});

registerPlugins(app)

app.mount('#app')
