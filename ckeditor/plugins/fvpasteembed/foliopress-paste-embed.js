
CKEDITOR.dialog.add( 'foliopress-paste-embed', function( editor ) {
   return {
      title : 'My IFrame Plugin', minWidth : 390, minHeight : 230,
      contents : [ {
            id : 'tab1', label : '', title : '', expand : true, padding : 0,
            elements : [ {
                   type : 'iframe',
                   src : editor.config.RootPath + 'foliopress-paste-embed.html',
                   width : 538, height : 478 - (CKEDITOR.env.ie ? 10 : 0)
            } ]
      } ]
      , buttons : []   // don't show the default buttons
   };
} );


