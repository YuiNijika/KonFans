import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/reset.css';

import VueLazyload from 'vue-lazyload';
import VueEasyLightbox from 'vue-easy-lightbox'

const app = createApp(App)

app.use(router)
app.use(Antd)
app.use(VueEasyLightbox)
app.use(VueLazyload, {
    preLoad: 1.3,
    error: '/assets/error.jpg', // 错误图片路径
    loading: '/assets/load.gif', // 加载中图片路径
    attempt: 1
});

app.mount('#app')
