<template>
    <v-row justify="center" dense>
        <v-col cols="12" sm="8" md="9" lg="10" class="flex-column">
            <v-card>
                <v-card-title class="text-h5">
                    スキャン対象 一覧
                </v-card-title>
                <v-card-text>
                    現在登録されているスキャン対象の一覧です。
                </v-card-text>
                <v-card-actions>
                    <v-btn variant="flat" color="info" elevation="2" :to="{name: 'cloud.new'}">スキャン対象を新規登録</v-btn>
                </v-card-actions>
            </v-card>

            <v-table density="compact">
                <thead>
                <tr>
                    <th>Cloud ID</th>
                    <th>Name</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="cloud in this.cloudList" :key="cloud">
                    <td>
                        <router-link :to="{name: 'cloud.detail', params:{'id': cloud.id}}">
                            {{ cloud.id }}
                        </router-link>
                    </td>
                    <td>{{ cloud.name }}</td>
                </tr>
                </tbody>
            </v-table>
        </v-col>
    </v-row>
</template>

<script>
import { getHttp } from '@/commons/utils.js';
import { usePageStore } from '@/stores/page.store.js';

const http = getHttp();

export default {
    data () {
        return {
            cloudList: []
        }
    },
    methods: {
        getClouds() {
            http.get('/sanctum/csrf-cookie').then(() => {
                http.get('/api/cloud/list').then((res) => {
                    let cloudList = res.data.clouds;
                    const pageStore = usePageStore();
                    pageStore.setCloudList(cloudList);
                }).catch(error => {
                    console.log(error);
                });
            });
        }
    },
    mounted() {
        // init
        const pageStore = usePageStore();
        this.cloudList = pageStore.cloudList;

        // update
        this.getClouds();
    }
}
</script>
