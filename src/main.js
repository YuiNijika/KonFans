// 引入样式文件
import './assets/main.css';

// 引入 Vue 和 App 组件
import { createApp } from 'vue';

import App from './App.vue';

// 引入路由配置
import router from './router';

// 创建 Vue 应用实例并使用路由
const app = createApp(App);

// 初始化 mdui
mdui.mutation();

// 挂载应用到 DOM
app.use(router).mount('#app');
