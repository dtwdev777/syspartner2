<script setup lang="ts">
import { ref , computed } from 'vue'
import { router } from '@inertiajs/vue3'; //  Импорт router для навигации
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
const props = defineProps({
  clients: {
    type: Array,
    required: true,
    default: () => []
  },
  // Добавьте другие пропсы, если они есть
});
const density = ref('compact');
const search = ref('')
const headers = [
  { title: 'ID', align: 'start', key: 'id' },
  { title: 'Имя', key: 'name' },
  { title: 'Активен', key: 'is_active', align: 'center' },
  { title: 'Лимиты', key: 'limit_count', align: 'end' },
  { title: 'Токен', key: 'token' },
  { title: 'Дата окончания', key: 'final_date' },
  { title: 'Действия', key: 'actions', sortable: false, align: 'center' },
];

// ------------------------------------
// 3. Методы форматирования и действий
// ------------------------------------

/**
 * Форматирует дату ISO-строки в читаемый формат (DD.MM.YYYY).
 * @param {string} dateString 
 */
const formatDate = (dateString) => {
    if (!dateString) return 'Нет данных';
    try {
        // Создаем объект Date из ISO строки
        const date = new Date(dateString);
        // Используем Intl.DateTimeFormat для локализованного вывода
        return new Intl.DateTimeFormat('ru-RU', { 
            year: 'numeric', 
            month: 'short', 
            day: '2-digit' 
        }).format(date);
    } catch (e) {
        return 'Ошибка даты';
    }
};

/**
 * Имитация перехода к редактированию
 */
const editClient = (client) => {
    // Здесь вы будете использовать Inertia для перехода:
    // router.get(`/users/${client.id}/edit`); 
    
    // Пока что просто выведем в консоль
    console.log('Редактирование клиента:', client.id, client.name);
     // Предполагаем, что у вас есть маршрут с именем 'users.create' (или просто '/form')
     router.get(`/edit-form/${client.id}`); 
};


const goToAddClient = () => {
    // ⭐️ Используем router.get() для перехода на маршрут создания
    // Предполагаем, что у вас есть маршрут с именем 'users.create' (или просто '/form')
    router.get('/form'); 
    
    // Если вы используете именованные маршруты Laravel/Inertia:
    // router.get(route('users.create')); 
};

const deleteClient = (client) => {
    if (confirm(`Вы уверены, что хотите удалить клиента "${client.name}" (ID: ${client.id})?`)) {
        // Отправка DELETE-запроса на сервер
        router.delete(`/client/${client.id}`, {
            onSuccess: () => {
                // Успех: Inertia автоматически обновит страницу (таблицу)
                console.log(`Клиент ${client.id} успешно удален.`);
            },
            onError: (errors) => {
                console.error("Ошибка при удалении:", errors);
                alert("Не удалось удалить клиента. Проверьте консоль.");
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

<template>
 <AppLayout :breadcrumbs="breadcrumbs">
 <v-card class="mx-auto mt-5 antenna-background" max-width="900">
    <v-card-title class="text-h5 pa-4 title-text">
      Список клиентов ({{ clients.length }})
      <v-spacer></v-spacer>
      
      <v-btn
        color="primary"
        prepend-icon="mdi-plus"
        @click="goToAddClient"
        size="small"
        variant="flat"
      >
        Добавить клиента
      </v-btn>
    </v-card-title>
    <v-divider></v-divider>

    <v-card-text>
      <v-text-field
        v-model="search"
        append-inner-icon="mdi-magnify"
        label="Поиск"
        single-line
        hide-details
        variant="outlined"
        density="compact"
        class="mb-3 search-bg"
      ></v-text-field>
    </v-card-text>

    <v-data-table
      :headers="headers"
      :items="props.clients"
      item-value="id"
      class="elevation-1"
      :density="density"
      :search="search"
    >
      <template v-slot:item.is_active="{ item }">
        <v-chip
          :color="item.is_active ? 'success' : 'error'"
          :prepend-icon="item.is_active ? 'mdi-check-circle' : 'mdi-close-circle'"
          size="small"
        >
          {{ item.is_active ? 'Да' : 'Нет' }}
        </v-chip>
      </template>

      <template v-slot:item.final_date="{ item }">
        {{ formatDate(item.final_date) }}
      </template>
      
      <template v-slot:item.actions="{ item }">
        <v-btn 
          icon 
          size="small" 
          variant="text" 
          color="primary"
          @click="editClient(item)"
          title="Редактировать"
        >
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
        <v-btn 
          icon 
          size="small" 
          variant="text" 
          color="error"
          @click="deleteClient(item)"
          title="Удалить"
        >
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </template>

    </v-data-table>
  </v-card>
</AppLayout>
</template>

<style scoped>
.antenna-background {
  /* Указываем путь к вашему изображению */
  background-image: url('@/../images/fon.png'); 
  
  /* Убеждаемся, что изображение покрывает весь контейнер */
  background-size: cover; 
  
  /* Фиксируем изображение, чтобы оно не прокручивалось с контентом */
  background-attachment: fixed; 
  
  /* Центрируем изображение */
  background-position: center center; 
  
  /* Устанавливаем черный или очень темный цвет фона 
     на случай, если изображение не загрузится */
  background-color: #121212; 
}
.search-bg {
    /* Стили, перенесенные из инлайна */
    background-color: #fff;
    border-radius: 4px;
}
.title-text {
color: #fff;
}
</style>