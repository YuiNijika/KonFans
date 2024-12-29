// main.js
// 引入样式文件
import './assets/main.css';

// 引入 Vue 和 App 组件
import { createApp } from 'vue';
import App from './App.vue';

// 引入路由配置
import router from './router';

// 引入 vue-lazyload
import VueLazyload from 'vue-lazyload';

// 创建 Vue 应用实例并使用路由
const app = createApp(App);

// 初始化 mdui
mdui.mutation();

// 使用 vue-lazyload
app.use(VueLazyload, {
    preLoad: 1.3,
    error: '/error.jpg', // 错误图片路径
    loading: '/load.gif', // 加载中图片路径
    attempt: 1
});

// 挂载应用到 DOM
app.use(router).mount('#app');
