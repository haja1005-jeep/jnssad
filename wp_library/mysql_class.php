<?
if(!$Mysql)
{
	// 재동작 방지 //
	$Mysql = 1;

	class Mysql
	{
		var $connect;
		var $result;
		var $row;
		var $total_record;

		// DB 접속 //
		function Connect()
		{
			$this->connect = mysql_connect('localhost','mpducom1','sudo8727') or die(Error("DB_ERROR"));
			mysql_select_db(mpducom1) or die(Error("QUERY_ERROR"));
		}

		// 쿼리 전송 //
		function Query($query)
		{
			return mysql_query($query,$this->connect) or die(Error("QUERY_ERROR"));
		}

		// DB 저장 //
		function Insert($table,$fields,$values)
		{
			// 배열 갯수 //
			$fields_count = count($fields);
			$values_count = count($values);

			// 배열 비교 //
			if($fields_count != $values_count)
			{
				return false;
			}

			for($i=0 ; $i < $fields_count ; $i++)
			{
				// fields and values 이용 퀴리 생성 //
				if(0 < $i)
				{
					// fields and values 2개 이상일 경우 //
					$fields_query .= ",";
					$values_query .= ",";
				}
				$fields_query .= $fields[$i];
				$values_query .= "'".addslashes(trim($values[$i]))."'";
			}

			// DB 입력 //
			$query = "INSERT INTO $table ($fields_query) VALUES ($values_query)";

			// 쿼리 전송 //
			$check = $this->Query($query);
			if(!$check)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		// 데이터가 있으면 입력 없으면 수정 //
		function DupInsert($table,$fields,$values)
		{
			$in_field = "";
			$in_val = "";
			if(is_array($fields) & is_array($values))
			{
				// 배열 갯수 //
				$fields_count = count($fields);
				$values_count = count($values);

				// 배열 비교 //
				if($fields_count != $values_count)
				{
					return false;
				}

				for($i = 0 ; $i < $fields_count ; $i++)
				{
					if(0 < $i)
					{
						$in_field .= ",";
						$in_val .= ",";
						$sub_query .= ",";
					}
					$in_field .= $fields[$i];
					$in_val .="'".addslashes(trim($values[$i]))."'";
					$sub_query .= $fields[$i]."='".addslashes(trim($values[$i]))."'";

				}
			}
			else if(!is_array($fields) & !is_array($values))
			{
				$sub_query = " ".$fields."='".addslashes($values)."' ";
			}
			else
			{
				return false;
			}

			if($sub_query)
			{
				// DB 수정 //
				$sub_query .= " ".$where;
				$query = "INSERT into $table ($in_field) values($in_val) ON DUPLICATE KEY UPDATE $sub_query";
				$check = $this->Query($query);
				if(!$check)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
		}


		// DB 수정 //
		function Update($table,$fields,$values,$where=" ")
		{

			if(is_array($fields) & is_array($values))
			{
				// 배열 갯수 //
				$fields_count = count($fields);
				$values_count = count($values);

				// 배열 비교 //
				if($fields_count != $values_count)
				{
					return false;
				}

				for($i = 0 ; $i < $fields_count ; $i++)
				{
					if(0 < $i)
					{
						$sub_query .= ",";
					}

					$sub_query .= $fields[$i]."='".addslashes(trim($values[$i]))."'";
				}
			}
			else if(!is_array($fields) & !is_array($values))
			{
				$sub_query = " ".$fields."='".addslashes($values)."' ";
			}
			else
			{
				return false;
			}

			if($where)
			{
				// DB 수정 //
				$sub_query .= " ".$where;
				$query = "UPDATE $table SET $sub_query";
				$check = $this->Query($query);
				if(!$check)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
		}

		// DB 삭제 //
		function Delete($table,$where)
		{
			if($where)
			{
				// DB 삭제 //
				$query = "DELETE FROM $table $where;";
				$check = $this->Query($query);
				if(!$check)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
		}

		// 쿼리 결과 배열화 //
		function ArrayQuery($query)
		{
			$tmp = mysql_query($query, $this->connect) or die(Error("QUERY_ERROR"));
			$result = mysql_fetch_array($tmp,MYSQL_BOTH) or die(Error("QUERY_ERROR"));
			return $result;
		}

		// 쿼리 결과 //
		function ResultQuery($query)
		{
			$this->result = mysql_query($query, $this->connect) or die(Error("QUERY_ERROR"));
			$this->row = mysql_num_rows($this->result);
		}

		// 데이터 포인트 //
		function DataSeek($i)
		{
			mysql_data_seek($this->result, $i);
			$temp=mysql_fetch_array($this->result, MYSQL_BOTH) or die(Error("QUERY_ERROR"));
			return $temp;
		}
		//총건수 가져오는 쿼리문 생성 - 세부종목까지 카운팅
		function getTotalRecord($table, $where){
			$query  = " SELECT count(*) total_record FROM ".$table.$where;
			$tmp = mysql_query($query, $this->connect) or die(Error("QUERY_ERROR"));
			$t_sql = mysql_fetch_object($tmp);
			return $t_sql->total_record;
		}

		//총건수 가져오는 쿼리문 생성 - 순수한 선수 참가신청 인원 카운팅
		function getTotalRecord2($table, $where){
			$query  = " SELECT DISTINCT pla_id FROM ".$table.$where;
			$tmp = mysql_query($query, $this->connect) or die(Error("QUERY_ERROR"));
			$t_sql = mysql_num_rows($tmp);
			return $t_sql;
		}
	}
}
?>