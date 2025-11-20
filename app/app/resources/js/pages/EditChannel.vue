<template>
  <v-card class="max-w-xl mx-auto mt-10" elevation="10" rounded="lg">
    <v-card-title class="text-h5 font-weight-bold py-4 text-black">
      <div class="text-center w-full">Редактирование Канала</div>
        <v-btn
            icon
            variant="text"
            size="small"
            @click="goBack"
            title="Назад к списку"
            class="mr-2"
          >
            <v-icon>mdi-arrow-left</v-icon>
          </v-btn>
    </v-card-title>

    <v-card-text class="py-6 px-4">
      <form @submit.prevent="submitForm" class="space-y-6">
        <v-text-field
          v-model="form.name"
          label="Название"
          :error-messages="form.errors.name ? [form.errors.name] : []"
          variant="outlined"
          density="compact"
          hide-details="auto"
        />

        <v-text-field
          v-model="form.title"
          label="Заголовок"
          :error-messages="form.errors.title ? [form.errors.title] : []"
          variant="outlined"
          density="compact"
          hide-details="auto"
        />

        <v-text-field
          v-model="form.link"
          label="Ссылка"
          type="url"
          :error-messages="form.errors.link ? [form.errors.link] : []"
          variant="outlined"
          density="compact"
          hide-details="auto"
        />

       

      
        <v-btn
          type="submit"
          color="primary"
          size="large"
          block
          class="mt-8"
          :loading="form.processing"
          :disabled="form.processing"
        >
          Сохранить Изменения
        </v-btn>
      </form>
    </v-card-text>
  </v-card>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useForm , router} from '@inertiajs/vue3';

interface Country {
  id: number;
  name: string;
}

interface Channel {
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
  channel: Channel;
 
}>();

// --- useForm с предзаполнением ---
const form = useForm({
  name: props.channel.name,
  title: props.channel.title,
  link: props.channel.link,
 
});



// --- Отправка формы ---
const submitForm = () => {
  console.log('Отправка формы:', form.data());
  form.put(`/channel/${props.channel.id}/update`, {
   // forceFormData: true,
    onSuccess: () => {
      console.log('Канал успешно обновлён');
    },
    onError: (e) => {
      console.error('Ошибка при обновлении:', e);
    }
  });
};

const goBack = () => {
    // 1. Использование router.back()
    // Это предпочтительный способ, он имитирует нажатие кнопки "Назад" в браузере.
   router.get('/channels')
    
    // 2. ИЛИ Использование router.get() на конкретный маршрут (если router.back() вызывает проблемы)
    // router.get('/users'); // Замените на фактический URL вашей таблицы
};
</script>

<style scoped>
.max-w-xl {
  max-width: 36rem;
}
</style>
