function dataTable(dataTableId, colsDef, ajaxUrl) {
    $(document).ready(function() {
        $(dataTableId).DataTable({
            "bDestroy": true,
            "pageLength": 10,
            "paging": true,
            "lengthChange": true,
            "ordering": true,
            "searching": true,
            "processing": true,
            "responsive": true,
            "serverSide": true,
            "info" : true, 
            "order": [[2, 'asc']],
            "columns": colsDef,
            "ajax": {
                "url": ajaxUrl,
                "type": "POST",
                error: function(e) {
                        alert(e);
                    },
                "dataSrc": function(data){
                    console.log(data);
                    return data["name"];
                }
            }
        } );
    } );
}