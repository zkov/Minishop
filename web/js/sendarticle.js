$(function() {     
    $(".listbutton").click(function(){
        var id = this.id;
        var name = $('#name'+id).text();
        var price = $('#price'+id).text();
        var quantity = $('#quantity'+id).text();
        $.ajax({type:'post',
                url: '/addtocart',               
                data: {'id': id, 'name':name, 'price':price, 'quantity': quantity}, 
                dataType: 'json',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                success: function(response){
                    $('#quantity'+response.id).text(response.quantity);
                    if ($('#quantity'+response.id).text() < 1){
                        $('#div'+response.id).hide();
                    }
                }
          
        });    
});
});