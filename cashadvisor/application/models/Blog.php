<?php
class Application_Model_Blog extends Zend_Db_Table_Abstract
{
	
		public function init(){
			$this->modelsuper = new Application_Model_SuperModel();
		}
		
		public function blogcategorylist()
		{
			$data=$this->modelsuper->Super_Get("blog_categories","1","fetchAll");
			$blogCatList = array();
			 $blogCatList[0]['key']="";
			 $blogCatList[0]['value']="select blog category";
			
			$bc=1;
			foreach ($data as $key=>$values) {		
				$blogCatList[$bc]['key'] = $values['bc_id'];
				$blogCatList[$bc]['value'] = $values['blog_category_title'];
				$bc++;
			}		
			return $blogCatList;
		}
		
		public function viewblogcomment($id=false){
				$where = "comment_blog_id='".$id."'";
				$joinArr = array(
					'0'=>array('users','user_id =blog_comment_user_id','left',array("user_first_name","user_last_name",'user_id','user_image')),
					);
				$result=$this->modelsuper->Super_Get("blog_comment",$where,"fetchALL",array(),$joinArr);
				return $result;
			}
}