<template>
 
<v-card
class="mx-auto rounded-xl shadow-2xl transition-all duration-300 hover:shadow-lg"
elevation="12"
max-width="500"
>
 
<v-card-title class="text-h6 font-weight-bold text-center pt-6 pb-2 text-blue-darken-2">
  
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
        <span class="text-h5 font-weight-bold text-blue-darken-2">
            <v-icon icon="mdi-pencil-box-multiple" size="28" class="mr-3"></v-icon>
            Создание  Устройства
        </span>
</v-card-title>
<v-card-subtitle class="text-center pb-4 border-b border-opacity-50">
Генерация ID/Пароля.
</v-card-subtitle>

 <v-card-text class="pt-6">
        <v-form @submit.prevent="handleRegister">
            
            <!-- Поле для Сгенерированного ID -->
            <v-text-field
                :model-value="deviceData.name"
                label="Сгенерированный ID (Логин)"
                placeholder="Нажмите 'Сгенерировать'"
                variant="outlined"
                class="generated-field mb-4"
                :color="deviceData.name ? 'success' : 'grey'"
                :prepend-inner-icon="deviceData.name ? 'mdi-account-check' : 'mdi-account'"
                readonly
                hide-details density="compact"
            ></v-text-field>

            <!-- Поле для Сгенерированного Пароля -->
            <v-text-field
                :model-value="deviceData.password"
                label="Сгенерированный Пароль"
                placeholder="Нажмите 'Сгенерировать'"
                variant="outlined"
                class="generated-field mb-4"
                :color="deviceData.password ? 'success' : 'grey'"
                :prepend-inner-icon="deviceData.password ? 'mdi-lock-check' : 'mdi-lock'"
                readonly
                hide-details density="compact"
            ></v-text-field>

            <!-- Поле Дата Годности (тип date для календаря) -->
            <v-text-field
                v-model="deviceData.validUntilDate"
                label="Годен до"
                type="date"
                :min="getTodayDate()"
                variant="outlined"
                color="primary"
                prepend-inner-icon="mdi-calendar-range"
                :disabled="isLoading"
                class="mb-4" density="compact"
            >
                <template v-slot:details>
                    <span class="text-caption text-medium-emphasis">Устройство будет недействительным после этой даты.</span>
                </template>
            </v-text-field>

            <!-- Переключатель Статуса Вкл/Выкл -->
            <v-switch
                v-model="deviceData.isEnabled"
                :label="deviceData.isEnabled ? 'Начальный Статус: ВКЛЮЧЕНО (Активно)' : 'Начальный Статус: ВЫКЛЮЧЕНО (Неактивно)'"
                :color="deviceData.isEnabled ? 'green' : 'red'"
                :prepend-icon="deviceData.isEnabled ? 'mdi-wifi' : 'mdi-wifi-off'"
                :disabled="isLoading"
                inset
                hide-details
                class="mb-6 px-3 py-2 bg-grey-lighten-4 rounded-lg" density="compact"
            ></v-switch>
            
            <!-- Кнопка ГЕНЕРАЦИЯ -->
            <v-btn
                @click="handleGenerate"
                :disabled="isLoading"
                color="yellow-darken-2"
                size="large"
                block
                class="mb-4 text-white font-weight-bold" density="compact"
            >
                <v-icon icon="mdi-refresh" class="mr-2"></v-icon>
                {{ deviceData.name ? 'Сгенерировать НОВЫЙ ID' : 'Сгенерировать ID и Пароль' }}
            </v-btn>

            <!-- Сообщение об ошибке/успехе -->
            <v-alert
                v-if="message"
                :type="isRegistered ? 'success' : message.includes('Сгенерирован') ? 'info' : 'error'"
                variant="tonal"
                class="mb-4"
                closable
                @click:close="message = ''"
            >
                {{ message }}
            </v-alert>

            <!-- Кнопка РЕГИСТРАЦИЯ -->
            <v-btn
                type="submit"
                :loading="isLoading"
                :disabled="isRegistered || !deviceData.name"
                color="blue-darken-2"
                size="large"
                block
                class="text-white font-weight-bold" density="compact"
            >
                <v-icon v-if="isRegistered" icon="mdi-check" class="mr-2"></v-icon>
                {{ isRegistered ? 'УСПЕШНО ЗАРЕГИСТРИРОВАНО' : 'ЗАРЕГИСТРИРОВАТЬ' }}
            </v-btn>

        </v-form>
    </v-card-text>
</v-card>
  
</template>

<script setup lang="ts">
import { ref , reactive } from 'vue';
import { router, usePage , useForm } from '@inertiajs/vue3'
const props = defineProps({
  token: {
    type: String,
    required: true,
    default: () => ''
  },
  // Добавьте другие пропсы, если они есть
});
// Интерфейс для данных устройства
interface DeviceData {
name: string;
password: string;
isEnabled: boolean;
validUntilDate: string;
}

// Утилиты
const generateNumericId = (): string => String(Math.floor(Math.random() * 99999999) + 1).padStart(8, '0');

const getOneYearFromNow = (): string => {
const date = new Date();
date.setFullYear(date.getFullYear() + 1);
return date.toISOString().split('T')[0];
};

const getTodayDate = (): string => new Date().toISOString().split('T')[0];

// --- Состояние приложения (Composition API) ---
const deviceData = useForm<DeviceData>({
name: '',           // ID Устройства (Логин)
password: '',       // Пароль
isEnabled: true,    // Статус: true = Включено
validUntilDate: getOneYearFromNow(), // Дата годности (по умолчанию + 1 год)
});

const isRegistered = ref(false);
const message = ref('');
const isLoading = ref(false);
const isSuccess = ref(false); 
// --- Методы ---

/**

Генерирует новый уникальный ID и пароль.
*/
const handleGenerate = (): void => {
deviceData.name = generateNumericId();
deviceData.password = deviceData.name
isRegistered.value = false;
message.value = 'Сгенерирован новый ID и Пароль. ';
};

/**

Имитация процесса регистрации (отправки данных на сервер).
*/
const handleRegister = (): void => {
if (!deviceData.name || !deviceData.password) {
message.value = ' Сначала сгенерируйте ID и Пароль.';
return;
}

isLoading.value = true;
message.value = 'Отправка данных на сервер...';

// Имитация задержки API-запроса
deviceData.post('/device-create', {
        // Трансформация данных (опционально, если бэкенд ожидает другие имена полей)
        // transform: data => ({
        //     device_id: data.name,
        //     password: data.password,
        //     is_active: data.isEnabled, // Пример переименования
        //     valid_until: data.validUntilDate,
        // }),
        
        // Обработка успеха
        onSuccess: () => {
            // Флеш-сообщение об успехе (если Laravel его возвращает)
            const flashSuccess = 'Устройво создано';
            
            isSuccess.value = true;
            message.value = flashSuccess || `🎉 Устройство ${deviceData.name} успешно зарегистрировано!`;

            // Очистка полей формы, если это форма регистрации
            deviceData.name = '';
            deviceData.password = ''; 
            // ... сброс других полей
        },
        
        // Обработка ошибок валидации (ошибки автоматически попадают в page.props.errors)
        onError: (errors) => {
            isSuccess.value = false;
            // Простая проверка, чтобы отобразить общее сообщение об ошибке
            if (Object.keys(errors).length > 0) {
                message.value = '❌ Ошибка валидации! Проверьте введенные поля.';
            } else {
                 message.value = '❌ Произошла неизвестная ошибка при регистрации.';
            }
            console.error('Validation Errors:', errors);
        },

        // Выполняется всегда в конце (успех или ошибка)
        onFinish: () => {
            isLoading.value = false;
            // Автоматическое скрытие сообщения через 10 секунд
            setTimeout(() => message.value = '', 10000); 
        },
    });

};

const goBack = () => {
    // 1. Использование router.back()
    // Это предпочтительный способ, он имитирует нажатие кнопки "Назад" в браузере.
   router.get('/devices')
    
    // 2. ИЛИ Использование router.get() на конкретный маршрут (если router.back() вызывает проблемы)
    // router.get('/users'); // Замените на фактический URL вашей таблицы
};

</script>

<style scoped>

</style>