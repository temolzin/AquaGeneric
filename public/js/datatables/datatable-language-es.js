$(document).ready(function() {
    $.extend(true, $.fn.dataTable.defaults, {
        language: {
            url: '/lang/datatables/datatable-lang-es.json'
        },
        order: [[0, "desc"]]
    });
});
