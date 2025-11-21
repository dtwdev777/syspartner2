<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link ,router } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid ,RadioReceiver ,Antenna ,Eclipse , Package } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Клиенты',
        href: dashboard(),
        icon: LayoutGrid,
    },
     {
        title: 'Ип портал ',
        href: '/devices',
        icon: RadioReceiver,
    },

      {
        title: 'Пакеты',
        href: '/packages',
        icon: Package ,
    
    },

     {
        title: 'Каналы',
        href: '/channels',
        icon: Antenna,
    
    },
      {
        title: 'Страны привязка',
        href: '/countries',
        icon: Eclipse,
    
    },
    
    
];

const footerNavItems: NavItem[] = [
   
  
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
              <NavUser />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
          
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
