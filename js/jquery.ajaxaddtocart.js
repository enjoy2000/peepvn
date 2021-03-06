(function($){
	$(function(){
		$(".button.btn-cart").each(function(){
			var onclick = String(this.onclick);
			this.onclick = function(){};
			try{
				var url = onclick.match(/\('(.+)'\)/)[1];
				var match = url.match(/\/product\/(\d+)\/form_key\/(.+)\//);
				var product = match[1];
				var form_key = match[2];
				$(this).click(function(){
					$j.fancybox.showLoading();
					$.ajax({
						url : BASE_URL + 'ajaxaddtocart/cart/add',
						data: {product: product, form_key: form_key},
						success: function(data){
							if(parseInt(data) != 0){
								$('a#hdCartLink').text('('+data+')');
								$('#hdCartLink').click();
							}
						}
					});
				});
			}
			catch(e){
				try{
					var url = this.form.action;
					var match = url.match(/\/product\/(\d+)\/form_key\/(.+)\//);
					var product = match[1];
					var form_key = match[2];
					$(this).click(function(){
						var data = $(this.form).serialize();	
						$j.fancybox.showLoading();
						$.ajax({
							url : BASE_URL + 'ajaxaddtocart/cart/add?product=' + product + "&form_key=" + form_key,
							data: data,
							success: function(data){
								if(parseInt(data) != 0){
									$('#hdCartLink').click();
									$('a#hdCartLink').text('('+data+')');
								}
							}
						});
					});
				}
				catch(e){
					
				}
			}
		});		
	});
})(jQuery);