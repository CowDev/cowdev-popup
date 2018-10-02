(function($){
    $(document).ready(function() {

        var cowdev_popup      = $('.cowdev-popup').attr('data-popup'),
            cowdev_expire     = $('.cowdev-popup').attr('data-expires'),
            cowdev_time       = new Date().getTime().toString();

        var cowdev_show_popup = localStorage.getItem( cowdev_popup ),
            cowdev_show_popup = JSON.parse( cowdev_show_popup );

        if (cowdev_show_popup == null ||
            ( cowdev_expire     != 0 &&
            ( cowdev_show_popup.timestamp < ( cowdev_time - ( cowdev_expire * 60000 ) ) ) ) ) {
                // Show popup here
                $('.cowdev-popup, .cowdev-overlay').fadeIn();
        }

        $('.cowdev-popup-close, .cowdev-overlay').on('click', function(){
            $('.cowdev-popup, .cowdev-overlay').fadeOut();

            var object          = {
                timestamp: new Date().getTime(),
                expire: cowdev_expire
            };
            localStorage.setItem( cowdev_popup, JSON.stringify(object));

        })

    });
})(jQuery);
