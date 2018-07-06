var server_url = '';


$(function () {

    var dem = 0;
    //load conversation when click
    $('.product-info a').click(loadConversation);
    //end load

    //send message
    $('.send').click(replyMessage);
    $("input[name='message']").keyup(function (e) {
        if (e.which === 13) {
            $('.send').trigger('click');
        }
    });
    //end message

    //submit order
    $('form .btn-success').click(function (e) {
        e.preventDefault();
        dem++;
        var ckInput=0;
        var ckSelect=0;
        $('.input-order select').each(function () {
           var sltEmpty=$(this).val();
           if (sltEmpty===''){
               ckSelect++;
           }
        });
        $('.input-order input').not("input[name='id_customer'],input[name='coupon']").each(function () {
           var empty=$(this).val();
           if(empty===''){
               ckInput++;
           }
        });

        if(ckInput===0 && ckSelect===0) {
            if (dem === 1) {
                var data = $("form :input").serializeArray();
                if(typeof typeSubmit !== 'undefined' && typeSubmit==='update'){
                    url='/order/submitupdateorder';
                }else{
                    url='/customer/submitorder';
                }
                getScriptAjax(url, "GET", {data: data}, function (response) {
                    var code=response.code;
                    var msg=response.msg;
                    if(code===0){
                        loadToastr(0,msg);
                    }
                    if(code===1){
                        var totalFee=response.data.TotalServiceFee.format(0,3,".",",");
                        var notify='Total: '+totalFee+'đ';
                        loadToastr(1,notify);
                    }
                });
            }
            setTimeout(function () {
                dem = 0;
            }, 1000);
        }else {
            loadToastr(0,'Vui lòng điền đầy đủ thông tin!');
        }
    });
    //end submit order


    //load district
    $('.slt-province').change(function () {
       var province_id=$('.slt-province option:selected').val();
       getScriptAjax('/customer/getdistrictbyid',"GET",{idProvince: province_id},function (response) {
           $('.slt-district option').remove();
           $('.slt-district').append(htmlOption(response[province_id],'district'))

       });

    });
    //end load
    // load wards
    $('.slt-district').change(function () {
       var district_id=$('.slt-district option:selected').val();
       getScriptAjax('/customer/getwardbyid',"GET",{idDistrict: district_id},function (response) {
           console.log(response);
           $('.slt-wards option').remove();
           $('.slt-wards').append(htmlOption(response[district_id],'wards'));
       });

    });
    //end load

    //load service
    $(document).on('blur','.slt-district, .weight-order, .long-order, .height-order, .width-order',function () {
        var weight=$("input[name='size']").val();
        var long=$("input[name='long']").val();
        var width=$("input[name='width']").val();
        var height=$("input[name='height']").val();
        var idDistrict=$('.slt-district').val();
        var service={
            "token": "5b06841f1070b07345645cf1",
            "Weight": parseInt(weight),
            "Length": parseInt(long),
            "Width": parseInt(width),
            "Height": parseInt(height),
            "FromDistrictID": parseInt(userDistrict),
            "ToDistrictID": parseInt(idDistrict),
            "CouponCode": ""
        };
        if(weight!==''&&long!==''&&width!==''&&height!==''&&idDistrict!==''){

            $('.service-radio, .label-service').remove();
            getScriptAjaxApi('https://console.ghn.vn/api/v1/apiv3/FindAvailableServices',"POST",JSON.stringify(service),function (response) {
                console.log(response);
                $('.group-service').append(htmlService(response.data));
            });
        }else{
            console.log('wrong');
        }

    });
    //end

    //cancel order
    $(".cancel-order").click(function () {
        var check=confirm("Bạn có chắc muốn hủy đơn hàng!");
        var order_code=$(this).data("order_code");
        var order_id=$(this).data("id");
        if(check==1) {
            getScriptAjax('/order/cancelorder', "GET", {orderCode: order_code,order_id:order_id}, function (response) {
                window.location.reload();
            });
        }
    });
    //end cancel
});
var replyMessage = function () {
    var idSend = $(this).data('id_send');
    var text = $("input[name='message']").val();

    getScriptAjax('/customer/sendmessage',"GET", {data: idSend, text: text}, function (response) {
        var text = $("input[name='message']").val('');
    });
};

var timeLoad = '';
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};
function loadToastr(status, msg) {

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "rtl": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": 300,
        "hideDuration": 1000,
        "timeOut": 5000,
        "extendedTimeOut": 1000,
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    if (status == 1) {
        toastr.success(msg, 'Success');
    } else {
        toastr.error(msg, 'Error');
    }
}
var loadConversation = function (e) {
    e.preventDefault();
    if (typeof timeLoad === 'number') {
        console.log(typeof timeLoad);
        console.log(timeLoad);
        clearInterval(timeLoad);
    }
    var name = $(this).text();
    var id = $(this).data('convid');
    var loadingHtml = ` <div id="loader">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="lading"></div>
        </div>`;
    $('.name-order').val(name);
    $('.id-order').val(id);
    $('.direct-chat-messages').html(loadingHtml);
    $('button.send').attr('data-id_send', id);
    timeLoad = setInterval(function () {
        console.log(id);
        getScriptAjax('/customer/message',"GET", {data: id}, function (reponse) {
            $('.direct-chat-messages').remove('#loader');
            $('.direct-chat-messages').html(htmlChat(reponse));
            var height = 0;
            $('div .direct-chat-msg').each(function (i, value) {
                height += parseInt($(this).height()) + 100;
            });
            height += '';
            $('.card-body').scrollTop(height);

        });
    }, 5000);

};
var getScriptAjax = function (url,method ,options, callback) {
    options = options || {};
    options.data = options.data || {};
    url = server_url + url;
    $.ajax({
        url: url,
        data: options,
        method: method,
        dataType: "json",
        cache: true,
        error: function (response) {

            console.log(response);

        },
        success: function (response) {

            ('function' == typeof callback) && callback(response);
        }

    });
};
var getScriptAjaxApi = function (url,method ,options, callback) {
    options = options || {};
    options.data = options.data || {};
    $.ajax({
        url: url,
        data: options,
        method: method,
        dataType: "json",
        cache: true,
        // ifModified:true,
        error: function (response) {

            console.log(response);

        },
        success: function (response) {

            ('function' == typeof callback) && callback(response);
        }

    });
};

function htmlChat(data) {

    return html`    
            ${data.map(function (item, index) {

        var tmpHtml = '';
        var d = new Date(item.created_time.date);
        var off = d.getTimezoneOffset();
        d.setMinutes(d.getMinutes() - off);

        if (item.from.id !== idPage) {

            tmpHtml += html`
                               <div class="direct-chat-msg">
                        <div class="direct-chat-info clearfix">
                            <span class="direct-chat-name float-left">${item.from.name}</span>
                            <span class="direct-chat-timestamp float-right">${d.toLocaleTimeString('en-US')}</span>
                        </div>
                        <!-- /.direct-chat-info -->
                        <img class="direct-chat-img" src="//graph.facebook.com/${item.from.id}/picture"
                             alt="message user image">
                        <!-- /.direct-chat-img -->
                        <div class="direct-chat-text">
                           ${item.message}
                        </div>
                        <!-- /.direct-chat-text -->
                    </div>
                    <!-- /.direct-chat-msg -->
                               `;
        } else {
            tmpHtml += html`
                               <!-- Message to the right -->
                    <div class="direct-chat-msg right">
                        <div class="direct-chat-info clearfix">
                            <span class="direct-chat-name float-right">${item.from.name}</span>
                            <span class="direct-chat-timestamp float-left">${d.toLocaleTimeString('en-US')}</span>
                        </div>
                        <!-- /.direct-chat-info -->
                        <img class="direct-chat-img" src="//graph.facebook.com/${item.from.id}/picture"
                             alt="message user image">
                        <!-- /.direct-chat-img -->
                        <div class="direct-chat-text">
                            ${item.message}
                        </div>
                        <!-- /.direct-chat-text -->
                    </div>
                               `;

        }
        return tmpHtml;

    }).join('')
        }`
}
function htmlOption(data,type) {

    return html`    
            <option value="">${((type==='district')?'Chọn quận/huyện':'Chọn phường/xã')}</option>
            ${data.map(function (item, index) {
                 var value='';
                    var text='';
                if(type === 'district'){
                     value=item.DistrictID;
                     text=item.DistrictName;
                }
                if(type === 'wards'){
                    value=item.WardCode;
                    text=item.WardName;
                }
                
            var tmpHtml='';
            tmpHtml+=html`<option value="${value}" >${text}</option>`
            return tmpHtml;       
    }).join('')
        }`
}
function htmlService(data) {
        var dem=1;
    return html`  
   
         <label class="label-service">Gói dịch vụ</label> 
            ${data.map(function (item, index) {
                
             var time=new Date(item.ExpectedDeliveryTime);
             
        console.log(time);
        var tmpHtml='';
            tmpHtml+=html`<div class="service-radio"><input type="radio"  ${((dem++===1)?'checked':'')} name="ServiceID" value="${item.ServiceID}">${""+item.Name+"| "+ item.ServiceFee.format(0,3,'.', ',')+"đ| "+ time.toLocaleDateString()} </div>`
            return tmpHtml;       
    }).join('')
        }    
        `
}


function html(literalSections, ...substs) {
    // Use raw literal sections: we don’t want
    // backslashes (\n etc.) to be interpreted
    let raw = literalSections.raw;

    let result = '';

    substs.forEach((subst, i) => {
        // Retrieve the literal section preceding
        // the current substitution
        let lit = raw[i];

    // In the example, map() returns an array:
    // If substitution is an array (and not a string),
    // we turn it into a string
    if (Array.isArray(subst)) {
        subst = subst.join('');
    }

    // If the substitution is preceded by a dollar sign,
    // we escape special characters in it
    if (lit.endsWith('$')) {
        subst = htmlEscape(subst);
        lit = lit.slice(0, -1);
    }
    result += lit;
    result += subst;
});
    // Take care of last literal section
    // (Never fails, because an empty template string
    // produces one literal section, an empty string)
    result += raw[raw.length-1]; // (A)

    return result;
}



