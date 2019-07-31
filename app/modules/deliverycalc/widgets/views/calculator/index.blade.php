<div id="delivery_calc" class="aside-item-content">
    <h5 class="text-center font-bold">Доставка в <span class="inline-block">{{ trans('cities.obl.accusative.'.$current_city) }}</span></h5>
    <ul class="font12 text-nowrap gray">
        <li class="invisible row" data-id="{{ $current_cdek_id }}">
            <span class="col-xs-6">{{ $current_city }}</span>
            <span class="calculated-price col-xs-3"></span>
            <span class="calculated-time  col-xs-3"></span>
        </li>
        @foreach($cities as $city)
        <li class="invisible row" data-id="{{ $city->id }}">
            <span class="col-xs-6">
                {{ $city->name }}
            </span>
            <span class="calculated-price col-xs-3"></span>
            <span class="calculated-time  col-xs-3"></span>
        </li>
        @endforeach
    </ul>
    <div class="text-center font11 text-justify">Приблизительная стоимость доставки указана за комплект шин (4 шт). Для точного расчета обратитесь в отдел продаж.</div>
</div>

<script type="text/javascript">
    (function ($) {
        $(function () {
            calcDelivery($('input[name=count]').val());
//        $('input[name=count]').change(function(){
//            calcDelivery($(this).val());
//        })
        });

        calcDelivery = function (tyreQuantity) {
            $('#delivery_calc').hide();

            $('#delivery_calc li[data-id]').each(function () {
                var $this = $(this);
                var formDataJson = {
                    "version": "1.0",
                    "senderCityId": "{{ $current_cdek_id }}",
                    "receiverCityId": $(this).data('id'),
                    "tariffId": "1",
                    "goods": []
                };
                for (i = 1; i <= 1; i++) {
                    formDataJson.goods.push({
                        "weight": "{{ min(25, $product->properties->weight) }}",
                        "length": "{{ min(50, $product->properties->diameter_outside/10) }}",
                        "width": "{{ min(50, $product->properties->diameter_outside/10) }}",
                        "height": "{{ min(50, $product->properties->width_mm ? $product->properties->width_mm/10 : $product->properties->width_inch*2.5) }}"
                    });
                }
                $.ajax({
                    url: 'http://api.cdek.ru/calculator/calculate_price_by_jsonp.php',
                    jsonp: 'callback',
                    data: {
                        "json": JSON.stringify(formDataJson)
                    },
                    type: 'GET',
                    dataType: "jsonp",
                    success: function (data) {
                        if (data.hasOwnProperty("result")) {
                        $this.find('.calculated-price').html(data.result.price + ' р.');
                                $this.find('.calculated-time').html((data.result.deliveryPeriodMin === data.result.deliveryPeriodMax ? data.result.deliveryPeriodMin : data.result.deliveryPeriodMin + '-' + data.result.deliveryPeriodMax) + ' дн. ');
                                $this.removeClass('invisible');
                                $('#delivery_calc').show();
                        } else {
                        @if (Config::get('app.debug'))
                                var html = '';
                        for (var key in data["error"]) {
                            html += html + 'Код ошибки: ' + data["error"][key].code + '<br />Текст ошибки: ' + data["error"][key].text + '<br /><br />';
                        }
                        $this.removeClass('invisible');
                        $('#delivery_calc').show();
                        $this.html(html);
                                @endif
                    }

                    }
                });
            })

        }
    })(jQuery)
</script>