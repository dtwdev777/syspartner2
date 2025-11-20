<template>
  <AppLayout :breadcrumbs="breadcrumbs">
  <v-card class="max-w-5xl mx-auto mt-10" elevation="6" rounded="lg">
       <v-alert
        v-if="flashMessage"
        type="success"
        title="Успех"
        icon="mdi-check-circle"
        closable
        class="mx-auto mt-5 mb-4 elevation-2"
        max-width="900"
        @click:close="flashMessage = null"
    >
        {{ flashMessage }}
    </v-alert>
    <v-card-title class="text-h5 font-weight-bold py-4 text-black">
      <div class="text-center w-full">Список Каналов</div>
        <v-btn
        color="primary"
        prepend-icon="mdi-plus"
        @click="goToAddClient"
        size="small"
        variant="flat"
      >
        Добавить каналы
      </v-btn>
    </v-card-title>

    <v-card-text>
      <!-- Поиск -->
      <v-text-field
        v-model="search"
        label="Поиск"
        placeholder="Введите название, заголовок или ссылку"
        prepend-inner-icon="mdi-magnify"
        variant="outlined"
        density="compact"
        class="mb-4"
        clearable
      ></v-text-field>

      <!-- Таблица -->
      <v-data-table
        :headers="headers"
        :items="filteredChannels"
        item-value="id"
        density="compact"
        hover
        class="elevation-1"
      >
        <!-- Ссылка -->
        <template #item.link="{ item }">
        
            {{ item.link }}
         
        </template>

        <!-- Действия -->
        <template #item.actions="{ item }">
          <div class="flex gap-2">
            <v-btn
              icon
              size="x-small"
              color="primary"
              @click="editChannel(item)"
              :ripple="false"  variant="text" 
            >
              <v-icon size="16">mdi-pencil</v-icon>
            </v-btn>
            <v-btn
              icon
              size="x-small"
              color="error"
              @click="deleteChannel(item.id)"
              :ripple="false"  variant="text" 
            >
              <v-icon size="16">mdi-delete</v-icon>
            </v-btn>
          </div>
        </template>
      </v-data-table>
    </v-card-text>
  </v-card>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed ,onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

interface Resource {
  id: number;
  name: string;
  title: string;
  link: string;
}

interface FlashProps {
    success: string | null;
    error: string | null;
    // Добавьте другие типы сообщений, если используете (e.g., warning: string | null)
}


const props = defineProps<{
  channels: Resource[];
  flash: {
        type: Object, // PropType<FlashProps>
        required: false,
        default: () => ({ success: null, error: null })
    },
}>();

const search = ref('');
const flashMessage = ref(props.flash.success);
// Заголовки таблицы
const headers = [
  { title: 'ID', key: 'id' },
  { title: 'Название', key: 'name' },
  { title: 'Заголовок', key: 'title' },
  { title: 'Ссылка', key: 'link' },
  { title: 'Действия', key: 'actions', sortable: false },
];

onMounted(() => {
    // Устанавливаем таймаут только если сообщение существует
    if (flashMessage.value) {
        setTimeout(() => {
            flashMessage.value = null; // Скрываем сообщение
        }, 5000); // 5 секунд
    }
});

// Фильтрация
const filteredChannels = computed(() => {
  if (!search.value) return props.channels;

  const query = search.value.toLowerCase();

  return props.channels.filter(channel =>
    channel.name.toLowerCase().includes(query) ||
    channel.title.toLowerCase().includes(query) ||
    channel.link.toLowerCase().includes(query)
  );
});

// Редактирование
const editChannel = (channel: Resource) => {
  router.get(`/channel/${channel.id}`);
};

const goToAddClient = () => {
    // ⭐️ Используем router.get() для перехода на маршрут создания
    // Предполагаем, что у вас есть маршрут с именем 'users.create' (или просто '/form')
    router.get('/channel-new'); 
    
    // Если вы используете именованные маршруты Laravel/Inertia:
    // router.get(route('users.create')); 
};

// Удаление
const deleteChannel = (id: number) => {
  if (confirm('Вы уверены, что хотите удалить этот канал?')) {
    router.delete(`/channel/${id}`, {
      onSuccess: () => {
        console.log(`Канал ${id} удалён`);
      },
      onError: (e) => {
        console.error('Ошибка при удалении:', e);
      }
    });
  }
};
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>
