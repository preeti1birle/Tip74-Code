$(window).scroll(function() {
    if($(window).scrollTop() > 100) {
        $('.stickybtn').addClass('fixed');
        // setTimeout(() => {
        //     $('.fixedPurchaseBtn').removeClass('fixed');
        // }, 1500);
    }
    else{
        $('.stickybtn').removeClass('fixed');
    }

});
