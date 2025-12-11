<?
class PageNumber
{
	var $total_page;
    	var $page;
	var $next_num;
	var $start_page;
	var $end_page;
	var $total_block;
	var $block;
	var $prev_page;
	var $next_page;
	var $prev;
	var $next;

	function PageNumber($total_record,$max_record,$max_page,$page)
	{
		// 현재 페이지 //
		if(!$page)
		{
			$this->page = 1;
		}
		else
		{
			$this->page = $page;
		}

		// 총 레코드 //
		if(!$total_record)
		{
			$this->start_page = 1;
			$this->end_page = 0;
		}
		else
		{
			// 시작 번호 //
			$this->start_page = $max_record*($this->page-1);

			// 마지막 번호 //
			$this->end_page = $max_record*$this->page;

			// 다음페이지 레코드 수 //
			$this->next_num = $total_record - $this->end_page;
			if($this->next_num > 0)
			{
				$this->end_page -=  1;
			}
			else
			{
				$this->end_page = $total_record - 1;
			}
		}

		// 총 페이지 수 //
		$this->total_page = ceil($total_record/$max_record);

		// 총 페이지 블럭 //
		$this->total_block = ceil($this->total_page/$max_page);

		// 현 페이지 블럭 //
		$this->block = ceil($this->page/$max_page);

		// 이전 페이지 수 //
		$this->prev_page = ($this->block-1)*$max_page;

		// 다음 페이지 수 //
		$this->next_page = $this->block*$max_page;

		// 현재 페이지 //
		if($this->block >= $this->total_block)
		{
			$this->next_page = $this->total_page;
		}

		// 이전 페이지 링크 //
		if($this->block > 1)
		{
			$this->prev = $this->prev_page;
		}

		// 다음 페이지 링크 //
		if($this->block < $this->total_block)
		{
			$this->next = $this->next_page+1;
		}
	}
}
?>