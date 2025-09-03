// Navigation configuration
export const getNavigationSections = (user) => {
    const sections = [
        {
            id: 'main',
            type: 'main',
            title: 'Menu Utama',
            color: 'bg-gradient-to-r from-blue-400 to-blue-500',
            items: [
                {
                    name: 'Dashboard',
                    route: 'dashboard',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>`,
                },
                {
                    name: 'Pengajuan Surat',
                    route: 'pengajuan.*',
                    routeLink: 'pengajuan.index',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>`,
                },
            ],
        },
        {
            id: 'profile',
            type: 'profile',
            title: 'Profil Desa',
            color: 'bg-gradient-to-r from-emerald-400 to-emerald-500',
            items: [
                {
                    name: 'Sejarah Desa',
                    route: ['profil.show', 'sejarah'],
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>`,
                },
                {
                    name: 'Visi & Misi',
                    route: ['profil.show', 'visi-misi'],
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>`,
                },
                {
                    name: 'Struktur Organisasi',
                    route: ['profil.show', 'struktur-organisasi'],
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>`,
                },
            ],
        },
    ];

    // Add admin section if user is admin
    if (user?.role === 'admin') {
        sections.push({
            id: 'admin',
            type: 'main',
            title: 'Menu Admin',
            color: 'bg-gradient-to-r from-orange-400 to-red-500 animate-pulse',
            items: [
                {
                    name: 'Profil Desa',
                    route: 'admin.profil-desa.*',
                    routeLink: 'admin.profil-desa.index',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>`,
                },
                {
                    name: 'Manajemen Surat',
                    route: 'admin.jenis-surat.*',
                    routeLink: 'admin.jenis-surat.index',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>`,
                },
                {
                    name: 'Pengajuan Aktif',
                    route: 'admin.proses.index',
                    routeLink: 'admin.proses.index',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>`,
                },
                {
                    name: 'Riwayat Pengajuan',
                    route: 'admin.proses.riwayat',
                    routeLink: 'admin.proses.riwayat',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>`,
                },
                {
                    name: 'Kelola Pengguna',
                    route: 'admin.users.*',
                    routeLink: 'admin.users.index',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>`,
                },
            ],
        });
    }

    return sections;
};