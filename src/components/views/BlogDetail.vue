<script>
import axios from 'axios';

export default {
    data() {
        return {
            post: null
        };
    },
    created() {
        this.fetchPost();
    },
    methods: {
        async fetchPost() {
            try {
                const cid = this.$route.params.cid;
                const response = await axios.get(`https://blog.miomoe.cn/api/post?cid=${cid}`);
                if (response.data.status === 'success') {
                    this.post = response.data.data;
                    document.title = this.post.title; // 设置 document.title
                    window.ViewImage && ViewImage.init('.mdui-typo img'); // 设置灯箱
                }
            } catch (error) {
                console.error('Error fetching post:', error);
            }
        },
        getFirstImage(digest) {
            const imgMatch = digest.match(/<img\s+src="([^"]+)"/);
            return imgMatch ? imgMatch[1] : '//ss.bscstorage.com/wpteam/Wallpaper/KON/Space/Group/%E5%90%88%E7%85%A7%20(338).webp'; // 使用默认图片路径
        },
        formatDate(timestamp) {
            const date = new Date(timestamp * 1000);
            return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
        }
    }
};
</script>

<template>
    <div v-if="post">
        <div class="mdui-card-primary-title">
            {{ post.title }}
        </div>
        <div class="mdui-divider"></div>
        <div class="mdui-card-actions mdui-card-primary-subtitle">
            {{ formatDate(post.date.timeStamp) }} - {{ post.categories.map(cat => cat.name).join(', ') }}
        </div>
        <div class="mdui-divider"></div>
        <div class="mdui-card-content mdui-typo" v-html="post.digest"></div>
    </div>
    <div v-else>
        <p>正在加载...</p>
    </div>
</template>
