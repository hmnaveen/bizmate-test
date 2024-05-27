function forgotPasswordModal(){

    $('#forgot-password-modal').modal({
        backdrop: 'static',
        keyboard: true, 
        show: true
    });

}
$('#forgot-password-modal').on('hidden.bs.modal', function () {
    $('#reset-email').val('');
    $('#modal-alert').hide();
})
$('#submit-email-reset').click(function () {
    $('.loader-cont').show();
    $('#modal-alert').hide();
    $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        type       : "POST",
        url        : "/verify-email",
        data       : {"email" : $('#reset-email').val()},
        dataType   : 'JSON',
        success    : function (data) {
            $('.loader-cont').hide();
            $('.verify-msg').text(data.message);
            $('#modal-alert').removeClass('alert-warning alert-danger');
            $('#modal-alert').addClass('alert-primary');
            $('#modal-alert').show();

        },
        error      : function(e) {
           $('.loader-cont').hide();
           if(e.status != 500){
                $('#modal-alert').remove('alert-primary');
                $('#modal-alert').addClass('alert-warning');
                $('.verify-msg').text(e.responseJSON.errors.email[0]);
           }else{
                $('#modal-alert').addClass('alert-danger');
                $('.verify-msg').text('something went wrong, contact web administrator');
           }
           
           $('#modal-alert').show();

        }
    });

});


$('#submit-new-pass').click(function () {
    let self = $(this),
    parentNode = self.parents( "div" );
    
    $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        type       : "PUT",
        url        : "/forgotpass/" + $(this).attr('data-attr') ,
        data       : {
 
            'password' : parentNode.find('#pass').val(),
            'password_confirmation' : parentNode.find('#re-pass').val(),

        },
        dataType   : 'JSON',
        success    : function (data) {
            window.location.replace(window.location.protocol + '//' + eval("window.location.host"));
            // $('.form-group').hide();
            // $('.sub-err-msg').text(data.message);
            // $('#redirect-link').show();
            // $('#submit-new-pass').hide();
            // $('#redirect-link').attr('href',window.location.protocol + "//" + window.location.host);
            // $('#modal-alert').removeClass('alert-warning alert-danger');
            // $('#modal-alert').addClass('alert-primary');
            // $('#modal-alert').show();

        },
        error : function(e) {
           $('#redirect-link').hide();
           if(e.status != 500){
                $('#modal-alert').addClass('alert-warning');
                if(e.status == 400){

                    $('.sub-err-msg').text(e.responseJSON.message);

                }else{
                    
                    if("password" in e.responseJSON.errors){

                        $('.sub-err-msg').text(e.responseJSON.errors['password'][0]);

                    }else{

                        $('.sub-err-msg').text(e.responseJSON.errors['password_confirmation'][0]);

                    }

                }  
           }else{
                $('#modal-alert').addClass('alert-danger');
                $('.sub-msg').text('Something went wrong, contact web administrator');
           }
           $('#modal-alert').show();
        }
    });

});

