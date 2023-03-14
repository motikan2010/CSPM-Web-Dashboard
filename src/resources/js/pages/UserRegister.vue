<template>
    <v-row>
        <v-col cols="2">
            <v-sheet>Name</v-sheet>
        </v-col>
        <v-col cols="3">
            <v-text-field v-model="name"></v-text-field>
        </v-col>
    </v-row>
    <v-row>
        <v-col cols="2">
            <v-sheet>Email</v-sheet>
        </v-col>
        <v-col cols="3">
            <v-text-field v-model="email"></v-text-field>
        </v-col>
    </v-row>
    <v-row>
        <v-col cols="2">
            <v-sheet>Password</v-sheet>
        </v-col>
        <v-col cols="3">
            <v-text-field type="password" v-model="password"></v-text-field>
        </v-col>
    </v-row>

    <v-row>
        <v-btn elevation="2" @click="register">Register</v-btn>
    </v-row>
</template>

<script>
import { getHttp } from '../commons/utils.js';

const http = getHttp();

export default {
    name: "UserRegister",
    data () {
        return{
            name: '',
            email: '',
            password: ''
        }
    },
    methods: {
        register() {
            http.get('/sanctum/csrf-cookie').then((res) => {
                http.post('/api/register', {name: this.name, email: this.email, password: this.password}).then((res) => {
                    console.log(res);
                    this.$router.push('/');
                })
            }).catch(error => {
                console.log(error);
            });
        }
    }
}
</script>
