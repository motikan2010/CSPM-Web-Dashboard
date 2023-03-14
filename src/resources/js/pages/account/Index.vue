<template>
    <v-row class="text-center" justify="center" dense>
        <v-col cols="12" sm="10" md="8" lg="6" class="flex-column justify-center align-center">
            <v-card  cols="12">
                <v-card-title>
                    <h3 class="display-1">ログイン</h3>
                </v-card-title>
                <v-card-text>
                    <v-form>
                        <v-text-field label="ユーザ名" v-model="user.name"></v-text-field>
                        <v-text-field label="メールアドレス" v-model="user.email"></v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-btn elevation="2" variant="flat" color="warning" @click="change">アカウント変更</v-btn>
                </v-card-actions>
            </v-card>

            <v-card class="my-5">
                <v-card-text>
                    <v-form>
                        <v-text-field label="現在のパスワード" type="password" v-model="this.password.current"></v-text-field>
                        <v-text-field label="変更後のパスワード" type="password" v-model="this.password.newPass"></v-text-field>
                        <v-text-field label="変更後のパスワード（確認）" type="password" v-model="this.password.newPass2"></v-text-field>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                    <v-btn elevation="2" variant="flat" color="warning" @click="changePassword">パスワード変更</v-btn>
                </v-card-actions>
            </v-card>
        </v-col>
    </v-row>
</template>

<script>
import { getHttp } from '@/commons/utils.js';

const http = getHttp();

export default {
    data () {
        return {
            user: {
                name: '',
                email: '',
            },
            password: {
                current: '',
                newPass: '',
                newPass2: '',
            }
        }
    },
    methods:{
        change() {
            http.get('/sanctum/csrf-cookie').then((res) => {
                http.put('/api/account', {name: this.user.name, email: this.user.email}).then((res) => {
                    console.log(res.data);
                })
            }).catch(error => {
                console.log(error);
            });
        },
        changePassword() {
            http.get('/sanctum/csrf-cookie').then((res) => {
                http.put('/api/account/password', {currentPassword: this.password.current, newPassword: this.password.newPass, newPassword2: this.password.newPass2})
                    .then((res) => {
                        console.log(res.data);
                })
            }).catch(error => {
                console.log(error);
            });
        }
    },
    mounted() {
        http.get('/api/account').then((res) => {
            console.log(res.data);
            this.user = res.data;
        })
    }
}
</script>
