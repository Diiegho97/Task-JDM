import './bootstrap';
import '@fortawesome/fontawesome-free/js/all.js';
require('bootstrap');
require('@fortawesome/fontawesome-free/js/all.js');
require('datatables.net-bs5');
require('datatables.net-responsive');

// Inicializar DataTables
$(document).ready(function() {
    $('#tasks-table').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        columnDefs: [
            { responsivePriority: 1, targets: 0 }, // Título
            { responsivePriority: 2, targets: -1 }, // Acciones
            { orderable: false, targets: -1 } // Deshabilitar ordenación en columna de acciones
        ]
    });
});