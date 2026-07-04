<?php
/**
 * Base Model Class
 * Core File
 */

require_once __DIR__ . '/Database.php';

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Run a general query with parameters
     */
    protected function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Get all rows
     */
    public function findAll() {
        $sql = "SELECT * FROM {$this->table}";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Find a row by primary key
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return $this->query($sql, ['id' => $id])->fetch();
    }

    /**
     * Insert data into table
     */
    public function insert($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        $this->query($sql, $data);
        return $this->db->lastInsertId();
    }

    /**
     * Update data in table
     */
    public function update($id, $data) {
        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= "{$key} = :{$key}, ";
        }
        $fields = rtrim($fields, ', ');
        $sql = "UPDATE {$this->table} SET {$fields} WHERE {$this->primaryKey} = :pk_id";
        
        $data['pk_id'] = $id;
        return $this->query($sql, $data);
    }

    /**
     * Delete a row by primary key (hard delete)
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->query($sql, ['id' => $id]);
    }
}
