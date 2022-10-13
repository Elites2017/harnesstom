function custom_showing() {
    $(document).ready(function() {
		$('#datatable').dataTable({
			"aLengthMenu": [[10, 25, 50, 75, 100, -1], [10, 25, 50, 75, 100, "All"]],
			"iDisplayLength": 25
		});
	} );
}

custom_showing();