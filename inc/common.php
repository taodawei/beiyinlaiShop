<?php
define('REGED',false);
function ewebeditor($style,$name,$content='',$width='1104')
{
	$editorId = $name;
	switch($style)
	{
		case 'kindeditor':
		    echo  '<textarea id="'.$name.'" name="'.$name.'" cols="100" rows="8" style="width:'.$width.'px;height:400px;">'.$content.'</textarea>
<script>
        var editor;
        KindEditor.ready(function(K) {
           editor = K.create("#'.$name.'",{allowFileManager : true});
        });
</script>';
			
			break;
		case 'ueditor':
			$toolbar = array(['fullscreen', 'source', '|', 'undo', 'redo', '|',
            'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
            'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
            'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
            'directionalityltr', 'directionalityrtl', 'indent', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
            'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
            'simpleupload', 'insertimage', 'emotion', 'scrawl', 'insertvideo', 'music', 'map','insertcode','pagebreak','|',
            'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
            'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
            'preview', 'searchreplace', 'help', 'drafts']);
			
			$config = json_encode(array('initialFrameHeight'=>400,'toolbars'=>$toolbar));
			echo '<textarea id="'.$editorId.'" rich-js  name="'.$editorId.'" style="width:'.$width.'px;height:400px;">'.$content.'</textarea>';
			echo <<<EOT
			<script>
			window.UEDITOR_CONFIG.serverUrl = '/erp/index.php?m=system&s=ueditor&a=server';
			
			UE.delEditor("{$editorId}");
			var ue_{$editorId} = UE.getEditor('{$editorId}', {$config});
			</script>
EOT;
			break;
	}
}

?>