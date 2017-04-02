import axios from 'axios';

let base = '/frontend';

export const requestLogin = params => { return axios.post(`/login`, params).then(res => res.data); };

export const getUserListPage = params => { return axios.get(`${base}/users`, { params: params }); };

export const removeUser = params => { return axios.get(`${base}/user/remove`, { params: params }); };

export const editUser = params => { return axios.get(`${base}/user/edit`, { params: params }); };

export const addUser = params => { return axios.post(`${base}/users`, params); };
