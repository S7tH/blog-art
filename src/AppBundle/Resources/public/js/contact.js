$(document).ready(function(){
    $(function(){
        $('#contactform').submit(function(e){
            e.preventDefault();
            var formulaire = $(this);
            var post_url = formulaire.attr('action');
            var post_data = formulaire.serialize();
            //Ajax call
            $.ajax({
                type: 'POST',
                url: post_url,
                data: post_data,
                success: function() {
                    //Affichage du formulaire avec un effet
                    $(form).fadeOut(800);

                    $("#result").html("<p>Votre email a été envoyé avec succès !</p>");
                    alert('Votre email a été envoyé avec succès !');
                    
                    $(form).load('views/navigation/form.html.twig', function (){
                        $(this).delay(9999999, function(){
                            location.reload();
                        });
                    });
                }
            });
        });
    });
});