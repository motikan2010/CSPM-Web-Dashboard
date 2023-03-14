import { defineStore } from 'pinia';

import { router } from '@/router';
import { useAlertStore } from '@/stores/alert.store.js';

import { getHttp } from '@/commons/utils';

const http = getHttp();

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user')),
        returnUrl: null
    }),
    actions: {
        async login(email, password) {
            http.get('/sanctum/csrf-cookie').then((res) => {
                http.post('/api/login', {email: email, password: password}).then((res) => {
                    this.user = res.data.user;
                    localStorage.setItem('user', JSON.stringify(this.user));
                    router.push(this.returnUrl || '/');
                }).catch(error => {
                    const alertStore = useAlertStore();
                    alertStore.error(error);
                });
            });
        },
        logout() {
            http.get('/sanctum/csrf-cookie').then((res) => {
                http.post('/api/logout').then((res) => {
                    this.user = null;
                    localStorage.removeItem('user');
                })
            });
        },
        isLogin() {
            if ( !localStorage.getItem('user') ) {
                return false;
            }
            http.get('/api/user').then((res) => {
                return true;
            }).catch(err => {
                localStorage.removeItem('user');
                this.user = null;
                router.push({name: 'login'});
                return false;
            });
        }
    }
});
