<?php
namespace Gframe;

class Db{
    private $_pdo = null; //PDO连接对象
    private $config =array();  //数据库配置

    /**
     * 构造函数
     * db constructor.
     */
    public function __construct($config){
        if (!class_exists('PDO')) throw_exception("不支持:PDO");

        $this->config['dbtype'] = $config['dbtype']?$config['dbtype']:getC('DB_TYPE');
        $this->config['host']= $config['host']?$config['host']:getC('DB_HOST');
        $this->config['dbname'] = $config['dbname']?$config['dbname']:getC('DB_NAME');
        $this->config['user'] = $config['user']?$config['user']:getC('DB_USER');
        $this->config['passwd'] = $config['passwd']?$config['passwd']:getC('DB_PWD');
        $this->config['dsn'] = $this->config['dbtype'].':host='.$this->config['host'].';dbname='.$this->config['dbname'];
        $this->config['db_encoding'] = $config['db_encoding']?$config['db_encoding']:getC('DB_ENCODING');
        $this->connect();
    }

    /**
     * PDO链接
     */
    private function connect(){
        try{
            $this->_pdo = new \PDO($this->config['dsn'],$this->config['user'],$this->config['passwd'],array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        }catch (PDOException $e){
            $this->outputError($e->getMessage());
        }
    }

    /**
     * 查询
     * @param $sql  SQL语句
     * @param string $queryType  查询类型 ALL为多条 其他为单条
     * @return null 返回结果
     */
    public function query($sql,$queryType = 'ALL'){
        $recordset = $this->_pdo->query($sql);
        $this->getPDOError();
        if($recordset){
            $recordset->setFetchMode(\PDO::FETCH_ASSOC);  //设置查询结果格式
            if($queryType =='ALL'){
                $result =$recordset->fetchall();
            }else{
                $result = $recordset->fetch();
            }
        }else{
            $result = null;
        }
        return $result;
    }

    /**
     * 更新
     * @param $table 表名
     * @param $arrDataVlue 修改的字段和值
     * @param string $where 条件
     * @return null 返回影响条数
     */
    public function update($table,$arrDataValue,$where=''){
        //检查字段是否存在表内
        $this->checkFields($table,$arrDataValue);
        //判断查询条件 不为空才操作
        if($where){
            $sql = '';
            foreach($arrDataValue as $k=>$v){
                $sql .= ",".$k." = '".$v."'";
            }
            $sql = ltrim($sql,',');
            $sql = "UPDATE $table  SET $sql WHERE $where";
            $result = $this->_pdo->exec($sql);
            $this->getPDOError();
        }else{
            $result = null;
        }
        return $result;
    }

    /**
     * 插入
     * @param $table 表名
     * @param $arrDataValue 插入的字段和值
     * @return mixed 返回影响条数
     */
    public function insert($table,$arrDataValue){
        //检查字段是否存在表内
        $this->checkFields($table,$arrDataValue);
        $sql ="INSERT INTO $table (";
        $sql .= implode(",",array_keys($arrDataValue));
        $sql .= ") VALUES('";
        $sql .=implode("','",array_values($arrDataValue));
        $sql .= "')";
        $result = $this->_pdo->exec($sql);
        $this->getPDOError();
        return $result;
    }

    /**
     * 删除
     * @param $table 表名
     * @param $arrDataValue 条件
     * @return mixed 返回影响条数
     */
    public function delete($table,$where=''){
        if($where == ''){
            $this->outputError('条件不能为空');
        }else{
            $sql = "DELETE FROM $table WHERE $where";
            $result = $this->_pdo->exec($sql);
            $this->getPDOError();
            return $result;
        }
    }

    /**
     * 执行SQL语句
     * @param $sql sql语句
     * @return mixed 返回资源
     */
    public function execSQL($sql){
        $result = $this->_pdo->exec($sql);
        $this->getPDOError();
        return $result;
    }

    /**
     * 事务处理多条SQL
     * @param $arrSql SQL语句数组
     * @return bool 执行成功与否
     */
    public function execTransaction($arrSql){
        $k = 1; //成功标识
        $this->beginTransaction(); //开启事务
        foreach($arrSql as $v){  //循环执行SQL语句数组
            if($this->execSQL($v)==0){  //判断是否执行成功
                $k = 0;  //如果执行失败 就标识失败
            }
        }
        if($k ==1){ //如果标识为成功就提交事务
            $this->commit();
            return true;
        }else{  //标识失败 就回滚事务
            $this->rollback();
            return false;
        }
    }


    /**
     * 事务开始
     */
    private function beginTransaction(){
        $this->_pdo->beginTransaction();
    }

    /**
     * 事务回滚
     */
    private function rollback(){
        $this->_pdo->rollback();
    }

    /**
     * 事务提交
     */
    private function commit(){
        $this->_pdo->commit();
    }

    /**
     * 检查字段是否在表中存在
     * @param $table
     * @param $arrDataValue
     */
    private function checkFields($table,$arrDataValue){
        $fields = $this->getFields($table);
        foreach($arrDataValue as $k=>$v){
            if(!in_array($k,$fields)){
                $this->outputError("字段'$k'不在表中");
            }
        }
    }

    /**
     * 获取表格的全部字段
     * @param $table 表明
     * @return array 字段数组
     */
    private function getFields($table){
        $fields = array();
        $recordset = $this->_pdo->query("SHOW COLUMNS FROM $table");
        $this->getPDOError();
        $recordset->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $recordset->fetchall();
        foreach($result as $v){
            $fields[] = $v['Field'];
        }
        return $fields;
    }

    /**
     * 捕获PDO错误 并将错误输出
     * @throws Exception
     */
    private function getPDOError(){
        if($this->_pdo->errorCode() !='00000'){
            $arrError = $this->_pdo->errorInfo();
            $this->outputError($arrError[2]);
        }
    }

    /**
     * 输出错误
     * @param $ErrMsg
     * @throws Exception
     */
    private function outputError($ErrMsg){
        throw new Exception('SQL Error:'.$ErrMsg);
    }

    /**
     * 关闭数据库
     */
    public function destruct(){
        $this->_pdo = null;
    }
}