(function(){
    var fvpasteembedCmd={
        exec:function(editor){
            editor.openDialog('fvpasteembed');
            return
        }
    };
CKEDITOR.plugins.add('fvpasteembed',{
    lang:['en','uk'],
    requires:['dialog'],
    init:function(editor){
        var commandName='fvpasteembed';
        editor.addCommand(commandName,fvpasteembedCmd);
        editor.ui.addButton('Fvpasteembed',{
            label:editor.lang.fvpasteembed.button,
            command:commandName,
            icon:this.path+"images/icon.png"
            });
        CKEDITOR.dialog.add(commandName,CKEDITOR.getUrl(this.path+'dialogs/fvpasteembed.js'))
        }
    })
})();
