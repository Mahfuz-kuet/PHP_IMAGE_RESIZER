
// imgage resizing function started
// gif
function gif_image_fix_orientation($filename){
	ImageResizerFunctionCaller($filename, "gif");
}
	
// jpg 
function jpg_image_fix_orientation($filename){
	
	set_error_handler(function(){
		throw new Exception();
	}, E_WARNING);

	try{
		$exif = exif_read_data($filename);
	}catch (Exception $e){
		$exif = false;
	}finally {
		restore_error_handler();
	}
	
	if (!empty($exif['Orientation'])){
		$image = imagecreatefromjpeg($filename);
		switch ($exif['Orientation']){
			case 3:
				$image = imagerotate($image, 180, 0);
				break;

			case 6:
				$image = imagerotate($image, -90, 0);
				break;

			case 8:
				$image = imagerotate($image, 90, 0);
				break;
		}
		if(imagejpeg($image,$filename, 90)){
			ImageResizerFunctionCaller($filename, "jpg");
		}
	}else{
		
		ImageResizerFunctionCaller($filename, "jpg");
	}	
}
	
// png 
function png_image_fix_orientation($filename){
	ImageResizerFunctionCaller($filename, "png");
}


// main resizing function whice is called by _image_fix_orientation function 
function ImageResizerFunctionCaller($filename, $type){
	$filenameBaseName = basename($filename);
	$profilePhontoCheker =  strpos($filenameBaseName, "file_picture_of_sawla_"); // should be three if profile picture 
	if($profilePhontoCheker==3){
		jpgPngAndGifImageResizeAndCompress($filename,32,$type);  // for comment fetching ajax
		jpgPngAndGifImageResizeAndCompress($filename,40,$type);  // for home, postDetails, sidebar etc
		jpgPngAndGifImageResizeAndCompress($filename,50,$type);  // for other
		jpgPngAndGifImageResizeAndCompress($filename,64,$type);  // for profile page only 
		jpgPngAndGifImageResizeAndCompress($filename,100,$type); // for other
		jpgPngAndGifImageResizeAndCompress($filename,128,$type); // for profile page when use tablet
		jpgPngAndGifImageResizeAndCompress($filename,256,$type); // for profile pic when use pc 
	}else{
		jpgPngAndGifImageResizeAndCompress($filename,256,$type);
		jpgPngAndGifImageResizeAndCompress($filename,300,$type);
		jpgPngAndGifImageResizeAndCompress($filename,400,$type);
		jpgPngAndGifImageResizeAndCompress($filename,512,$type);
		jpgPngAndGifImageResizeAndCompress($filename,768,$type);
	}
}		


function jpgPngAndGifImageResizeAndCompress($filename, $resizedHeightOrWidth,$type){
	$filenameBaseName 	 		= basename($filename);
	$filenameBaseNameExtenssion = pathinfo($filenameBaseName,PATHINFO_EXTENSION);
	list($width,$height)	    = getimagesize($filename);
	
	if($resizedHeightOrWidth==32 || $resizedHeightOrWidth==40 ||  $resizedHeightOrWidth==50 || $resizedHeightOrWidth==64 || $resizedHeightOrWidth==100  || $resizedHeightOrWidth==128){
		if($height>=$width){
			$newWidth = $resizedHeightOrWidth;
			$newheight = ($newWidth*$height)/$width;
		}else{
			$newheight = $resizedHeightOrWidth;
			$newWidth = ($newheight*$width)/$height;
		}
	}else{
		if($width>$resizedHeightOrWidth){
				$newWidth = $resizedHeightOrWidth;
		}else{
			$newWidth = $width;
		}
		$newheight = ($newWidth*$height)/$width;
	}
	
	$imagetruecolor = imagecreatetruecolor($newWidth,$newheight);
	
	if($type=="gif"){
		$newimage = imagecreatefromgif($filename);
	}
	if($type=="jpg"){
		$newimage = imagecreatefromjpeg($filename);
	}
	if($type=="png"){
		$newimage = imagecreatefrompng($filename);
	}
	
	imagecopyresampled($imagetruecolor,$newimage,0,0,0,0,$newWidth,$newheight,$width,$height);
	$newFileName = basename($filename);	
	
	if($type=="jpg" || $type=="png"){
		$filenameLimit = strlen($newFileName)-3;
		$newFileName =  substr($newFileName,0,$filenameLimit);
		imagejpeg($imagetruecolor,"../img/".$resizedHeightOrWidth."_resized_".$newFileName."jpg",100);
		// for jpg and png image you will get a jpg image 
	}

	if($type=="gif"){
		imagegif($imagetruecolor,"../img/".$resizedHeightOrWidth."_resized_".$newFileName,100);
	}
}
// imgage resizing function finished

