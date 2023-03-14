<template>
    <v-row justify="center" dense>
        <v-col cols="12" sm="8" md="9" lg="10" class="flex-column">
            <v-card>
                <v-card-title class="text-h5">
                    スキャン対象 登録
                </v-card-title>
                <v-card-text></v-card-text>

                <v-card-actions>
                    <v-btn elevation="2" @click="this.$router.go(-1)">戻る</v-btn>
                </v-card-actions>

                <v-col cols="4">
                    <v-text-field
                        v-model="this.cloud.name"
                        :counter="10"
                        label="Name"
                        required
                    ></v-text-field>
                </v-col>

                <v-col cols="4">
                    <v-text-field
                        v-model="this.cloud.aws_key"
                        :counter="20"
                        label="AWS Key"
                        required
                    ></v-text-field>
                </v-col>

                <v-col cols="4">
                    <v-text-field
                        v-model="this.cloud.aws_secret"
                        :counter="40"
                        label="AWS Secret"
                        required
                    ></v-text-field>
                </v-col>

                <v-card-actions>
                    <v-btn variant="flat" color="info" elevation="2" @click="register">登録</v-btn>
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
            cloud: {
                name: "",
                aws_key: "",
                aws_secret: ""
            }
        }
    },
    methods: {
        register() {
            http.get('/sanctum/csrf-cookie').then((res) => {
                http.post('/api/cloud/new',
                    {name: this.cloud.name, aws_key: this.cloud.aws_key, aws_secret: this.cloud.aws_secret}
                ).then((res) => {
                    console.log(res);
                    this.$router.push({name: 'cloud.detail', params:{'id': res.data.id}});
                })
            }).catch(error => {
                console.log(error);
            });
        }
    }
}
</script>

<style scoped>

</style>
