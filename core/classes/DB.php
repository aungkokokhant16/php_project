
<?php

class DB
{
    private static $dbh=null;
    private static $res,$data,$count,$sql;

    public function __construct()
    {
        self::$dbh= new PDO('mysql:host=localhost;dbname=php_project','root','');
        // echo 'connected';
        self::$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }

    public function query($params=[])
    {
        self::$res= self::$dbh->prepare(self::$sql);
        self::$res->execute($params);
        return $this;
    }

    public function get()
    {
        $this->query();
        self::$data = self::$res->fetchAll(PDO::FETCH_OBJ);
        return self::$data;
    }

    public function getOne()
    {
        $this->query();
        self::$data = self::$res->fetch(PDO::FETCH_OBJ);
        return self::$data;
    }

    public function count()
    {
        $this->query();
        self::$count= self::$res->rowCount();
        return self::$count;
    }

    public static function table($table)
    {
        $sql = "select * from $table";
        self::$sql = $sql;
        $db= new DB();
        // $db->query();
        return $db;//same like this;
    }

    public function orderBy($col, $value)
    {
        self::$sql .= " order by $col $value";
        $this->query();
        return $this;
    }

    public function where($col,$operator,$value='')
    {
        if( func_num_args() ==2){
            self::$sql .= " where $col='$operator'";
        }else{
            self::$sql .= " where $col $operator '$value'";
        }
        
      
        return $this;
    }

    public function andwhere($col,$operator,$value='')
    {
        if( func_num_args() ==2){
            self::$sql .= " and $col='$operator'";
        }else{
            self::$sql .= " and $col $operator '$value'";
        }
        
        
        return $this;
    }

    public function orWhere($col,$operator,$value='')
    {
        if( func_num_args() ==2){
            self::$sql .= " or $col='$operator'";
        }else{
            self::$sql .= " or $col $operator '$value'";
        }
        
       
        return $this;
    }

    public static function create($table,$data)
    {
        
        $db = new DB();
        $str_col = implode(',', array_keys($data));
        $v = "";
        $x = 1;
        foreach($data as $d){
            $v .= "?";
            if($x < count($data)){
                $v .=",";
                $x++;
            }
        }
        
        $sql = " insert into $table ($str_col) values($v)";
        self::$sql = $sql;
        $values = array_values($data);
        $db->query($values);
        $id =  self::$dbh->lastInsertId();
        return DB::table($table)->where('id',$id)->getOne();

       
    }

    public static function update($table,$data,$id)
    {
        //update users set name=>?,age=>?,location=>? whree id =3
        $db = new DB();
        $sql = "update $table set ";
        $value = "";
        $x = 1;

        foreach ($data as $k=>$v)
        {
            $value .= "$k=?";
            if($x<count($data))
            {
                $value .= ',';
                $x++;;
            }
        }

        $sql .= "$value where id = $id";
        self::$sql = $sql;
        
        $db->query(array_values($data));
        return DB::table($table)->where('id',$id)->getOne();
    }

    public static function delete($table,$id)
    {
        $sql = "delete from $table where id = $id";
        self::$sql = $sql;
        $db = new DB();
        $db->query();
        return true;
    }

    public static function raw($sql)
    {   
        $db = new DB();
        self::$sql = $sql;
        return $db;
    }

    public function paginate($records_per_page)
    {
        if(isset($_GET['page']))
        {
            $page_no = $_GET['page'];
        }else{
            $page = $_GET['page']=1;
        }
        if($_GET['page']<1)
        {
            $page = 1;

        }




        // if (!isset($_GET['page']))
        // {
        //     $page_no = 1;
        // }
        // if(isset($_GET['page'])and $_GET['page'] < 1){
        //     $page_no = 1;
        // }

        //get total count
        $this->query();
        $count = self::$res->rowcount();
        

        $index = ($page_no - 1) * $records_per_page;
        //select * from users limti 0,5
        self::$sql .= " limit $index,$records_per_page";
        $this->query();
        self::$data = self::$res->fetchAll(PDO::FETCH_OBJ);

       
        
        
        $prev_no = $page_no - 1 ;
        $next_no = $page_no + 1;


        $prev_page = "?page=".$prev_no;
        $next_page = "?page=".$next_no;

        $data = [
            'data'=>self::$data,
            'total'=>$count,
            'pre_page'=>$prev_page,
            'next_page'=>$next_page,
        ];

        return $data;
    }

}

// $db= new DB();
// $users = $db->query('select * from users')->get();
// echo $db->query('select * from users')->count();
// print_r($users);
// $db->query("select * from users")->get();


// $user = DB::table('crud')->orderBy('name','asc')->get();
// echo '<pre>';
// var_dump($user);

// $user = DB::table('users')->where('name','like','%m%')->orWhere('location','Yangon')->get();
// echo "<pre>";
// var_dump($user);
// // DB::table('product')->where('price')

// $user = DB::create('users',[
//     'name'=>'Banana',
//     'age'=>'23',
//     'location'=>'Yangon',
   
// ]);
// print_r($user);

// $user = DB::update('users',[
//     'name'=>'update',
//     'age'=>'25',
//     'location'=>'update',
// ],6);
// print_r($user);

// if(DB::delete('users',5))
// {
//     echo 'success';
// }


// $user = DB::table('users')->paginate(5);
//  $user = DB::raw("select * from users")->paginate(5);

//  print_r($user);
