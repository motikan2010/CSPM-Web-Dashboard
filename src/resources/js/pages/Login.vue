<template>
    <v-container fluid>
        <v-row>
            <v-col cols="2">
                <v-subheader>Email</v-subheader>
            </v-col>
            <v-col cols="3">
                <v-text-field v-model="email" @keyup.enter="login"></v-text-field>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="2">
                <v-subheader>Password</v-subheader>
            </v-col>
            <v-col cols="3">
                <v-text-field type="password" v-model="password" @keyup.enter="login"></v-text-field>
            </v-col>
        </v-row>

        <v-row>
            <v-btn elevation="2" @click="login">Login</v-btn>
        </v-row>
    </v-container>
</template>

<script>
import { useAuthStore } from '@/stores/auth.store.js';

export default {
    data () {
        return{
            email: '',
            password: ''
        }
    },
    methods: {
        login() {
            const authStore = useAuthStore();
            authStore.login(this.email, this.password);
        }
    }
}
</script>
