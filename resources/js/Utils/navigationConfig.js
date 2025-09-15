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
                    name: 'Layanan',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>`,
                    children: [
                        {
                            name: 'Pengajuan Surat',
                            route: 'pengajuan.*',
                            routeLink: 'pengajuan.index',
                        },
                        {
                            name: 'Pengaduan Warga',
                            route: 'pengaduan.*',
                            routeLink: 'pengaduan.index',
                        },
                    ],
                },
            ],
        },
        {
            id: 'profile',
            type: 'profile',
            title: 'Info Desa',
            color: 'bg-gradient-to-r from-emerald-400 to-emerald-500',
            items: [
                {
                    name: 'Profil Desa',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>`,
                    children: [
                        { name: 'Sejarah Desa', route: ['profil.show', 'sejarah'] },
                        { name: 'Visi & Misi', route: ['profil.show', 'visi-misi'] },
                        { name: 'Struktur Organisasi', route: ['profil.show', 'struktur-organisasi'] },
                    ],
                },
            ],
        },
    ];

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
                    name: 'Perangkat Desa',
                    route: 'admin.perangkat-desa.*',
                    routeLink: 'admin.perangkat-desa.index',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>`,
                },
                {
                    name: 'Manajemen Surat',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>`,
                    children: [
                        { name: 'Jenis Surat', route: 'admin.jenis-surat.*', routeLink: 'admin.jenis-surat.index' },
                        { name: 'Pengajuan Aktif', route: 'admin.proses.index', routeLink: 'admin.proses.index' },
                        { name: 'Riwayat Pengajuan', route: 'admin.proses.riwayat', routeLink: 'admin.proses.riwayat' },
                    ],
                },
                {
                    name: 'Manajemen Pengaduan',
                    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>`,
                    children: [
                        {
                            name: 'Pengaduan Aktif',
                            route: 'admin.pengaduan.index',
                        },
                        {
                            name: 'Riwayat Pengaduan',
                            route: 'admin.pengaduan.riwayat',
                        },
                    ],
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