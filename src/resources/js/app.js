import './bootstrap';

import { createApp } from 'vue';
import 'vuetify/lib/styles/main.sass'
import '@mdi/font/css/materialdesignicons.css'
import App from './App.vue';
import { createVuetify } from 'vuetify'
import { createPinia } from 'pinia'
import { router } from './router.js'


import HeaderComponent from './components/Header.vue';
import AlertComponent from './components/Alert.vue';

const vuetify = createVuetify()
const pinia = createPinia()

const app = createApp(App);
app.use(pinia);
app.use(vuetify);
app.use(router);
app.component('header-component', HeaderComponent);
app.component('alert-component', AlertComponent);
app.mount('#app');
