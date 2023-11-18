import Dashboard from './components/Dashboard.vue'

export default[
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard
    },
    {
        path: '/admin/appointments',
        name: 'admin.appointments',
        component: () => import('./pages/appointments/ListAppointments.vue')
    },
    {
        path: '/admin/users',
        name: 'admin.users',
        component: () => import('./pages/users/ListUsers.vue')
    },
    {
        path: '/admin/settings',
        name: 'admin.settings',
        component: () => import('./pages/settings/UpdateSetting.vue')
    },
    {
        path: '/admin/profile',
        name: 'admin.profile',
        component: () => import('./pages/profile/UpdateProfile.vue')
    }
]