(function() {
	//var selected = false;
	var caption = new Object();
	var align = false;

	var fvCaption = {
		exec : function(editor) {
			align = Math.floor(Math.random() * 11)
			var dialog = editor.openDialog('FVCaptionAlign');
			selected = false;
			return;

			var selection = editor.getSelection();

			if (CKEDITOR.env.ie) {
				selection.unlock(true);
			}
			var el = selection.getStartElement();

			if (el.is('div') && el.hasClass('wp-caption')) {
				alert('ie-div');
				var caption = el;
				var selected = true;
			}
			if (!selected) {
				var parent = el.getParent();
				if (parent.is('div') && parent.hasClass('wp-caption')) {
					alert('par-div');
					var caption = parent;
					var selected = true;
				}
			}
			if (!selected) {
				var wrap = parent.getParent();
				if (wrap.hasClass('wp-caption')) {
					alert('wrap-div');
					var caption = wrap;
					var selected = true;
				}
			}
			if (selected) {
				var align = caption.getAttribute('class').replace('wp-caption', '').replace(/\s*/, '');
			}

			new editor.openDialog('FVCaptionAlign');
			selected = false;
			return;

			/*
			var parent = el.getParent();

			//console.log(selection);
			//console.log(el);
			console.log(parent);

			if(parent.is('div')) {
			alert ('div');
			} else {
			alert ('look for parent');
			}

			var wrap = parent.getParent();
			if(wrap.hasClass('wp-caption')) {
			alert('bingo');
			}
			if(wrap.hasClass('alignright')) {
			alert('bingo 2');
			wrap.removeClass( 'alignright' )
			wrap.addClass( 'aligncenter' )

			}

			console.log(wrap);
			*/
			// editor.openDialog('FVCaptionAlign');
			//return;

			//var mySelection = editor.getSelection().getSelectedElement();
			//console.log(mySelection);
			//mySelection.deleteFromDocument();
			/*
			if (CKEDITOR.env.ie) {
			mySelection.unlock(true);
			selectedText = mySelection.getNative().createRange().text;
			} else {
			selectedText = mySelection.getNative();
			}

			//var node = editor.document.getBody().getSelection();
			console.log(selectedText);
			*/
			//console.log(sel.selectElement(sel.getStartElement()));
			//var node = editor.document.getBody().getFirst();
			//var parent = node.getParent();
			//alert( node.getName() );  // "body"
		}
	};
	CKEDITOR.plugins.add('fvcaption', {

		requires:['dialog', 'iframedialog'],
		init : function(editor) {

			CKEDITOR.dialog.add('FVCaptionAlign', function() {

				return {
					title : 'FV Align WordPress Media',
					minWidth : 400,
					minHeight : 150,
					contents : [{
						id : 'fvcaption',
						label : 'FV Align WordPress Media',
                                                style : 'width :100%; text-align:center;',
						expand : true,
						elements : [{
							type : 'html',
                                                        html: '<br />'
							//html : '<table cellpadding="5" style="width: 80%; "><tbody><tr ><td width="20%" nowrap="nowrap"><strong>Alignment</strong></td><td width="20%" nowrap="nowrap"><input  name="aligment_radio" value="alignnone" id="alignnone" type="radio"><label id="alignnone_label" for="alignnone"> <img src="'+CKEDITOR.plugins.get('fvcaption').path+'images/align-none.png" alt="None" align="middle"/> None</label></td>  <td width="20%" nowrap="nowrap"><input  name="aligment_radio" value="alignleft" id="alignleft" type="radio"><label id="alignleft_label" for="alignleft">Left</label></td>  <td width="20%" nowrap="nowrap"><input  name="aligment_radio" value="aligncenter" id="aligncenter" type="radio"><label id="aligncenter_label" for="aligncenter">Center</label></td>  <td width="20%" nowrap="nowrap"><input  name="aligment_radio" value="alignright" id="alignright" type="radio"><label id="alignright_label" for="alignright">Right</label></td></tr></tbody></table>'
						}, {
							type : 'radio',
							id : 'aligment',
							//style : 'width :80%',
							items : [['<img src="'+CKEDITOR.plugins.get('fvcaption').path+'images/align-none.png" alt="None" style="vertical-align:middle;" /> None', 'alignnone'], 
                                                                 ['<img src="'+CKEDITOR.plugins.get('fvcaption').path+'images/align-left.png" alt="Left" style="vertical-align:middle;" /> Left', 'alignleft'], 
                                                                 ['<img src="'+CKEDITOR.plugins.get('fvcaption').path+'images/align-center.png" alt="Center" style="vertical-align:middle;" /> Center', 'aligncenter'], 
                                                                 ['<img src="'+CKEDITOR.plugins.get('fvcaption').path+'images/align-right.png" alt="Right" style="vertical-align:middle;" /> Right', 'alignright']],
							onClick : function() {
								// this = CKEDITOR.ui.dialog.radio
								// alert('Current value: ' + this.getValue());
							}
						}]
					}, {
						id : 'fvcaption_none',
						label : 'FV Align WordPress Media',
						style : 'background-color: yellow; font-size: 20px; color: red; padding: 12px; font-weight: bold; height: 100%',
						hidden : true,
						elements : [{
							type : 'html',
							id : 'fvcaption_none_html',
							style : 'background-color: yellow; font-size: 20px; color: red; padding: 12px; font-weight: bold; height: 100%',
							html : '<p>No WordPress media is selected<br /><br />Please, click on image which you want to change aligment first.</p>'
						}]
					}],

					onShow : function() {

						var elem = this.getParentEditor().getSelection().getSelectedElement();
						//get the current selected element
						this.setupContent(elem);

						var selected = false;
						var selection = editor.getSelection();

						if (CKEDITOR.env.ie) {
							selection.unlock(true);
						}
						var el = selection.getStartElement();

						if (el.is('div') && el.hasClass('wp-caption')) {
							//console.log('ie-div');
							caption = el;
							selected = true;
						}
						if (!selected) {
							var parent = el.getParent();
							if (parent.is('div') && parent.hasClass('wp-caption')) {
								//console.log('par-div');
								caption = parent;
								selected = true;
							}
						}
						if (!selected) {
							var wrap = parent.getParent();
							if (wrap.hasClass('wp-caption')) {
								//console.log('wrap-div');
								caption = wrap;
								selected = true;
							}
						}

						//var elem = this.getParentEditor().getSelection().getSelectedElement();
						//get the current selected element
						//this.setupContent(elem);

						if (selected) {
							align = caption.getAttribute('class').replace('wp-caption', '').replace(/\s*/, '');
							if (align.lenght < 2)
								align = 'alignnone';
							this.setValueOf('fvcaption', 'aligment', align);

							//var lable = jQuery(":radio[value=alignnone]").attr('aria-labelledby');
							//jQuery('#' + lable).addClass('align image-align-none-label');
							jQuery('#' + this.getButton('ok').domId).show();
                                                        //jQusery('#'+align).attr('checked', true);
							//document.getElementById(this.getButton('ok').domId).style.display = 'inline-block';

						} else {
							//this.disableButton("ok");
							//document.getElementById(this.getButton('ok').domId).style.display = 'none';
							jQuery('#' + this.getButton('ok').domId).hide();
							this.hidePage('fvcaption');
							this.selectPage('fvcaption_none');
						}
					},

					onOk : function() {
						if (align.lenght > 5) {
							align = '';
						}
						if (this.getValueOf('fvcaption', 'aligment').length > 4) {
							var new_align = this.getValueOf('fvcaption', 'aligment');
						} else
							new_align = '';

						//var elem = this.getParentEditor().getSelection().getSelectedElement();
						//get the current selected element
						//this.setupContent(elem);

						var selected = false;
						var selection = editor.getSelection();

						if (CKEDITOR.env.ie) {
							//console.log(caption);
							caption.removeClass(align);
							caption.addClass(new_align);
							selected = true;
							//selection.unlock(true);
						} else {
							var el = selection.getStartElement();

							if (el.is('div') && el.hasClass('wp-caption')) {
								el.removeClass(align);
								el.addClass(new_align);
								selected = true;
							}
							if (!selected) {
								var parent = el.getParent();
								if (parent.is('div') && parent.hasClass('wp-caption')) {
									parent.removeClass(align);
									parent.addClass(new_align);
									selected = true;
								}
							}
							if (!selected) {
								var wrap = parent.getParent();
								if (wrap.hasClass('wp-caption')) {
									wrap.removeClass(align);
									wrap.addClass(new_align);
									selected = true;
								}
							}
						}

						//console.log (align);
						//console.log (caption);
						//alert(this.getValueOf('fvcaption', 'aligment'));

					}
				};
			});

			var commandName = 'fvcaption';

			//editor.addCommand(commandName,fvCaption);

			editor.ui.addButton('fvcaption', {
				label : 'FV Align WordPress Media',
				command : commandName,
				icon : this.path + "images/align.png"
			});
			editor.addCommand(commandName, new CKEDITOR.dialogCommand('FVCaptionAlign'));

		}
	})
})();
