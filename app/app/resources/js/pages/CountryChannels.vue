<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <v-card 
        class="max-w-xl mx-auto mt-10"
        elevation="10"
        rounded="lg"
    >
       <v-alert
        v-if="flashMessage"
        type="success"
        title="Успех"
        icon="mdi-check-circle"
        closable
        class="mx-auto mt-5 mb-4 elevation-2"
        max-width="900"
        @click:close="flashMessage = null"
    >
        {{ flashMessage }}
    </v-alert>
        <v-card-title class="text-h5 font-weight-bold py-4 text-black">
            <div class="text-center w-full">Привязка  Каналов по Стране </div>
        </v-card-title>
        
        <v-card-text class="py-6 px-4">
            <form @submit.prevent="submitForm" class="space-y-6">
                
               
                  <!-- Множественный выбор стран -->
                <v-autocomplete
                    v-model="form.channels"
                    :items="transformedChannels"
                    item-title="title"
                    item-value="id"
                    label="Выберите Каналы "
                    multiple
                    chips
                    clearable
                    variant="outlined"
                    density="compact"
                    :error-messages="form.errors.countries ? [form.errors.countries] : []"
                    hide-details="auto"
                    hint="Найти"
                ></v-autocomplete>

             

                <!-- Множественный выбор стран -->
                <v-autocomplete
                    v-model="form.countries"
                    :items="transformedCountries"
                    item-title="name"
                    item-value="id"
                    label="Выберите Страны Доступности"
                    multiple
                    chips
                    clearable
                    variant="outlined"
                    density="compact"
                    :error-messages="form.errors.countries ? [form.errors.countries] : []"
                    hide-details="auto"
                    hint="Найти"
                ></v-autocomplete>




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
                    Привязать
                </v-btn>
            </form>
        </v-card-text>
    </v-card>

    <v-card class="max-w-6xl mx-auto mt-10" elevation="6" rounded="lg">
    <v-card-title class="text-h5 font-weight-bold py-4 text-black">
      <div class="text-center w-full">Каналы и Страны Доступности</div>
    </v-card-title>

    <v-card-text>
       <v-text-field
        v-model="search"
        append-inner-icon="mdi-magnify"
        label="Поиск"
        single-line
        hide-details
        variant="outlined"
        density="compact"
        class="mb-3 search-bg"
      ></v-text-field>

      <v-dialog v-model="showModal" max-width="600px">
  <v-card>
    <v-card-title>
      Страна для удаления: {{ selectedItem?.title || '...' }}
    </v-card-title>
    <v-card-text>
      <div class="flex flex-wrap gap-2">
        <v-chip
          v-for="country in selectedItem?.countries || []"
          :key="country.id"
          color="primary"
          size="small"
          variant="outlined"
          closable
          @click:close="detachCountry(selectedItem.id, country.id)"
        >
          {{ country.name }}
        </v-chip>
      </div>
    </v-card-text>
    <v-card-actions>
      <v-spacer />
      <v-btn text @click="showModal = false">Закрыть</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>

      <v-data-table
        :headers="headers"
        :items="countries_list"
        v-model:selected="selectedItem"
        item-value="id"
        density="compact"
        hover
        class="elevation-1"
         :search="search"
        
        
       
      >
       <template  v-slot:item.countries="{ item }">
       
  <v-btn  @click="openModal(item)">
    <v-icon size="small">mdi-delete</v-icon>
  </v-btn>
</template>
     
      </v-data-table>
    </v-card-text>
  </v-card>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed ,ref  , onMounted} from 'vue';
import { useForm , router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

interface FlashProps {
    success: string | null;
    error: string | null;
    // Добавьте другие типы сообщений, если используете (e.g., warning: string | null)
}

interface Country {
    id: number;
    name: string;
}
interface Channel {
    id: number;
    title: string;
}

interface Channel_List {
  id: number;
  name: string;
  title: string;
  link: string;
  countries: Country[];
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const props = defineProps<{
    countries: Country;
    channels : Channel,
    countries_list : Channel_List ,
    flash: {
        type: Object, // PropType<FlashProps>
        required: false,
        default: () => ({ success: null, error: null })
    },
}>();

const flashMessage = ref(props.flash.success);

const headers = [
  { title: 'ID', key: 'id' },
  { title: 'Название', key: 'title' },

  { title: 'Страны', key: 'countries' },
  { title: 'link', key: 'link' },
  
];

const density = ref('compact');

//modal
const showModal = ref(false);
const selectedItem = ref(null);

const openModal = (item ) => {
  console.log(item)
  if (item !== null && item !== undefined) {
    selectedItem.value = item;
    showModal.value = true;
  }
};

// --- useForm ---
const form = useForm({
   
    link: '',
    countries: [] as number[],
    channels : [] as number[]
  
});
const search = ref('')

// --- Computed для стран ---
const transformedCountries = computed<Country[]>(() => {
    const data = props.countries;
    if (data && typeof data === 'object' && !Array.isArray(data)) {
        return Object.entries(data).map(([id, title]) => ({
            id: Number(id),
            title: String(title)
        }));
    }
    return Array.isArray(data) ? data : [];
});

const transformedChannels = computed<Country[]>(() => {
    const data = props.channels;
  
    if (data && typeof data === 'object' && !Array.isArray(data)) {
        return Object.entries(data).map(([id, name]) => ({
          
            id: Number(id),
            name: String(name)
        }));
    }
    return Array.isArray(data) ? data : [];
});



const submitForm = () => {
    form.post('/countries', {
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
const detachCountry = (channelId: number, countryId: number) => {
  if (confirm('Отвязать страну от канала?')) {
    router.delete(`/channel-country/${channelId}/${countryId}`, {
      onSuccess: () => {
        console.log('Страна отвязана');
      },
      onError: (e) => {
        console.error('Ошибка при отвязке:', e);
      }
    });
  }};

  onMounted(() => {
    // Устанавливаем таймаут только если сообщение существует
    if (flashMessage.value) {
        setTimeout(() => {
            flashMessage.value = null; // Скрываем сообщение
        }, 5000); // 5 секунд
    }
});

</script>

<style scoped>
.max-w-xl {
    max-width: 36rem;
}
.v-data-table .v-data-table__tr {
  cursor: pointer;
}
</style>
