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
</v-card>
    <v-card class="max-w-xl mx-auto mt-10" elevation="6" rounded="lg">
    <v-card-title class="text-h5 font-weight-bold py-4 text-black">
      <div class="text-center w-full">Ссылки для скачивание</div>
    </v-card-title>
   
    <v-card-text class="py-6 px-4">
     <div
    v-for="(link, index) in localLinks"
    :key="index"
    class="d-flex align-center mb-4"
  >
    <v-text-field
      v-model="link.url"
      label="Ссылка (URL)"
      variant="outlined"
      prepend-inner-icon="mdi-link"
      hide-details
      class="mr-2"
      density="compact"
     
    append-inner-icon="mdi-content-copy"
    @click:append-inner="copyToClipboard(link.url)"
   
    />

    <v-checkbox v-if="link.isVideo"
      v-model="link.isVideo"
      label="m3u8"
      color="primary"
      @change="updateUrl(link)"
    />
     <v-checkbox v-else="link.isVideo"
      v-model="link.isVideo"
      label="m3u"
      color="primary"
      @change="updateUrl(link)"
    />

       
    </div>

       
    </v-card-text>
 </v-card>
    <!-- Snackbar для успеха -->
    <v-snackbar
      v-model="successAlert"
      color="green"
      timeout="3000"
      location="bottom right"
    >
      ✅ Клиент успешно привязан к пакету
    </v-snackbar>
 
</template>

<script setup lang="ts">
import { ref ,watch ,onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';

interface Client {
  id: number;
  name: string;
}

interface Link {
  id: number;
  url :string;
}

interface Package {
  id: number;
  name: string;
}

const props = defineProps<{
  client: Client;
  packages: Package[];
  links: Link[]
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


// преобразуем строки в объекты с флагом
const localLinks = ref(props.links.map(url => ({
  url,
  isVideo: true // по умолчанию считаем видео
})));

// обновляем ссылку при смене флага
const updateUrl = (link) => {
  // удаляем старый параметр, если есть
  link.url = link.url.replace(/([&?])type=(m3u8|m3u)/, '');

  // добавляем новый параметр
  const suffix = link.isVideo ? 'm3u8' : 'm3u';
  link.url += (link.url.includes('?') ? '&' : '?') + `type=${suffix}`;
};

const copyToClipboard = (text) => {
  if (navigator.clipboard) {
    try {
      navigator.clipboard.writeText(text);
      console.log('Скопировано:', text);
    } catch (err) {
      console.error('Ошибка копирования:', err);
    }
  } else {
    // fallback для http
    const input = document.createElement('input');
    input.value = text;
    document.body.appendChild(input);
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);
    console.log('Скопировано через fallback:', text);
  }
};

onMounted(() => {
  localLinks.value.forEach(link => updateUrl(link));
});



const goBack = () => {
  window.history.back();
};
</script>

<style scoped>
.max-w-xl {
  max-width: 36rem;
}
</style>
