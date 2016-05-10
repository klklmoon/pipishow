/*
 * author: blueflu
 */
$.fn.extend({   
	selected:function (s)
	{
		var tmp = s.split(',');
		for(i=0; i<this[0].length; i++)
		{
			for(j=0; j<tmp.length; j++) {
				if (this[0][i].value == tmp[j]) {
					this[0][i].selected = true;
					break;
				} else {
					this[0][i].selected = false;
				}
			}
		}
	},
	
	selectAll:function (s)
	{
		for(i=0; i<this[0].length; i++)
		{

			this[0][i].selected = s;
		}
	},
	
	checked:function (s)
	{
		$(this).each(function (){
			$(this)[0].checked = (s.indexOf($(this)[0].value) >= 0);
		});
	},
	
	checkAll:function (s, bt_id)
	{
		$(this).each(function (){
			$(this)[0].checked = s;
		});
		if (bt_id) {
			$(bt_id).each(function (i) {
				$(bt_id)[i].disabled = !s;
			})
		}
	},
	
	checkItem:function (s, bt_id)
	{
		var flag = true;
		var bt_flag = false;
		$('input[name="'+this[0].name+'"]').each(function (){
			if (!this.checked) {
				flag = false;
			} else {
				bt_flag = true;	
			}
		});
		
		if (bt_id) {
			if (bt_flag) { 
				$(bt_id).each(function (i) {
					$(bt_id)[i].disabled = false;
				})
			} else {
				$(bt_id).each(function (i) {
					$(bt_id)[i].disabled = true;
				})
			}
		}
		$(s)[0].checked = flag;
	}
});