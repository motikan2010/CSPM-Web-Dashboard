import { defineStore } from 'pinia';

export const usePageStore = defineStore('status', {
    state: () => ({
        cloudList: {},
        cspmExecHistoryList: {},
    }),
    actions: {
        setCloudList(cloudList) {
            for ( const cloud of Object.entries(cloudList) ) {
                this.cloudList[cloud[1].id] = cloud[1];
            }
        },
        deleteCloud(cloudId) {

        },
        setCspmExecHistoryList(cloudId, cspmExecHistoryList) {
            this.cspmExecHistoryList[cloudId] = cspmExecHistoryList;
        }
    },
});
