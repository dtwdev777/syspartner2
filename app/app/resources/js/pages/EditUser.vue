<script setup>
import { ref, onMounted, computed, defineProps , watch } from 'vue';
import { useForm ,router } from '@inertiajs/vue3';
// useForm и router уже импортированы
const domain = "http://"+window.location.hostname+"/playlist";
const BASE_URL = domain;
// ------------------------------------
// 0. Props: Получение данных пользователя
// ------------------------------------
const props = defineProps({
    user: {
        type: Object,
        required: true,
        default: () => ({ 
            name: '', 
            token: '', 
            isActive: true, 
            limitCount: 100, 
            links: [''], 
            // Предполагаем, что сервер может отправить дату как строку '2025-11-08'
            finalDate: new Date().toISOString().slice(0, 10), 
        })
    }
});


// ------------------------------------
// 1. Состояние формы: Инициализация данными пользователя
// ------------------------------------
const formData = useForm({
    // ⭐️ Инициализируем данные из props.user
    name: props.user.name || '', 
    token: props.user.token || '',
    isActive: props.user.isActive ?? true, 
    limitCount: props.user.limitCount ?? 100,
    
    // Инициализируем поля даты для режимов
    dateRangeType: 'День', // В режиме редактирования обычно не используется, но оставляем
    customDate: props.user.finalDate || '', 
    
    links: props.user.links && props.user.links.length > 0 ? props.user.links : [''], // Проверка на пустой массив
});

const formSubmitted = ref(false);
// Если дата установлена, по умолчанию используем DatePicker
const useDatePicker = ref(!!props.user.finalDate); 
const validationErrors = ref({});
// ... (определение dateRangeOptions и rules - остается прежним)
const dateRangeOptions = ref(['День', 'Неделя', 'Месяц']);

const rules = {
  required: (value) => !!value || 'Это поле обязательно для заполнения.',
  integer: (value) => Number.isInteger(Number(value)) || 'Должно быть целое число.',
  positive: (value) => value >= 0 || 'Не может быть отрицательным.',
  optionalUrl: (value) => {
    if (!value) return true; 
    return /^(ftp|http|https):\/\/[^ "]+$/.test(value) || 'Некорректный формат URL.';
  },
};

// ------------------------------------
// 2. Логика генерации токена (остается прежней)
// ------------------------------------
const generateRandomToken = () => {
    const part1 = Math.random().toString(36).substring(2, 10);
    const part2 = Date.now().toString(36);
    formData.token = `${part1}${part2}`.toUpperCase();
};

// ------------------------------------
// 3. Вычисляемое свойство (остается прежним)
// ------------------------------------
const calculatedDate = computed(() => {
    if (useDatePicker.value) {
        return formData.customDate;
    }
    // ... (логика вычисления интервала)
    const today = new Date();
    const type = formData.dateRangeType;
    let targetDate = new Date(today);

    if (type === 'Неделя') {
        targetDate.setDate(today.getDate() + 7);
    } else if (type === 'Месяц') {
        targetDate.setMonth(today.getMonth() + 1);
    } 

    const year = targetDate.getFullYear();
    const month = String(targetDate.getMonth() + 1).padStart(2, '0');
    const day = String(targetDate.getDate()).padStart(2, '0');
    
    return `${year}-${month}-${day}`;
});

const syncLinksWithToken = (newToken) => {
	if (!newToken) return;

	const newQueryParam = `token=${newToken}`;
	
	// Регулярное выражение для поиска параметра `token=...`.
	// Оно захватывает разделитель (либо `?`, либо `&`) в группе 1, чтобы его сохранить.
	const tokenRegex = /([?&])token=[A-Z0-9]+/i; 

	for (let i = 0; i < formData.links.length; i++) {
		let currentLink = formData.links[i];
		
		// Пропускаем пустые ссылки или ссылки, не начинающиеся с нашего BASE_URL
		if (!currentLink || !currentLink.startsWith(BASE_URL)) continue;

		if (tokenRegex.test(currentLink)) {
			// 1. Токен найден: Заменяем только его значение, сохраняя разделитель ($1) и другие параметры.
			// Это решает проблему, когда перезаписывалась вся строка.
			formData.links[i] = currentLink.replace(tokenRegex, `$1${newQueryParam}`);
		} else if (i === 0) {
			// 2. Токен не найден, но это ПЕРВАЯ ссылка: Добавляем токен, сохраняя другие параметры.
			// Определяем разделитель: `?` если нет других параметров, `&` если есть.
			const separator = currentLink.includes('?') ? '&' : '?';
			formData.links[i] = `${currentLink}${separator}${newQueryParam}`;
		}
		// 3. Если токен не найден и это НЕ ПЕРВАЯ ссылка, мы ее не трогаем (предполагаем ручной ввод).
	}
};
// ------------------------------------
// 4. Хук жизненного цикла
// ------------------------------------
onMounted(() => {
    // В режиме редактирования не генерируем токен, если он уже есть
    if (!props.user.token) {
        generateRandomToken();
    }
     
});

watch(() => formData.token, (newToken) => {
    syncLinksWithToken(newToken);
});


// ------------------------------------
// 5. Метод отправки формы: PUT/PATCH
// ------------------------------------
const submitForm = () => {
    const finalDate = useDatePicker.value ? formData.customDate : calculatedDate.value;
    
    // ⭐️ ИСПОЛЬЗУЕМ form.transform() И form.put()
    formData.transform((data) => ({
        ...data,
        finalDate: finalDate, 
        
        // Удаляем поля, которые не нужны серверу
        dateRangeType: undefined,
        customDate: undefined,
    }))
    // ⭐️ Используем PUT для обновления с динамическим ID
    .put(`/client/${props.user.id}`, {
        onSuccess: () => {
            formSubmitted.value = true;
            console.log('Пользователь успешно обновлен!');
        },
        onError: (errors) => {
            validationErrors.value = errors;
            console.error('Ошибка при обновлении:', errors);
        },
    });
};

// ------------------------------------
// 6. Методы для управления ссылками (остаются прежними)
// ------------------------------------
const addLink = () => {
	const currentToken = formData.token;
	const newLink = currentToken ? `${BASE_URL}?token=${currentToken}` : BASE_URL;
	
    // Добавляем новую ссылку в конец массива
	formData.links.push(newLink);
};

const removeLink = (index) => {
    formData.links.splice(index, 1);
};

const goBack = () => {
    // 1. Использование router.back()
    // Это предпочтительный способ, он имитирует нажатие кнопки "Назад" в браузере.
   router.get('/')
    
    // 2. ИЛИ Использование router.get() на конкретный маршрут (если router.back() вызывает проблемы)
    // router.get('/users'); // Замените на фактический URL вашей таблицы
};

</script>

<template>
<v-container>
   <v-alert
        v-if="formSubmitted"
        type="success"
        class="mt-4"
        variant="tonal"
    >
        Данные обновлены: 
    </v-alert>
  <v-alert
        v-if="Object.keys(validationErrors).length > 0"
        type="error"
        title="Обнаружены ошибки валидации"
        class="mb-4"
        variant="tonal"
    >
        Пожалуйста, исправьте следующие поля:
        <ul class="pt-2">
            <li v-for="(error, key) in validationErrors" :key="key">
                <strong>{{ key }}:</strong> {{ error }}
            </li>
        </ul>
    </v-alert>
    <v-form @submit.prevent="submitForm">
      <v-card class="mx-auto pa-5">
        <v-card-title class="text-h5 mb-4 ">
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
          Редактирование клиента: id {{ props.user.id }}
          
        </v-card-title>
        
        <v-text-field
          v-model="formData.name"
          :rules="[rules.required]"
          label="Имя пользователя"
          variant="outlined"
          prepend-inner-icon="mdi-account"
          required density="compact"
        ></v-text-field>

<v-text-field
  v-model="formData.token"
  :rules="[rules.required]"
  label="Токен"
  variant="outlined"
  prepend-inner-icon="mdi-key"
  required density="compact"
>
  <template #append-inner>
    <v-btn
      icon
      variant="text"
      size="small"
      @click="generateRandomToken"
      title="Сгенерировать новый токен"
    >
      <v-icon>mdi-cached</v-icon>
    </v-btn>
  </template>
</v-text-field>

<v-text-field
          v-model.number="formData.limitCount"
          :rules="[rules.required, rules.integer, rules.positive]"
          label="Количество лимитов"
          variant="outlined"
          prepend-inner-icon="mdi-counter"
          type="number"
          min="0"
          required
          class="mb-4"
          density="compact"
        ></v-text-field>

        <v-switch
          v-model="formData.isActive"
          :label="formData.isActive ? 'Активен' : 'Отключен'"
          color="primary"
          inset
          class="mb-4"
          density="compact"
        ></v-switch>

      <v-switch
          v-model="useDatePicker"
          :label="useDatePicker ? 'Режим: Выбор конкретной даты' : 'Режим: Выбор интервала'"
          color="secondary"
          inset
          class="mb-4"
          density="compact"
        ></v-switch>

        <div v-if="!useDatePicker">
          <v-select
            v-model="formData.dateRangeType"
            :items="dateRangeOptions"
            label="Тип интервала (от текущей даты)"
            variant="outlined"
            prepend-inner-icon="mdi-calendar-range"
            required
            class="mb-4"
            density="compact"
          ></v-select>

          <v-text-field
            :model-value="calculatedDate"
            label="Итоговая дата (вычислено)"
            variant="outlined"
            prepend-inner-icon="mdi-calendar-check"
            readonly
            hide-details
            class="mb-4"
            density="compact"
          ></v-text-field>
        </div>

        <div v-else>
          <v-text-field
            v-model="formData.customDate"
            :rules="[rules.required]"
            label="Конкретная дата начала"
            variant="outlined"
            prepend-inner-icon="mdi-calendar"
            type="date"
            required
            class="mb-4"
            density="compact"
          ></v-text-field>
        </div>

    <div v-for="(link, index) in formData.links" :key="index" class="d-flex align-center mb-4">
          <v-text-field
            v-model="formData.links[index]"
            label="Ссылка (URL)"
            variant="outlined"
            prepend-inner-icon="mdi-link"
            :rules="[rules.optionalUrl]"
            hide-details
            class="mr-2"
            density="compact"
          ></v-text-field>

          <v-btn
            v-if="formData.links.length > 1"
            icon
            color="error"
            variant="tonal"
            size="small"
            @click="removeLink(index)"
            class="mr-2"
            density="compact"
          >
            <v-icon>mdi-minus</v-icon>
          </v-btn>

          <v-btn
            v-if="index === formData.links.length - 1"
            icon
            color="success"
            size="small"
            @click="addLink"
            density="compact"
          >
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </div>
        
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn type="submit" density="compact" color="primary" size="small">
                Обновить пользователя
            </v-btn>
        </v-card-actions>
      </v-card>
    </v-form>

   
</v-container>
</template>

<style scoped>
.v-container {
    max-width: 650px;
}
</style>