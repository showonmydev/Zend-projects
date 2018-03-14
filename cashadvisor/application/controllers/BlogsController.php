<?php
class BlogsController extends Zend_Controller_Action
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
		$ifAdmin = $this->getRequest()->getParam('isadmin');
		$this->view->ifAdmin =$ifAdmin;
		$blogUrl = $this->getRequest()->getParam('blog_url');
		$this->view->blogUrl =$blogUrl;
		$this->view->show='blog';
		
		$form = new Application_Form_BlogForm();
		$form->comment();
		$this->view->form = $form;

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
							'1'=>array("blog_like","like_blog_url=blog_url","left",array("total_like"=>new Zend_Db_Expr("COUNT(DISTINCT(like_id))"))),
							'2'=>array("blog_comment","comment_blog_id=blog_id","left",array("total_comments"=>new Zend_Db_Expr("COUNT(DISTINCT(b_comment_id))"))),
					);
			
					$GetBlogByUrl = $this->modelStatic->Super_Get("blogs","blog_url = '".$blogUrl."'","fetch",array(),$joinArrUrl);
					$this->view->GetBlogByUrl =$GetBlogByUrl;
					
					
// get comments on blog
       	$comments = $this->modelBlog->viewblogcomment($blog_id);
		$this->view->comments = $comments; 
		
		//check if liked		 
		 $likedata=  $this->modelStatic->Super_Get("blog_like","like_blog_url='".$blogUrl."' and like_blog_user_id = '".$user_id."'", "fetch"); 
		 $this->view->likedata = $likedata;
					
			}
			else{
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
						'pagination'=>1,
						'order'=>"blog_modified_on desc"
						);
				$joinArr = array(
						'0'=>array('blog_categories','bc_id =blog_cat_id','left',array('blog_category_title')),
				);
				$AllBlogs=$this->modelStatic->Super_Get("blogs",$where,"fetchAll",$extra,$joinArr);
			//	prd($AllBlogs);
				$this->view->AllBlogs = $AllBlogs;
				
				$adapter = new Zend_Paginator_Adapter_DbSelect($AllBlogs);
				$paginator = new Zend_Paginator($adapter);
				$page = $this->_getParam('page',1);
				$rec_counts = 4; //Item per page
				
				$paginator->setItemCountPerPage($rec_counts);
				$paginator->setCurrentPageNumber($page);
				$paginator->setDefaultPageRange(8);
				
				$paginationControl = new Zend_View_Helper_PaginationControl($paginator, 'sliding', 'pagination-control.phtml');
				$this->view->paginationControl=$paginationControl;
				$this->view->paginator=$paginator;
			}
			
		$BlogCat = $this->modelStatic->Super_Get("blog_categories",'1',"fetchAll");
		$this->view->BlogCat = $BlogCat;
		
		/*$joinArr1 = array(
				'0'=>array('blog_categories','bc_id =blog_cat_id','left',array('blog_category_title')),
		);
		$RecentBlogs=$this->modelStatic->Super_Get("blogs","1","fetchAll",array('order'=>'RAND()','limit'=>'3'),$joinArr1);
		$this->view->RecentBlogs = $RecentBlogs;*/
	}
// edit comment by admin
	public function editbcommentAction(){
			$b_c_id = $this->getRequest()->getParam("b_c_id");
			$formData = $this->getRequest()->getPost();
			if ($this->getRequest()->isPost()){
				$data=array();
				$data['blog_comment']=$formData['blog_comment_edit'];
				$isInserted = $this->modelStatic->Super_Insert("blog_comment",$data,'b_comment_id="'.$b_c_id.'"');
				if($isInserted->success){
					$Updated = $this->modelStatic->Super_Get("blog_comment",'b_comment_id="'.$b_c_id.'"','fetch');
					print_r(json_encode($Updated));
				}else{
					echo "0";
				}
			}else{
				echo "0";
				}
			exit();
		}

//Ajax form submit-For commenting
	public function saveblogcommentAction(){
		$this->_helper->layout->disableLayout();
		$blog_id=$_REQUEST['blog_id'];
		$comment = $_REQUEST['CMsz'];
		$todaydate=date('Y-m-d H:i:s');
		$commentdata=array(
			'blog_comment'=>$comment,
			'comment_blog_id'=>$blog_id,
			'blog_comment_user_id'=>$this->view->user->user_id,
			'blog_comment_date'=>$todaydate,
		);
		$insert_data= $this->modelStatic->Super_Insert("blog_comment",$commentdata);
		exit();
	}
	
	public function blogcommentsAction(){
 		$this->_helper->layout->disableLayout();
		$blog_id = $_REQUEST['blog_id'];
       	$comments = $this->modelBlog->viewblogcomment($blog_id);
		$this->view->comments = $comments; 
	}
	
	public function deleteblogcommentAction(){
		$b_comment_id=$_REQUEST['comment_id'];
		//prd($b_comment_id);
		$where = "b_comment_id='".$b_comment_id."'";
		$delComments = $this->modelStatic->Super_Delete("blog_comment" , $where);
		exit();
	}
		
// like blog
	public function likeblogAction(){
			$this->_helper->layout->disableLayout();
			  $blog_url = $this->getRequest()->getParam("blog_url");
			  
			//  $blogUrl = $this->modelStatic->Super_Get("blogs","blog_id='".$blog_id."'","fetch",array('fields'=>'blog_url'));
			  $user_id = $this->view->user->user_id;
			  
			  $like_data=$this->modelStatic->Super_Get("blog_like","like_blog_url='".$blog_url."' and like_blog_user_id='".$user_id."'","fetch");
				if(!empty($like_data)){
					 $like_unlike=0;
					 $deleteWhere = "like_blog_url='".$blog_url."' and like_blog_user_id='".$user_id."'";
					 $deleteLike = $this->modelStatic->Super_Delete("blog_like",$deleteWhere);
				}else{
						 $like_unlike=1;
						 $todaydate=date('Y-m-d H:i:s');
						 $InsertData = array(
												"like_blog_url"=>$blog_url,
												"like_blog_user_id"=>$user_id,
												"like_blog_date"=>$todaydate
											);
						 $res=$this->modelStatic->Super_Insert("blog_like",$InsertData);
					}
			
			$likecount=  $this->modelStatic->Super_Get("blog_like","like_blog_url='".$blog_url."'", "fetchAll",array("fields"=>"like_id")); 
			echo count($likecount)." Likes|||".$like_unlike;
			exit();
			$this->_redirect("/blog/".$blog_url);
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