
require(['jquery'], function($){ 
    $(document).ready(function ($) {


        if ($('body').hasClass('cms-hochbeete-bauen')) {

            /*var cat = $('.gartenhaus-second-row-wrapper-elem');
            if (cat.length > 0) {
                cat.each(function(){
                    var button = $(this).find('img , .gartenhaus-second-row-wrapper-texts, .gartenhaus-second-row-wrapper-header');
                    button.click(function(){
                        var classes = $(this).closest('.gartenhaus-second-row-wrapper-elem')[0].className.split(" ");
                        var column = classes[classes.length - 1];
                        var table = $('.home-table2');
                        var tableColumns =table.find('[class^="column-"]');
                        var selectedColumns = table.find('.'+column);
                        tableColumns.removeClass('active');
                        selectedColumns.addClass('active');
                        table.css('z-index','100');
                        table.css('opacity','1');
                    });
                });
            }
            $('.home-table2 .close').click(function(){
                var table = $('.home-table2');
                table.css('z-index','-1');
                table.css('opacity','0');
            });

            $('body').click(function(e){
                if (!($.contains( $('.home-table2')[0], e.toElement) ||
                    $.contains( $('.gartenhaus-second-row-wrapper .gartenhaus-second-row-wrapper-elem')[0], e.toElement) ||
                    $.contains( $('.gartenhaus-second-row-wrapper .gartenhaus-second-row-wrapper-elem')[1], e.toElement) ||
                    $.contains( $('.gartenhaus-second-row-wrapper .gartenhaus-second-row-wrapper-elem')[2], e.toElement) ||
                    $.contains( $('.gartenhaus-second-row-wrapper .gartenhaus-second-row-wrapper-elem')[3], e.toElement) ||
                    $.contains( $('.gartenhaus-second-row-wrapper .gartenhaus-second-row-wrapper-elem')[4], e.toElement)
                )) {
                    var table = $('.home-table2');
                    table.css('z-index','-1');
                    table.css('opacity','0');
                }
            });

            var columnHeaders = $('[class^="column-"].header:not(.column-1)');
            columnHeaders.click(function(){
                var columnSelector = this.className.split(" ")[0];
                var table = $('.home-table2');
                var tableColumns =table.find('[class^="column-"]');
                tableColumns.removeClass('active');
                var selectedColumns = table.find("."+columnSelector);
                selectedColumns.addClass('active');
            });*/
            
        }
        else if ($('body').hasClass('product-hochbeet-classic-neu')) {

            var elems = $('#options_22_text,#options_25_text, #select_28, #select_31, #select_34, #select_37,#qty');
            elems.each(function(){
                this.started = false;
            });
            var priceWrapper = $('.product-info-price');
            priceWrapper.prepend('<span id="calculated-price" style="font-size: 36px;line-height: 36px;font-weight:600;"></span>');
            $('.price-final_price').remove();

            elems.change(function(){
                if (this.started) {
                    $('#options_22_text').css('border','1px solid #c2c2c2');
                    $('#options_25_text').css('border','1px solid #c2c2c2');
                    var price = calculatePrice();
                    $('#calculated-price').text(price + ' €');
                }
                else this.started = true;
                
            });

            function calculatePrice(){
                var lange = $('#product-options-wrapper #options_22_text').val();
                if (!isNumeric(lange) || lange < 50) {
                    $('#options_22_text').css('border','1px solid red');
                    return 0;
                }
                lange = Number(lange);
                var breite = $('#product-options-wrapper #options_25_text').val();
                if (!isNumeric(breite) || breite < 50) {
                    $('#options_25_text').css('border','1px solid red');
                    return 0;
                }
                breite = Number(breite);
                var z1 = $('#product-options-wrapper #select_28').val();
                z1 = z1 == 25 ? 1 : 0;
                var z2 = $('#product-options-wrapper #select_31').val();
                z2 = z2 == 31 ? 1 : 0;
                var z3 = $('#product-options-wrapper #select_34').val();
                z3 = z3 == 37 ? 1 : 0;
                var z4 = $('#product-options-wrapper #select_37').val();
                z4 = z4 == 43 ? 1 : 0;
                var qty = Number($('#qty').val());

                basic = 203;
                classic = 0.484;

                nagetiergitter = (( lange + breite )* 2 * 5 / 100) * z1;

                handlauf = ((lange+breite)*2*12.5/100)*z2;

                schneckenkante = (((lange+breite)*2+200) / 200) * 14 * z3;

                aussteifungsset = (79.9 * z4);

                price = ((( lange + breite) *2 * classic + basic + nagetiergitter + handlauf + aussteifungsset + schneckenkante ) * qty).toFixed(2);

                return price;

            }
        }
        else if ($('body').hasClass('product-hochbeet-basic-neu')) {
            var elems = $('#options_4_text,#options_7_text, #select_10, #select_13, #select_16, #select_19,#qty');
            elems.each(function(){
                this.started = false;
            });
            var priceWrapper = $('.product-info-price');
            priceWrapper.prepend('<span id="calculated-price" style="font-size: 36px;line-height: 36px;font-weight:600;"></span>');
            $('.price-final_price').remove();

            elems.change(function(){
                if (this.started) {
                    $('#options_4_text').css('border','1px solid #c2c2c2');
                    $('#options_7_text').css('border','1px solid #c2c2c2');
                    var price = calculatePrice();
                    $('#calculated-price').text(price + ' €');
                }
                else this.started = true;
            });

            function calculatePrice(){
                var lange = $('#product-options-wrapper #options_4_text').val();
                if (!isNumeric(lange) || lange < 50) {
                    $('#options_4_text').css('border','1px solid red');
                    return 0;
                }
                lange = Number(lange);
                var breite = $('#product-options-wrapper #options_7_text').val();
                if (!isNumeric(breite) || breite < 50) {
                    $('#options_7_text').css('border','1px solid red');
                    return 0;
                }
                breite = Number(breite);
                var z1 = $('#product-options-wrapper #select_10').val();
                z1 = z1 == 25 ? 1 : 0;
                var z2 = $('#product-options-wrapper #select_13').val();
                z2 = z2 == 31 ? 1 : 0;
                var z3 = $('#product-options-wrapper #select_16').val();
                z3 = z3 == 37 ? 1 : 0;
                var z4 = $('#product-options-wrapper #select_19').val();
                z4 = z4 == 43 ? 1 : 0;
                var qty = Number($('#qty').val());

                basic = 77;
                classic = 0.34;

                nagetiergitter = (( lange + breite )* 2 * 5 / 100) * z1;

                handlauf = ((lange+breite)*2*12.5/100)*z2;

                schneckenkante = (((lange+breite)*2+200) / 200) * 14 * z3;

                aussteifungsset = (39.9 * z4);

                price = ((( lange + breite) *2 * classic + basic + nagetiergitter + handlauf + aussteifungsset + schneckenkante ) * qty).toFixed(2);

                return price;

            }
        }

        function isNumeric(value) {
            return /^\d+$/.test(value);
        }

    });
});


