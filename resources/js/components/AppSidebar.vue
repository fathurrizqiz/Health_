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
import { Link, usePage } from '@inertiajs/vue3';
import { BookCopy, BookMarked, BookOpen, ChartLine, Folder, GraduationCap, LayoutGrid, Library, Signature } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';


interface MyPageProps {
    auth: {
        user: {
            id: number;
            name: string;
            role: string | string[]; // <-- allow single or multiple roles
        } | null;
    };
}
const { props } = usePage<any>() as { props: MyPageProps };
const rawRole = props.auth.user?.role || [];
const roles = Array.isArray(rawRole) ? rawRole : [rawRole];

const mainNavItems:  (NavItem & { roles?: string[] })[] = [
    {
        title: 'Dashboard Diklat',
        href: dashboard(),
        icon: LayoutGrid,
        roles: ['admin_diklat'],
    },
    {
        title: 'Diklat',
        href: '/Diklat',
        icon: BookMarked,
    },
    {
        title: 'Diklat Internal',
        href: '/DiklatInternal/user',
        icon: BookMarked,
    },
    {
        title: 'Persetujuan',
        href: '/Approve/Diklat',
        icon: Signature,
        roles: ['admin_diklat'],
    },
    {
        title: 'Library Materi',
        href: '/Materi',
        icon: Library,
    },
    {
        title: 'Rencana Diklat',
        href: '/RencanaDiklat/RPT/PF',
        icon: GraduationCap,
        roles: ['admin_diklat'],
    },
    {
        title: 'Evaluasi',
        href: '/Diklat/Evaluasi',
        icon: ChartLine,
        roles: ['admin_diklat'],
    },
    {
        title: 'Master Data',
        href: '/MasterData/home',
        icon: BookCopy,
        roles: ['admin_diklat'],
    },
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
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
