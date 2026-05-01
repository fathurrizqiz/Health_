<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import { SidebarProvider, SidebarTrigger } from '@/components/ui/sidebar';
import Sidebar from '@/components/AppSidebar.vue';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <SidebarProvider>
        <Sidebar /> <!-- Komponen Sidebar kamu -->
        <main class="flex-1 overflow-auto">
            <header class="flex h-16 shrink-0 items-center gap-2 border-b px-4 lg:hidden">
                <!-- Tombol Burger Menu Mobile -->
                <SidebarTrigger class="-ml-1" />
                <Separator orientation="vertical" class="mr-2 h-4" />
                <span class="text-sm font-medium">Dashboard</span>
            </header>
            <slot />
        </main>
    </SidebarProvider>
</template>
