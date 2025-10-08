import axios from 'axios';
window.axios = axios;

// Importar biblioteca para apresentar o alerta
import Swal from 'sweetalert2';
window.Swal = Swal;

// Importar biblioteca Alpine.js para usar no dropdown
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
