<template>
    <div v-if="isLoading" class="loading-text">
        加载中...
    </div>
    <div v-else>
        <v-card>
            <v-card-title>
                {{ Kon.data.title }}
            </v-card-title>
            <v-card-subtitle>
                {{ Kon.data.subTitle }}
            </v-card-subtitle>
            <v-card-text>
                <div v-html="Kon.data.content"></div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

interface KonDataType {
    title: string;
    subTitle: string;
    content: string;
}

interface KonResponse {
    code: string;
    data: KonDataType;
}

const Kon = ref<KonResponse>({
    code: '200',
    data: {
        title: '',
        subTitle: '',
        content: '',
    },
});

const isLoading = ref(true);

onMounted(async () => {
    try {
        const response = await axios.get('https://api-v2.x-x.work/web/kon/announcement');
        if (response.data.code === '200') {
            Kon.value = response.data;
        } else {
            console.error('Failed to fetch announcement:', response.data);
        }
    } catch (error) {
        console.error('Error fetching announcement:', error);
    } finally {
        isLoading.value = false;
    }
});
</script>

<style scoped>
.loading-text {
    text-align: center;
    margin-top: 20px;
    font-size: 18px;
    color: #666;
}
</style>