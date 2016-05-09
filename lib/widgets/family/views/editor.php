<script>
	var editor;
	KindEditor.ready(function(K) {
		editor = K.create('textarea[name="<?php echo $name;?>"]', {
			resizeType : 1,
			allowPreviewEmoticons : false,
			allowImageUpload : false,
			items : [
						'emoticons',
//						'image'
					]
		});
	});
</script>