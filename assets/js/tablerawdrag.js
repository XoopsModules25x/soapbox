//$(document).ready(function () {
//    // Initialise the table
//    $("#table-article").tableDnD();
//});

$(document).ready(
    function () {
        // Controls Drag + Drop
        $('#table-article tbody.xo-module').sortable({
                placeholder: 'ui-state-highlight'
                //update: function(event, ui) {
                //var list = $(this).sortable( 'serialize');
                //$.post( 'admin.php?fct=modulesadmin&op=order', list );
            }
        )
    }
);
