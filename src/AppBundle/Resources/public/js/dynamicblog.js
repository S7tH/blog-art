
        if($('#blogstate').show())
        {
            $('#blog').hide();
            $('#gallery').show();
            $('#blogstate').hide();
            $('#gallerystate').show();
        }
        if($('#gallerystate').show())
        {
            $('#blog').show();
            $('#gallery').hide();
            $('#blogstate').show();
            $('#gallerystate').hide();
        }



         $('#gallerybtn').click(function(e){
             $('#blog').hide();
             $('#gallery').show();

             $('#blogstate').hide();
             $('#gallerystate').show();
             $('.tm-intro').hide();
            });

        $('#blogbtn').click(function(e){
             $('#gallery').hide();
             $('#blog').show();

             $('#blogstate').show();
             $('#gallerystate').hide();

            });

        $('#doubleup').click(function(e){
             $('.tm-intro').show();
            });
  