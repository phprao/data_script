<?php
	/*
	 *  @desc:   数据model接口
	 *  @author: xxm
	 *  @email:  237886849@qq.com
	 *  @note:   所有文件命名以小写，所有子类名以小写
	 *		     
	 */
	interface basicmodel /*extends ArrayAccess*/{
		/*
		 *  @desc: get 
	     */
		public function get($id,$default);
		
		/*
		 *  @desc: set 
	     */
		public function set($id,$data);
		
		/*
		 *  @desc: insert 
	     */
		public function insert($id,$data);

		/*
		 *  @desc: update 
	     */
		public function update($id,$data);

		/*
		 *  @desc: insert 
	     */
		public function delete($id);

		/*
		 * @desk: copy model
		 */
		public function copy(basicmodel $model);
	}
?>