var end = false;
var isLoading = false;
var loadingPart = 1;
$(function(){
    $(".submit").click(function(){
        end = false;
        loadingPart = 1;
        var search = $("#search").val();
        startLoadingAnimation();
        $.ajax({
            type: "GET",
            url: "start_search",
            data: {"search": search,
                   "loadPart": 1},
            success: function(response){
                var html = response;
                var check = html.substr(html.length - 4, html.length);
                if (check === ['stop'].toString()){
                    end = true;
                    html = html.substr(0, html.length - 4);
                }
                $(".table-insert").html(html);
                stopLoadingAnimation();
            }
        });
        return false;
    });
});


function startLoadingAnimation()
{
    var imgObj = $("#loadImg");
    imgObj.show();
    var centerY = $(window).scrollTop() + ($(window).height() - imgObj.height())/2;
    var centerX = $(window).scrollLeft() + ($(window).width() - imgObj.width())/2;
    imgObj.offset({top:centerY, left:centerX});
}

function stopLoadingAnimation()
{
    $("#loadImg").hide();
}




function getNewData(){
    loadingPart++;
    var search = $("#search").val();
    startLoadingAnimation();
    $.ajax({
        type: "GET",
        url: "start_search",
        data: {"search": search,
            "loadPart": loadingPart},
        success: function(response){
            var html = response;
            var check = html.substr(html.length - 4, html.length);
            if (check === ['stop'].toString()){
                end = true;
                html = html.substr(0, html.length - 4);
            }
            $(".table-insert").append(html);
            stopLoadingAnimation();
        }
    });
    isLoading = false;
}

$(window).scroll(function(){
    if (isLoading) return false;
    if((($(window).scrollTop()+$(window).height()))>=$(document).height()) {
        isLoading = true;
        if (!end)
            getNewData();
    }
});


/* $("#search").keyup(function(){*/