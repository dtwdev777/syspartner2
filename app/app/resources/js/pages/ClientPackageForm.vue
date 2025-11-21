<template>
  <v-card class="max-w-xl mx-auto mt-10" elevation="6" rounded="lg">
    <v-card-title class="text-h5 font-weight-bold py-4 text-black">
      <div class="text-center w-full">Привязка клиента к пакету</div>
    </v-card-title>

    <v-card-text class="py-6 px-4">
      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Поле клиента (только для чтения) -->
        <v-text-field
          v-model="clientName"
          label="Клиент"
          variant="outlined"
          density="compact"
          readonly
        />

        <!-- Выбор пакета -->
       <v-select
  v-model="form.package_id"
  :items="packages"
  item-title="name"
  item-value="id"
  label="Пакет"
  variant="outlined"
  density="compact"
  :error-messages="form.errors.package_id ? [form.errors.package_id] : []"
  hide-details="auto"
/>

        <!-- Кнопки -->
        <v-card-actions class="flex justify-between mt-6">
          <v-btn
            color="grey"
            variant="outlined"
            prepend-icon="mdi-arrow-left"
            @click="goBack"
          >
            Назад
          </v-btn>

          <v-btn
            type="submit"
            color="primary"
            size="large"
            prepend-icon="mdi-link"
            :loading="form.processing"
            :disabled="form.processing"
          >
            Привязать
          </v-btn>
        </v-card-actions>
      </form>
    </v-card-text>

    <!-- Snackbar для успеха -->
    <v-snackbar
      v-model="successAlert"
      color="green"
      timeout="3000"
      location="bottom right"
    >
      ✅ Клиент успешно привязан к пакету
    </v-snackbar>
  </v-card>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

interface Client {
  id: number;
  name: string;
}

interface Package {
  id: number;
  name: string;
}

const props = defineProps<{
  client: Client;
  packages: Package[];
}>();

// --- useForm ---
const form = useForm({
   package_id: props.client.package_id ?? null,
});

// --- Отображение имени клиента ---
const clientName = props.client.name;

// --- Snackbar состояние ---
const successAlert = ref(false);

// --- Отправка формы ---
const submitForm = () => {
  form.post(`/client-save/${props.client.id}`, {
    onSuccess: () => {
      successAlert.value = true; // ✅ показать алерт
      form.reset('package_id');
    },
    onError: (e) => {
      console.error('Ошибка при привязке:', e);
    }
  });
};

const goBack = () => {
  window.history.back();
};
</script>

<style scoped>
.max-w-xl {
  max-width: 36rem;
}
</style>
