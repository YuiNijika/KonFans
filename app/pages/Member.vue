<script setup>
useSeoMeta({
    title: '成员列表',
    description: '轻音小窝成员列表',
    keywords: '轻音部, 成员, 列表'
})

const baseUrl = '/apiService/member';

const members = ref([]);
const loadingMembers = ref(false);
const submittingMember = ref(false);
const memberPagination = ref({
    page: 1,
    size: 50,
    order: 'asc',
    total: 0
});

const memberForm = ref({
    name: '',
    email: '',
    url: '',
    bio: '',
    gender: 'secret'
});

const showMemberForm = ref(false);

// 获取成员列表
const fetchMembers = async () => {
    loadingMembers.value = true;
    try {
        const response = await $fetch(baseUrl, {
            params: {
                page: memberPagination.value.page,
                size: memberPagination.value.size,
                order: memberPagination.value.order
            }
        });

        if (response.success) {
            members.value = response.data;
            memberPagination.value.total = response.pagination.total;
        } else {
            console.error('获取成员列表失败:', response.error);
        }
    } catch (error) {
        console.error('获取成员列表失败:', error.data?.message || error.message);
    } finally {
        loadingMembers.value = false;
    }
};

// 排序变化
const handleSortChange = () => {
    memberPagination.value.page = 1; // 重置到第一页
    fetchMembers();
};

// 提交加入社团
const submitMemberApplication = async () => {
    if (!memberForm.value.name.trim()) {
        alert('请输入昵称');
        return;
    }

    if (!memberForm.value.email.trim()) {
        alert('请输入邮箱');
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(memberForm.value.email)) {
        alert('请输入有效的邮箱地址');
        return;
    }

    if (!['male', 'female', 'secret'].includes(memberForm.value.gender)) {
        alert('请选择有效的性别');
        return;
    }

    if (memberForm.value.url && !memberForm.value.url.startsWith('http')) {
        memberForm.value.url = 'https://' + memberForm.value.url;
    }

    submittingMember.value = true;

    try {
        const response = await fetch(baseUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(memberForm.value)
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => null);
            throw new Error(errorData?.error || `HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            alert('提交成功！');
            // 关闭对话框
            showMemberForm.value = false;
            // 重置表单
            memberForm.value = {
                name: '',
                email: '',
                url: '',
                bio: '',
                gender: 'secret'
            };
            // 刷新列表
            await fetchMembers();
        } else {
            alert(data.error || '提交失败');
        }
    } catch (error) {
        console.error('提交错误详情:', error);
        alert('提交失败: ' + error.message);
    } finally {
        submittingMember.value = false;
    }
};
// 分页变化
const handleMemberPageChange = (page) => {
    memberPagination.value.page = page;
    fetchMembers();
};

onMounted(() => {
    fetchMembers();
});
</script>

<template>
    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-8">
            <button @click="showMemberForm = true" class="btn btn-primary">
                加入轻音部
            </button>
        </div>

        <div v-if="showMemberForm" class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">提交表</h3>
                <div class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">昵称*</span>
                        </label>
                        <input v-model="memberForm.name" type="text" placeholder="您的昵称"
                            class="input input-bordered w-full">
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">性别</span>
                        </label>
                        <select v-model="memberForm.gender" class="select select-bordered w-full">
                            <option value="secret">保密</option>
                            <option value="male">男</option>
                            <option value="female">女</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">邮箱*</span>
                        </label>
                        <input v-model="memberForm.email" type="email" placeholder="您的邮箱"
                            class="input input-bordered w-full">
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">个人主页</span>
                        </label>
                        <input v-model="memberForm.url" type="text" placeholder="https://"
                            class="input input-bordered w-full">
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">个人简介</span>
                        </label>
                        <textarea v-model="memberForm.bio" class="textarea h-24 w-full"
                            placeholder="介绍一下你自己, 例如会弹唱滑滑蛋..."></textarea>
                    </div>

                    <div class="modal-action">
                        <button @click="showMemberForm = false" class="btn btn-ghost">取消</button>
                        <button @click="submitMemberApplication" :disabled="submittingMember" class="btn btn-primary">
                            <span v-if="submittingMember" class="loading loading-spinner"></span>
                            加入
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-md">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6">轻音部成员</h2>

                <div class="flex items-center space-x-2">
                    <span class="text-sm">排序方式：</span>
                    <select v-model="memberPagination.order" @change="handleSortChange"
                        class="select select-bordered select-sm">
                        <option value="asc">从旧到新</option>
                        <option value="desc">从新到旧</option>
                    </select>
                </div>

                <div v-if="loadingMembers" class="text-center py-8">
                    <span class="loading loading-spinner loading-lg"></span>
                    <p class="mt-4">正在加载成员列表...</p>
                </div>

                <div v-else-if="members.length === 0" class="text-center py-8">
                    <p>暂无成员信息</p>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="member in members" :key="member.id" class="card bg-base-100 shadow-sm">
                        <NuxtLink :to="`/space/${member.id}`">
                            <div class="card-body">
                                <div class="flex items-center space-x-4">
                                    <div class="avatar">
                                        <div class="w-16 rounded-full">
                                            <img :src="member.avatar" :alt="member.name">
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold">{{ member.name }}</h3>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <p class="text-sm">{{ member.bio || '没有填写简介...' }}</p>
                                </div>
                                <div class="mt-2 text-xs opacity-50">
                                    加入时间: {{ new Date(member.created_at).toLocaleDateString() }}
                                </div>
                            </div>
                        </NuxtLink>
                    </div>
                </div>

                <div class="flex justify-center mt-6">
                    <div class="btn-group">
                        <button v-for="page in Math.ceil(memberPagination.total / memberPagination.size)" :key="page"
                            @click="handleMemberPageChange(page)" class="btn"
                            :class="{ 'btn-active': page === memberPagination.page }">
                            {{ page }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}
</style>