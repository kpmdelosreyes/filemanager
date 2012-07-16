<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>FileManager</title>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/main.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/added.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/window.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/scroller.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery-ui/jquery.ui.all.css" />
		<script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>/assets/js/jquery-min.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>/assets/js/jquery-ui-min.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>/assets/js/sorttable.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>/assets/js/scripts.js"></script>
	</head>
	<body>
	
	<input type="hidden" id="text_file_url" value="">
	<input type="hidden" id="text_file_name" value="">
	<input type="hidden" id="text_file_owner" value="">
	<input type="hidden" id="directory_name_cp" value="">
	<input type="hidden" id="action" value="">
	
	<div class="main_container">
    <h4 class="main_container_title gt-nonSelectableText">FileManager</h4>
      <div class="main_container_content">
			<div id="pageHeader" class="gt-pageHeader gt-nonSelectableText">
				<span style="float: left;">
					<div id="actions-container">
					<ul class="gt-nonSelectableText">
						<li class="action-icons gt-nonSelectableText" id="create">
							<img src="<?php echo base_url(); ?>assets/img/create.png" style="width: 16px; height: 16px; vertical-align: text-bottom; margin-right: 0px" />&nbsp;Create
						</li>
						<li class="action-icons" id="delete">
							<img src="<?php echo base_url(); ?>assets/img/delete.png" style="width: 16px; height: 16px; vertical-align: text-bottom; margin-right: 0px" />&nbsp;Delete
						</li>
						<li class="action-icons" id="rename">
							<img src="<?php echo base_url(); ?>assets/img/rename.png" style="width: 16px; height: 16px; vertical-align: text-bottom; margin-right: 0px" />&nbsp;Rename
						</li>
						<li class="action-icons" onclick="copyFile()">
							<img src="<?php echo base_url(); ?>assets/img/copy.png" style="width: 16px; height: 16px; vertical-align: text-bottom; margin-right: 0px" />&nbsp;Copy
						</li>
						<li class="action-icons" onclick="moveFile()">
							<img src="<?php echo base_url(); ?>assets/img/move.png" style="width: 16px; height: 16px; vertical-align: text-bottom; margin-right: 0px" />&nbsp;Move
						</li>
						<li class="action-icons" id="upload">
							<img src="<?php echo base_url(); ?>assets/img/upload.png" style="width: 16px; height: 16px; vertical-align: text-bottom; margin-right: 0px" />&nbsp;Upload
						</li>
						<li class="action-icons" id="backup">
							<img src="<?php echo base_url(); ?>assets/img/backup.png" style="width: 16px; height: 16px; vertical-align: text-bottom; margin-right: 0px" />&nbsp;Backup
						</li>
						<li class="action-icons" onclick="popup()">
							<img src="<?php echo base_url(); ?>assets/img/about.png" style="width: 16px; height: 16px; vertical-align: text-bottom; margin-right: 0px" />&nbsp;About
						</li>
					</ul>
					</div>
				</span>
				<span style="float: right;">
					<form method="POST" style="margin: 0; padding: 0; display: inline;">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="text" name="search" class="search_input" placeholder="Search">&nbsp;<input type="submit" name="find" value="Search" style="font:normal 12px/16px Segoe UI,Arial,sans-serif; display:none;">
							</form>
					<img src="<?php echo base_url(); ?>assets/img/user.png" style="width: 16px; height: 16px; vertical-align: text-bottom; margin-right: 0px" />
					Current User: <u><i> daemon</i></u>
				</span>
				
			</div>	
			<div style="clear: both;"></div>
			
			<div id="fileManager" class="gt-fileManager">
				
				<div id="toolbar">
					<div id="mainmenu">
					<ul>
						<li>Current Directory:</li>
						
						<?php
						if(isset($complete_url))
						{	
							foreach($complete_url as $links)
							{
								if($links == end($complete_url))
								{
									echo "
										<li>&raquo;</li>
										<li class=active><a onClick=updateCurrentDirectory('/$links/') href=".reduce_double_slashes(base_url()."/?dir=/$links>$links")."</a></li>
									";
								}
								else
								{
									echo "
										<li>&raquo;</li>
										<li><a onClick=updateCurrentDirectory('/$links/') href=".reduce_double_slashes(base_url()."/?dir=/$links>$links")."</a></li>
									";
								}
							}
						}
						?>
					</ul>
					</div>
				</div>
				
				<div id="info-pane">
				
				</div>
				
				<div id="file-data" class="gt-gridView">
					<table border=0 cellpadding=0 cellspacing=0 id="grid-data" class="sortable">
						<thead>
							<tr>
								<th id="checkbox-head" class="gt-gridViewColumn sorttable_nosort gt-nonSelectableText"><input type="checkbox" id="files" title="check/uncheck"></th>
								<th id="filename-head" class="gt-gridViewColumn gt-nonSelectableText">Name</th>
								<th id="filesize-head" class="gt-gridViewColumn gt-nonSelectableText">Size</th>
								<th id="filetype-head" class="gt-gridViewColumn gt-nonSelectableText">Type</th>
								<th id="datemodi-head" class="gt-gridViewColumn gt-nonSelectableText">Date Modified</th>
								<th id="owner-head" class="gt-gridViewColumn gt-nonSelectableText">Owner</th>
							</tr>
						</thead>
						<?php
						$x=0;
						$path = base_url().'assets/img/';
						if(isset($total_files))
						{
							if($total_files > 0)
							{
								foreach($file_name as $name)
								{
									switch($file_type[$x])
									{
										case "jpg" : $ext_img = $path."img.gif"; $info_ext = "Image"; break;
										case "gif" : $ext_img = $path."gif.png"; $info_ext = "Image"; break;
										case "png" : $ext_img = $path."png.png"; $info_ext = "Image"; break;
										case "bmp" : $ext_img = $path."bmp.png"; $info_ext = "Image"; break;
										case "pdf" : $ext_img = $path."pdf.gif"; $info_ext = "Adobe acrobat"; break;
										case "ppt" : $ext_img = $path."ppt.gif"; $info_ext = "MS PowerPoint"; break;
										case "pptx" : $ext_img = $path."ppt.gif"; $info_ext = "MS PowerPoint"; break;
										case "doc" : $ext_img = $path."word.gif"; $info_ext = "MS Word"; break;
										case "docx" : $ext_img = $path."word.gif"; $info_ext = "MS Word"; break;
										case "xls" : $ext_img = $path."excel.gif"; $info_ext = "MS Excel"; break;
										case "xlsx" : $ext_img = $path."excel.gif"; $info_ext = "MS Excel"; break;
										case "txt" : $ext_img = $path."txt.gif"; $info_ext = "Text File"; break;
										case "zip" : $ext_img = $path."zip.png"; $info_ext = "Zip File"; break;
										case "rar" : $ext_img = $path."zip.png"; $info_ext = "Rar File"; break;
										case "exe" : $ext_img = $path."exe.gif"; $info_ext = "Executable File"; break;
										case "wma" : $ext_img = $path."audio.png"; $info_ext = "Audio File"; break;
										case "mp3" : $ext_img = $path."audio.png"; $info_ext = "Audio File"; break;
										case "wav" : $ext_img = $path."audio.png"; $info_ext = "Audio File"; break;
										case "directory" : $ext_img = $path."folder.gif"; $info_ext = "directory"; break;
										default : $ext_img = $path."etc.gif"; $info_ext = "Others"; break;
									}
									//echo "<script>alert('".$name."');</script>";	
									if($info_ext == "directory")
									{
										if($file_owner[$x] == "daemon") //if file is a directory
										{
											echo "
												<tr>
													<td><input type=checkbox name=file[] class=file value='$current_dir/$name' onClick=registerOwner('$file_owner[$x]') /></td>
													<td onClick=updateCurrentDirectory('$current_dir/$name')><img class='file_type_img' src=$ext_img /><a href=?dir=$current_dir/$name>$name</a></td>
													<td>$date_modified[$x]</td>
													<td>".humanize($info_ext)."</td>
													<td>$file_size[$x]</td>
													<td class=daemon>$file_owner[$x]</td>
												</tr>
											";
										}
										else
										{
											echo "
												<tr>
													<td><input type=checkbox name=file[] class=file value='$current_dir/$name' onClick=registerOwner('$file_owner[$x]') /></td>
													<td onClick=updateCurrentDirectory('$current_dir/$name')><img class='file_type_img' src=$ext_img /><a href=?dir=$current_dir/$name>$name</a></td>
													<td>$date_modified[$x]</td>
													<td>".humanize($info_ext)."</td>
													<td>$file_size[$x]</td>
													<td>$file_owner[$x]</td>
												</tr>
											";
										}
									}
									else if($info_ext == "Image") // if file is an image
									{
										echo "
													<tr>
													<td><input type=checkbox name=file[] class=file value='$current_dir/$name' onClick=registerOwner('$file_owner[$x]') /></td>
													<td onClick=getImage('$current_dir/','$name')><img class='file_type_img' src=$ext_img />$name</td>
													<td>$date_modified[$x]</td>
													<td>$info_ext</td>
													<td>$file_size[$x]</td>
											";	
											if($file_owner[$x] == "daemon") { echo "<td class=daemon>$file_owner[$x]</td>";}
											else { echo "<td>$file_owner[$x]</td>"; }
										echo "</tr>";
									}
									else if($info_ext == "Text File") // if file is a text file
									{
										echo "
											<tr>
												<td><input type=checkbox name=file[] class=file value='$current_dir/$name' onClick=registerOwner('$file_owner[$x]') /></td>
												<td onClick=getTextFile('$current_dir/','$name','$file_owner[$x]')><img class='file_type_img' src=$ext_img />$name</td>
												<td>$date_modified[$x]</td>
												<td>$info_ext</td>
												<td>$file_size[$x]</td>
										";	
											if($file_owner[$x] == "daemon") { echo "<td class=daemon>$file_owner[$x]</td>";}
											else { echo "<td>$file_owner[$x]</td>"; }
										echo "</tr>";
									}
									else // if others
									{
										echo "
											<tr>
												<td><input type=checkbox name=file[] class=file value='$current_dir/$name' onClick=registerOwner('$file_owner[$x]') /></td>
												<td><img class='file_type_img' src=$ext_img><a href=".base_url()."index.php/central_controller/forceDownloadFile/?dir=$current_dir&file=$name >$name</a></td>
												<td>$date_modified[$x]</td>
												<td>$info_ext</td>
												<td>$file_size[$x]</td>
											";	
											if($file_owner[$x] == "daemon") { echo "<td class=daemon>$file_owner[$x]</td>";}
											else { echo "<td>$file_owner[$x]</td>"; }
										echo "</tr>";
									}
									$x++;
								}
								
								if(isset($is_search_page))
								{
									echo "<tr>
											<td colspan=6 class=empty>
												Search Results: $total_files 
												Files &nbsp;&nbsp;&nbsp;&rarr;&nbsp;
												<a href=".base_url().">
													<span class=red>Go Back
													</span>
												</a>
											</td>
										</tr>";
								}	
							}
							else
							{
								if(isset($is_search_page))
								{
									echo "<tr><td colspan=6 class=empty><br><span class=red>Files Found: NONE</span><br>&nbsp;</td></tr>";
								}
								else
								{
									echo "<tr><td colspan=6 class=empty><br><span class=red>Directory is empty</span><br>&nbsp;</td></tr>";
								}
							}
						}
						?>
						</tbody>
					</table>
					
				</div>
			</div>
		</div>
	</div>
	<!-- dialog boxes -->
	
	<div id="dialog-view-image">
			<div id="image_container"></div>
		</div>
		<div id="dialog-text-contents">
			<div id="text_container">
				<textarea id="text_data" rows=15></textarea>
			</div>
		</div>	
		<div id="dialog-create-file" title="Create File">
			<div align="middle">
				<fieldset><legend>Select type of File</legend>
				<ul class="panel_dialog">
					<li><input type="radio" name="create_selection" id="create_direc" value="direc"></li>
					<li><label for="create_direc">Create Directory</label></li>
					<li><input type="radio" name="create_selection" id="create_file" value="txt"></li>
					<li><label for="create_file">Create Text File</label></li>
				</ul>
				</fieldset>
				<fieldset><legend>Please avoid Special Characters</legend>
				<ul class="panel_dialog">
					<li><label for="file_name">Name: </label></li>
					<li><input type="text" name="file_name" id="file_name">&nbsp;&nbsp;<span class="type">(.txt)</span></li>
				</ul>
				</fieldset>
			</div>
		</div>
		<div id="dialog-confirm-delete" title="Delete Confirmation">
			<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
			<p>These files will be permanently deleted and cannot be recovered. Are you sure?</p>
		</div>
		<div id="dialog-rename" title="Rename Operation">
			<div align="middle">
				<fieldset><legend>Old Name</legend>
				<ul class="panel" id="old_name">
					
				</ul>
				</fieldset>
				<fieldset><legend>Please avoid Special Characters</legend>
				<ul class="panel_dialog">
					<li><label for="new_file_name">New Name: </label></li>
					<li><input type="text" name="new_file_name" id="new_file_name"></li>
				</ul>
				</fieldset>
			</div>
		</div>
		<div id="dialog-upload-file" title="File Upload">
			<div align="middle">
				<fieldset><legend>Select a file to upload</legend>
				<ul class="panel_dialog">
					<li><label for="file_upload">Path: </label></li>
					<li><input type="file" name="file_upload" id="file_upload"></li>
				</ul>
				<p style="text-align: middle;"><?=nbs(6);?>Maximum of 2MB (2048 KB) per file.</p>
				</fieldset>
			</div>
		</div>
		<div id="dialog-copy-file" title="Copy File">
			<div align="middle" style="margin-top: 0;">
				<ul class="panel_dialog" style="margin-top: 0; margin-buttom: 20px;">
					<li><label for="file_path">Path: </label></li>
					<li><input type="text" name="file_path" id="file_path" style="width: 80%;" value="/"></li>
				</ul>
			</div>
			<div id="directory_container" align="middle">
				<table class="data sortable" cellpadding=0 style='width: 97.5%; margin-top: 5px;'>
					<thead>
						<tr>
							<th class="header-bg">Name</th>
							<th class="header-bg">Date Modified</th>
							<th class="header-bg">Size</th>
							<th class="header-bg">Owner</th>
						</tr>
					</thead>
					<tbody id="copy_data">
					
					</tbody>
				</table>
			</div>
		</div>
		<div id="dialog-backup-file" title="Backup">
			<div align="middle" style="margin-top: 7px; margin-bottom: 0px;">
				<span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span>
				<p>Do you want to proceed to backup?</p>
			</div>
		</div>
		<div id="dialog-process-backup" title="Backup">
			<div align="middle" style="margin-top: 7px; margin-bottom: 0px;">
				<p>Backup Complete. Click view to open Backup List</p>
				<ul class="panel_dialog" style="margin-top: 0; margin-buttom: 20px;">
					<li><button type="button" id="view_list">View</button></li>
					<li><button type="button" id="view_log">Log</button></li>
					<li><button type="button" id="close_backup">Close</button></li>
				</ul>
			</div>
			<div id="verbose_container">
				<p>Initiate Backup</p>
			</div>
		</div>
		<div id="dialog-backup-list" title="Backup List">
			<div id="directory_container" align="middle">
				<table class="data sortable" cellpadding=0 style='width: 97.5%; margin-top: 5px;'>
					<thead>
						<tr>
							<th class="header-bg">Backup Name</th>
							<th class="header-bg">Backup Date</th>
							<th class="header-bg">Size</th>
						</tr>
					</thead>
					<tbody id="backup_data">
					
					</tbody>
				</table>
			</div>
		</div>
		
		<div id="dialog-overlay"></div>
		<div id="dialog-box">
			<div id="dialog-title">
				<span style="float: left;">FileManager</span>
				<span class="close_button">
					<a href="#" class="button1">X</a>
				</span>
				<div style="clear: both;"></div>
			</div>
			<div class="dialog-content">
				<div id="dialog-message" style="text-align: middle;">
					<br/>
					<marquee behavior="alternate"><marquee behavior="alternate" direction="up" height=50>Hello! and welcome to FileManager!</marquee></marquee>
					<br/>
					<marquee behavior="alternate" scrollamount=1>Made by <span class="red">Chabs</span></marquee>
					<br/>
					<marquee behavior="alternate" scrollamount=20 direction="right">Powered by CodeIgniter 2.1.0</span></marquee>
					<br/><br/>
				</div>
			</div>
		</div>
		
		<!-- end of dialog boxes -->
	</body>
</html>