var server_url = '/face';
$(function () {
    $('.product-info a').click(function (e) {
      e.preventDefault();
      var id=$(this).data('convid');
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
        console.log(id);
        getScriptAjax('/index/message',{data:id},function (reponse) {

            console.log($('.direct-chat-messages').html(htmlChat(reponse)));;
      });
    });



});
var getScriptAjax = function (url, options, callback) {
    options = options || {};
    options.data = options.data || {};
    url = server_url + url;
    $.ajax({
        url: url,
        data: options,
        dataType: "json",
        cache: true,
        error:function(response){
            console.log(response);
        },
        success: function (response) {

            ('function' == typeof callback) && callback(response);
        }
    });
};
function htmlChat(data) {
    return html`    
            ${data.map(function(item,index){
                var tmpHtml='';
                           if(item.from.id !== "871090909744554" ){
                               tmpHtml+=html`
                               <div class="direct-chat-msg">
                        <div class="direct-chat-info clearfix">
                            <span class="direct-chat-name float-left">${item.from.name}</span>
                            <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
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
                           }else{
                               tmpHtml += html`
                               <!-- Message to the right -->
                    <div class="direct-chat-msg right">
                        <div class="direct-chat-info clearfix">
                            <span class="direct-chat-name float-right">${item.from.name}</span>
                            <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
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

