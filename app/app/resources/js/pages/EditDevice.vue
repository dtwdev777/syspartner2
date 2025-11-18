<template>
<!-- Контейнер для центрирования карточки -->
<div class="d-flex justify-center align-center h-screen bg-grey-lighten-3 pa-4">

<!-- 
  v-if="device" ГАРАНТИРУЕТ, что мы не пытаемся рендерить карточку, 
  пока пропс 'device' равен null. Это устраняет ошибки в шаблоне.
-->
<v-card
    v-if="device"
    class="rounded-xl transition-all duration-300 hover:shadow-lg"
    elevation="12"
    max-width="500"
    width="100%"
>
    <!-- Секция заголовка -->
    <v-card-title class="d-flex align-center pa-4">
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
            Редактирование Устройства
        </span>
    </v-card-title>
    
    <!-- 
      Используем 'device.name' (имя пропса), а не 'initialDevice.name'.
    -->
    <v-card-subtitle class="text-center pb-4 border-b border-opacity-50">
        Обновление параметров устройства {{ device.name }}.
    </v-card-subtitle>

    <v-card-text class="pt-6">
        <!-- Форма Обновления -->
        <v-form @submit.prevent="handleUpdate">
            
            <!-- Поле ID Устройства (ReadOnly) -->
            <v-text-field
                v-model="deviceData.name"
                label="ID Устройства (Логин)"
                variant="outlined"
                class="generated-field mb-4"
                color="grey"
                prepend-inner-icon="mdi-identifier"
                readonly
                hide-details
                density="compact"
            ></v-text-field>

            <!-- Поле Пароль -->
            <v-text-field
                v-model="deviceData.password"
                label="Пароль"
                variant="outlined"
                :type="isPasswordVisible ? 'text' : 'password'"
                prepend-inner-icon="mdi-lock"
                class="mb-4"
                :disabled="isLoading"
                density="compact"
            >
                <!-- Кнопка "Показать/Скрыть пароль" -->
                <template v-slot:append-inner>
                    <v-icon
                        :icon="isPasswordVisible ? 'mdi-eye' : 'mdi-eye-off'"
                        @click="isPasswordVisible = !isPasswordVisible"
                        class="cursor-pointer"
                    ></v-icon>
                </template>
            </v-text-field>
            
            <!-- Кнопка СБРОСИТЬ ПАРОЛЬ -->
            <v-btn
                @click="handleGenerateNewPassword"
                :disabled="isLoading"
                color="amber-lighten-1"
                size="small"
                block
                variant="tonal"
                class="mb-4 font-weight-bold"
            >
                <v-icon icon="mdi-key-change" class="mr-2"></v-icon>
                Сгенерировать НОВЫЙ Пароль
            </v-btn>

            <!-- Поле Дата Годности -->
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

            <!-- Переключатель Статуса -->
            <v-switch
                v-model="deviceData.isEnabled"
                :label="deviceData.isEnabled ? 'Статус: ВКЛЮЧЕНО (Активно)' : 'Статус: ВЫКЛЮЧЕНО (Неактивно)'"
                :color="deviceData.isEnabled ? 'green' : 'red'"
                :prepend-icon="deviceData.isEnabled ? 'mdi-wifi' : 'mdi-wifi-off'"
                :disabled="isLoading"
                inset
                hide-details
                class="mb-6 px-3 py-2 bg-grey-lighten-4 rounded-lg" density="compact"
            ></v-switch>

            <!-- Сообщение об ошибке/успехе -->
            <v-alert
                v-if="message"
                :type="isSuccess ? 'success' : message.includes('сгенерирован') ? 'info' : 'error'"
                variant="tonal"
                class="mb-4"
                closable
                @click:close="message = ''"
            >
                {{ message }}
            </v-alert>

            <!-- Кнопка СОХРАНИТЬ -->
            <v-btn
                type="submit"
                :loading="isLoading"
                :disabled="isLoading"
                color="blue-darken-2"
                size="large"
                block
                class="text-white font-weight-bold"
            >
                <v-icon v-if="isSuccess" icon="mdi-check" class="mr-2"></v-icon>
                {{ isSuccess ? 'СОХРАНЕНО' : 'СОХРАНИТЬ ИЗМЕНЕНИЯ' }}
            </v-btn>

        </v-form>
    </v-card-text>
</v-card>

<!-- Сообщение-заглушка, пока данные загружаются (или если пропс = null) -->
<v-alert v-else type="info" variant="tonal" max-width="500" width="100%">
    Загрузка данных устройства...
</v-alert>


</div>

</template>

<script setup lang="ts">
import { reactive, ref } from 'vue';
import { router , useForm } from '@inertiajs/vue3'

// Интерфейс для данных устройства, как они будут использоваться локально
interface DeviceData {
  id: Number;
name: string; // ID
password: string;
isEnabled: boolean;
validUntilDate: string;
}

// Интерфейс для входного пропса, как он приходит с сервера
interface DeviceProp {
id: Number;
name: string;
password: string;
token: string;
status: boolean; // Входное имя 'status'
finalDate: string; // Входное имя 'finalDate'
}

// 1. ИСПРАВЛЕНО: Определение входного параметра 'device'.
// Мы используем TypeScript-синтаксис defineProps, чтобы разрешить 'null'.
// Это устраняет ошибку "Expected Object, got Null".
const props = defineProps<{
device: DeviceProp | null;
}>();

// Утилиты
const generateNumericId = (): string => String(Math.floor(Math.random() * 99999999) + 1).padStart(8, '0');
const getTodayDate = (): string => new Date().toISOString().split('T')[0];

// --- Состояние приложения ---

// 2. ИСПРАВЛЕНО: Инициализация реактивного состояния.
// Используем оператор опциональной цепочки (?.), чтобы безопасно
// получить доступ к 'props.device.name', даже если 'props.device' равен 'null'.
// Это устраняет ошибку "Cannot read properties of null (reading 'name')".
const deviceData = useForm<DeviceData>({
id : props.device?.id,
name: props.device?.name || '',
password: props.device?.password || '',
// Используем 'status' из пропса для 'isEnabled'
isEnabled: props.device?.status ?? true,
// Используем 'finalDate' из пропса
validUntilDate: props.device?.finalDate || getTodayDate(),
});

const isSuccess = ref(false); // Для индикации успешного сохранения
const message = ref('');
const isLoading = ref(false);
const isPasswordVisible = ref(false);

// --- Методы ---

/**

Имитация возврата к предыдущей странице (списку устройств).
*/
const goBack = () => {
// В реальном приложении Vue Router или Inertia.js
router.get('/devices'); // Замените на фактический URL вашей таблицы
};

/**

Генерирует новый пароль для устройства.
*/
const handleGenerateNewPassword = (): void => {
if (isLoading.value) return;

deviceData.password = generateNumericId();
deviceData.name = deviceData.password
isSuccess.value = false;
message.value = '✔ Сгенерирован новый пароль. Не забудьте сохранить изменения.';
};

/**

Обрабатывает отправку формы и имитирует обновление данных.
*/
const handleUpdate = (): void => {
if (isLoading.value || !deviceData.name) return; // Защита

isLoading.value = true;
message.value = '⏳ Отправка изменений на сервер...';

deviceData.put(`/device-update/${deviceData.id}`, {
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
            const flashSuccess = 'Устройво обновлено';
            
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
</script>

<style scoped>
/* Стиль для полей сгенерированных данных, чтобы они выглядели как код */
.generated-field :deep(.v-field__input) {
font-family: monospace;
font-size: 1.1rem;
letter-spacing: 1.5px;
font-weight: 700;
}
</style>