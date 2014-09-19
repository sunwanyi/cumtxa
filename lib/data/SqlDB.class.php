<?PHP
/**
* 数据库类
* @author linyh,sg
*/
class SqlDB {
    const SELECT_ARRAY_KEY=0;
    const SELECT_ARRAY_VALUE=1;

    private static $db;
	private static $selfObject;
	
	/**
	* 构造函数，获取pdo引用
	*/
 	private function __construct() {
		if(isset(self::$db)) return;
		
		global $config;
		$host = $config['mysql']["host"];
		$port = $config['mysql']["port"];
		$user = $config['mysql']["username"];
		$pass = $config['mysql']["password"];
		$dbname = $config['mysql']["dbname"];

		try {
			$DSN = "mysql:host={$host};port={$port};dbname={$dbname}";
			$handle = new PDO($DSN,$user,$pass);
			$handle -> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			$handle-> query("set names utf8");
            self::$db = $handle;
		} catch(PDOException $e) {
			echo "Connection failed:".$e->getMessage();
		}
		return;
	}
	
	/**
	 * 获取数据库操作类的饮用对象
	 */
	static public function init(){
		if(!isset(self::$selfObject)){
			self::$selfObject= new SqlDB();
		}
		return self::$selfObject;
	}

    /**
     * 对id格式化为sql查询语句
     * @param array|int $id
     * @return string
     */
    protected function getIdCondition($id){
        if(!isset($id)){
            $idCondition="1=1";
        }else if(is_array($id)){
            $idCondition='(1=0 ';
            foreach($id as $v){
                $intV=(int)$v;
                $idCondition.="or `id`={$intV} ";
            }
            $idCondition.=")";
        }else{
            $intId=(int)$id;
            $idCondition="`id`={$intId}";
        }
        return $idCondition;
    }

    /**
     * 对一个字符串加引号并过滤
     * @param string $s
     * @return string
     */
    function quote($s){
        return self::$db->quote($s);
    }
	
	/**
	* 函数直接执行sql语句
	* @param string $sql 要执行的SQL语句
	* @return int 返回修改的行数
	*/
	public function sqlExec($sql){
		$result=self::$db->exec($sql);
		return $result;
	}

	/**
	* 检查sql语句是否能查询到内容
	* @param string $sql 要执行的SQL语句
	* @return bool 是否查询到内容
	*/
	function getExist($sql){
		$tmp=self::$db->query($sql);
		$ifGet = $tmp->fetch(PDO::FETCH_BOUND);
		$ifGetText=$ifGet?"True":"False";
		return $ifGet;
	}

	/**
	* 使用sql语句获取数据
	* 这一句sql必须保证只查询到一行一列
	* @param string $sql 要执行的SQL语句
	* @return string 获取到的内容
	*/
	function getValue($sql){
		$tmp=self::$db->query($sql);
		if(!empty($tmp)){
			$array= $tmp->fetch(PDO::FETCH_NUM);
			return $array[0];
		}else{
			return '';
		}
	}

	/**
	* 函数使用sql语句获取一行数据
	* @param string $sql 要执行的SQL语句
	* @return array 一个1维数组
	*/
	function getOne($sql){
		$tmp=self::$db->query($sql);
		if(!empty($tmp)){
			$array= $tmp->fetch(PDO::FETCH_ASSOC);
			return $array;
		}else{
			return array();
		}
	}

    /**
     * 函数使用sql语句获取所有查询到的数据
     * @param string $sql 要执行的SQL语句
     * @return array 一个2维数组
     */
    function getAll($sql){
        $tmp=self::$db->query($sql);
        if(!empty($tmp)){
            $array= $tmp->fetchall(PDO::FETCH_ASSOC);
            return $array;
        }else{
            return array();
        }
    }
	function get_all($sql){
		return $this->getAll($sql);
	}

    /**
     * 函数使用sql语句获取所有查询到的数据
     * @param string $sql 要执行的SQL语句
     * @return \SqlDBStatement|null
     */
    function query($sql){
        // TODO 这里要自己处理错误信息，重要！！需要统一处理
        try {
            $stmt = self::$db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->execute();
            return new SqlDBStatement($stmt);
        }catch (PDOException $e) {
            echo $e->getMessage();
        }
        return null;
    }

    /**
     * 函数使用sql语句获取所有查询到的数据
     * @unused
     * @param string $tableName
     * @param int|array $id
     * @param array|string $data
     * @param int $selectArrayType
     * @return array 一个2维数组
     */
    private function selectOne($tableName, $id, $data, $selectArrayType=self::SELECT_ARRAY_KEY){
        // TODO 数据库查询类的SELECT系列函数有待完成
        $db=self::$db;
        if(!is_array($data)|| count($data)<=0){
            trigger_error("SqlDB->selectOne 查询参数错误");
        }
        // 构造SQL查询语句
        $dataKey=($selectArrayType==self::SELECT_ARRAY_KEY)?
            $dataKey=array_keys($data): $dataKey=$data;
        $selectColumn=implode(",",$dataKey);
        $sql="SELECT {$selectColumn} FROM {$tableName} where ".$this->getIdCondition($id)." and `enable`='1' LIMIT 1";
        $tmp=$db->query($sql);
        if(!empty($tmp)){
            $array= $tmp->fetchall(PDO::FETCH_BOUND);
            return $array;
        }else{
            return array();
        }
    }

	/**
	* 插入数据
	* @param string $tableName 要插入的表名
	* @param array $data 要插入的数组，键名为数据库字段名，值为要插入的值
	* @return int 插入了多少内容
	*/
	function insert($tableName,$data){
		$db=self::$db;

        $line_arr=array();	//插入的列
        $value_arr=array();	//插入的值
		if(!is_array($data) || count($data)<=0){
			return false; //没有要插入的数据
		}else{		
			foreach($data as $key=>$result){			
				$line_arr[]="`{$key}`";
				$value_arr[]=$db->quote($result);
			}
			$line=implode(",",$line_arr);
			$value=implode(",",$value_arr);
			$sql="INSERT INTO `{$tableName}`({$line})values({$value});";
			return $this->sqlExec($sql);
		}
	}
	function insertId(){
		$db=self::$db;
		return $db->lastInsertId();
	}

	/**
	* 更新数据
	* @param string $tableName 表名
	* @param array|int $id 要修改的id号，如果id不存在则修改失败
	* @param array $data 要更新的数据，键名为数据库字段名，值为要插入的值
	* @return int 修改了多少内容
	*/
	function update($tableName,$id,$data){
		$db=self::$db;
		
		//查询要修改的id号，如果id不存在则修改失败
		$up_arr=array();	//更新的语句
		if(!is_array($data) || count($data)<=0){
			return 0; //没有要插入的数据
		}else{
			foreach($data as $key=>$value){
				$up_arr[]="`{$key}`=".$db->quote($value);
			}
			$up_str=implode(",",$up_arr);
			$sql="UPDATE {$tableName} set {$up_str} where ".$this->getIdCondition($id);
			return $this->sqlExec($sql);
		}
	}

	/**
	* 添加或者更新数据，根据id存在与否新建或者修改一条数据
	* @param string $tableName 表名
	* @param array|int $id 要修改的id号，如果id不存在则新建一行数据
	* @param array $data 要更新的数据，键名为数据库字段名，值为要插入的值
	* @return int 修改了多少内容
	*/
	function modify($tableName,$id,$data){
		// 先修改，若没有修改成功则插入一行
		$updateModifyNum=$this->update($tableName,$id,$data);
		// var_dump($updateModifyNum);
		if($updateModifyNum){
			return $updateModifyNum;
		}else{
			return $this->insert($tableName,$data);
		}
	}

	/**
	* 删除数据
	* @param string $tableName 表名
	* @param array|int $id
	* @return int 删除了多少内容
	*/
	function delete($tableName,$id){
		$sql="DELETE from {$tableName} where ".$this->getIdCondition($id);
		return $this->sqlExec($sql);
	}

	/**
	* 将一条数据enable修改为false
	* @param string $tableName 表名
	* @param array|int $id
	* @return int 修改了多少内容
	*/
	function hide($tableName,$id){
		return $this->update($tableName,$id,array("enable"=>'0'));
	}

	/**
	* 切换一条数据的enable，如果原本是true修改为false，原本是false修改为true
	* @param string $tableName 表名
	* @param array|int $id
	* @return int 修改了多少内容
	*/
	function toggle($tableName,$id){
		$sql="update `{$tableName}` set `enable`=1-`enable` where `id`='{$id}'";
		return $this->sqlExec($sql);
	}
	
}


/**
 * 数据库结果集，用于foreach循环
 * @author linyh,sg
 */
class SqlDBStatement implements Iterator {
    /**
     * 记录当前是第几条记录
     * @var int
     */
    protected $currentNum=0;
    /**
     * @var array
     */
    protected $currentData;
    // 要处理的PDOStatement
    protected $statement;
    /**
     * @var callable
     */
    protected $dealFunction=null;

    /**
     * @param PDOStatement $s
     */
    public function __construct(PDOStatement $s) {
        $this->statement=$s;
    }

    public function getArray(){
        return iterator_to_array($this,false);
    }

    /**
     * 设置每一行数据的处理方式
     * 参数需要时一个函数，有一个参数，类型为数组，用于处理每一条获取到的数据
     * 注意，最后的时候必须要返回处理完的值
     * @param callable $f
     */
    public function setDealMethod($f){
        if(is_callable($f)){
            $this->dealFunction=$f;
        }
    }

    // Iterator接口函数调用顺序：rewind\next valid current key ……

    public function rewind(){
        $this->currentData=$this->statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_FIRST);
        $this->currentNum=0;
    }
    public function next(){
        $this->currentNum++;
        $this->currentData=$this->statement->fetch(PDO::FETCH_ASSOC,PDO::FETCH_ORI_NEXT);
    }
    public function valid(){
        return isset($this->currentData)&&!empty($this->currentData);
    }
    public function current(){
        if($f=$this->dealFunction){
            return $f($this->currentData);
        }else{
            return $this->currentData;
        }
    }
    public function key(){
        return $this->currentNum;
    }
}