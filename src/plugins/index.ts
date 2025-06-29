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

// Types
import type { App } from 'vue'

export function registerPlugins (app: App) {
  app
    .use(router)
    .use(Antd)
}