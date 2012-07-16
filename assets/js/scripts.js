// globals

var sImageContainer, oDataRows, sImageHeader, textContentContainer = '';
var filenameContainer, dateContainer, filesizeContainer, fileownerContainer = new Array();
var urlContainer = '';
var base_url = 'http://www.chabs.com/filemanager/';

var global_url, global_file, global_owner = '';

// special functions
function setCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
if (!Array.prototype.filter)
{
  Array.prototype.filter = function(fun /*, thisp*/)
  {
    var len = this.length;
    if (typeof fun != "function")
      throw new TypeError();

    var res = new Array();
    var thisp = arguments[1];
    for (var i = 0; i < len; i++)
    {
      if (i in this)
      {
        var val = this[i]; // in case fun mutates this
        if (fun.call(thisp, val, i, this))
          res.push(val);
      }
    }

    return res;
  };
}
	
$(document).ready(function (){

	// declare jquery ui buttons
	
	// end of buttons
	 $('a.btn-ok, #dialog-overlay, #dialog-box').click(function () {    
        $('#dialog-overlay, #dialog-box').hide();      
        return false;
    });
     
    // if user resize the window, call the same function again
    // to make sure the overlay fills the screen and dialogbox aligned to center   
    $(window).resize(function () {
         
        //only do it if the dialog box is not hidden
        if (!$('#dialog-box').is(':hidden')) popup();      
    });
     

	
	$("#dialog:ui-dialog").dialog("destroy");

	$("#dialog-view-image").dialog({
        autoOpen: false,
        modal: true,
		height: 600,
		width: 600,
		buttons: {
			OK: function ()
			{
				$(this).dialog("close");
			}
		}
	});
	
	
	// check type of creation (Directory/File)
	
	$("#dialog-create-file").dialog({
        autoOpen: false,
        modal: true,
		height: 340,
		width: 350,
		buttons: {
			Save: function ()
			{
				var file_name = $("#file_name").val();
				file_name = file_name.replace(" ", "+");
				var directory = getCookie("current_dir");
				var create_type = $("input[name='create_selection']:checked").val();
				if(file_name != '')
				{
					if(create_type == "txt")
					{
						$.ajax({
							url: base_url + "index.php/central_controller/createNewTextFile/?dir=" + directory + "&name=" + file_name,
							type: 'GET'
						});
						alert('Text File Created!');
						$(this).dialog("close");
						location.reload();
						
					}
					else if(create_type == "direc")
					{
						$.ajax({
							url: base_url + "index.php/central_controller/createNewDirectory/?dir=" + directory + "&name=" + file_name,
							type: 'GET',
							dataType: 'json',
							success: function(data)
							{
								if(data > 0)
								{
									alert('Please type another folder name!');
								}
								else
								{
									alert('Directory Created!');
									$(this).dialog("close");
									location.reload();
								}
							}
						});
			
					}
					
				}
				else
				{
					alert('Please provide a name');
				}
				
				//$(this).dialog("close");
			},
			Cancel: function ()
			{
				$(this).dialog("close");
				$("#file_name").val('');
			}
		}
	});
	
	$('#dialog-create-file').bind('dialogclose', function(event) {
		$("#file_name").val('');
	});

	// Edit Text File
	
	$("#dialog-text-contents").dialog({
        autoOpen: false,
        modal: true,
		height: 400,
		width: 600,
		buttons: {
			Save: function ()
			{
				var file_url = $("#text_file_url").val();
				var file_name = $("#text_file_name").val();
				var file_owner = $("#text_file_owner").val();
				var textContents = $("#text_data").val();
				textContents = textContents.split("\n").join("\\n");
				if(file_owner == 'daemon')
				{
					$.ajax({
						url: base_url + "index.php/central_controller/writeTextContents/?insert_text=1&url=" + file_url + "&file=" + file_name,
						type: 'GET',
						data: "text="+textContents
					});
					alert('Edit Successful');
					$(this).dialog("close");
					parent.location.reload();
					
				}
				else
				{
					alert('Sorry you do not own this File');
				}
				
				$(this).dialog("close");
			},
			Cancel: function ()
			{
				$(this).dialog("close");
			}
		}
	});
	
	$("#create").click(function (){
		$("#dialog-create-file").dialog("open");
	});
	
	$("#create_file").click(function () {
	   $(".type").html("(.txt)");
	});

	$("#create_direc").click(function () {
	   $(".type").html("(Folder)");
	});
	
	$('#files').click(function(){ 
		if($(this).attr("checked"))
		{
			$('input[name="file[]"]').attr("checked", "checked");
		}
		else
		{
			$('input[name="file[]"]').removeAttr("checked");
		}
	});



	
	var radios = $('input:radio[name=create_selection]');
    if(radios.is(':checked') === false) {
        radios.filter('[value=txt]').attr('checked', true);
	}
	
	
	$("#dialog-confirm-delete").dialog({
        autoOpen: false,
        modal: true,
		height: 'auto',
		width: 380,
		buttons: {
			Delete: function ()
			{
				var selected_files = new Array();
				$('td :checked').each(function() {
					selected_files.push($(this).val());
				});
				var file_owner = $("#text_file_owner").val();
				
					$.ajax({
					url: base_url + "index.php/central_controller/deleteFile/?delete=" + selected_files,
					type: 'GET'
					});
					$('input[type=checkbox]').attr('checked',false);
					$(this).dialog("close");
					alert('Delete Successful');
					parent.location.reload();
					
				
					$(this).dialog("close");
					$('input[type=checkbox]').attr('checked',false);
				
			},
			Cancel: function ()
			{
				$(this).dialog("close");
			}
		}
	});
	
	
	
	$("#delete").click(function () {
		var selected_files = new Array();
		$('td :checked').each(function() {
			selected_files.push($(this).val());
		});
		if(selected_files.length < 1)
		{
			alert('Please select a file/s');
		}
		else
		{
			$("#dialog-confirm-delete").dialog("open");
		}
	});
	
	$("#dialog-rename").dialog({
        autoOpen: false,
        modal: true,
		height: 360,
		width: 'auto',
		buttons: {
			Save: function ()
			{
				var new_file_name = $("#new_file_name").val();
				var old_file_name;
				var file_owner = $("#text_file_owner").val();
				if(new_file_name != '')
				{
					var selected_files = new Array();
					$('td :checked').each(function() {
						selected_files.push($(this).val());
					});
					
					$.each(selected_files, function(index, element){
						old_file_name = element;
					})
					
					
						$.ajax({
						url: base_url + "index.php/central_controller/renameFile/?rename=1" + "&old_name=" + old_file_name + "&new_name=" + new_file_name + "&current_dir=" + getCookie("current_dir"),
						type: 'GET'
						});
						alert('Rename Successful');
						$(this).dialog("close");
						$('input[type=checkbox]').attr('checked',false);
						parent.location.reload();
					
					
						$(this).dialog("close");
						$('input[type=checkbox]').attr('checked',false);
					
				}
				else
				{
					alert('Please provide a name');
				}
				
				//$(this).dialog("close");
			},
			Cancel: function ()
			{
				$(this).dialog("close");
				$("#new_file_name").val('');
			}
		}
	});
	
	$('#dialog-rename').bind('dialogclose', function(event) {
		$("#new_file_name").val('');
	});

	$("#rename").click(function () {
		var selected_files = new Array();
		$('td :checked').each(function() {
			selected_files.push($(this).val());
		});
		if(selected_files.length == 1)
		{
			$("#dialog-rename").dialog("open");
			$.each(selected_files, function(index, element){
				$("ul#old_name").html("<li>"+element+"</li>");
			})
			
		}
		else if(selected_files.length < 1)
		{
			alert('Please select a file/s');
		}
		else if(selected_files.length > 1)
		{
			alert('You are can only rename 1 at a time');
			$('input[name="file[]"]').removeAttr("checked");
		}
	});
	
	$("#dialog-upload-file").dialog({
        autoOpen: false,
        modal: true,
		height: 'auto',
		width: 400,
		buttons: {
			Upload: function ()
			{
				var upload_file = $("#file_upload").val();
				var directory_owner = $("#text_file_owner").val();
				var data = new FormData();
				var current_dir = getCookie("current_dir");
				data.append('upload', $('#file_upload')[0].files[0] );
				if(upload_file != '')
				{
					$.ajax(
					{
						url: base_url + 'index.php/central_controller/uploadFile/?dir=' + current_dir,
						type: 'POST',
						contentType: false,
						processData: false,
						data: data
					});	
					alert('Upload Successful');
					$(this).dialog("close");
					parent.location.reload();
				}
				else
				{
					alert('Please select a file');
				}
				
			},
			Cancel: function ()
			{
				$(this).dialog("close");
				$("#file_upload").val('');
			}
		}
	});
	
	$("#upload").click(function () {
		$("#dialog-upload-file").dialog("open");
	});
	
	$('#dialog-upload-file').bind('dialogclose', function(event) {
		$("#file_upload").val('');
	});
	
	$("#dialog-copy-file").dialog({
        autoOpen: false,
        modal: true,
		height: 400,
		width: 600,
		buttons: {
			Submit: function ()
			{
				var file_path = $("#file_path").val();
				var selected_files = new Array();
				var current_dir = getCookie("current_dir");
				var action = $("#action").val();
				$('td :checked').each(function() {
					selected_files.push($(this).val());
				});
				if(action == "Copy")
				{
					$.ajax({
						url: base_url + "index.php/central_controller/copyFile/?copy=" + selected_files + "&path=" + file_path + "&current_dir=" + current_dir,
						type: 'GET'
					});
					alert('Copy Successful');
				}
				else if(action == "Move")
				{
					$.ajax({
						url: base_url + "index.php/central_controller/moveFile/?move=" + selected_files + "&path=" + file_path + "&current_dir=" + current_dir,
						type: 'GET'
					});
					alert('Move Successful');
				}
				
				$('input[type=checkbox]').attr('checked',false);
				$(this).dialog("close");
				parent.location.reload();
				$("#directory_name_cp").val('');
				$("#action").val('');
			},
			Cancel: function ()
			{
				$(this).dialog("close");
				$("#directory_name_cp").val('');
				$("#action").val('');
			}
		}
	});
	
	$("#dialog-backup-file").dialog({
        autoOpen: false,
        modal: true,
		height: 150,
		width: 350,
		buttons: {
			Continue: function ()
			{
				var verbose_container = "<span class=red>Backup Starting...</span><br>";
				$("#dialog-process-backup").dialog("open");
				$(this).dialog("close");
				$.ajax({
					url: base_url + "index.php/central_controller/backupFile/?backup=1",
					type: 'GET',
					success: function(data)
					{
						var parsed_data = jQuery.parseJSON(data);
						$.each(parsed_data, function(index, element){
							verbose_container += element + "<br>";
						})
						verbose_container += "<span class=daemon>Backup Done!</span>"
						$("#verbose_container").html(verbose_container);
					}
				});
			},
			Cancel: function ()
			{
				$(this).dialog("close");
			}
		}
	});
	
	$("#dialog-process-backup").dialog({
        autoOpen: false,
        modal: true,
		height: 'auto',
		width: 500
	});
	
	$("#dialog-backup-list").dialog({
        autoOpen: false,
        modal: true,
		height: 300,
		width: 600
	});
	
	$('#dialog-copy-file').bind('dialogclose', function(event) {
		$("#directory_name_cp").val('');
		$("#action").val('');
	});
	
	$("#backup").click(function (){
		var list_container = '';
		$("#dialog-backup-file").dialog("open");
		
	});
	
	$("#view_list").click(function (){
		var list_container;
		$("#dialog-backup-list").dialog("open");
		$.ajax({
			url: base_url + "index.php/central_controller/getBackupList/?backup_list=1",
			type: "GET",
			dataType: 'json',
			success: function(data)
			{
				$.each(data.file_name, function(index, element){
					list_container += "<tr>";
			
					list_container += "<td>";
					list_container += "<img src="+base_url+"assets/img/zip.png style='margin-right: 5px;' />";
					list_container += element;
					list_container += "</td>";
					
					list_container += "<td>";
					list_container += data.date_modified[index];
					list_container += "</td>";
					
					list_container += "<td>";
					list_container += data.file_size[index];
					list_container += "</td>";
					
					list_container += "/<tr>";
					});
			
				$("#backup_data").html(list_container);
			}
		});
	});
	
	$("#view_log").click(function (){
		$("#verbose_container").toggle();

	});
	
	$("#close_backup").click(function (){
		$("#dialog-process-backup").dialog("close");

	});
}); // end of document.ready


function popup(message) {
         
    // get the screen height and width 
    var maskHeight = $(document).height(); 
    var maskWidth = $(window).width();
     
    // calculate the values for center alignment
    var dialogTop =  (maskHeight/3) - ($('#dialog-box').height() - 150); 
    var dialogLeft = (maskWidth/2) - ($('#dialog-box').width()/2);
     
    // assign values to the overlay and dialog box
    $('#dialog-overlay').css({height:maskHeight, width:maskWidth}).show();
    $('#dialog-box').css({top:dialogTop, left:dialogLeft}).show();
     message = "Hello! and welcome to my File Manager. <br/>Made by <u>Chabs</u>";
    // display the message
   // $('#dialog-message').html(message);
             
}

function getImage(url,img)
{
	var imagesPath = base_url + "external";
	var full_path = imagesPath + url + img;
	
	
	$("#dialog-view-image").dialog("open");
	sImageHeader += img;
	sImageContainer = "<img height=485 width=580 src=" + full_path + ">";
	$("#dialog-view-image").dialog('option','title','<span class=file_header>Image Viewer - ' + img + "</span>");
	$("#image_container").html(sImageContainer);
	
}

function getTextFile(url, file, owner)
{
	$("#text_file_url").val(url);
	$("#text_file_name").val(file);
	$("#text_file_owner").val(owner);
	global_url = url;
	global_file = file;
	var parsedText = '';
	$.ajax({
		url: base_url + "index.php/central_controller/getTextContents/?url=" + url + "&file=" + file,
		dataType: "json",
		type: 'GET',
		success: function(data)
		{
			$.each(data, function(index, element){
				if(element == '')
				{
					parsedText += "\n";
				}
				else
				{
					parsedText += element.split("\\n").join("\n").replace(/$/g, "");
				}
			});
			
			$("#dialog-text-contents").dialog("open");
			$("#dialog-text-contents").dialog('option','title','<span class=file_header>Text Editor - ' + file + '</span>');
			$("#text_data").val(parsedText);
			
		}
		
	});
}


function gotoDirectory(directory)
{
	var current_dir = $("#directory_name_cp").val();
	//alert(directory);
	if(current_dir == '' || current_dir == undefined) current_dir == '/';
	$.ajax({
		url: base_url + "index.php/central_controller/getAllDirectories/?directories=" + directory + "&current=" + current_dir,
		type: 'GET',
		dataType: 'json',
		success: function(data)
		{
			var directoryContainer = '';	
			var x=0;
			if(data.file_name != '')
			{
				$.each(data.file_name, function(index, element){
				//directoryContainer[x] = data.file_name[x];
			
				directoryContainer += "<tr>";
			
				directoryContainer += '<td onclick=registerDirectoryCopy("/'+current_dir+"/"+element+"/"+'")>';
				directoryContainer += "<img src="+base_url+"assets/img/folder.gif style='margin-right: 5px;' />";
				directoryContainer += "<a href=javascript:gotoDirectory('/"+current_dir+element+"');>"+element+"</a>";
				
				directoryContainer += "</td>";
				
				directoryContainer += "<td>";
				directoryContainer += data.date_modified[x];
				directoryContainer += "</td>";
				
				directoryContainer += "<td>";
				directoryContainer += data.file_size[x];
				directoryContainer += "</td>";
				
				directoryContainer += "<td>";
				directoryContainer += data.file_owner[x];
				directoryContainer += "</td>";
				
				directoryContainer += "</tr>";
				x++;
			
				
				});
			}
			else
			{
				directoryContainer += "<tr><td colspan=6 class=empty><br><span class=red>Directory is empty</span><br>&nbsp;</td></tr>";
			}
			
			$("#copy_data").html(directoryContainer);
		}
	});
	
}

function copyFile()
{
	var selected_files = new Array();
	$('td :checked').each(function() {
		selected_files.push($(this).val());
	});
	if(selected_files.length < 1)
	{
		alert('Please select a file/s');
	}
	else
	{
		$("#dialog-copy-file").dialog("open");
		$("#dialog-copy-file").dialog('option','title','Copy File');
		gotoDirectory('');
		$("#action").val("Copy");
	}
}	

function moveFile()
{
	var selected_files = new Array();
	$('td :checked').each(function() {
		selected_files.push($(this).val());
	});
	if(selected_files.length < 1)
	{
		alert('Please select a file/s');
	}
	else
	{
		$("#dialog-copy-file").dialog("open");
		$("#dialog-copy-file").dialog('option','title','Move File');
		gotoDirectory('');
		$("#action").val("Move");
	}
}

function updateCurrentDirectory(dir)
{
	if(dir == "/Parent/") dir = '';
	setCookie("current_dir", dir, 1);
}

function registerOwner(owner)
{
	$("#text_file_owner").val(owner);
}

function registerDirectoryCopy(dir)
{
	var replaced = '';
	$("#directory_name_cp").val(dir);
	$("#file_path").val($("#directory_name_cp").val());
}