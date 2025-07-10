import AccountIndex from '../pages/accounts/Index.vue';

export default [
    {
        path: '/accounts',
        name: 'accounts.index',
        component: AccountIndex,
        meta: {
            title: 'Accounts',
            requiresAuth: true,
            permission: 'view_accounts'
        }
    }
]; 