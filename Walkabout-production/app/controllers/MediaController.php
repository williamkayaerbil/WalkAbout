<?php

class MediaController extends \BaseController {



    public function upload(){
    	$config = Config::get('media');
		//TODO switch to array
		extract($config, EXTR_OVERWRITE);
		$base_url=url('/');
		$thumbs_base_path=('../files/user_'.Auth::user()->id."/thumbnails/");
		$upload_dir=('/files/user_'.Auth::user()->id."/media/");
		$current_path=public_path().'/'.'files'.'/'.'user_'.Auth::user()->id.'/'."/media/";
		$user_path=public_path().'/'.'files'.'/'.'user_'.Auth::user()->id.'/';
		

		if ($_SESSION['RF']["verify"] != "RESPONSIVEfilemanager")
		{
			response('forbiden', 403)->send();
			exit;
		}

		if (isset($_POST['path']))
		{
		   $storeFolder = $_POST['path'];
		   $storeFolderThumb = $_POST['path_thumb'];
		}
		else
		{
		   $storeFolder = $current_path.$_POST["fldr"]; // correct for when IE is in Compatibility mode
		   $storeFolderThumb = $thumbs_base_path.$_POST["fldr"];
		}

		$path_pos  = strpos($storeFolder,$current_path);
		$thumb_pos = strpos($storeFolderThumb,$thumbs_base_path);

		if ($path_pos!==0
			|| $thumb_pos !==0
			|| strpos($storeFolderThumb,'../',strlen($thumbs_base_path)) !== FALSE
			|| strpos($storeFolderThumb,'./',strlen($thumbs_base_path)) !== FALSE
			|| strpos($storeFolder,'../',strlen($current_path)) !== FALSE
			|| strpos($storeFolder,'./',strlen($current_path)) !== FALSE )
				die('wrong path');


		$path = $storeFolder;
		$cycle = TRUE;
		$max_cycles = 50;
		$i = 0;
		while ($cycle && $i < $max_cycles)
		{
			$i++;
			if ($path == $current_path) $cycle = FALSE;
			if (file_exists($path."config.php"))
			{
				require_once $path."config.php";
				$cycle = FALSE;
			}
			$path = fix_dirname($path).'/';
		}


		if ( ! empty($_FILES))
		{
			$info = pathinfo($_FILES['file']['name']);

			if (in_array(fix_strtolower($info['extension']), $ext))
			{
				$tempFile = $_FILES['file']['tmp_name'];
				$targetPath = $storeFolder;
				$targetPathThumb = $storeFolderThumb;
				$_FILES['file']['name'] = fix_filename($_FILES['file']['name'],$transliteration,$convert_spaces, $replace_with);

			 	// Gen. new file name if exists
				if (file_exists($targetPath.$_FILES['file']['name']))
				{
					$i = 1;
					$info = pathinfo($_FILES['file']['name']);

					// append number
					while(file_exists($targetPath.$info['filename']."_".$i.".".$info['extension'])) {
						$i++;
					}
					$_FILES['file']['name'] = $info['filename']."_".$i.".".$info['extension'];
				}

				$targetFile =  $targetPath. $_FILES['file']['name'];
				$targetFileThumb =  $targetPathThumb. $_FILES['file']['name'];

				// check if image (and supported)
				if (in_array(fix_strtolower($info['extension']),$ext_img)) $is_img=TRUE;
				else $is_img=FALSE;

				// upload
				move_uploaded_file($tempFile,$targetFile);
				chmod($targetFile, 0755);

				if ($is_img)
				{
					$memory_error = FALSE;
					if ( ! create_img($targetFile, $targetFileThumb, 122, 91))
					{
						$memory_error = FALSE;
					}
					else
					{
						// TODO something with this long function baaaah...
						if( ! new_thumbnails_creation($targetPath,$targetFile,$_FILES['file']['name'],$current_path,$relative_image_creation,$relative_path_from_current_pos,$relative_image_creation_name_to_prepend,$relative_image_creation_name_to_append,$relative_image_creation_width,$relative_image_creation_height,$relative_image_creation_option,$fixed_image_creation,$fixed_path_from_filemanager,$fixed_image_creation_name_to_prepend,$fixed_image_creation_to_append,$fixed_image_creation_width,$fixed_image_creation_height,$fixed_image_creation_option))
						{
							$memory_error = FALSE;
						}
						else
						{
							$imginfo = getimagesize($targetFile);
							$srcWidth = $imginfo[0];
							$srcHeight = $imginfo[1];

							// resize images if set
							if ($image_resizing)
							{
								if ($image_resizing_width == 0) // if width not set
								{
									if ($image_resizing_height == 0)
									{
										$image_resizing_width = $srcWidth;
										$image_resizing_height = $srcHeight;
									}
									else
									{
										$image_resizing_width = $image_resizing_height*$srcWidth/$srcHeight;
									}
								}
								elseif ($image_resizing_height == 0) // if height not set
								{
									$image_resizing_height = $image_resizing_width*$srcHeight/$srcWidth;
								}

								// new dims and create
								$srcWidth = $image_resizing_width;
								$srcHeight = $image_resizing_height;
								create_img($targetFile, $targetFile, $image_resizing_width, $image_resizing_height, $image_resizing_mode);
							}

							//max resizing limit control
							$resize = FALSE;
							if ($image_max_width != 0 && $srcWidth > $image_max_width && $image_resizing_override === FALSE)
							{
								$resize = TRUE;
								$srcWidth = $image_max_width;

								if ($image_max_height == 0) $srcHeight = $image_max_width*$srcHeight/$srcWidth;
							}

							if ($image_max_height != 0 && $srcHeight > $image_max_height && $image_resizing_override === FALSE){
								$resize = TRUE;
								$srcHeight = $image_max_height;

								if ($image_max_width == 0) $srcWidth = $image_max_height*$srcWidth/$srcHeight;
							}

							if ($resize) create_img($targetFile, $targetFile, $srcWidth, $srcHeight, $image_max_mode);
						}
					}

					// not enough memory
					if ($memory_error)
					{
						unlink($targetFile);
						header('HTTP/1.1 406 Not enought Memory',TRUE,406);
						exit();
					}
				}
				echo $_FILES['file']['name'];
			}
			else // file ext. is not in the allowed list
			{
				header('HTTP/1.1 406 file not permitted',TRUE,406);
				exit();
			}
		}
		else // no files to upload
		{
			header('HTTP/1.1 405 Bad Request', TRUE, 405);
			exit();
		}

		// redirect
		if (isset($_POST['submit']))
		{
			$query = http_build_query(array(
				'type'	  	=> $_POST['type'],
				'lang'	  	=> $_POST['lang'],
				'popup'	 	=> $_POST['popup'],
				'field_id'  => $_POST['field_id'],
				'fldr'	  	=> $_POST['fldr'],
			));

			header("location: dialog.php?" . $query);
		}



    }
	public function dialog(){




		return View::make('media.dialog')
		->with('config',Config::get('media'));

	}

   
    public function force_download(){
		$config = Config::get('media');
		//TODO switch to array
		extract($config, EXTR_OVERWRITE);
		$base_url=url('/');
		$thumbs_base_path=('../files/user_'.Auth::user()->id."/thumbnails/");
		$upload_dir=('/files/user_'.Auth::user()->id."/media/");
		$current_path=public_path().'/'.'files'.'/'.'user_'.Auth::user()->id.'/'."/media/";
		$user_path=public_path().'/'.'files'.'/'.'user_'.Auth::user()->id.'/';
		

		if ($_SESSION['RF']["verify"] != "RESPONSIVEfilemanager")
		{
			response('forbiden', 403)->send();
			exit;
		}

	

		if (
			strpos($_POST['path'], '/') === 0
			|| strpos($_POST['path'], '../') !== false
			|| strpos($_POST['path'], './') === 0
		)
		{
			response('wrong path', 400)->send();
			exit;
		}


		if (strpos($_POST['name'], '/') !== false)
		{
			response('wrong path', 400)->send();
			exit;
		}

		$path = $current_path . $_POST['path'];
		$name = $_POST['name'];

		$info = pathinfo($name);

		if ( ! in_array(fix_strtolower($info['extension']), $ext))
		{
			response('wrong extension', 400)->send();
			exit;
		}

		if ( ! file_exists($path . $name))
		{
			response('File not found', 404)->send();
			exit;
		}

		$img_size = (string) (filesize($path . $name)); // Get the image size as string

		$mime_type = get_file_mime_type($path . $name); // Get the correct MIME type depending on the file.

		response(file_get_contents($path . $name), 200, array(
			'Pragma'              => 'private',
			'Cache-control'       => 'private, must-revalidate',
			'Content-Type'        => $mime_type,
			'Content-Length'      => $img_size,
			'Content-Disposition' => 'attachment; filename="' . ($name) . '"'
		))->send();

		exit;
    }
	public function execute(){

		$config = Config::get('media');
		//TODO switch to array
		extract($config, EXTR_OVERWRITE);
		 $base_url=url('/');
		 $thumbs_base_path=('../files/user_'.Auth::user()->id."/thumbnails/");
		 $upload_dir=('/files/user_'.Auth::user()->id."/media/");
		 $current_path=public_path().'/'.'files'.'/'.'user_'.Auth::user()->id.'/'."/media/";
		$user_path=public_path().'/'.'files'.'/'.'user_'.Auth::user()->id.'/';
	

		if ($_SESSION['RF']["verify"] != "RESPONSIVEfilemanager")
		{
			response('forbiden', 403)->send();
			exit;
		}

		$thumb_pos  = strpos($_POST['path_thumb'], $thumbs_base_path);

		if ($thumb_pos !=0
		    || strpos($_POST['path_thumb'],'../',strlen($thumbs_base_path)+$thumb_pos)!==FALSE
		    || strpos($_POST['path'],'/')===0
		    || strpos($_POST['path'],'../')!==FALSE
		    || strpos($_POST['path'],'./')===0)
		{
		    response('wrong path')->send();
			exit;
		}

		if (isset($_SESSION['RF']['language_file']) && file_exists($_SESSION['RF']['language_file']))
		{
			//TODO Very bad practice
		    require_once $_SESSION['RF']['language_file'];
		}
		else
		{
		    response('Language file is missing!', 500)->send();
			exit;
		}

		$base = $current_path;
		$path = $current_path.$_POST['path'];
		$cycle = TRUE;
		$max_cycles = 50;
		$i = 0;
		while($cycle && $i<$max_cycles)
		{
		    $i++;
		    if ($path == $base)  $cycle=FALSE;

		    if (file_exists($path."config.php"))
		    {
		        require_once $path."config.php";
		        $cycle = FALSE;
		    }
		    $path = fix_dirname($path)."/";
		    $cycle = FALSE;
		}

		$path = $current_path.$_POST['path'];
		$path_thumb = $_POST['path_thumb'];
		$path_thumb=str_replace('../files', public_path().'/files', $path_thumb);

		if (isset($_POST['name']))
		{
		    $name = fix_filename($_POST['name'],$transliteration,$convert_spaces, $replace_with);
		    if (strpos($name,'../') !== FALSE)
			{
				response('wrong name', 400)->send();
				exit;
			}
		}

		$info = pathinfo($path);
		if (isset($info['extension']) && !(isset($_GET['action']) && $_GET['action']=='delete_folder') && !in_array(strtolower($info['extension']), $ext) && $_GET['action'] != 'create_file')
		{
			response('wrong extension', 400)->send();
			exit;
		}

		if (isset($_GET['action']))
		{
		    switch($_GET['action'])
		    {
		        case 'delete_file':
		            if ($delete_files){
		                unlink($path);
		                
		                if (file_exists($path_thumb)) unlink($path_thumb);

		                $info=pathinfo($path);
		                if ($relative_image_creation){
		                    foreach($relative_path_from_current_pos as $k=>$path)
		                    {
		                        if ($path!="" && $path[strlen($path)-1]!="/") $path.="/";

		                        if (file_exists($info['dirname']."/".$path.$relative_image_creation_name_to_prepend[$k].$info['filename'].$relative_image_creation_name_to_append[$k].".".$info['extension']))
		                        {
		                            unlink($info['dirname']."/".$path.$relative_image_creation_name_to_prepend[$k].$info['filename'].$relative_image_creation_name_to_append[$k].".".$info['extension']);
		                        }
		                    }
		                }

		                if ($fixed_image_creation)
		                {
		                    foreach($fixed_path_from_filemanager as $k=>$path)
		                    {
		                        if ($path!="" && $path[strlen($path)-1] != "/") $path.="/";

		                        $base_dir=$path.substr_replace($info['dirname']."/", '', 0, strlen($current_path));
		                        if (file_exists($base_dir.$fixed_image_creation_name_to_prepend[$k].$info['filename'].$fixed_image_creation_to_append[$k].".".$info['extension']))
		                        {
		                            unlink($base_dir.$fixed_image_creation_name_to_prepend[$k].$info['filename'].$fixed_image_creation_to_append[$k].".".$info['extension']);
		                        }
		                    }
		                }
		            }
		            break;
		        case 'delete_folder':
		            if ($delete_folders){
		                if (is_dir($path_thumb))
		                {
		                    deleteDir($path_thumb);
		                }

		                if (is_dir($path))
		                {
		                    deleteDir($path);
		                    if ($fixed_image_creation)
		                    {
		                        foreach($fixed_path_from_filemanager as $k=>$paths){
		                            if ($paths!="" && $paths[strlen($paths)-1] != "/") $paths.="/";

		                            $base_dir=$paths.substr_replace($path, '', 0, strlen($current_path));
		                            if (is_dir($base_dir)) deleteDir($base_dir);
		                        }
		                    }
		                }
		            }
		            break;
		        case 'create_folder':
		            if ($create_folders)
		            {
		                create_folder(fix_path($path,$transliteration,$convert_spaces, $replace_with),fix_path($path_thumb,$transliteration,$convert_spaces, $replace_with));
		            }
		            break;
		        case 'rename_folder':
		            if ($rename_folders){
		                $name=fix_filename($name,$transliteration,$convert_spaces, $replace_with);
		                $name=str_replace('.','',$name);

		                if (!empty($name)){
		                    if (!rename_folder($path,$name,$transliteration,$convert_spaces))
							{
								response(trans('Rename_existing_folder'), 403)->send();
								exit;
							}

		                    rename_folder($path_thumb,$name,$transliteration,$convert_spaces);
		                    if ($fixed_image_creation){
		                        foreach($fixed_path_from_filemanager as $k=>$paths){
		                            if ($paths!="" && $paths[strlen($paths)-1] != "/") $paths.="/";

		                            $base_dir=$paths.substr_replace($path, '', 0, strlen($current_path));
		                            rename_folder($base_dir,$name,$transliteration,$convert_spaces);
		                        }
		                    }
		                }
		                else {
		                    response(trans('Empty_name'), 400)->send();
							exit;
		                }
		            }
		            break;
		        case 'create_file':
		            if ($create_text_files === FALSE) {
		                response(sprintf(trans('File_Open_Edit_Not_Allowed'), strtolower(trans('Edit'))), 403)->send();
						exit;
		            }

		            if (!isset($editable_text_file_exts) || !is_array($editable_text_file_exts)){
		                $editable_text_file_exts = array();
		            }

		            // check if user supplied extension
		            if (strpos($name, '.') === FALSE){
		                response(trans('No_Extension').' '.sprintf(trans('Valid_Extensions'), implode(', ', $editable_text_file_exts)), 400)->send();
						exit;
		            }

		            // correct name
		            $old_name = $name;
		            $name=fix_filename($name,$transliteration,$convert_spaces, $replace_with);
		            if (empty($name))
		            {
		                response(trans('Empty_name'), 400)->send();
						exit;
		            }

		            // check extension
		            $parts = explode('.', $name);
		            if (!in_array(end($parts), $editable_text_file_exts)) {
		                response(trans('Error_extension').' '.sprintf(trans('Valid_Extensions'), implode(', ', $editable_text_file_exts)), 400)->send();
						exit;
		            }

		            // correct paths
		            $path = str_replace($old_name, $name, $path);
		            $path_thumb = str_replace($old_name, $name, $path_thumb);

		            // file already exists
		            if (file_exists($path)) {
		                response(trans('Rename_existing_file'), 403)->send();
						exit;
		            }

		            $content = $_POST['new_content'];

		            if (@file_put_contents($path, $content) === FALSE) {
		                response(trans('File_Save_Error'), 500)->send();
						exit;
		            }
		            else {
		                if (is_function_callable('chmod') !== FALSE){
		                    chmod($path, 0644);
		                }
		                response(trans('File_Save_OK'))->send();
						exit;
		            }

		            break;
		        case 'rename_file':
		            if ($rename_files){
		                $name=fix_filename($name,$transliteration,$convert_spaces, $replace_with);
		                if (!empty($name))
		                {
		                    if (!rename_file($path,$name,$transliteration))
							{
								response(trans('Rename_existing_file'), 403)->send();
								exit;
							}

		                    rename_file($path_thumb,$name,$transliteration);

		                    if ($fixed_image_creation)
		                    {
		                        $info=pathinfo($path);

		                        foreach($fixed_path_from_filemanager as $k=>$paths)
		                        {
		                            if ($paths!="" && $paths[strlen($paths)-1] != "/") $paths.="/";

		                            $base_dir = $paths.substr_replace($info['dirname']."/", '', 0, strlen($current_path));
		                            if (file_exists($base_dir.$fixed_image_creation_name_to_prepend[$k].$info['filename'].$fixed_image_creation_to_append[$k].".".$info['extension']))
		                            {
		                                rename_file($base_dir.$fixed_image_creation_name_to_prepend[$k].$info['filename'].$fixed_image_creation_to_append[$k].".".$info['extension'],$fixed_image_creation_name_to_prepend[$k].$name.$fixed_image_creation_to_append[$k],$transliteration);
		                            }
		                        }
		                    }
		                }
		                else {
		                    response(trans('Empty_name'), 400)->send();
							exit;
		                }
		            }
		            break;
		        case 'duplicate_file':
		            if ($duplicate_files)
		            {
		                $name=fix_filename($name,$transliteration,$convert_spaces, $replace_with);
		                if (!empty($name))
		                {
		                    if (!duplicate_file($path,$name))
							{
								response(trans('Rename_existing_file'), 403)->send();
								exit;
							}

		                    duplicate_file($path_thumb,$name);

		                    if ($fixed_image_creation)
		                    {
		                        $info=pathinfo($path);
		                        foreach($fixed_path_from_filemanager as $k=>$paths)
		                        {
		                            if ($paths!="" && $paths[strlen($paths)-1] != "/") $paths.= "/";

		                            $base_dir=$paths.substr_replace($info['dirname']."/", '', 0, strlen($current_path));

		                            if (file_exists($base_dir.$fixed_image_creation_name_to_prepend[$k].$info['filename'].$fixed_image_creation_to_append[$k].".".$info['extension']))
		                            {
		                                duplicate_file($base_dir.$fixed_image_creation_name_to_prepend[$k].$info['filename'].$fixed_image_creation_to_append[$k].".".$info['extension'],$fixed_image_creation_name_to_prepend[$k].$name.$fixed_image_creation_to_append[$k]);
		                            }
		                        }
		                    }
		                }
		                else
		                {
		                    response(trans('Empty_name'), 400)->send();
							exit;
		                }
		            }
		            break;
		        case 'paste_clipboard':
		            if ( ! isset($_SESSION['RF']['clipboard_action'], $_SESSION['RF']['clipboard']['path'], $_SESSION['RF']['clipboard']['path_thumb'])
		                || $_SESSION['RF']['clipboard_action'] == ''
		                || $_SESSION['RF']['clipboard']['path'] == ''
		                || $_SESSION['RF']['clipboard']['path_thumb'] == '')
		            {
		                response()->send();
						exit;
		            }

		            $action = $_SESSION['RF']['clipboard_action'];
		            $data = $_SESSION['RF']['clipboard'];
		            $data['path'] = $current_path.$data['path'];
		            $pinfo = pathinfo($data['path']);

		            // user wants to paste to the same dir. nothing to do here...
		            if ($pinfo['dirname'] == rtrim($path, '/')) {
		                response()->send();
						exit;
		            }

		            // user wants to paste folder to it's own sub folder.. baaaah.
		            if (is_dir($data['path']) && strpos($path, $data['path']) !== FALSE){
		                response()->send();
						exit;
		            }

		            // something terribly gone wrong
		            if ($action != 'copy' && $action != 'cut'){
		                response('no action', 400)->send();
						exit;
		            }

		            // check for writability
		            if (is_really_writable($path) === FALSE || is_really_writable($path_thumb) === FALSE){
		                response(trans('Dir_No_Write').'<br/>'.str_replace('../','',$path).'<br/>'.str_replace('../','',$path_thumb), 403)->send();
						exit;
		            }

		            // check if server disables copy or rename
		            if (is_function_callable(($action == 'copy' ? 'copy' : 'rename')) === FALSE){
		                response(sprintf(trans('Function_Disabled'), ($action == 'copy' ? lcfirst(trans('Copy')) : lcfirst(trans('Cut')))), 403)->send();
						exit;
		            }

		            if ($action == 'copy')
		            {
		                rcopy($data['path'], $path);
		                rcopy($data['path_thumb'], $path_thumb);
		            }
		            elseif ($action == 'cut')
		            {
		                rrename($data['path'], $path);
		                rrename($data['path_thumb'], $path_thumb);

		                // cleanup
		                if (is_dir($data['path']) === TRUE){
		                    rrename_after_cleaner($data['path']);
		                    rrename_after_cleaner($data['path_thumb']);
		                }
		            }

		            // cleanup
		            $_SESSION['RF']['clipboard']['path'] = NULL;
		            $_SESSION['RF']['clipboard']['path_thumb'] = NULL;
		            $_SESSION['RF']['clipboard_action'] = NULL;

		            break;
		        case 'chmod':
		            $mode = $_POST['new_mode'];
		            $rec_option = $_POST['is_recursive'];
		            $valid_options = array('none', 'files', 'folders', 'both');
		            $chmod_perm = (is_dir($path) ? $chmod_dirs : $chmod_files);

		            // check perm
		            if ($chmod_perm === FALSE) {
		                response(sprintf(trans('File_Permission_Not_Allowed'), (is_dir($path) ? lcfirst(trans('Folders')) : lcfirst(trans('Files')) )), 403)->send();
						exit;
		            }

		            // check mode
		            if (!preg_match("/^[0-7]{3}$/", $mode)){
		                response(trans('File_Permission_Wrong_Mode'), 400)->send();
						exit;
		            }

		            // check recursive option
		            if (!in_array($rec_option, $valid_options)){
		                response("wrong option", 400)->send();
						exit;
		            }

		            // check if server disabled chmod
		            if (is_function_callable('chmod') === FALSE){
		                response(sprintf(trans('Function_Disabled'), 'chmod'), 403)->send();
						exit;
		            }

		            $mode = "0".$mode;
		            $mode = octdec($mode);

		            rchmod($path, $mode, $rec_option);

		            break;
		        case 'save_text_file':
		            $content = $_POST['new_content'];
		            // $content = htmlspecialchars($content); not needed
		            // $content = stripslashes($content);

		            // no file
		            if (!file_exists($path)) {
		                response(trans('File_Not_Found'), 404)->send();
						exit;
		            }

		            // not writable or edit not allowed
		            if (!is_writable($path) || $edit_text_files === FALSE) {
		                response(sprintf(trans('File_Open_Edit_Not_Allowed'), strtolower(trans('Edit'))), 403)->send();
						exit;
		            }

		            if (@file_put_contents($path, $content) === FALSE) {
		                response(trans('File_Save_Error'), 500)->send();
						exit;
		            }
		            else {
		                response(trans('File_Save_OK'))->send();
						exit;
		            }

		            break;
		        default:
		            response('wrong action', 400)->send();
					exit;
		    }
		}



	}
}