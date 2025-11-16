<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import Users from './Users.vue' // Компонент, отображающий таблицу

// 1. Получаем пропсы Inertia
const page = usePage();
// Предполагаем, что ваш Laravel-контроллер передает массив клиентов/пользователей под ключом 'clients'
const clients = (page.props.clients ?? []) as any[]; 

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <!-- 2. ПЕРЕДАЧА ДАННЫХ В КОМПОНЕНТ USERS -->
            <Users :clients="clients" />
            
        </div>
    </AppLayout>
</template>
