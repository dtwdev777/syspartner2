<template>
  <v-card class="max-w-xl mx-auto mt-10" elevation="6" rounded="lg">
    <v-card-title class="text-h5 font-weight-bold py-4 text-black">
      <div class="text-center w-full">Редактирование Пакета</div>
      <v-btn
    color="grey"
    variant="outlined"
    prepend-icon="mdi-arrow-left"
    @click="goBack"
  >
    Назад
  </v-btn>
    </v-card-title>

    <v-card-text class="py-6 px-4">
      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Название -->
        <v-text-field
          v-model="form.name"
          label="Название"
          :error-messages="form.errors.name ? [form.errors.name] : []"
          variant="outlined"
          density="compact"
          hide-details="auto"
        />

        <!-- Страны -->
        <v-autocomplete
          v-model="form.countries"
          :items="transformedCountries"
          item-title="name"
          item-value="id"
          label="Страны"
          multiple
          chips
          clearable
          variant="outlined"
          density="compact"
          :error-messages="form.errors.countries ? [form.errors.countries] : []"
          hide-details="auto"
        />

       <!-- Активность -->
        <div class="flex items-center gap-4">
         <v-switch
  v-model="form.is_active"
  :label="form.is_active ? 'Включен ✅' : 'Отключен ⛔'"
  color="primary"
  inset
/>
        </div>

        <!-- Кнопка -->
        <v-btn
          type="submit"
          color="primary"
          size="large"
          block
          class="mt-8"
          :loading="form.processing"
          :disabled="form.processing"
        >
          Обновить
        </v-btn>
      </form>
    </v-card-text>
  </v-card>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

interface Country {
  id: number;
  name: string;
}

interface Channel {
  id: number;
  name: string;
  is_active: boolean;
  countries: number[]; // массив ID стран
}

const props = defineProps<{
  package: Channel;
  countries: Country[] | Record<number, string>;
}>();

// --- useForm с предзаполненными данными ---
const form = useForm({
  name: props.package.name,
  countries: props.package.countries,
  is_active: props.package.is_active,
});

// --- Преобразование стран ---
const transformedCountries = computed<Country[]>(() => {
  const data = props.countries;
  if (data && typeof data === 'object' && !Array.isArray(data)) {
    return Object.entries(data).map(([id, name]) => ({
      id: Number(id),
      name: String(name),
    }));
  }
  return Array.isArray(data) ? data : [];
});

// --- Отправка формы ---
const submitForm = () => {
  form.put(`/package/${props.package.id}/update`, {
    onSuccess: () => {
      console.log('Канал обновлён');
    },
    onError: (e) => {
      console.error('Ошибка при обновлении:', e);
    }
  });
};

const goBack = () => {
  // Вариант 1: просто назад в истории браузера
  window.history.back();

  // Вариант 2: переход на список пакетов
  // router.get('/packages');
};
</script>

<style scoped>
.max-w-xl {
  max-width: 36rem;
}
</style>
