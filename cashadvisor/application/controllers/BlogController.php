<?php
class BlogController extends Zend_Controller_Action
{
	public function init(){	
 		$this->modelStatic = new Application_Model_Static();
		$this->modelUser = new Application_Model_User();
		$this->modelSuper = new Application_Model_SuperModel();
		$this->SuperModel = new Application_Model_SuperModel();
		$this->modelProject = new Application_Model_Project();
		$this->modelBlog = new Application_Model_Blog();
   	}
//All blog show	
 	public function indexAction(){
		$blogUrl = $this->getRequest()->getParam('blog_url');
		$this->view->blogUrl =$blogUrl;
		$this->view->show='blog';
		
		if($blogUrl!='\s+'){
					if(isset($this->view->user) && !empty($this->view->user)){
					$user_id = $this->view->user->user_id;
					}else{
						$user_id = "";
						}
					 
			// get blog_id 
					$GetBlogID = $this->modelStatic->Super_Get("blogs","blog_url = '".$blogUrl."'","fetch",array('fields'=>'blog_id'));
					$blog_id = $GetBlogID['blog_id'];
			// view full blog		 
					$joinArrUrl = array(
							'0'=>array('blog_categories','bc_id =blogs.blog_cat_id','left',array('blog_category_title')),
					);
			
					$GetBlogByUrl = $this->modelStatic->Super_Get("blogs","blog_url = '".$blogUrl."'","fetch",array(),$joinArrUrl);
					$this->view->GetBlogByUrl =$GetBlogByUrl;
			}else{
				$params = $this->_request->getParams();
					if((isset($params['s']) && $params['s']!='')){
						$SeaschBlogTilte = $params['s'];
						$this->view->SeaschBlogTilte = $SeaschBlogTilte;
						$where ="blog_title like '%".$params['s']."%' || blog_category_title like '%".$params['s']."%' || blog_tag like '%".$params['s']."%'";
					}
					elseif( isset($params['tag']) && $params['tag']!=''){
						$BlogTagWith = $params['tag'];
						$this->view->BlogTagWith = $BlogTagWith;
						$where ="blog_tag like '%".$params['tag']."%'";
					}
					elseif( isset($params['category']) && $params['category']!=''){
						$BlogCategory = $params['category'];
						$this->view->Bcategory = $BlogCategory;
						$where ="blog_category_title like '%".$params['category']."%'";
					}else{
						$where = "1";
						}
					
				$extra = array(
						//'group'=>"blog_id",
						'order'=>"blog_modified_on desc"
						);
				$joinArr = array(
						'0'=>array('blog_categories','bc_id =blog_cat_id','left',array('blog_category_title')),
				);
				$AllBlogs=$this->modelStatic->Super_Get("blogs",$where,"fetchAll",$extra,$joinArr);
			//	prd($AllBlogs);
				$this->view->AllBlogs = $AllBlogs;
			}
			
		$BlogCat = $this->modelStatic->Super_Get("blog_categories",'1',"fetchAll");
		$this->view->BlogCat = $BlogCat;
		
		$joinArr1 = array(
				'0'=>array('blog_categories','bc_id =blog_cat_id','left',array('blog_category_title')),
		);
		$RecentBlogs=$this->modelStatic->Super_Get("blogs","1","fetchAll",array('order'=>'RAND()','limit'=>'3'),$joinArr1);
		$this->view->RecentBlogs = $RecentBlogs;
	}
	
// details blog 		
	public function blogdetailAction(){
		$blogUrl = $this->getRequest()->getParam('blog_url');
		$this->view->blogUrl =$blogUrl;
		if(isset($this->view->user) && !empty($this->view->user)){
		$user_id = $this->view->user->user_id;
		}else{
			$user_id = "";
			}
		 
// get blog_id 
		$GetBlogID = $this->modelStatic->Super_Get("blogs","blog_url = '".$blogUrl."'","fetch",array('fields'=>'blog_id'));
		$blog_id = $GetBlogID['blog_id'];
// view full blog		 
		$joinArrUrl = array(
				'0'=>array('blog_categories','bc_id =blogs.blog_cat_id','left',array('blog_category_title')),
		);

		$GetBlogByUrl = $this->modelStatic->Super_Get("blogs","blog_url = '".$blogUrl."'","fetch",array(),$joinArrUrl);
		$this->view->GetBlogByUrl =$GetBlogByUrl;

		$BlogCat = $this->modelStatic->Super_Get("blog_categories",'1',"fetchAll");
		$this->view->BlogCat = $BlogCat;
		
		// recent Blog
		$joinArr = array(
				'0'=>array('blog_categories','bc_id =blogs.blog_cat_id','left',array('blog_category_title')),
		);
		$RecentBlogs=$this->modelStatic->Super_Get("blogs","1","fetchAll",array('limit'=>'3'),$joinArr);
		$this->view->RecentBlogs = $RecentBlogs;
    }	

			
	public function searchblogbycatAction(){
					global $objSession ; 
					$this->_helper->layout->disableLayout();
					$blogCatID = $this->getRequest()->getParam('id');
					
					$getCatTitle =$this->modelStatic->Super_Get("blog_categories",'bc_id ="'.$blogCatID.'"',"fetch",array('fields'=>'blog_category_title'));
					
					$this->view->CategoryTitle = $getCatTitle['blog_category_title'];
					//prn($getCatTitle);
					$where = "blog_category_title like '%".$getCatTitle['blog_category_title']."%'";
					//prd($where);
					$extra = array();
					$joinArr = array(
							'0'=>array('blog_categories','bc_id =blog_cat_id','left',array('blog_category_title')),
					);
					$AllBlogs=$this->modelStatic->Super_Get("blogs",$where,"fetchAll",$extra,$joinArr);
					$this->view->AllBlogs = $AllBlogs;
			}
}