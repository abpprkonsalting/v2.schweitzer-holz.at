
require(['jquery'], function($){ 
    $(document).ready(function ($) {

        var secondAddToCartButton = jQuery(".second-add-to-cart-button button");
        secondAddToCartButton.attr("id","product-addtocart-button-second");
        if (secondAddToCartButton.length > 0) {
            secondAddToCartButton.click(function(){
                var realAddToCartButton = jQuery("#product-addtocart-button");
                if (realAddToCartButton.length > 0) {
                    realAddToCartButton.click();
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                }
            });
        }

    });
});