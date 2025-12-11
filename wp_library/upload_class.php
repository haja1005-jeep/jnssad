<?
class Upload
{
	var $file;
	var $path;
	var $size;
	var $name;
	var $expansion_name;
	var $expansion;

	// 클래스 초기화 //
	function Init($file,$path,$size,$name)
	{
		$this->file = $file;
		$this->path = $path;
		$this->size = $size;
		$this->name = $name;
		$this->expansion_name = $this->CutName("name");
		$this->expansion = $this->CutName("exp");
	}

	// 파일명 체크 //
	function CutName($mode)
	{
		$len = strlen($this->name);
		$pos = strpos($this->name,".");

		if($mode == "name")
		{
			return substr($this->name,0,$pos);
		}
		if($mode == "exp")
		{
			return substr($this->name,$pos+1,$len);
		}
	}

	// 파일크기 제한 //
	function LimitSize($limit_size)
	{
		if($this->size > $limit_size)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	// 확장자 제한 //
	function LimitExp($mode)
	{
		echo $mode;
		global $img_file_type;
		global $normal_file_type;
		$extention = strtolower($this->expansion);
		if($mode == "normal")
		{
			if(in_array($extention, $normal_file_type)){
				return true;
			}else{
				return false;
			}
		}
		if($mode == "img")
		{
			if(in_array($extention, $img_file_type)){
				return true;
			}else{
				return false;
			}
		}
		if($mode == "movie")
		{
			if(in_array($extention, $movie_file_type)){
				return true;
			}else{
				return false;
			}
		}
	}

	// 파일명 중복 체크 //
	function GetName()
	{
		$i = 1;
		sleep(1);

		// 파일명 변환 //
		$save_name = time();
		$str_name = $save_name.".".$this->expansion;
		while(file_exists($this->path.$str_name))
		{
			$str_name = $save_name."_".$i.".".$this->expansion;
			$i++;
		}
		return $str_name;
	}

	// 시군 파일명 중복 체크 //
	function GetName2($clu_code)
	{
		$i = 1;
		sleep(1);

		// 파일명 변환 //
		$str_name = $clu_code.".".$this->expansion;
		while(file_exists($this->path.$str_name))
		{
			$str_name = $save_name."_".$i.".".$this->expansion;
			$i++;
		}
		return $str_name;
	}


	// 파일 저장 //
	function FileSave($save_file_name)
	{
		copy("$this->file",$this->path.$save_file_name);
		unlink("$this->file");
	}
}
?>