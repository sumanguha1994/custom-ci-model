<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FunctionModel extends CI_Model {
    //dbname prefix
    private $dbname = '';
    //write log file 
    private function write_log($level, $msg)
    {
        return log_message($level, $msg);
    }

	public function __construct()
	{
        parent::__construct();
        $this->load->database();
	}
    
    //insert into db #(dbname, data array);
    public function _insert($db, $array)
    {
        $this->security->xss_clean($array);
        if(!empty($db)):
            $this->db->insert($this->dbname.$db, $array);
            return $this->db->insert_id();
        else:
            $this->write_log('error', 'table name not given');
            return False;
        endif;
    }
    //get from db #(dbname) (limit, offset no., order_by ASC | DESC, order_by column name, specific id)
    public function _get($db, $limit = '', $offset = '', $order_by = '', $order_column = '', $id = '')
    {
        if(!empty($db)):
            $this->db->select('*');
            $this->db->from($this->dbname.$db);
            if(empty($id)):
                if(!empty($limit)):
                    $offset = empty($offset) ? 0 : $offset;
                    $this->db->limit($limit, $offset);
                endif;
                if(!empty($order_by)):
                    $order_column = empty($order_column) ? 'id' : $order_column;
                    $this->db->order_by($order_column, $order_by);
                endif;
                $this->db->get();
                $this->write_log('info', 'get all table values.');
                return $this->db->result_array();
            else:
                $this->db->where('id', $id);
                if(!empty($limit)):
                    $offset = empty($offset) ? 0 : $offset;
                    $this->db->limit($limit, $offset);
                endif;
                if(!empty($order_by)):
                    $order_column = empty($order_column) ? 'id' : $order_column;
                    $this->db->order_by($order_column, $order_by);
                endif;
                $this->db->get();
                $this->write_log('info', 'get table values by id.');
                return $this->db->row_array();
            endif;
        else:
            $this->write_log('error', 'table name not given');
            return FALSE;
        endif;
    }
    //delete from db #(dbname, specific id)
    public function _delete($db, $id)
    {
        if(!empty($db) || !empty($id)):
            $this->db->where('id', $id);
            $this->db->delete($this->dbname.$db);
            return TRUE;
        else:
            $this->write_log('error', 'table name not given or Delete id not given');
            return FALSE;
        endif;
    }
    //join tables #(dbname, joinning dbnames) (specific id)
    public function _join($db, $dbnames, $id = '')
    {
        if(!empty($db)):
            $this->db->select('*');
            $this->db->from($this->dbname.$db);
            if(!empty($id)):
                $this->db->where($this->dbname.$db.'id', $id);
            endif;
            for($i = 0; $i < count($this->dbname.$dbnames); $i++):
                $this->write_log('info', $this->dbname.$dbnames[$i].' table joinning with '.$this->dbname.$db);
                $this->db->join($this->dbname.$dbnames[$i], $this->dbname.$db.id.' = '.$this->dbname.$dbnames[$i].id);
            endfor;
            $this->db->get();
            return $this->db->result_array();
        else:   
            $this->write_log('error', 'table name not given');
            return False;
        endif;
    }
    
}
