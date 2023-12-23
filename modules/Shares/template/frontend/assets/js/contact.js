(function ($) {
    "use strict";
        $(document).on('submit','#contact_form_submit',function(e){
            e.preventDefault();
            var name = $('#contact_name').val();
            var email = $('#contact_email').val();
            var message = $('#contact_message').val();
            if (!name) {
                 $('#contact_name').removeClass('error');
                 $('#contact_name').addClass('error').attr('placeholder','Please Enter Name');
             }else{
                 $('#contact_name').removeClass('error');
             }
            if (!email) {
                 $('#contact_email').removeClass('error');
                 $('#contact_email').addClass('error').attr('placeholder','Please Enter Email')
             }else{
                 $('#contact_email').removeClass('error');
             }
            if (!message) {
                 $('#contact_message').removeClass('error');
                 $('#contact_message').addClass('error').attr('placeholder','Please Enter Your Message')
             }else{
                 $('#contact_message').removeClass('error');
             }
            if ( name && email && message ) {
                 $.ajax({
                     type: "POST",
                     url:'../../contact.php',
                     data:{
                         'name': name,
                         'email': email,
                         'message': message,
                     },
                     success:function(data){
                         $('#contact_form_submit').children('.email-success').remove();
                         $('#contact_form_submit').prepend('<span class="alert alert-success email-success contact-alert">'+data+'</span>');
                         $('#contact-name').val('');
                         $('#contact-email').val('');
                         $('#contact-message').val('');
                         $('#contact-map').height('576px');
                         $('.email-success').fadeOut(3000);
                     },
                     error:function(res){
                     }
                 });
             }else{
                $('#contact_form_submit').children('.email-success').remove();
                $('#contact_form_submit').prepend('<span class="alert alert-danger email-success contact-alert">Somenthing went wrong</span>');
                $('.email-success').fadeOut(3000);
             }
        });
}(jQuery));