<?php

    class DB{
        protected $db;
        protected $user;
        protected $pass;
        protected $pdo;
        protected $query;
        protected $table;

        private function __construct($db="invest",$user="root",$pass=""){
            $this->db=$db;
            $this->user=$user;
            $this->pass=$pass;
            $this->connection();
        }

        private function connection(){
            try{
                $this->pdo=new PDO("mysql:host=localhost;dbname={$this->db}",$this->user,$this->pass);
            }
            catch(Exception $e){
                die($e->getMessage());
            }
        }

        private static function getInstance(){
            return new static();
        }

        public static function table($table){
            $db=static::getInstance();
            $db->table=$table;
            return $db;
        }

        public function get(){
            $sql=$this->pdo->prepare($this->query);
            $sql->execute();
            $res=$sql->fetchAll(PDO::FETCH_OBJ);
            return $res;
            // return $this->query;
        }
        
        public function run(){
            $sql=$this->pdo->prepare($this->query);
            $sql->execute();
        }

        public function all(){
            $this->query="SELECT * FROM {$this->table}";
            return $this;
        }
        
        public function where($col,$oprator,$val)
        {
            $this->query=$this->query." WHERE {$col} {$oprator} {$val}";
            return $this;
        }

        public function find($id){
            $this->all()->where('id','=',$id);
            return $this->get();
        }

        public function OrderBy($col,$order="ASC"){
            $this->query=$this->query." ORDER BY {$col} {$order}";
            return $this;
        }

        public function insert($field,$data){
            if(is_array($data)){
                $info="'".implode("','",$data)."'";
            }
            else{
                $info="'".$data."'";
            }
            if(is_array($field)){
                $fields=implode(",",$field);
            }
            else{
                $fields=$field;
            }
             $sql=$this->pdo->prepare("insert into {$this->table} ({$fields}) VALUES ($info)");
             $sql->execute();
        }

        public function delete(){
            $this->query="DELETE FROM {$this->table}";
            return $this;
        }

        public function update($field,$data){
            foreach($field as $key=>$val){
                $txt[]=$val."='".$data[$key]."'"; 
            }
            $query=implode(",",$txt);
            $this->query="UPDATE {$this->table} set ".$query;
            return $this;
        }

        public function query($query){
            $this->query=$query;
            return $this;
        }

   }

   //var_dump(DB::table('users')->query("SELECT * FROM users")->where('id','=','5')->get());
   //var_dump(DB::table('users')->all()->where('id','=',4)->get());
   //var_dump(DB::table('users')->find(4));
   //DB::table('users')->insert(['username','password'],['alireza',12345666]);
   //DB::table('users')->delete()->where('id','=',4)->run();
   //DB::table('users')->update(['username','password'],['ali','123456'])->where('id','=','5')->run();
?>
