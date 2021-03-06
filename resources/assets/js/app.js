
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));

Vue.component('data-list-input', require('./components/form/DataListInput.vue'));
Vue.component('date-input', require('./components/form/DateInput.vue'));
Vue.component('date-time', require('./components/form/DateTimeInput.vue'));
Vue.component('file-input', require('./components/form/FileInput.vue'));
Vue.component('password-input', require('./components/form/PasswordInput.vue'));
Vue.component('submit-button', require('./components/form/SubmitButton.vue'));
Vue.component('text-area', require('./components/form/TextArea.vue'));
Vue.component('text-input', require('./components/form/TextInput.vue'));
Vue.component('text-input-group', require('./components/form/TextInputGroup.vue'));
Vue.component('time-input', require('./components/form/TimeInput.vue'));

const app = new Vue({
    el: '#app'
});
