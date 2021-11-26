<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {


    public function __construct()
	{
		parent::__construct();
        $this->load->model('M_api');
        $this->load->library('form_validation');

    }

    //-------------------------------------- LOGIN ---------------------------------------------------  

    public function login()
    {
        //PAKE METHOD POST DI FORM-DATA
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['username']) && isset($_POST['password'])) {
                
                $user_login = $this->M_api->proses_login($_POST['username'], $_POST['password']);
                $result['id_customer']   = null;

                if ($user_login->num_rows() == 1) {
                    $result['value'] = 1;
                    $result['pesan'] = "sukses login!";
                    $result['id_customer']   = $user_login->row()->id_customer;
                    $result['nama']   = $user_login->row()->nama;
                } else {
                    $result['value'] = 0;
                    $result['pesan'] = "username / password salah!";
                }
            } else {
                $result['value'] = 0;
                $result['pesan'] = "beberapa inputan masih kosong!";
            }
        } else {
            $result['value'] = 0;
            $result['pesan'] = "invalid request method!";
        }

        echo json_encode($result);
    }

    //-------------------------------------- REGISTER ---------------------------------------------------  

    

    public function register()
    {
                //PAKE METHOD POST DI FORM-DATA

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['alamat']) && isset($_POST['gender']) && isset($_POST['no_telepon']) && isset($_POST['no_ktp']) && isset($_POST['role_id']) && isset($_POST['nama'])) {
                if ($this->M_api->cek_user_register($_POST['username'])->num_rows() == 1) {
                    $result['value'] = "0";
                    $result['username'] = "username sudah ter registrasi!";
                } else if ($this->M_api->cek_user_exist_register($_POST['username'])->num_rows() == 1) {
                    $result['value'] = "0";
                    $result['pesan'] = "username sudah terdaftar!";
                } else {
                    $this->M_api->proses_register();
                    $result['value'] = "1";
                    $result['pesan'] = "registrasi berhasil!";
                }
            } else {
                $result['value'] = "0";
                $result['pesan'] = "beberapa inputan masih kosong!";
            }            
        } else {
            $result['value'] = "0";
            $result['pesan'] = "invalid request method!";
        }

        echo json_encode($result);
    }


    //-------------------------------------- Contoh Crud ---------------------------------------------------  


    public function index()
    {
        //PAKE METHOD GET ->GUNAIN DI PARAMS
        $villa = $this->M_api->get_all();

        $response = array();

        foreach($villa->result() as $hasil) {

            $response[] = array(
                'idVilla' => $hasil->id_villa,
                'kodeType' => $hasil->kode_type,
                'merk'     => $hasil->merk,         
                'noVilla'     => $hasil->no_villa,
                'noTelp' => $hasil->no_telp,
                'warna' => $hasil->warna,
                'lokasi' => $hasil->lokasi,
                'status' => $hasil->status,
                'harga' => $hasil->harga,
                'fasilitas' => $hasil->fasilitas,
                'gambar' => $hasil->gambar,         
            );

        }
        
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'success' => true,
                'message' => 'Get All Data Villa',
                'data'    => $response  
            )
        );

    }

    public function simpan()
    {
        //PAKE METHODNYA POST DI BODY -> FORM-DATA
        //set validasi
        $this->form_validation->set_rules('nama','Nama Mahasiswa','required');
        $this->form_validation->set_rules('nim','Nim Mahasiswa','required');
        $this->form_validation->set_rules('jurusan','Jurusan Mahasiswa','required');

        if($this->form_validation->run() == TRUE){

            $data = array(
                'nama' => $this->input->post("nama"),
                'nim'     => $this->input->post("nim"),
                'jurusan'     => $this->input->post("jurusan"),
            );

            $simpan = $this->M_api->simpan_mahasiswa($data);

            if($simpan) {

                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        'success' => true,
                        'message' => 'Data Berhasil Disimpan!'
                    )
                );

            } else {

                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        'success' => false,
                        'message' => 'Data Gagal Disimpan!'
                    )
                );
            }

        }else{

            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'success'    => false,
                    'message'    => validation_errors()
                )
            );

        }

    }

    public function getUserBy($id_villa)
    {
    $data = $this->villa->getUser($id_villa);
 
    $villa = [
        'id_villa' => intval($data['id_villa']),
        'merk' => $data['merk'],
        'lokasi' => $data['lokasi'],
        'herga' => $data['harga'],
    ];       
    return $this->respond($villa, 200);   
    }

    public function detail($id_villa)
    {
        //PAKE METHOD GET DI BODY ->FORM-DATA ATAU DI PARAMS
        //get Mahasiswa by ID from URL
        $id = $this->uri->segment(3);

        $mahasiswa = $this->M_api->detail_mahasiswa($id_villa)->row();
     
        if($mahasiswa) {

            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'success' => true,
                    'data'    => array(
                        'merk' => $mahasiswa->merk,
                        'harga'     => $mahasiswa->harga,   
                        'lokasi'     => $mahasiswa->lokasi   
                    )  
                )
            );

        } else {

            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'success' => false,
                    'message' => 'Data Mahasiswa Tidak Ditemukan!'
                )
            );

        }
    }

    public function update()
    {
                //PAKE METHOD POST DI BODY ->FORM-DATA 

        //set validasi
        $this->form_validation->set_rules('id','ID Mahasiswa','required');
        $this->form_validation->set_rules('nama','Nama Mahasiswa','required');
        $this->form_validation->set_rules('nim','Nim Mahasiswa','required');
        $this->form_validation->set_rules('jurusan','Jurusan Mahasiswa','required');

        if($this->form_validation->run() == TRUE){

            $id['id'] = $this->input->post("id");
            $data = array(
                'nama' => $this->input->post("nama"),
                'nim'     => $this->input->post("nim"),
                'jurusan'     => $this->input->post("jurusan"),
            );

            $update = $this->M_api->update_mahasiswa($data, $id);

            if($update) {

                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        'success' => true,
                        'message' => 'Data Berhasil Diupdate!'
                    )
                );

            } else {

                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        'success' => false,
                        'message' => 'Data Gagal Diupdate!'
                    )
                );
            }

        }else{

            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'success'    => false,
                    'message'    => validation_errors()
                )
            );

        }
    }

    public function delete($id)
    {
        //PAKE METHOD DELETE
        //get ID siswa from URL
        $id = $this->uri->segment(3);

        //delete data from model
        $delete = $this->M_api->delete_mahasiswa($id);

        if($delete) {

            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'success' => true,
                    'message' => 'Data Berhasil Dihapus!'
                )
            );

        } else {

            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'success' => false,
                    'message' => 'Data Gagal Dihapus!'
                )
            );
        }

    }




}