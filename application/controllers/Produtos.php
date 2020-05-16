<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Produtos extends MY_Controller
{

    /**
     * author: Ramon Silva
     * email: silva018-mg@yahoo.com.br
     *
     */

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->model('produtos_model');
        $this->data['menuProdutos'] = 'Produtos';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar produtos.');
            redirect(base_url());
        }

        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('produtos/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->produtos_model->count('produtos');

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->produtos_model->get('produtos', '*', '', $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'produtos/produtos';
        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar produtos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('produtos') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $precoCompra = $this->input->post('precoCompra');
            $precoCompra = str_replace(",", "", $precoCompra);
            $precoVenda = $this->input->post('precoVenda');
            $precoVenda = str_replace(",", "", $precoVenda);
            $data = [
                'codDeBarra' => set_value('codDeBarra'),
                'produto' => set_value('produto'),
                'embalagem' => set_value('embalagem'),
                'rendimento' => set_value('rendimento'),
                'diluicao' => set_value('diluicao'),
                'descricao' => set_value('descricao'),
                'unidade' => set_value('unidade'),                
                'precoCompra' => $precoCompra,
                'precoVenda' => $precoVenda,
                'estoque' => set_value('estoque'),
                'estoqueMinimo' => set_value('estoqueMinimo'),
                'saida' => set_value('saida'),
                'entrada' => set_value('entrada'),
            ];

            if ($this->produtos_model->add('produtos', $data) == true) {
                $this->session->set_flashdata('success', 'Produto adicionado com sucesso!');
                log_info('Adicionou um produto');
                redirect(site_url('produtos/adicionar/'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>An Error Occured.</p></div>';
            }
        }
        $this->data['view'] = 'produtos/adicionarProduto';
        return $this->layout();
    }

    public function editar()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar produtos.');
            redirect(base_url());
        }
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('produtos') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $precoCompra = $this->input->post('precoCompra');
            $precoCompra = str_replace(",", "", $precoCompra);
            $precoVenda = $this->input->post('precoVenda');
            $precoVenda = str_replace(",", "", $precoVenda);
            $data = [
                'codDeBarra' => set_value('codDeBarra'),
                'produto' => $this->input->post('produto'),
                'embalagem' => $this->input->post('embalagem'),
                'rendimento' => $this->input->post('rendimento'),
                'diluicao' => $this->input->post('diluicao'),
                'descricao' => $this->input->post('descricao'),
                'unidade' => $this->input->post('unidade'),
                'precoCompra' => $precoCompra,
                'precoVenda' => $precoVenda,
                'estoque' => $this->input->post('estoque'),
                'estoqueMinimo' => $this->input->post('estoqueMinimo'),
                'saida' => set_value('saida'),
                'entrada' => set_value('entrada'),
            ];

            if ($this->produtos_model->edit('produtos', $data, 'idProdutos', $this->input->post('idProdutos')) == true) {
                $this->session->set_flashdata('success', 'Produto editado com sucesso!');
                log_info('Alterou um produto. ID: ' . $this->input->post('idProdutos'));
                redirect(site_url('produtos/editar/') . $this->input->post('idProdutos'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>An Error Occured</p></div>';
            }
        }

        $this->data['result'] = $this->produtos_model->getById($this->uri->segment(3));
        $this->data['anexos'] = $this->produtos_model->getAnexos($this->uri->segment(3));
        $this->data['view'] = 'produtos/editarProduto';
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
                        $this->load->model('Produtos_model');
                        $this->Produtos_model->anexar($this->input->post('idOsServico'), $upload_data['file_name'], base_url() . 'assets/anexosproduto/', 'thumb_' . $upload_data['file_name'], realpath('./assets/anexosproduto/'));
                    }
                } else {

                    $success[] = $upload_data;

                    $this->load->model('Produtos_model');


                    $this->Produtos_model->anexar($this->input->post('idOsServico'), $upload_data['file_name'], base_url() . 'assets/anexosproduto/', '', realpath('./assets/anexosproduto/'));
                }
            }
        }

        if (count($error) > 0) {
            echo json_encode(array('result' => false, 'mensagem' => 'Nenhum arquivo foi anexado.'));
        } else {

            log_info('Adicionou anexo(s) a um produto.');
            echo json_encode(array('result' => true, 'mensagem' => 'Arquivo(s) anexado(s) com sucesso .'));
        }
    }
    public function excluirAnexo($id = null)
    {
        if ($id == null || !is_numeric($id)) {
            echo json_encode(array('result' => false, 'mensagem' => 'Erro ao tentar excluir anexo.'));
        } else {

            $this->db->where('idAnexos', $id);
            $file = $this->db->get('anexos_produtos', 1)->row();

            unlink($file->path . '/' . $file->anexo);

            if ($file->thumb != null) {
                unlink($file->path . '/thumbs/' . $file->thumb);
            }

            if ($this->produtos_model->delete('anexos_produtos', 'idAnexos', $id) == true) {

                log_info('Removeu anexo de uma OS.');
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

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar produtos.');
            redirect(base_url());
        }

        $this->data['result'] = $this->produtos_model->getById($this->uri->segment(3));

        if ($this->data['result'] == null) {
            $this->session->set_flashdata('error', 'Produto não encontrado.');
            redirect(site_url('produtos/editar/') . $this->input->post('idProdutos'));
        }

        $this->data['view'] = 'produtos/visualizarProduto';
        return $this->layout();
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'idProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir produtos.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir produto.');
            redirect(base_url() . 'index.php/produtos/gerenciar/');
        }

        $this->produtos_model->delete('produtos_os', 'produtos_id', $id);
        $this->produtos_model->delete('itens_de_vendas', 'produtos_id', $id);
        $this->produtos_model->delete('produtos', 'idProdutos', $id);

        log_info('Removeu um produto. ID: ' . $id);

        $this->session->set_flashdata('success', 'Produto excluido com sucesso!');
        redirect(site_url('produtos/gerenciar/'));
    }

    public function atualizar_estoque()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para atualizar estoque de produtos.');
            redirect(base_url());
        }

        $idProduto = $this->input->post('id');
        $novoEstoque = $this->input->post('estoque');
        $estoqueAtual = $this->input->post('estoqueAtual');

        $estoque = $estoqueAtual + $novoEstoque;

        $data = [
            'estoque' => $estoque,
        ];

        if ($this->produtos_model->edit('produtos', $data, 'idProdutos', $idProduto) == true) {
            $this->session->set_flashdata('success', 'Estoque de Produto atualizado com sucesso!');
            log_info('Atualizou estoque de um produto. ID: ' . $idProduto);
            redirect(site_url('produtos/visualizar/') . $idProduto);
        } else {
            $this->data['custom_error'] = '<div class="alert">Ocorreu um erro.</div>';
        }
    }
}
