<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

interface AnnouncementType {
    title: string;
    subTitle: string;
    content: string;
}

interface ApiResponse {
    code: string;
    data: AnnouncementType;
}

const announcement = ref<AnnouncementType>({
    title: '',
    subTitle: '',
    content: ''
});

const isLoading = ref(true);
const error = ref(false);

onMounted(async () => {
    try {
        const response = await axios.get('https://api-v2.x-x.work/web/kon/announcement');
        if (response.data.code === 200) {
            announcement.value = response.data.data;
        } else {
            console.error('Failed to fetch announcement:', response.data);
            error.value = true;
        }
    } catch (err) {
        console.error('Error fetching announcement:', err);
        error.value = true;
    } finally {
        isLoading.value = false;
    }
});
</script>

<template>
    <div v-if="isLoading" class="loading-text">
        <v-skeleton-loader type="paragraph"></v-skeleton-loader>
    </div>
    <div v-else-if="error">
        <p>加载失败，请稍后再试。</p>
    </div>
    <v-card v-else style="margin-bottom: 20px;">
        <v-card-title>{{ announcement.title }}</v-card-title>
        <v-card-subtitle>{{ announcement.subTitle }}</v-card-subtitle>
        <v-card-text v-html="announcement.content"></v-card-text>
    </v-card>
</template>

<style scoped>
.loading-text {
    text-align: center;
    margin-top: 20px;
    font-size: 18px;
    color: #666;
}
</style>