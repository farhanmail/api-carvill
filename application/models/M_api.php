<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_api extends CI_Model {

    //-------------------------------------- LOGIN ---------------------------------------------------   

    public function proses_login($username, $password)
    {
        return $this->db->query("SELECT * FROM customer WHERE username = '$username' AND password = MD5('$password')");
    }

    //-------------------------------------- REGISTER ---------------------------------------------------   

    public function cek_user_register($username)
    {
        return $this->db->query("SELECT username FROM customer WHERE username = '$username'");
    }

    public function cek_if_register($username)
    {
        return $this->db->query("SELECT * FROM customer WHERE username = '$username' AND username IS NOT NULL");
    }

    public function cek_user_exist_register($username)
    {
        return $this->db->query("SELECT username FROM customer WHERE username = '$username'");
    }

    public function proses_register()
    {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $alamat = $_POST['alamat'];
        $nama = $_POST['nama'];
        $gende = $_POST['gender'];
        $no_telepon = $_POST['no_telepon'];
        $no_ktp = $_POST['no_ktp'];
        $role_id = $_POST['role_id'];

        $this->db->query("INSERT into customer (username,password,alamat,nama,gender,no_telpon,no_ktp,role_id) values ('$username','$password','$alamat','$nama','$gende','$no_telepon','$no_ktp''$role_id')");
    }


    //-------------------------------------- Contoh Crud --------------------------------------------------- 
    
    public function get_all()
    {
        $this->db->select("*");
        $this->db->from("villa");
        $this->db->order_by("id_villa", "ASC");
        return $this->db->get();

    }


    public function simpan_mahasiswa($data)
    {
        $simpan = $this->db->insert("mahasiswa", $data);

        if($simpan) {
            return TRUE;
        } else {
            return FALSE;
        }

    }

    public function getUser($id_Villa = false)
    {
        if($id_Villa === false){
            return $this->db->table($this->table)->get()->getResult();
        } else {
            return $this->getWhere(['id_villa' => $id_Villa])->getRowArray();
        }  
    }

    public function detail_mahasiswa($id)
    {
        $this->db->select("*");
        $this->db->from("villa");
        $this->db->where("id_Villa", $id_Villa);
        return $this->db->get();
    }

    public function update_mahasiswa($data, $id)
    {
        $update = $this->db->update("mahasiswa", $data, $id);

        if($update) {
            return TRUE;
        } else {
            return FALSE;
        }

    }

    public function delete_mahasiswa($id)
    {
        $this->db->where('id', $id);
        $delete = $this->db->delete('mahasiswa');

        if($delete) {
            return TRUE;
        } else {
            return FALSE;
        }

    }


}