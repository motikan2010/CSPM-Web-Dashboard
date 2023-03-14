<template>
    <v-card>
        <v-card-title class="text-h5">
            {{ this.name }}
        </v-card-title>
        <v-card-text>
            <div>{{ this.id }}</div>
        </v-card-text>
        <v-card-actions>
            <v-btn elevation="2" style="text-transform: none" :to="{name: 'cloud.list'}">一覧</v-btn>
            <v-btn variant="flat" color="info" elevation="2" style="text-transform: none" @click="runCheck">スキャン開始</v-btn>
            <v-btn variant="flat" color="error" elevation="2" style="text-transform: none" @click="this.dialog = true">削除</v-btn>
        </v-card-actions>
    </v-card>

    <v-table>
        <thead>
        <tr>
            <th class="text-left">Run Date</th>
            <th class="text-left">Status</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="cspmExecHistory in this.cspmExecHistoryList" :key="cspmExecHistory.id">
            <td>
                <router-link :to="{name: 'cspm.result', params:{'id': this.id, 'execDate': cspmExecHistory.exec_date}}">
                    {{ cspmExecHistory.exec_date }}
                </router-link>
            </td>
            <td>{{ cspmExecHistory.response_status_code }}</td>
        </tr>
        </tbody>
    </v-table>

    <!-- 削除ダイアログ -->
    <v-row justify="center">
        <v-dialog v-model="dialog" persistent width="auto">
            <v-card>
                <v-card-title class="text-h5">
                    検査対象クラウドを削除しますか？
                </v-card-title>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="green-darken-1"
                        variant="text"
                        @click="deleteCloud"
                    >
                        はい
                    </v-btn>
                    <v-btn
                        color="green-darken-1"
                        variant="text"
                        @click="dialog = false"
                    >
                        いいえ
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>

<script>
import { getHttp } from '@/commons/utils.js';
import { usePageStore } from "@/stores/page.store.js";
import { useAlertStore } from "@/stores/alert.store.js";
import { router } from "@/router";

const http = getHttp();

export default {
    props: ['id'],
    data () {
        return {
            name: "",
            cspmExecHistoryList: [],
            dialog: false,
        }
    },
    methods: {
        getDetail(){
            http.get('/api/cloud/detail', {params: {id: this.id}}).then((res) => {
                let cloud = res.data.cloud;
                this.name = cloud.name;
            })
        },
        getStatus(){
            http.get('/api/cspm/status', {params: {id: this.id}}).then((res) => {
                const statusList = res.data.statusList;
                this.cspmExecHistoryList = statusList
                const pageStore = usePageStore();
                pageStore.setCspmExecHistoryList(this.id, statusList);
            })
        },
        runCheck() {
            http.get('/sanctum/csrf-cookie').then((res) => {
                http.post('/api/cspm/run', {id: this.id}).then((res) => {
                    console.log(res.data.status);
                    this.cspmExecHistoryList.unshift(res.data.status)
                    const alertStore = useAlertStore();
                    alertStore.success(res.data);
                }).catch(error => {
                    console.log(error);
                });
            });
        },
        deleteCloud() {
            http.get('/sanctum/csrf-cookie').then((res) => {
                http.delete('/api/cloud/', {params: {id: this.id}}).then((res) => {
                    this.dialog = false;
                    const pageStore = usePageStore();
                    delete pageStore.cloudList[this.id];
                    router.push({name: 'cloud.list'});
                }).catch(error => {
                    console.log(error);
                });
            });
        }
    },
    mounted() {
        // init
        const pageStore = usePageStore();
        let cloud = pageStore.cloudList[this.id];
        if ( cloud != null ) {
            this.name = cloud.name;
        }

        this.cspmExecHistoryList = pageStore.cspmExecHistoryList[this.id];

        // update
        this.getDetail();
        this.getStatus();
    }
}
</script>
