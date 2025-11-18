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
import { BookOpen, Folder, LayoutGrid ,RadioReceiver } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Playlist',
        href: dashboard(),
        icon: LayoutGrid,
    },
     {
        title: 'Devices',
        href: '/devices',
        icon: RadioReceiver,
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
