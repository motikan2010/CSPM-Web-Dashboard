import { createRouter, createWebHistory} from 'vue-router'

// Store
import { useAlertStore } from '@/stores/alert.store.js';
import { useAuthStore } from '@/stores/auth.store.js';

// Page
import Home from './pages/Home.vue';
import Login from './pages/Login.vue';
import UserRegister from './pages/UserRegister.vue';
import AccountIndex from './pages/account/Index.vue';
import CloudList from '@/pages/cloud/List.vue';
import CloudNew from '@/pages/cloud/New.vue';
import CloudDetail from '@/pages/cloud/Detail.vue';
import CspmResult from '@/pages/cspm/Result.vue';


export const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'index',
            component: Home,
        },
        {
            path: '/account/login',
            name: 'login',
            component: Login,
        },{
            path: '/account/register',
            name: 'register',
            component: UserRegister,
        },{
            path: '/account',
            name: 'account.index',
            component: AccountIndex,
        },{
            path: '/cloud',
            name: 'cloud.list',
            component: CloudList,
        },{
            path: '/cloud/new',
            name: 'cloud.new',
            component: CloudNew,
        },{
            path: '/cloud/cid-:id',
            name: 'cloud.detail',
            component: CloudDetail,
            props: true,
        },{
            path: '/cloud/cid-:id/:execDate',
            name: 'cspm.result',
            component: CspmResult,
            props: true,
        },
    ]
});

router.beforeEach(async (to) => {
    // clear alert on route change
    const alertStore = useAlertStore();
    alertStore.clear();

    // redirect to login page if not logged in and trying to access a restricted page
    const publicPages = ['/account/login', '/account/register'];
    const authRequired = !publicPages.includes(to.path);
    const authStore = useAuthStore();

    if ( authRequired && !authStore.user ) {
        authStore.returnUrl = to.fullPath;
        return '/account/login';
    }
});
