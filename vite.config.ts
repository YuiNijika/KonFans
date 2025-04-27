import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// 自动路由和组件引入插件
import VueRouter from 'unplugin-vue-router/vite'
import { VueRouterAutoImports } from 'unplugin-vue-router'
import Components from 'unplugin-vue-components/vite'
import AutoImport from 'unplugin-auto-import/vite'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    // 必须先配置路由插件 自动生成路由
    VueRouter({
      routesFolder: 'src/pages',          // 指定页面目录（类似Nuxt的pages目录）
      extensions: ['.vue'],               // 文件扩展名
      dts: 'src/typed-router.d.ts',       // 路由类型声明文件
      routeBlockLang: 'yaml',             // <route> 块的语言（可选）
      importMode: 'async',                // 异步加载组件
      exclude: ['**/components/*.vue'],   // 排除components目录
    }),

    // Vue插件 必须在VueRouter之后
    vue({
      script: {
        defineModel: true,                // 启用实验性defineModel
        propsDestructure: true            // 启用props解构
      }
    }),

    // 开发工具插件
    vueDevTools(),

    // 自动API导入 包含vue-router的自动导入
    AutoImport({
      imports: [
        'vue',
        VueRouterAutoImports,             // 自动导入vue-router的API
        {
          // 其他自定义自动导入
          '@vueuse/core': [
            'useMouse',
            'useFetch'
          ]
        }
      ],
      dts: 'src/auto-imports.d.ts',       // 自动导入类型声明
      dirs: [
        'src/composables',                // 自动导入composables目录
        'src/stores'                      // 自动导入stores目录
      ],
      vueTemplate: true                   // 在模板中自动导入
    }),

    // 自动组件导入
    Components({
      dirs: ['src/components'],           // 自动注册components目录
      extensions: ['vue'],                 // 文件扩展名
      deep: true,                         // 深度扫描子目录
      dts: 'src/components.d.ts',         // 组件类型声明
      directoryAsNamespace: true,         // 使用目录名作为命名空间
      globalNamespaces: ['global'],       // 全局命名空间
      resolvers: [
        // 可添加组件库解析器（如Element Plus等）
      ]
    })
  ],

  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
      '~': fileURLToPath(new URL('./', import.meta.url)) // 根目录别名
    }
  },

  // 开发服务器配置
  server: {
    port: 5173,
    strictPort: true,
    open: true
  },

  // 构建配置
  build: {
    chunkSizeWarningLimit: 2000,          // 增大chunk大小警告限制
    rollupOptions: {
      output: {
        manualChunks: {
          // 自定义代码分割
          'vue-vendor': ['vue', 'vue-router', 'pinia']
        }
      }
    }
  }
})