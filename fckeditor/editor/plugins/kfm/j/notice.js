var Notice=new Class({
	getWrapper:function(){
		var w=document.getElementById('notice_wrapper');
		if(w)return w;
		w=document.createElement('div');
		w.id='notice_wrapper';
		w.style.position='absolute';
		w.style.top=5+'px';
		w.style.right=5+'px';
		w.style.zIndex=222;
		document.body.appendChild(w);
		return w;
	},
	initialize:function(message){
		var id=_Notices++;
		this.id=id;
		var notice_message=document.createElement('div');
		notice_message.id='notice_message_'+id;
		notice_message.className='notice';
		$jn=$j(notice_message);
		$jn.css({opacity:0});
		notice_message.innerHTML=message;
		this.getWrapper().appendChild(notice_message);
		$jn.animate({ opacity:1},50,'linear',function(){
			$jn.animate({ opacity:0},50,'linear',function(){
				$jn.animate({ opacity:1},50,'linear',function(){
					$jn.animate({ opacity:0},50,'linear',function(){
						$jn.animate({ opacity:1},1000,'linear',function(){
							$jn.animate({ opacity:1 },4000,'linear',function(){
								$jn.animate({ height:0,opacity:0 },500,'linear',function(){
									$jn.remove();
								});
							});
						});
					});
				});
			});
		});
	}
});
var _Notices=0;
