<template>
    <header>
        <v-app-bar>
            <v-toolbar-title>Cloud Scan</v-toolbar-title>
            <template v-slot:append>
                <v-tabs>
                    <v-tab v-if="isLogin" :to="{name: 'index'}">HOME</v-tab>
                    <v-tab v-if="isLogin" :to="{name: 'cloud.list'}">Cloud</v-tab>
                    <v-tab v-if="isLogin" :to="{name: 'account.index'}">Account</v-tab>
                    <v-tab v-if="!isLogin" :to="{name: 'login'}">LOGIN</v-tab>
                    <v-tab v-if="!isLogin" :to="{name: 'register'}">REGISTER</v-tab>
                    <v-tab v-if="isLogin" @click="logout">LOGOUT</v-tab>
                </v-tabs>
            </template>
        </v-app-bar>
    </header>
</template>

<script>
import { useAuthStore } from '@/stores/auth.store.js';
import {router} from "@/router";

export default {
    data () {
        return {
            menuItems: []
        }
    },
    methods: {
        logout() {
            const authStore = useAuthStore();
            authStore.logout();
            router.push({name: 'login'});
        }
    },
    computed: {
        isLogin () {
            const authStore = useAuthStore();
            authStore.isLogin();
            return !!authStore.user;
        }
    }
}
</script>
