jQuery(document).ready(function($) {
    var pageNumber = 1;
    var loading = false;

    // Lyssna på klick på <a>-länken inuti #skk-load-posts
    $('#skk-load-posts a').on('click', function(e) {
        e.preventDefault();

        if (loading) return;
        loading = true;

        var $link = $(this);
        var originalText = $link.text();
        $link.text('Laddar...');

        $.ajax({
            url: '/wp-content/themes/heisenberg/loadmore.php',
            type: 'GET',
            data: {
                pageNumber: pageNumber,
                numPosts: 5, // Antal inlägg som hämtas per klick
                categoryName: ''
            },
            success: function(response) {
                if ($.trim(response) !== '') {
                    // Lägg till de nya inläggen precis före knappen/paragrafen
                    $('#skk-load-posts').before(response);
                    pageNumber++;
                    $link.text(originalText);
                    loading = false;
                } else {
                    // Inga fler inlägg finns att hämta
                    $link.text('Inga fler inlägg').addClass('disabled');
                    $('#skk-load-posts').fadeOut(2000);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Load More Error:', error);
                $link.text(originalText);
                loading = false;
            }
        });
    });
});