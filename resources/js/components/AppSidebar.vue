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
import { BookCopy, BookLock, BookMarked, BookOpen, CalendarCheck, CalendarCheck2, ChartLine, Folder, GraduationCap, LayoutGrid, Library, Settings, Signature } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { computed } from 'vue';


// const filteredNavItems = computed(() => {
//     return mainNavItems.filter(item => {
//         if (!item.roles) return true; // menu umum
//         return item.roles.some(role => roles.includes(role));
//     });
// });

interface MyPageProps {
    auth: {
        user: {
            id: number;
            name: string;
            role: string | string[]; // <-- allow single or multiple roles
        } | null;
    };
}

const page = usePage();
const persetujuanCount = computed(() => page.props.notifications?.persetujuan_count || 0);
const jadwalCount = computed(() => page.props.notifications?.jadwal_count || 0);
const { props } = usePage<any>() as { props: MyPageProps };
const rawRole = props.auth.user?.role || [];
const roles = Array.isArray(rawRole) ? rawRole : [rawRole];

const mainNavItems = computed(() => [
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
        badge: persetujuanCount.value > 0 ? persetujuanCount.value : null,
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
        title: 'Jadwal Diklat',
        href: '/JadwalDiklat/Internal',
        icon: CalendarCheck,
        // roles: ['admin_diklat'],
        badge: jadwalCount.value > 0 ? jadwalCount.value : null,
    },
    {
        title: 'Evaluasi',
        href: '/Diklat/Evaluasi',
        icon: ChartLine,
        roles: ['admin_diklat'],
    },
    {
        title: 'Laporan',
        href: '/Laporan/Diklat',
        icon: BookLock,
        roles: ['admin_diklat'],
    },
    {
        title: 'Master Data',
        href: '/MasterData/home',
        icon: BookCopy,
        roles: ['admin_diklat'],
    },
    {
        title: 'Whattsapp Settings',
        href: '/Template/WA',
        icon: Settings,
        roles: ['admin_diklat'],
    },
    
]);
const filteredNavItems = computed(() => {
    return mainNavItems.value.filter(item => {
        if (!item.roles) return true;
        return item.roles.some(role => roles.includes(role));
    });
});

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
            <NavMain :items="filteredNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
