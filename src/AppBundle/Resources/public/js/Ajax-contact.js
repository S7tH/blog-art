$(document).ready(function(){
    $(function(){
        $('#contactform').submit(function(e){
            e.preventDefault();
            var formulaire = $(this);
            var post_url = formulaire.attr('action');
            var post_data = formulaire.serialize();
            var count = 0;
            //Ajax call
            $.ajax({
                type: 'POST',
                url: post_url,
                data: post_data,
                success: function(response) {
                    if(response == 'success')
                    {
                        $(form).fadeOut(800);
                        $("#result").html("<p>Votre email a été envoyé avec succès !</p>");
                        alert('Votre email a été envoyé avec succès !');
                        $(form).load('views/navigation/form.html.twig', function (){
                            $(this).delay(800, function(){
                                location.reload();
                            });
                        });
                    }
                    else
                    {
                        $("#result").html("<p>Une erreur s\'est produite ! Veuillez vérifier l\'intégrité de vos champs ou réessayer plus tard.</p>");
                        alert('Une erreur s\'est produite ! Veuillez vérifie l\'intégrité de vos champs ou réessayer plus tard.');
                        $(form).empty().append(response);
                    }
                }
            });
        });
    });
});
