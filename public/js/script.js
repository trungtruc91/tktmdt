$(function () {
    $('.save-user').click(function () {
        var data=[];
        $('.panel-body .user').each(function () {
            var type=$(this).find('.type').text();
            var postId=$(this).find('.PostID').text();
            var MemberID=$(this).find('.link-face').attr('href').split('/').pop();
            var username=$(this).find('.full-name').text();
            var comment=$(this).find('.question .text').text();

            var document={
                'TypeInteract':type,
                'PostID':postId,
                'MemberID':MemberID,
                'Username':username,
                'Comment':comment
            };
            data.push(document);

        });
        getScriptAjax('/interact/submitmember','post',{arrData:data},function (response) {
            if(response.code===1){
                loadToastr(1,'Success!');
            }
        });
    });

});