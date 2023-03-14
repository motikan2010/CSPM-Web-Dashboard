<template>
    <v-row justify="center" dense>
        <v-col cols="12" sm="8" md="9" lg="10" class="flex-column">
            <pre>{{ this.user }}</pre>

            <v-table density="compact">
                <thead>
                <tr>
                    <th>クラウドID</th>
                    <th>実行日時</th>
                    <th>コード</th>
                    <th>ステータス</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="cspmResult in this.cspmResultList" :key="cspmResult">
                    <td>{{ cspmResult.cloud_id }}</td>
                    <td>{{ cspmResult.exec_date }}</td>
                    <td>{{ cspmResult.response_status_code }}</td>
                    <td>{{ cspmResult.status }}</td>
                </tr>
                </tbody>
            </v-table>
        </v-col>
    </v-row>
</template>

<script>
import { getHttp } from '@/commons/utils.js';

const http = getHttp();

export default {
    data () {
        return{
            user: null,
            cspmResultList: [],
        }
    },
    mounted() {
        axios.get('/api/user').then((res) => {
            this.user = res.data;
        }).catch(error => {
            console.log(error);
        });

        axios.get('/api/cspm/all-result').then((res) => {
            console.log(res.data.cspm_result_list);
            this.cspmResultList = res.data.cspm_result_list;
        }).catch(error => {
            console.log(error);
        });
    }
}
</script>
