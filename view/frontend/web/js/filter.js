define([
        'jquery',
        'Magento_Ui/js/modal/modal',
        'owlCarousel'
    ],
    function($, owlCarousel, modal) {
        $(document).ready(function() {
            /* animate filter */

            var owlAnimateFilter = function(even) {
                $(this)
                    .addClass('__loading')
                    .delay(70 * $(this).parent().index())
                    .queue(function() {
                        $(this).dequeue().removeClass('__loading')
                    })
            }

            function callFilter(filterData) {
                var owl = $('.owl-carousel');

                owl.owlFilter(filterData, function(_owl) {
                    $(_owl).find('.item').each(owlAnimateFilter);
                });
            }

            $(document).on('click', '.filter-btn', function(e){
                //console.log('clicked');

                var filter_data = $(this).data('filter');
                /* return if current */
                if($(this).hasClass('btn-active')) return;

                /* active current */
                $('.btn-active').removeClass('btn-active');
                $(this).addClass('btn-active');
                //$(this).toggleClass('btn-active').siblings().removeClass('btn-active');

                /* Filter */
                callFilter(filter_data);
            });
        });
    });