$( "#inn-form" ).submit(function( event ) {
    if ($("#InputINN").val().length > 0)
        $.get( "/api/validate?inn="+$("#InputINN").val(), function( data ) {
            if (data['status'] != true) {
                $(".my-error").html(data['message']);
                $(".my-error").show()
                $(".my-success").hide()
            } else {
                $(".my-success").html(data['message']);
                $(".my-error").hide();
                $(".my-success").show();
            }
        });
    event.preventDefault();
});
