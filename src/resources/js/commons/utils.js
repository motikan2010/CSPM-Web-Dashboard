import axios from 'axios';

export function getHttp() {
    return axios.create({
        baseURL: '/',
        withCredentials: true,
    });
}
