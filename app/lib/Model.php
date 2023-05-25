<?php

class Model
{
    private $conn;
    protected static $table;
    protected static $columns;

    public $select = [];
    public $where;
    public $orderBy;
    public $limit;
    public $elements = [
        ' SELECT ',
        ' FROM ',
        ' WHERE ',
        ' ORDER BY ',
        ' LIMIT '
    ];

    public function __construct()
    {
        $instance = Database::getInstance();
        $this->conn = $instance->getConnection();
    }
    
    public function select($select = [])
    {
        $this->select = $select;
        return $this;
    }

    public function where($conditions)
    {
        $this->where = $conditions;
        return $this;
    }

    public function orderBy($column, $order = 'DESC')
    {
        $this->orderBy = "{$column} {$order}";
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function sql()
    {
        $sql = $this->elements[0];
        
        if (is_array($this->select) && !empty($this->select)) {
            $sql .= implode(', ', $this->select);
        } else {
            $sql .= '*';
        }

        $sql .= $this->elements[1];
        $sql .= static::$table;

        if (!empty($this->where)) {
            $conditions = $this->where;
            $firstCondition = array_shift($conditions);
            
            $sql .= $this->elements[2];
            $sql .= $firstCondition;
            
            if (count($conditions)) {
                foreach ($conditions as $condition) {
                    $sql .= " AND {$condition}";
                }
            }
        }

        if (!empty($this->orderBy)) {
            $sql .= $this->elements[3];
            $sql .= $this->orderBy;
        }

        if (!empty($this->limit)) {
            $sql .= $this->elements[4];
            $sql .= $this->limit;
        }
        
        return $sql;
    }

    public function get()
    {
        $conn = $this->conn;
        $sql = $this->sql();

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return [];
    }

    public function first()
    {
        $conn = $this->conn;
        $sql = $this->sql();

        $result = $conn->query($sql);

        return $result->fetch_row();
    }

    public function insert($data)
    {
        $conn = $this->conn;
        
        $table = static::$table;
        $dbColumns = static::$columns;

        $columns = [];
        $values = [];
        $bindParams = [];
        $bindTypes = [];
        
        foreach ($data as $column => $value) {
            if (!in_array($column, $dbColumns)) {
                throw new Exception('Unkown column');
            }

            $columns[] = $column;
            $values[] = $value;

            $bindParams[] = '?';
            $bindTypes[] = $this->bindCheck($value);
        }

        $columns = implode(', ', $columns);
        $bindParams = implode(', ', $bindParams);
        $bindTypes = implode('', $bindTypes);
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$bindParams})";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($bindTypes, ...$values);
        $inserted = $stmt->execute();

        if (!$inserted) {
            throw new Exception('Record not inserted');
        }

        return $conn->insert_id;
    }

    public function update($data, $updateColumn)
    {
        $conn = $this->conn;
        
        $table = static::$table;
        $dbColumns = static::$columns;

        $values = [];
        $bindParams = [];
        $bindTypes = [];
        
        foreach ($data as $column => $value) {
            if (!in_array($column, $dbColumns)) {
                throw new Exception('Unkown column');
            }
            
            $bindParams[] = "{$column} = ?";
            $values[] = $value;
            $bindTypes[] = $this->bindCheck($value);
        }

        $key = key($updateColumn);

        if (!in_array($key, $dbColumns)) {
            throw new Exception('Unkown column');
        }

        $val = $updateColumn[$key];

        $values[] = $val;
        $bindTypes[] = $this->bindCheck($val);

        $bindParams = implode(', ', $bindParams);
        $bindTypes = implode('', $bindTypes);
        
        $sql = "UPDATE {$table} SET {$bindParams} WHERE {$key} = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($bindTypes, ...$values);
        $inserted = $stmt->execute();

        if (!$inserted) {
            throw new Exception('Record not updated');
        }

        return $stmt->affected_rows;
    }

    public function delete($deleteColumn)
    {
        $conn = $this->conn;
        
        $table = static::$table;
        $dbColumns = static::$columns;

        $key = key($deleteColumn);

        if (!in_array($key, $dbColumns)) {
            throw new Exception('Unkown column');
        }

        $value = $deleteColumn[$key];
        $bindType = $this->bindCheck($value);

        $sql = "DELETE FROM {$table} WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($bindType, $value);
        $inserted = $stmt->execute();

        if (!$inserted) {
            throw new Exception('Record not deleted');
        }

        return true;
    }

    private function bindCheck($type)
    {
        switch($type) {
            case is_string($type):
                return 's';
            case is_integer($type):
                return 'i';
        }
    }
}