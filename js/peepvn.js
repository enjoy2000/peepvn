$j = jQuery.noConflict();
//init menu
function menuInit(){
	$j('ul.navSub').prepend('<li class="multiMenu daily-deal-menu"><a href="http://www.lazada.vn/mega-deals/" title="Mega deals"><span class="navSubTxt">Mega deals <div class="sbm-bge"><div>SALE</div></div></span></a></li>');
}

/////////////////////////////////////
// INIT MENU * DO NOT CHANGE      //
///////////////////////////////////\
$j(document).ready(function(){
	menuInit();
	
	//main menu on hover
	$j('ul.navSub li').hover(function(){
		$j('.navLayer.sbnyl', this).stop(true,true).css('top',$j(this).index()*(-1)*($j(this).height()+2)).animate({'opacity':1},200);
	},
	function(){
		$j('.navLayer.sbnyl', this).stop(true,true).delay(300).css('top', -1000).animate({'opacity':0},200);
	});
	
	//top tooltip toogle function
	$j('div.toggle-button').click(function(){
		if($j(this).hasClass('active')){
			$j(this).parent().find('.toggle-content .tip').slideUp(100);
			$j(this).removeClass('active');
		}else{
			$j('.toggle-content .tip').slideUp(100);
			$j('div.toggle-button').removeClass('active');
			$j(this).parent().find('.toggle-content .tip').slideDown(100);
			$j(this).addClass('active');
		}
	});
	//hide all tip when click outside
	$j(document).mouseup(function (e)
	{
	    var container = $j(".hsbhb");
	
	    if (!container.is(e.target) // if the target of the click isn't the container...
	        && container.has(e.target).length === 0) // ... nor a descendant of the container
	    {
	        container.find('.tip').slideUp(100);
	    }
	});
});
