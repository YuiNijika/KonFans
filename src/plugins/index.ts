/**
 * plugins/index.ts
 *
 * Automatically included in `./src/main.ts`
 */
import '../assets/main.css'
// Plugins
import router from '../router'
import Antd from 'ant-design-vue'
import 'ant-design-vue/dist/reset.css'

import VueLazyload from 'vue-lazyload';
import VueEasyLightbox from 'vue-easy-lightbox'

// Types
import type { App } from 'vue'

export function registerPlugins (app: App) {
  app
    .use(router)
    .use(Antd)
    .use(VueEasyLightbox)
    .use(VueLazyload, {
      preLoad: 1.3,
      error: '/assets/error.jpg', // 错误图片路径
      loading: '/assets/load.gif', // 加载中图片路径
      attempt: 1
    });
}