<?php
require_once('initialise.php');
require_once('functions.php');
if(!($kfm->isAdmin() || $kfm->setting('allow_user_file_associations'))) die ('Users are not allowed to create their own file associations');
//$kfm->requireAdmin();
//if($kfm->user_status!=1)die ('No authorization to view this page');
print 'There are '.count($kfm->plugins).' plugins available';
$uid=$kfm->user_id;
$extensions=db_fetch_all('SELECT * FROM '.KFM_DB_PREFIX.'plugin_extensions WHERE user_id='.$uid);
?>
<script type="text/javascript">
function new_association(){
	input_props='size=12 maxsize=16';
	fc='<h3>New extension</h3>';
	fc+='Extension <input type="text" id="new_association_extension" '+input_props+'/> ';
	fc+='<?php echo get_plugin_list('no_default','0222');?>';
	//fc+='Plugin <input type="text" id="new_association_plugin" '+input_props+'/>';
	$.prompt(fc,{
		buttons:{cancel:false, OK:true},
		callback:function(v,m){
			if(v){
				ext=m.children('#new_association_extension').val();
				plugin=m.children('#plugin_selector_0222').val();
				if(ext==''){
					$.prompt('Extension can not be empty');
					return;
				}
				$.post('association_new.php',{extension:ext,plugin:plugin},function(res){eval(res);});
			}
		}
	});
}
function change_association_plugin(id){
	var newval=$("#plugin_selector_"+id).val();
	$.post('association_change_plugin.php',{aid:id,plugin:newval},function(res){eval(res);});
}
function association_delete(id){
	$.prompt('Are you sure you want to delete this association?',{
		buttons:{cancel:false, 'Yes':true},
		callback:function(v,m){
			if(v){
				$.post('association_delete.php',{aid:id},function(res){eval(res);});
			}
		}
	});
}
function association_extension_change(id){
	var newext=$("#association_extension_"+id).val();
	$.post('association_extension_change.php',{aid:id,extension:newext},function(res){eval(res);})
}
function rand (n){
	  return (Math.floor(Math.random()*n+1));
}
</script>
<div id="associations_container">
<table id="association_table">
<thead>
	<tr>
		<th>Extension</th>
		<th>Plugin</th>
		<th></th>
	</tr>
</thead>
<tbody>
<?php
foreach($extensions as $ext){
	echo get_association_row($ext['extension'],$ext['plugin'],$ext['id']);
}
?>
</tbody>
</table>
<br/>
<span class="button" onclick="new_association()">New</span>
</div>
