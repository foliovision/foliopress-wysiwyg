<?php
require_once('initialise.php');
$kfm->requireAdmin(true);
//if($kfm->user_status!=1)die ('error("No authorization aquired")');
require_once('functions.php');
$users=db_fetch_all('SELECT * FROM '.KFM_DB_PREFIX.'users WHERE id>1');
if(!is_array($users))die ('error retrieving user list');
$lhtml='<table id="kfm_admin_users_table">
<thead>
	<tr>
		<th>Name</th>
		<th></th>
		<th>Status</th>
	</tr>
</thead>
<tbody>
';
foreach($users as $user){
	$lhtml.=user_row($user['id'],$user['username'],$user['status']);
}
$lhtml.="</tbody>\n</table>\n";
//if(count($users)==0)$lhtml='<span class="message">No users found</span>';

?>
<script type="text/javascript">
/* User functions */
function new_user(){
	input_props='size=12 maxsize=16';
	fc='<h3>New user</h3>';
	fc+='Username <input type="text" id="newuser_username" '+input_props+'/><br/>';
	fc+='Password <input type="password" id="newuser_password" '+input_props+'/>';
	$.prompt(fc,{
		buttons:{cancel:false, OK:true},
		callback:function(v,m){
			if(v){
				un=m.children('#newuser_username').val();
				pw=m.children('#newuser_password').val();
				if(un==''){
					$.prompt('Username can not be empty');
					return;
				}
				$.post('user_new.php',{username:un,password:pw},function(res){eval(res);});
			}
		}
	});
}
function delete_user(uid, username){
	$.prompt('Are you sure you want to delete user '+username+'?',{
		buttons:{cancel:false,'I am sure':true},
		callback:function(v,m){
			if(v) $.post('user_delete.php',{uid:uid},function(res){eval(res);});
		}
	});
}
var testerbj = null;
function edit_user_settings(uid, username){
  $.post('settings.php',{uid:uid, ismodal:1}, function(data){
     $('<div title="Settings for '+username+'">'+data+'</div>').dialog({
        modal:true,
        width:800,
        close: function(event, ui){
          $(this).parents('.ui-dialog').empty();
        }
     });
     /*$.prompt(data,{
       prefix:'jqisettings'
     });
     */
  });
}
function user_status_change(uid, status){
	$.post('user_status_change.php',{uid:uid,status:status},function(res){eval(res);});
}
function password_reset(uid, username){
	var input_props='size=12 maxsize=16';
	var fc='<h3>New password for user '+username+'</h3>';
	fc+='<h3 id="newpass_errors" style="color:red;"></h3>';
	fc+='<input type="password" id="newpass" '+input_props+'/><br/>';
	fc+='<input type="password" id="newpass2" '+input_props+'/>';
	$.prompt(fc,{
		buttons:{cancel:false,"Reset pass":true},
		callback:function(v,m){
			if(v){
				np=m.children('#newpass').val();
				np2=m.children('#newpass2').val();
				$.post('password_change.php',{uid:uid,npw:np,npw2:np2},function(res){eval(res);});
			}
		},
		submit:function(v,m){
			if(v){
				np=m.children('#newpass').val();
				np2=m.children('#newpass2').val();
				if(np!=np2){
					m.children('#newpass_errors').text('The two passwords do not match');
					return false;
				}
			}
			return true;
		}
	});
}
</script>
<?php echo $lhtml;?>
<br />
<span class="button" onclick="new_user()">New user</span>
<br />
