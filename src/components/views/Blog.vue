<script>
import axios from 'axios';

export default {
    data() {
        return {
            posts: []
        };
    },
    created() {
        this.fetchPosts();
        document.title = '鼠子Blog'; 
    },
    methods: {
        async fetchPosts() {
            try {
                const response = await axios.get('https://blog.miomoe.cn/api/posts?filterType=category&filterSlug=Note');
                if (response.data.status === 'success') {
                    this.posts = response.data.data.dataSet;
                }
            } catch (error) {
                console.error('Error fetching posts:', error);
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
    <router-link :to="{ name: 'BlogDetail', params: { cid: post.cid } }" v-for="post in posts" :key="post.cid">
        <div class="mdui-m-b-2">
            <div class="mdui-card-primary-title">
                {{ post.title }}
            </div>
            <div class="mdui-card-primary-subtitle">
                {{ formatDate(post.date.timeStamp) }} - {{ post.categories.map(cat => cat.name).join(', ') }}
            </div>
        </div>
    </router-link>
</template>
