<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Comodato extends MY_Controller
{

    /**
     * author: Cláudio Rocha
     * email: claudioroch@hotmail.com
     *
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->model('comodato_model');
        $this->data['menuComodato'] = 'Comodato';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vComodato')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar Comodato.');
            redirect(base_url());
        }

        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('comodato/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->comodato_model->count('comodato');

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->comodato_model->get('comodato', '*', '', $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'comodato/comodato';
        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aComodato')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar Comodato.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('comodato') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $data = [
                'codComodato' => set_value('codComodato'),
                'comodato' => set_value('comodato'),
                'fabricante' => set_value('fabricante'),
            ];

            if ($this->comodato_model->add('comodato', $data) == true) {
                $this->session->set_flashdata('success', 'Comodato adicionado com sucesso!');
                log_info('Adicionou um equipamento');
                redirect(site_url('comodato/adicionar/'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>An Error Occured.</p></div>';
            }
        }
        $this->data['view'] = 'comodato/adicionarComodato';
        return $this->layout();
    }

    public function editar()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eComodatoo')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar comodato.');
            redirect(base_url());
        }
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('comodato') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            
            $data = [
                'codComodato' => $this->input->post('codComodato'),
                'comodato' => $this->input->post('comodato'),
                'fabricante' => $this->input->post('fabricante'),
                
            ];

            if ($this->comodato_model->edit('comodato', $data, 'idComodato', $this->input->post('idComodato')) == true) {
                $this->session->set_flashdata('success', 'Equipamento editado com sucesso!');
                log_info('Alterou um comodato. ID: ' . $this->input->post('idComodato'));
                redirect(site_url('comodato/editar/') . $this->input->post('idComodato'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>An Error Occured</p></div>';
            }
        }

        $this->data['result'] = $this->comodato_model->getById($this->uri->segment(3));
        $this->data['anexos'] = $this->comodato_model->getAnexos($this->uri->segment(3));
        $this->data['view'] = 'comodato/editarComodato';
        return $this->layout();
    }
    public function anexar()
    {

        $this->load->library('upload');
        $this->load->library('image_lib');

        $upload_conf = array(
            'upload_path' => realpath('./assets/anexosproduto'),
            'allowed_types' => 'jpg|png|gif|jpeg|JPG|PNG|GIF|JPEG|pdf|PDF|cdr|CDR|docx|DOCX|txt', // formatos permitidos para anexos de os
            'max_size' => 0,
        );

        $this->upload->initialize($upload_conf);

        foreach ($_FILES['userfile'] as $key => $val) {
            $i = 1;
            foreach ($val as $v) {
                $field_name = "file_" . $i;
                $_FILES[$field_name][$key] = $v;
                $i++;
            }
        }
        unset($_FILES['userfile']);

        $error = array();
        $success = array();

        foreach ($_FILES as $field_name => $file) {
            if (!$this->upload->do_upload($field_name)) {
                $error['upload'][] = $this->upload->display_errors();
            } else {

                $upload_data = $this->upload->data();

                if ($upload_data['is_image'] == 1) {

                    // set the resize config
                    $resize_conf = array(

                        'source_image' => $upload_data['full_path'],
                        'new_image' => $upload_data['file_path'] . 'thumbs/thumb_' . $upload_data['file_name'],
                        'width' => 200,
                        'height' => 125,
                    );

                    $this->image_lib->initialize($resize_conf);

                    if (!$this->image_lib->resize()) {
                        $error['resize'][] = $this->image_lib->display_errors();
                    } else {
                        $success[] = $upload_data;
                        $this->load->model('Comodato_model');
                        $this->Comodato_model->anexar($this->input->post('idOsServico'), $upload_data['file_name'], base_url() . 'assets/anexosproduto/', 'thumb_' . $upload_data['file_name'], realpath('./assets/anexosproduto/'));
                    }
                } else {

                    $success[] = $upload_data;

                    $this->load->model('Ccomodato_model');


                    $this->Comodato_model->anexar($this->input->post('idOsServico'), $upload_data['file_name'], base_url() . 'assets/anexosproduto/', '', realpath('./assets/anexosproduto/'));
                }
            }
        }

        if (count($error) > 0) {
            echo json_encode(array('result' => false, 'mensagem' => 'Nenhum arquivo foi anexado.'));
        } else {

            log_info('Adicionou anexo(s) a um equipamento.');
            echo json_encode(array('result' => true, 'mensagem' => 'Arquivo(s) anexado(s) com sucesso .'));
        }
    }
    public function excluirAnexo($id = null)
    {
        if ($id == null || !is_numeric($id)) {
            echo json_encode(array('result' => false, 'mensagem' => 'Erro ao tentar excluir anexo.'));
        } else {

            $this->db->where('idAnexos', $id);
            $file = $this->db->get('anexos_comodato', 1)->row();

            unlink($file->path . '/' . $file->anexo);

            if ($file->thumb != null) {
                unlink($file->path . '/thumbs/' . $file->thumb);
            }

            if ($this->Comodato_model->delete('anexos_comodato', 'idAnexos', $id) == true) {

                log_info('Removeu anexo de um comodato.');
                echo json_encode(array('result' => true, 'mensagem' => 'Anexo excluído com sucesso.'));
            } else {
                echo json_encode(array('result' => false, 'mensagem' => 'Erro ao tentar excluir anexo.'));
            }
        }
    }

    public function visualizar()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vComodato')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar comodato.');
            redirect(base_url());
        }

        $this->data['result'] = $this->comodato_model->getById($this->uri->segment(3));

        if ($this->data['result'] == null) {
            $this->session->set_flashdata('error', 'Equipamento não encontrado.');
            redirect(site_url('comodato/editar/') . $this->input->post('idComodato'));
        }

        $this->data['view'] = 'comodato/visualizarComodato';
        return $this->layout();
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'idComodato')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir comodato.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir Comodato.');
            redirect(base_url() . 'index.php/comodato/gerenciar/');
        }

        $this->comodato_model->delete('comodato_os', 'comodato_id', $id);
        $this->comodato_model->delete('itens_de_vendas', 'comodato_id', $id);
        $this->comodato_model->delete('comodato', 'idComodato', $id);

        log_info('Removeu um comodato. ID: ' . $id);

        $this->session->set_flashdata('success', 'Equipamento excluido com sucesso!');
        redirect(site_url('comodato/gerenciar/'));
    }
}
