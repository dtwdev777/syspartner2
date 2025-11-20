<template>
    <v-card 
        class="max-w-xl mx-auto mt-10"
        elevation="10"
        rounded="lg"
    >
        <v-card-title class="text-h5 font-weight-bold py-4 text-black">
            <div class="text-center w-full">Загрузка Каналов</div>
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
                    v-model="form.link"
                    label="URL-Ссылка"
                    placeholder="https://example.com"
                    type="url"
                    :error-messages="form.errors.link ? [form.errors.link] : []"
                    variant="outlined"
                    density="compact"
                    hide-details="auto"
                ></v-text-field>


               <v-file-input
    label="Загрузить Файл"
    v-model="form.file"
    :error-messages="form.errors.file ? [form.errors.file] : []"
    variant="outlined"
    density="compact"
    prepend-icon="mdi-paperclip"
    :clearable="!form.processing"
    hide-details="auto"
    accept="*"
/>
                <p v-if="form.file && !form.errors.file" class="text-sm text-gray-500 mt-2">
                    Выбран файл: {{ form.file.name }}
                </p>

                <v-btn
                    type="submit"
                    color="primary"
                    size="large"
                    block
                    class="mt-8"
                    :loading="form.processing"
                    :disabled="form.processing"
                    :ripple="!form.processing"
                >
                    Сохранить Ресурс
                </v-btn>
            </form>
        </v-card-text>
    </v-card>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useForm ,router } from '@inertiajs/vue3';

type CountriesProp = Record<number, string> | Country[];

interface Country {
    id: number;
    name: string;
}

const props = defineProps<{
    countries: CountriesProp;
}>();

// --- useForm ---
const form = useForm({
   
    link: '',
   
    file: null as File | null,
});

// --- Computed для стран ---
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

// --- Методы ---
const handleFileInput = (file: File | null) => {
    form.file = file;
};


const submitForm = () => {
    form.post('/channel-create', {
        forceFormData: true,
        onSuccess: () => {
            form.reset(); // очищает все поля
            console.log('Resource successfully uploaded and form cleared!');
        },
        onError: (e) => {
            console.error('Submission failed:', e);
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
