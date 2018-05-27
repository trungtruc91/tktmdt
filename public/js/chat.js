var server_url = '/face';

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
        console.log(dem);
        if (dem === 1) {
            var data = $("form :input").serializeArray();
            getScriptAjax('/index/submitorder', {data: data}, function (response) {
                console.log(response);
            });
        }
        setTimeout(function () {
            dem = 0;
        }, 1000);
    });
    //end submit order
    //load district
    $('.slt-province').change(function () {
       var province_id=$('.slt-province option:selected').val();
       getScriptAjax('/index/getdistrictbyid',{idProvince: province_id},function (response) {
           $('.slt-district option').remove();
           $('.slt-district').append(htmlOption(response[province_id],'district'))

       });

    });
    //end load
    // load wards
    $('.slt-district').change(function () {
       var district_id=$('.slt-district option:selected').val();
       getScriptAjax('/index/getwardbyid',{idDistrict: district_id},function (response) {
           console.log(response);
           $('.slt-wards option').remove();
           $('.slt-wards').append(htmlOption(response[district_id],'wards'));
       });

    });
    //end load
});
var replyMessage = function () {
    var idSend = $(this).data('id_send');
    var text = $("input[name='message']").val();

    getScriptAjax('/index/sendmessage', {data: idSend, text: text}, function (response) {
        var text = $("input[name='message']").val('');
    });
};
var timeLoad = '';
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
        getScriptAjax('/index/message', {data: id}, function (reponse) {
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
var getScriptAjax = function (url, options, callback) {
    options = options || {};
    options.data = options.data || {};
    url = server_url + url;
    $.ajax({
        url: url,
        data: options,
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

