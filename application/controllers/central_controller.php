<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Central_controller extends CI_Controller {

	public $parent_directory = "/home/chabs/public_html/filemanager/external/";

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form','url','string','inflector','download','html'));
		
	}

	public function index()
	{
		$this->homepage();
		if(!is_dir($this->parent_directory))
		{	
			exec("mkdir ".$this->parent_directory);
		}
		if(!is_dir("/home/chabs/public_html/filemanager/backup"))
		{	
			exec("mkdir /home/chabs/public_html/filemanager/backup");
		}
		exec("chmod 777 -R /home/chabs/public_html/filemanager/external/");
		exec("chmod 777 -R /home/chabs/public_html/filemanager/backup/");
	}
	
	public function deleteFile()
	{
		$results = array();
		if(isset($_GET['delete']))
		{
			$files = $_GET['delete'];
			$files = explode(",", $files);
			foreach($files as $file)
			{
				$complete_dir = reduce_double_slashes($this->parent_directory.$file);
				exec("rm -rf ".$complete_dir);
				//echo "rm -rf ".$complete_dir;
			}
			// echo "<pre>";
			// print_r($files);
			// echo "</pre>";
		}
	}
	
	public function copyFile()
	{
		$old_place = $new_place = '';
		if(isset($_GET['copy']))
		{
			$files = $_GET['copy'];
			$path = $_GET['path'];
			$current_dir = $_GET['current_dir'];
			$files = explode(",",$files);
			foreach($files as $file)
			{
				$old_place = reduce_double_slashes($this->parent_directory."/".$file);
				$new_place = reduce_double_slashes($this->parent_directory."/".$path."/");
				exec("cp -r ".$old_place. " ".$new_place);
			}
			//print_r("yes | cp -R ".$old_place. " ".$new_place);
		}
	}
	
	public function moveFile()
	{
		$results = array();
		$old_place = $new_place = '';
		if(isset($_GET['move']))
		{
			$files = $_GET['move'];
			$path = $_GET['path'];
			$current_dir = $_GET['current_dir'];
			$files = explode(",",$files);
			foreach($files as $file)
			{
				$old_place = reduce_double_slashes($this->parent_directory.$file);
				$new_place = reduce_double_slashes($this->parent_directory."/".$path."/");
				exec("yes | mv -f ".$old_place. " ".$new_place);
				//echo "find ".$parent_directory." -name ".$file;
			}
			
			print_r($old_place);
		}
	}
	
	
	
	public function uploadFile()
	{
		if(isset($_GET['dir']))
		{
			$dir = $_GET['dir'];
			$filename = underscore($_FILES['upload']['name']);
			$upload = $this->input->post('upload');
			$complete_dir = reduce_double_slashes($this->parent_directory."/".$dir."/".$filename);
			move_uploaded_file($_FILES['upload']['tmp_name'], $complete_dir);
		}
		
	}
	
	public function forceDownloadFile()
	{
		if(isset($_GET['file']))
		{
			$file = $_GET['file'];
			$directory = $_GET['dir'];
			$complete_path = reduce_double_slashes($this->parent_directory."/".$directory."/".$file);
			$data = file_get_contents($complete_path);
			force_download($file, $data);
		}
	}
	
	public function backupFile()
	{
		$results = array();
		if(isset($_GET['backup']))
		{
			// ini_set('date.timezone', 'Asia/Manila');
			// $date = date('YmdHis');
			// exec("tar -cvf /home/chabs/public_html/filemanager/backup/"."backup".$date.".tar ".$this->parent_directory."*" , $results);
			// exec("gzip /home/chabs/public_html/filemanager/backup/"."backup".$date.".tar", $return);
			// exec("chmod 777 -R /home/chabs/public_html/filemanager/backup");
			// $old = "/home/chabs/public_html/filemanager/backup/"."backup".$date.".tar.gz";
			// $new = '/home/alex/public_html/files/chabs/'."backup".$date.".tar.gz";
			// copy($old, $new) or die("Unable to copy $old to $new.");
			// exec("rm -rf /home/chabs/public_html/filemanager/backup/*");
			// //echo "tar -cvf /home/chabs/public_html/filemanager/backup/"."backup".$date.".tar ".$this->parent_directory."*";
			exec("sh /home/chabs/public_html/filemanager/backup/backup.sh",$results);
			echo json_encode($results);
		}
	}
	
	public function getBackupList()
	{
		$final_data = array();
		$out = array();
		$final_out = array();
		$final_data['file_name'] = array();
		$final_data['date_modified'] = array();
		$final_data['file_size'] = array();
		$final_data['file_owner'] = array();
		$final_data['command'] = array();
		$results = "";
		
		if(isset($_GET['backup_list']))
		{
			$parent_directory = "/home/alex/public_html/files/chabs";
			$info = array();
			exec("ls -lt $parent_directory", $out);
			unset($out[0]);
			unset($out[1]);
			unset($out[2]);
			foreach($out as $value)
			{
				$final_out[] = $value;
			}
			for ($i=0;$i<count($final_out);$i++)
			{
				$str = str_replace("  ", " ", $final_out[$i]);

				for ($j=0;$j<10;$j++)
				{
					$str = str_replace("  ", " ", $str);
				}

				$info = explode(" ", $str);
				 
				$fsize = $info[4];
				$name = $info[8];
				$date = $info[5] . " " . $info[6] . " " . $info[7];
				$owner = $info[3];
			
				if($fsize > 1)
				{
					$f_size = "{$fsize} bytes";
				}
				else
				{
					$f_size = "{$fsize} byte";
				}
				$ext = explode(".", $name);
				@$ext = $ext[1];
				
				array_push($final_data['file_name'],$name);
				array_push($final_data['date_modified'],$date);
				array_push($final_data['file_size'],$f_size);
			}
			echo json_encode($final_data);
		}
	}
	
	public function findEntry($directory, $string)
	{
		$results = array();
		$parent_directory = reduce_double_slashes($this->parent_directory."/".$directory."/");
		exec("find $parent_directory -name '$string'", $results);
		
		return $results;
	}
	
	public function renameFile()
	{
		$complete_old = "";
		$complete_new = "";
		$new_name = "";
		$check = array();
		$check2 = array();
		$info = array();
		if(isset($_GET['rename']))
		{
			$old = $_GET['old_name'];
			$new = $_GET['new_name'];
			$current_dir = $_GET['current_dir'];
			if(strpos($old, "."))
			{
				$ext = explode(".", $old);
				while($this->findEntry($current_dir, $new.".".$ext[1]) != NULL)
				{
					$new = increment_string($new, '');
				}
				$complete_old = reduce_double_slashes($this->parent_directory.$old);
				$complete_new = reduce_double_slashes($this->parent_directory.$current_dir."/".$new.".".$ext[1]);
				exec("mv -i ".$complete_old." ".$complete_new);
			}
			else
			{
				while($this->findEntry($current_dir, $new) != NULL)
				{
					$new = increment_string($new, '');
				}
				$complete_old = reduce_double_slashes($this->parent_directory.$old);
				$complete_new = reduce_double_slashes($this->parent_directory.$current_dir."/".$new);
				exec("mv -i ".$complete_old." ".$complete_new);
				
			}
			
			//echo "mv -i ".$complete_old." ".$complete_new;
		}
	}
	
	public function createNewTextFile()
	{
		if(isset($_GET['dir']))
		{
			$dir = $_GET['dir'];
			$complete_dir = reduce_double_slashes($this->parent_directory.$dir);
			$name = underscore($_GET['name']);
			while($this->findEntry($current_dir, $name.".txt") != NULL)
			{
				$name = increment_string($name, '');
			}
			exec("echo > ".$complete_dir."/$name.txt");
			//echo "echo > ".$complete_dir."/$name.txt";
		}
	}
	
	public function createNewDirectory()
	{
		$error = 0;
		if(isset($_GET['dir']))
		{
			$dir = $_GET['dir']."/";
			$name = $_GET['name'];
			$name = underscore($name);
		
			$complete_dir = reduce_double_slashes($this->parent_directory."/".$dir."/".$name);
			exec("mkdir \"$complete_dir\"",$results, $error);
			echo json_encode($error);
			
			//print_r($this->findEntry($parent_directory, $name));
			//echo $complete_dir;
			
			//echo "mkdir ".$complete_dir;
		}
	}
	
	public function getTextContents()
	{
		$results = "";
		$parsed_results = "";
		
		if(isset($_GET['file']))
		{
			$file = $_GET['file'];	
			$url = $_GET['url'];	
			$complete_address = reduce_double_slashes($this->parent_directory.$url.$file);
			exec('cat -e '.$complete_address, $results);
			$results = str_replace("$","",$results);
			$results = str_replace("^M","",$results);
			$results = str_replace("M-oM-;M-?","",$results);
			echo json_encode($results);
		}
	}
	
	public function writeTextContents()
	{
		$results = "";
		if(isset($_GET['insert_text']))
		{
			if(isset($_GET['file']))
			{
				$file = $_GET['file'];
				$url = $_GET['url'];	
				$text = $_GET['text'];
				$complete_address = reduce_double_slashes($this->parent_directory.$url.$file);
				exec('cat /dev/null > '.$complete_address);
				exec("echo \"$text\" >> ".$complete_address);
			}
		}
	}
	
	public function getAllDirectories()
	{
		$final_data = array();
		$out = array();
		$final_out = array();
		$final_data['file_name'] = array();
		$final_data['date_modified'] = array();
		$final_data['file_size'] = array();
		$final_data['file_owner'] = array();
		$final_data['command'] = array();
		$results = "";
		
		if(isset($_GET['directories']))
		{
			$current_directory = $_GET['current'];
			$directory = $_GET['directories'];
			$complete_directory = reduce_double_slashes($this->parent_directory."/"."/".$directory);
			$info = array();
			exec("ls -al ".reduce_double_slashes($complete_directory)." | egrep '^d'", $out);
			unset($out[0]);
			foreach($out as $value)
			{
				$final_out[] = $value;
			}
			for ($i=0;$i<count($final_out);$i++)
			{
				$str = str_replace("  ", " ", $final_out[$i]);

				for ($j=0;$j<10;$j++)
				{
					$str = str_replace("  ", " ", $str);
				}

				$info = explode(" ", $str);
				 
				$fsize = $info[4];
				$name = $info[8];
				$date = $info[5] . " " . $info[6] . " " . $info[7];
				$owner = $info[3];
			
				if($fsize > 1)
				{
					$f_size = "{$fsize} bytes";
				}
				else
				{
					$f_size = "{$fsize} byte";
				}
				$ext = explode(".", $name);
				@$ext = $ext[1];
				
				array_push($final_data['file_name'],$name);
				array_push($final_data['date_modified'],$date);
				array_push($final_data['file_size'],$f_size);
				array_push($final_data['file_owner'],$owner);
				
			}
			array_push($final_data['command'],"ls -al ".$complete_directory." | egrep '^d'");
			echo json_encode($final_data);
		}
		
	}
	
	public function listCommand($command)
	{
		$results = array();
		$data['file_name'] = array();
		$data['date_modified'] = array();
		$data['file_type'] = array();
		$data['file_size'] = array();
		$data['file_owner'] = array();
		
		$command = reduce_double_slashes($command);
		exec($command, $results);
		
		for($i = count($results)-1; $i>0; $i--)
		{
			$str = str_replace("  ", " ", $results[$i]);
			for ($j=0;$j<10;$j++)
			{
				$str = str_replace("  ", " ", $str);
			}
			$info = explode(" ", $str);
			$dir = substr($info[0],0,1);
			$fsize = $info[4];
			$name = $info[8];
			$date = $info[5] . " " . $info[6] . " " . $info[7];
			$owner = $info[3];
			if ($dir == "d")
			{
				$ext_img = "folder.gif";
				$ext = "Directory";
				$info_ext = "File Folder";
				$f_size = "{$fsize} bytes";
			}
			else
			{
				if($fsize > 1)
				{
					$f_size = "{$fsize} bytes";
				}
				else
				{
					$f_size = "{$fsize} byte";
				}
				$ext = explode(".", $name);
				@$ext = $ext[1];
			}
			
			array_push($data['file_name'],$name);
			array_push($data['date_modified'],$date);
			array_push($data['file_type'],strtolower($ext));
			array_push($data['file_size'],$f_size);
			array_push($data['file_owner'],$owner);
		}
		
		$data['total_files'] = count($data['file_name']);
		
		return $data;
	}
	
	public function spliceLinks($links)
	{
		$tokens = array();
		$tokens = explode("/", $links);
		$tokens = array_filter($tokens, 'strlen');
		array_unshift($tokens,'Parent');
		return $tokens;
	}
	
	public function homepage()
	{
		$data = array();
		$dir = '';
		if(!isset($_GET['dir']) || $_GET['dir'] == "/Parent")
		{ 
			$dir = '';
		} 
		else
		{
			$dir = $_GET['dir'];
		}
		
		//echo $_GET['dir'];
		$search = $this->input->post('find');
		if($search == NULL) // load main page
		{
			$data = $this->listCommand("ls -lr ".reduce_double_slashes($this->parent_directory.$dir));
			$data['current_dir'] = $dir;	
			$data['complete_url'] = $this->spliceLinks($dir);
			// echo "<pre>";
			// print_r($data);
			// echo "</pre>";
		}
		else // load search page
		{
			$search_string = $this->input->post('search');
			if($search_string != NULL)
			{
				$data = $this->listCommand("ls -l ".reduce_double_slashes($this->parent_directory."/".$dir."/"." | grep $search_string"));
				$data['current_dir'] = $dir;	
				$data['complete_url'] = $this->spliceLinks($dir);
				//echo "ls -al ".reduce_double_slashes($parent_dir."/".$dir."/"." | grep ".$search_string);
				$data['is_search_page'] = 1;
			}
			else
			{
				echo "<script>alert('Please enter a word/keyword');</script>";
				$data = $this->listCommand("ls -l ".reduce_double_slashes($this->parent_directory.$dir));
				$data['current_dir'] = $dir;	
				$data['complete_url'] = $this->spliceLinks($dir);
			}
		}
		
		$this->load->view('main',$data);
	}
	
	public function test()
	{
		// echo exec("sh /home/chabs/public_html/filemanager/assets/shell/login.sh");
		// echo exec("whoami");
		//print_r($_SERVER['DOCUMENT_ROOT']);
		//echo increment_string('file','');
		//$file_name = str_replace("%20"," ", $file_name);
		//exec("rm -rf /home/chabs/public_html/filemanager/external/Annie\ in\ the\ Sink.jpg");
		//echo $this->parent_directory;
		// $data = array();
		// exec('stat '.$this->parent_directory, $data);
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		fopen("/home/chabs/public_html/filemanager/external/filename.txt","w");
	}
}

/* End of file central_controller.php */
/* Location: ./application/controllers/central_controller.php */