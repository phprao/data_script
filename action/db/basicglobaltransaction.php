<?php
	class basicglobaltransaction extends basicsingleton{
		protected $m_stran = null;
		protected function init() {
			$this->m_stran = new basictransactiontask();
		}

		public function get_strans() {
			if(is_null($this->m_stran)) {
				$this->m_stran = new basictransactiontask();
			}//var_dump($this->m_stran);
			return $this->m_stran;
		}

	}
?>