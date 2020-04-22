
define([
    "jquery"
], function($){
        "use strict";
        return function(config, element) {
            $(document).ready(function ($) {
                window.abpSpinnerTimer = window.setInterval(function(){
                    var condition = $(".navigation ul").css("background-color");
                    if (condition == "rgb(0, 114, 85)") {
                        $('.abp-spinner-wrapper').css('opacity',0);
                        window.setTimeout(function(){$('.abp-spinner-wrapper').css('display','none');$('.abp-spinner-wrapper').css('z-index','0');},1000);
                        clearInterval(window.abpSpinnerTimer);
                    }
                },1000);
            });
        }
    }
)