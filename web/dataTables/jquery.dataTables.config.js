$.extend( $.fn.dataTable.defaults, {
    "lengthChange": false,
    "searching": false,
    "aaSorting": [],
    "stateSave": true,
    "oLanguage": {
        "sProcessing":   "Подождите...",
        "sLengthMenu":   "Показать _MENU_ записей",
        "sZeroRecords":  "Записи отсутствуют.",
        "sInfo":         "Записи с _START_ до _END_ из _TOTAL_ записей",
        "sInfoEmpty":    "Записи с 0 до 0 из 0 записей",
        "sInfoFiltered": "(отфильтровано из _MAX_ записей)",
        "sInfoPostFix":  "",
        "sSearch":       "Поиск:",
        "sUrl":          "",
        "oPaginate": {
            "sFirst": "Первая",
            "sPrevious": "Предыдущая",
            "sNext": "Следующая",
            "sLast": "Последняя"
        },
        "oAria": {
            "sSortAscending":  ": активировать для сортировки столбца по возрастанию",
            "sSortDescending": ": активировать для сортировки столбцов по убыванию"
        }
    },
    "fnDrawCallback": function(oSettings) {

        var $dataTablesWrapper = $(oSettings.nTableWrapper);

        if (oSettings.fnRecordsDisplay() <= oSettings._iDisplayLength) {

            $dataTablesWrapper.find('.dataTables_info').hide();
            $dataTablesWrapper.find('.dataTables_paginate').hide();

        }

    }
} );
