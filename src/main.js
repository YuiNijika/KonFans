// main.js
// 引入样式文件
import './assets/main.css';

// 引入 Vue 和 App 组件
import { createApp } from 'vue';
import App from './App.vue';

// 引入路由配置
import router from './router.js';

// 引入 vue-lazyload
import VueLazyload from 'vue-lazyload';

// 创建 Vue 应用实例并使用路由
const app = createApp(App);

// 初始化 mdui
mdui.mutation();

// 使用 vue-lazyload
app.use(VueLazyload, {
    preLoad: 1.3,
    error: '/assets/error.jpg', // 错误图片路径
    loading: '/assets/load.gif', // 加载中图片路径
    attempt: 1
});

// 挂载应用到 DOM
app.use(router).mount('#app');

// 下雪效果
function createSnowflake() {
    const snowflake = document.createElement('div');
    snowflake.classList.add('snowflake');
    snowflake.innerHTML = '❄'; // 雪花的图标可以更改

    const size = Math.random() * 20 + 10; // 雪花大小范围
    let initialX = Math.random() * window.innerWidth;

    snowflake.style.fontSize = `${size}px`;
    snowflake.style.left = `${initialX}px`;

    document.body.appendChild(snowflake);

    setTimeout(() => {
        snowflake.remove();
    }, 10000); // 与动画时间一致
}

function snowfall() {
    setInterval(createSnowflake, 300); // 控制雪花的生成速度
}

snowfall();