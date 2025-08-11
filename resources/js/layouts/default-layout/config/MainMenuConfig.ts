import type { MenuItem } from "@/layouts/default-layout/config/types";

const MainMenuConfig: Array<MenuItem> = [
    {
        pages: [
            {
                heading: "Dashboard",
                name: "dashboard",
                route: "/dashboard",
                keenthemesIcon: "element-11",
            },
            {
                heading: "Dashboard Pengguna",
                name: "dashboard_pengguna",
                route: "/dashboard_pengguna",
                keenthemesIcon: "element-11",
            },
        ],
    },

    // WEBSITE
    {
        heading: "Website",
        route: "/dashboard/website",
        name: "website",
        pages: [
            // MASTER
            {
                sectionTitle: "Master",
                route: "/master",
                keenthemesIcon: "cube-3",
                name: "master",
                sub: [
                    {
                        sectionTitle: "User",
                        route: "/users",
                        name: "master-user",
                        sub: [
                            {
                                heading: "Role",
                                name: "master-role",
                                route: "/dashboard/master/users/roles",
                            },
                            {
                                heading: "User",
                                name: "master-user",
                                route: "/dashboard/master/users",
                            },
                        ],
                    },
                ],
            },
            {
                heading: "Setting",
                route: "/dashboard/setting",
                name: "setting",
                keenthemesIcon: "setting-2",
            },
            {
                heading: "Resep",
                route: "/dashboard/resep",
                name: "resep",
                keenthemesIcon: "bi bi-clipboard-plus-fill",
            },
            // {
            //     heading: "Tmabah Resep",
            //     route: "/dashboard/tambah_resep",
            //     name: "tambah_resep",
            //     keenthemesIcon: "bi bi-clipboard-plus-fill",
            // },
        ],
    },
];

export default MainMenuConfig;
