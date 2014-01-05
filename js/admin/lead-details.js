// For datatable
jQuery(document).ready(function($) {


    var my_leads_datatable = $('#placester_saved_search_list').dataTable( {
            "bFilter": false,
            "bProcessing": true,
            "bServerSide": true,
            "sServerMethod": "POST",
            'sPaginationType': 'full_numbers',
            'sDom': '<"dataTables_top"pi>lftpir',
            "sAjaxSource": ajaxurl, //wordpress url thing
            "aoColumns" : [
                { sWidth: '120px' },    
                { sWidth: '230px' },    
                { sWidth: '330px' },    
                { sWidth: '100px' },    
                { sWidth: '200px' }     
            ],
            "fnServerParams": function ( aoData ) {
                aoData.push( { "name": "action", "value" : "datatable_leads_searches_ajax"} );
                aoData.push( { "name": "lead_id", "value" : $('input#lead_id').val()} );
            }
        });







    var my_favorites_datatable = $('#placester_favorite_listings_list').dataTable( {
            "bFilter": false,
            "bProcessing": true,
            "bServerSide": true,
            "sServerMethod": "POST",
            'sPaginationType': 'full_numbers',
            'sDom': '<"dataTables_top"pi>lftpir',
            "sAjaxSource": ajaxurl, //wordpress url thing
            "aoColumns" : [
                { sWidth: '60px' },    
                { sWidth: '200px' },    
                { sWidth: '60px' },    
                { sWidth: '60px' },    
                { sWidth: '100px' },  
                { sWidth: '60px' },   
                { sWidth: '100px' }     
            ],
            "fnServerParams": function ( aoData ) {
                aoData.push( { "name": "action", "value" : "datatable_favorites_ajax"} );
                aoData.push( { "name": "lead_id", "value" : $('input#lead_id').val()} );
            }
        });



    // hide/show action links in rows
    $('tr.odd, tr.even').live('mouseover', function(event) {
        $(this).find(".row_actions").show();
    });
    $('tr.odd, tr.even').live('mouseout', function(event) {
        $(this).find(".row_actions").hide();
    });

});



