<template>
    <div class="text-center">
        <v-pagination
            v-model="this.current_page"
            :length="this.last_page"
            :total-visible="7"
        ></v-pagination>
    </div>

    <v-table density="compact">
        <thead>
        <tr>
            <th>Service</th>
            <th>Description</th>
            <th>region</th>
            <th>status</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="result in this.cspmResultList">
            <td>{{ result.category }}</td>
            <td>{{ result.description }}</td>
            <td>{{ result.region }}</td>
            <td>{{ result.status }}</td>
        </tr>
        </tbody>
    </v-table>

    <div class="text-center">
        <v-pagination
            v-model="this.current_page"
            :length="this.last_page"
            :total-visible="7"
        ></v-pagination>
    </div>
</template>

<script>
import { getHttp } from '@/commons/utils.js';

const http = getHttp();

export default {
    props: ['id', 'execDate'],
    data () {
        return {
            cspmResultList: [],
            current_page: 1,
            last_page: 0,
        }
    },
    methods: {
        getResult(){
            http.get('/api/cspm/result', {params: {id: this.id, exec_date: this.execDate, page: this.current_page}}).then((res) => {
                const cspm_result = res.data.cspm_result
                this.current_page = cspm_result.current_page;
                this.last_page = cspm_result.last_page;
                this.cspmResultList = cspm_result.data;
            })
        }
    },
    mounted() {
        this.getResult();
    },
    watch: {
        current_page: function (){
            this.getResult();
        }
    }
}
</script>
