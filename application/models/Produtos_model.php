<?php
class Produtos_model extends CI_Model
{

    /**
     * author: Ramon Silva
     * email: silva018-mg@yahoo.com.br
     *
     */
    
    public function __construct()
    {
        parent::__construct();
    }

    
    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('idProdutos', 'desc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
        }
        
        $query = $this->db->get();
        
        $result =  !$one  ? $query->result() : $query->row();
        return $result;
    }

    public function getById($id)
    {
        $this->db->where('idProdutos', $id);
        $this->db->limit(1);
        return $this->db->get('produtos')->row();
    }
    
    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        }
        
        return false;
    }
    
    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }
        
        return false;
    }
    
    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }
        
        return false;
    }
    
    public function count($table)
    {
        return $this->db->count_all($table);
    }

    public function updateEstoque($produto, $quantidade, $operacao = '-')
    {
        $sql = "UPDATE produtos set estoque = estoque $operacao ? WHERE idProdutos = ?";
        return $this->db->query($sql, [$quantidade, $produto]);
    }
    public function anexar($os, $anexo, $url, $thumb, $path)
    {
        $this->db->set('anexo', $anexo);
        $this->db->set('url', $url);
        $this->db->set('thumb', $thumb);
        $this->db->set('path', $path);
        $this->db->set('produto_id', $os);

        return $this->db->insert('anexos_produtos');
    }

    public function getAnexos($os)
    {
        $this->db->where('produto_id', $os);
        return $this->db->get('anexos_produtos')->result();
    }
}
