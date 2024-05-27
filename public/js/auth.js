$(function() {
    $body = $(this);
});

function googleLogin(creds){

    $body.find('#pre-loader').show();
    $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });
    $.ajax({
        type       : "POST",
        url        : "/google-login",
        data       : creds,
        dataType   : 'JSON',
        success    : function (data) {
            
            $body.find('#pre-loader').hide();
            window.location.replace(window.location.protocol + '//' + eval("window.location.host")+data.redirect_uri);

        },
        error : function(e) {
            $body.find('#pre-loader').hide();
            // console.log(e);
           // window.location.replace(window.location.protocol + '//' + eval("window.location.host") + '/?err=6');
        }
    });

}

// function fbLogin(){
    
//     FB.login(function(response) {
        
//         $.ajaxSetup({
//             headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
//         });
//         $.ajax({
//             type       : "POST",
//             url        : "/fb-login",
//             data       : creds,
//             dataType   : 'JSON',
//             success    : function (data) {
//                 $body.find('#pre-loader').hide();
//                 window.location.replace(window.location.protocol + '//' + eval("window.location.host")+data.redirect_uri);

//             },
//             error : function(e) {
//                 $body.find('#pre-loader').hide();
//                 console.log(e);
//                // window.location.replace(window.location.protocol + '//' + eval("window.location.host") + '/?err=6');
//             }
//         });

        
//     });    
        
// }
