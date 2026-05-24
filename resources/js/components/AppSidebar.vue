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
import { BookCopy, BookLock, BookMarked, BookOpen, CalendarCheck, CalendarCheck2, ChartLine, FileBadge, Folder, GraduationCap, Inbox, LayoutGrid, Library, Settings, ShieldAlert, ShieldCheck, Signature, TriangleAlert, UsersRound } from 'lucide-vue-next';
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
            roles: string | string[]; // <-- allow single or multiple roles
        } | null;
    };
}

const page = usePage();
const persetujuanCount = computed(() => page.props.notifications?.persetujuan_count || 0);
const jadwalCount = computed(() => page.props.notifications?.jadwal_count || 0);
const countInboxData = computed(() => page.props.notifications?.InboxCount || 0);
const isImpersonating = computed(() => page.props.auth?.is_impersonating || false);
const { props } = usePage<any>() as { props: MyPageProps };
const rawRole = props.auth.user?.roles || []; 
const roles = Array.isArray(rawRole) ? rawRole : [rawRole];

const mainNavItems = computed(() => [
    // ...(isImpersonating.value ? [{
    //     title: 'Back to admin',
    //     href: '/super-admin/stop-impersonate', 
    //     method: 'post', // Kita akan menangani ini di NavMain atau pakai custom click
    //     as: 'button',
    //     icon: TriangleAlert,
    //     roles: ['super-admin', 'admin_diklat', 'user'], // Berikan akses ke semua role saat menyamar agar muncul
    // }] : []),
    {
        title: 'User Management',
        href: '/super-admin/home', 
        icon: ShieldAlert,
        roles: ['super-admin'],
    },
   
   
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
        title: 'Sertifikat Internal',
        href: '/DiklatInternal/user',
        icon: FileBadge,
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
        href: '/Settings',
        icon: Settings,
        roles: ['admin_diklat'],
    },
    {
        title: 'Indbox',
        href: '/HLC/Home/user',
        icon: Inbox,
        badge: countInboxData.value > 0 ? countInboxData.value : null,
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
