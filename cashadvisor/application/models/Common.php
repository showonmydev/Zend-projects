<?php

    class Application_Model_Common extends Zend_Db_Table_Abstract
    {

        protected $_name = "admin";

        function init()
        {
            
        }
		
		public function gethttp($mystring)
		{
			$findme   = 'http://';
       		$pos =  strpos($mystring, $findme);
			if ($pos === false)
			{
				$findme1   = 'https://';
				$pos1 =  strpos($mystring, $findme1);
				if ($pos1 === false)
				{
					$strname="https:".$mystring;
				}
				else
				{
					$strname=$mystring;
				}
			}
			else
			{
				$strname=$mystring;
			}
			return $strname;
		}
		

        public function getMaxId($tableName, $idFieldName,$whereCond=1)
        {
            $this->_name = $tableName;

            $maxId = $this->select()
                            ->from($this, array(new Zend_Db_Expr("max($idFieldName) as maxid")))
							->where($whereCond)
                            ->query()->fetch();
            return $maxId;
        }
		
		public function getAvg($tableName, $idFieldName,$whereCond=1)
        {
            $this->_name = $tableName;

            $maxId = $this->select()
                            ->from($this, array(new Zend_Db_Expr("avg($idFieldName) as average")))
							->where($whereCond)
                            ->query()->fetch();
            return $maxId;
        }

        public function getTotalCount($idFieldName, $tableName, $whereCond = 1)
        {
            $this->_name = $tableName;

            $totalcount = $this->select()
                            ->from($this, array(new Zend_Db_Expr("count($idFieldName) as totalcount")))
                            ->where($whereCond)
                            ->query()->fetch();
            return $totalcount;
        }
		
		public function getSum($idFieldName, $tableName, $whereCond = 1)
        {
            $this->_name = $tableName;

            $totalcount = $this->select()
                            ->from($this, array(new Zend_Db_Expr("SUM($idFieldName) as totalsum")))
                            ->where($whereCond)
                            ->query()->fetch();
            return $totalcount;
        }
		
		 public function getTotalCountWithJoin($idFieldName, $tableName, $whereCond = 1,$joinTableName,$joinCondition)
        {
           

            $totalcount = $this->getAdapter()->select()
                            ->from($tableName, array(new Zend_Db_Expr("count($idFieldName) as totalcount")))
							->join($joinTableName,$joinCondition)
                            ->where($whereCond)
                            ->query()->fetch();
							
            return $totalcount;
        }

        public function getOneRecordDetail($tableName, $cond = "1", $limit = NULL, $order = NULL)
		{
            $this->_name = $tableName;
            if (empty($cond))
                $cond = "1";

            $records = $this->select()->where($cond);

            if ($limit != NULL)
                {
					$limitClause=explode(',',$limit);
					if(count($limitClause)>1)
					{
						$records = $records->limit($limitClause[0],$limitClause[1]);
					}
					else
					{
						$records = $records->limit($limit);
					}					
				}

            if ($order != NULL)
                $records = $records->order($order);

//            prd($records->__toString());

            $records = $records->query()->fetch();
            return $records;			
		}
		
	    public function getAllRecordDetail($tableName, $cond = "1", $limit = NULL, $order = NULL)
        {
            $this->_name = $tableName;

            if (empty($cond))
                $cond = "1";

            $records = $this->select()->where($cond);

            if ($limit != NULL)
                {
					$limitClause=explode(',',$limit);
					if(count($limitClause)>1)
					{
						$records = $records->limit($limitClause[0],$limitClause[1]);
					}
					else
					{
						$records = $records->limit($limit);
					}
					
					
				}

            if ($order != NULL)
                $records = $records->order($order);
//echo $records; 
//            prd($records->__toString());

            $records = $records->query()->fetchAll();
            return $records;
        }
		
		 public function getAllRecordDetailobj($tableName, $cond = "1", $limit = NULL, $order = NULL)
        {
            $this->_name = $tableName;

            if (empty($cond))
                $cond = "1";

            $records = $this->select()->where($cond);

            if ($limit != NULL)
                {
					$limitClause=explode(',',$limit);
					if(count($limitClause)>1)
					{
						$records = $records->limit($limitClause[0],$limitClause[1]);
					}
					else
					{
						$records = $records->limit($limit);
					}
					
					
				}

            if ($order != NULL)
                $records = $records->order($order);
			
            //$records = $records->query()->fetchAll();
            return $records;
        }
		
		
        public function getAllRecordFromLeftJoin($fromTableArr, $otherTableArr, $joinCond, $whereCond = 1, $limit = NULL, $order = NULL)
        {
            if (empty($whereCond))
                $whereCond = 1;

            $records = $this->getAdapter()->select()
                    ->from($fromTableArr)
                    ->where($whereCond)
                    ->joinLeft($otherTableArr, $joinCond);

            if ($limit != NULL)
			{
				$limitClause=explode(',',$limit);
					if(count($limitClause)>1)
					{
						$records = $records->limit($limitClause[0],$limitClause[1]);
					}
					else
					{
						$records = $records->limit($limit);
					}
					 //$records = $records->limit($limit);
			}
               

            if ($order != NULL)
                $records = $records->order($order);
           // echo $records; 
            $records = $records->query()->fetchAll();
            return $records;
        }
		
		public function getAllRecordFromLeftJoinobj($fromTableArr, $otherTableArr, $joinCond, $whereCond = 1, $limit = NULL, $order = NULL)
        {
            if (empty($whereCond))
                $whereCond = 1;

            $records = $this->getAdapter()->select()
                    ->from($fromTableArr)
                    ->where($whereCond)
                    ->joinLeft($otherTableArr, $joinCond);

            if ($limit != NULL)
			{
				$limitClause=explode(',',$limit);
					if(count($limitClause)>1)
					{
						$records = $records->limit($limitClause[0],$limitClause[1]);
					}
					else
					{
						$records = $records->limit($limit);
					}
					 //$records = $records->limit($limit);
			}
               

            if ($order != NULL)
                $records = $records->order($order);
           // echo $records; 
           // $records = $records->query()->fetchAll();
            return $records;
        }

        public function insertRecordData($data_array, $tableName = NULL)
        {
			try{
            if ($tableName != NULL)
                $this->_name = $tableName;
           
			$isSend = $this->insert($data_array); 
			return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Inserted","inserted_id"=>$isSend) ;
			}
			catch(Zend_Exception  $e) {/* Handle Exception Here  */
			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>$e->getCode()) ;
 		    }
        }

        public function updateRecordData($data_array, $whereCond, $tableName = NULL)
        { 
		    /*if ($tableName != NULL)
                $this->_name = $tableName;

            $this->update($data_array, $whereCond);*/
			try{
            if ($tableName != NULL)
                $this->_name = $tableName;
           
			$isSend = $this->update($data_array,$whereCond); 
			return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Updated") ;
			}
			catch(Zend_Exception  $e) {/* Handle Exception Here  */
			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>$e->getCode()) ;
 		    }
        }

        public function deleteRecord($whereCond, $tableName = NULL)
        {
           /* if ($tableName != NULL)
                $this->_name = $tableName;

            $this->delete($whereCond);*/
			try{
            if ($tableName != NULL)
                $this->_name = $tableName;
           
			$isSend = $this->delete($whereCond); 
			return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Deleted") ;
			}
			catch(Zend_Exception  $e) {/* Handle Exception Here  */
			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>$e->getCode()) ;
 		    }
        }

        public function getRecordMaxId($idFieldName, $tableName)
        {
            $this->_name = $tableName;

            $maxid = $this->select()
                            ->from($this, array(new Zend_Db_Expr("max($idFieldName) as maxId")))
                            ->query()->fetch();
            return $maxid;
        }

        public function getdata($tableName, $whereCond = 1, $order = NULL)
        {
            $this->_name = $tableName;

            $record = $this->select()->from($tableName)->where($whereCond);

            if ($order != NULL)
                $records = $records->order($order);

            $record = $record->query()->fetch();
            return $record;
        }

        public function is_exists($tableName, $whereCond = 1)
        {
            $this->_name = $tableName;

            $record = $this->select()->where($whereCond);
			//prd($record->__toString());
            $record = $record->query()->fetch();
            return $record;
        }

        public function updateAndReplaceData($tableName, $columnName, $toReplaceName, $fromReplaceName, $whereString)
        {
            $sql = "UPDATE $tableName  SET $columnName = REPLACE($columnName, ',$toReplaceName', '$fromReplaceName') WHERE $whereString";
            $this->getAdapter()->query($sql);
        }

        public function runCoreQuery($yourQuery)
        {
            return $this->getAdapter()->query($yourQuery)->fetchAll();
        }

        public function getLastInsertedId($tableName)
        {
            return $this->getAdapter()->lastInsertId($tableName);
        }

		
}