
require(['jquery'], function($){ 
    $(document).ready(function ($) {


        if ($('body').hasClass('cms-hochbeete-bauen')) {

            var cat = $('.gartenhaus-second-row-wrapper-elem');
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
            });
            
        }
    });
});


