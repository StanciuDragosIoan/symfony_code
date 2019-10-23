$(document).ready(()=>{
    $('.js-like-article').on('click', (e)=>{
        e.preventDefault();
        
        var $link = $(e.currentTarget);
        $link.toggleClass('fa-heart-o').toggleClass('fa-heart'); 

        $.ajax({
            method: 'POST',
            url: $link.attr('href')
        }).done((data)=>{
            $('.js-like-article-count').html(data.hearts);
        });

       
    });
});