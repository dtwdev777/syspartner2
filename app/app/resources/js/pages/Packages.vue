<template>
    <AppLayout :breadcrumbs="breadcrumbs">
  <v-card class="max-w-screen-xl mx-auto mt-10 p-6" elevation="8" rounded="xl">
    <!-- Заголовок карточки -->
    <v-card-title class="text-h5 font-bold pb-4">
      Управление пакетами
    </v-card-title>
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

    <!-- Кнопка создать -->
    <div class="flex justify-end mb-6">
      <v-btn
        color="success"
        prepend-icon="mdi-plus-box"
        @click="createPackage"
        variant="elevated"
        size="small"
      >
      Добавить
      
      </v-btn>
    </div>

    <!-- Сетка карточек -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <v-card
        v-for="pkg in packages"
        :key="pkg.id"
        elevation="6"
        rounded="lg"
        class="flex flex-col justify-between p-4 hover:scale-[1.02] transition-transform duration-300"
      >
        <!-- Заголовок -->
        <v-card-title class="text-h6 font-bold pb-2">
          <v-icon icon="mdi-package-variant" color="primary" class="mr-2" />
          {{ pkg.name }}
        </v-card-title>

        <!-- Контент -->
        <v-card-text class="space-y-3">
          <div class="flex items-center gap-2">
            <span class="font-medium">Статус:</span>
            <v-chip
              :color="pkg.is_active ? 'green' : 'grey'"
              size="small"
              variant="elevated"
            >
              {{ pkg.is_active ? '✅ Активен' : '⛔ Неактивен' }}
            </v-chip>
          </div>

          <div v-if="pkg.countries?.length">
            <span class="font-medium">Страны:</span>
            <div class="flex flex-wrap gap-2 mt-1">
              <v-chip
                v-for="country in pkg.countries"
                :key="country.id"
                color="indigo"
                size="small"
                variant="outlined"
                class="text-white bg-indigo-600"
              >
                {{ country.name }}
              </v-chip>
            </div>
          </div>
        </v-card-text>

        <!-- Кнопки -->
        <v-card-actions class="flex gap-3 pt-4">
          <v-btn
            color="primary"
            variant="elevated"
            class="flex-1"
            prepend-icon="mdi-pencil"
            @click="updatePackage(pkg.id)"
          >
             Обновить
          </v-btn>

          <v-btn
            color="error"
            variant="elevated"
            class="flex-1"
            prepend-icon="mdi-trash-can"
            @click="deletePackage(pkg.id)"
          >
             Удалить
          </v-btn>
        </v-card-actions>
      </v-card>
    </div>
  </v-card>
  </AppLayout>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref , computed ,onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
interface Country {
  id: number;
  name: string;
}

interface Package {
  id: number;
  name: string;
  is_active: boolean;
  countries?: Country[];
}

const props = defineProps<{
  packages: Package[];
   flash: {
        type: Object, // PropType<FlashProps>
        required: false,
        default: () => ({ success: null, error: null })
    },
}>();

const flashMessage = ref(props.flash.success);
const createPackage = () => {
  router.get('/package-new');
};

const updatePackage = (id: number) => {
  router.get(`/package-edit/${id}`);
};

const deletePackage = (id: number) => {
  if (confirm('Удалить пакет?')) {
    router.delete(`/packages/${id}`);
  }
};

onMounted(() => {
    // Устанавливаем таймаут только если сообщение существует
    if (flashMessage.value) {
        setTimeout(() => {
            flashMessage.value = null; // Скрываем сообщение
        }, 5000); // 5 секунд
    }
});
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>
