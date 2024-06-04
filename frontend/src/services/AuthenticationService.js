import Api from './Api'

export default {
    logout(credentials) {
        return Api().post('/logout', credentials);
    },
    login(credentials) {
        return Api().post('/login', credentials);
    },
    register(credentials) {
        return Api().post('/register', credentials);
    },
    insertImages(credentials) {
        return Api().post('/insertImages', credentials);
    },
    getImages(credentials) {
        return Api().get('/getImages', credentials);
    },
    deleteImage(credentials) {
        return Api().post('/deleteImage', credentials);
    },
    updateImage(credentials) {
        return Api().post('/updateImage', credentials);
    },
    getProfile() {
        return Api().get('/getProfile');
    },
    getInfo() {
        return Api().get('/getInfo');
    },
    updateInfo(credentials) {
        return Api().post('/updateUserInfo', credentials);
    },
    updateProfile(credentials) {
        return Api().post('/changeProfile', credentials);
    },
    changePassword(credentials) {
        return Api().post('/changePass', credentials)
    }
}
