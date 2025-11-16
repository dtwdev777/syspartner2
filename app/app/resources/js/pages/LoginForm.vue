<template>
  <v-container class="fill-height align-center justify-center">
    <v-card 
      class="pa-4 pa-sm-8" 
      max-width="400" 
      width="100%" 
      elevation="8"
    >
      <v-card-title class="text-h5 text-center mb-4">
        Авторизация
      </v-card-title>
      
      <v-form @submit.prevent="login" ref="form">
        
        <!-- Сообщение об общей ошибке -->
        <v-alert
          v-if="errorMessage"
          type="error"
          variant="tonal"
          class="mb-4"
          density="compact"
        >
          {{ errorMessage }}
        </v-alert>

        <v-text-field
          v-model="email"
          :rules="[rules.required, rules.email]"
          label="Email"
          prepend-inner-icon="mdi-email-outline"
          variant="outlined"
          required density="compact"
        ></v-text-field>

        <v-text-field
          v-model="password"
          :rules="[rules.required]"
          label="Пароль"
          prepend-inner-icon="mdi-lock-outline"
          :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
          :type="showPassword ? 'text' : 'password'"
          @click:append-inner="showPassword = !showPassword"
          variant="outlined"
          required
          class="mb-4" density="compact"
        ></v-text-field>

        <v-btn 
          type="submit" 
          color="primary" 
          size="large" 
          block
          :loading="loading"
        >
          Войти
        </v-btn>

     

      </v-form>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3'
const page = usePage();
// 1. Состояния формы и UI
const email = ref('admin@sample.com'); // Предзаполнение для удобства
const password = ref('password');       // Предзаполнение для удобства
const showPassword = ref(false);
const loading = ref(false);
const form = ref(null); 
const errorMessage = ref(null); // Состояние для отображения ошибки сервера

// 2. Правила валидации
const rules = {
  required: value => !!value || 'Поле обязательно для заполнения.',
  email: value => {
    const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return pattern.test(value) || 'Введите корректный Email.';
  },
};

// 3. Функция отправки формы, интегрированная с API (имитация Laravel)
async function login() {
  // Сброс предыдущей ошибки
  errorMessage.value = null;

  // 1. Клиентская валидация Vuetify
  const validation = await form.value.validate();
  if (!validation.valid) {
    return;
  }

  loading.value = true;
  
  // URL вашего эндпоинта авторизации Laravel
  // В реальном приложении замените этот URL на ваш домен/api/login
  const LOGIN_URL = '/login'; 

  try {
   router.post('/login', {
 
  password: password.value ,
  email: email.value,
})

  } catch (error) {
    console.error('Fetch error:', error);
    errorMessage.value = 'Не удалось подключиться к серверу. Проверьте соединение.';
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.v-card-title {
  font-weight: 600;
}
/* Стили Vuetify обеспечивают адаптивность, но можно добавить свои */
.v-container {
  /* Занимает всю доступную высоту, чтобы карточка была по центру */
  min-height: 100vh; 
}
</style>