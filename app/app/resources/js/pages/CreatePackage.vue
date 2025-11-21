<template>
  <v-card class="max-w-xl mx-auto mt-10" elevation="6" rounded="lg">
    <v-card-title class="text-h5 font-weight-bold py-4 text-black">
      <div class="text-center w-full">Создание Канала</div>
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
        <v-switch
          v-model="form.is_active"
          label="Активен"
          color="primary"
          inset
        />

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
          Сохранить
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

const props = defineProps<{
  countries: Country[] | Record<number, string>;
}>();

// --- useForm ---
const form = useForm({
  name: '',
  countries: [] as number[],
  is_active: true,
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
  form.post('/package', {
    onSuccess: () => {
      console.log('Канал создан');
      form.reset();
    },
    onError: (e) => {
      console.error('Ошибка при создании:', e);
    }
  });
};
</script>

<style scoped>
.max-w-xl {
  max-width: 36rem;
}
</style>