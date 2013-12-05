(function($){
	$(function(){
		$(".button.btn-cart").each(function(){
			var onclick = String(this.onclick);
			this.onclick = function(){};
			var url = onclick.match(/\('(.+)'\)/)[1]
			var match = url.match(/\/product\/(\d+)\/form_key\/(.+)\//)
			var product = match[1];
			var form_key = match[2];
			$(this).click(function(){
				$.ajax({
					url : BASE_URL + 'ajaxaddtocart/cart/add',
					data: {product: product, form_key: form_key},
					success: function(data){
						if(parseInt(data) == 1){
							$.ajax({
								url : BASE_URL + 'ajaxaddtocart/cart/index',
								success: function(cart){
									alert(cart);
								}
							});
						}
					}
				});
			});
		});
	});
})(jQuery);