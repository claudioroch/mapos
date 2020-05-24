<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aComodato')) { ?>
    <a href="<?php echo base_url(); ?>index.php/comodato/adicionar" class="btn btn-success"><i class="fas fa-plus"></i> Adicionar Comodato</a>
    <a href="#modal-etiquetas" role="button" data-toggle="modal" class="btn btn-success span2" style="float: right;">
        <i class="fas fa-barcode"></i> Gerar Etiquetas</a>

<?php } ?>

<div class="widget-box">
    <div class="widget-title">
        <span class="icon">
            <i class="fas fa-archive"></i>
        </span>
        <h5>Comodato</h5>
    </div>
    <div class="widget-content nopadding">
        <table class="table table-bordered ">
            <thead>
            <tr style="backgroud-color: #2D335B">
                <th>ID Comodato</th>
                <th>Cod. Equipamento</th>
                <th>Equipamento</th>
                <th>Fabricante</th>
            </tr>
            </thead>
            <tbody>
            <?php

            if (!$results) {
                echo '<tr>
                                <td colspan="5">Nenhum Equipamento Cadastrado</td>
                                </tr>';
            }
            foreach ($results as $r) {
                echo '<tr>';
                echo '<td>' . $r->idComodato . '</td>';
                echo '<td>' . $r->codComodato . '</td>';
                echo '<td>' . $r->comodato . '</td>';
                echo '<td>' . $e->Fabricante . '</td>';
                
                echo '<td>';
                if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vComodato')) {
                    echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/comodato/visualizar/' . $r->idComodato . '" class="btn tip-top" title="Visualizar Comodato"><i class="fas fa-eye"></i></a>  ';
                }
                if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eComodato')) {
                    echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/comodato/editar/' . $r->idComodato . '" class="btn btn-info tip-top" title="Editar Comodato"><i class="fas fa-edit"></i></a>';
                }
                if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dComodato')) {
                    echo '<a style="margin-right: 1%" href="#modal-excluir" role="button" data-toggle="modal" comodato="' . $r->idComodato . '" class="btn btn-danger tip-top" title="Excluir Comodato"><i class="fas fa-trash-alt"></i></a>';
                }
                // if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eComodato')) {
                //     echo '<a href="#atualizar-estoque" role="button" data-toggle="modal" comodato="' . $r->idComodato . '" estoque="' . $r->estoque . '" class="btn btn-primary tip-top" title="Atualizar Estoque"><i class="fas fa-plus-square"></i></a>';
                // }
                echo '</td>';
                echo '</tr>';
            } ?>
            </tbody>
        </table>
    </div>
</div>
<?php echo $this->pagination->create_links(); ?>

<!-- Modal -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/comodato/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel"><i class="fas fa-trash-alt"></i> Excluir Comodato</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idComodato" class="idComodato" name="id" value=""/>
            <h5 style="text-align: center">Deseja realmente excluir este equipamento?</h5>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button class="btn btn-danger">Excluir</button>
        </div>
    </form>
</div>

<!-- Modal Estoque -->
<!-- <div id="atualizar-estoque" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="?php echo base_url() ?>index.php/comodato/atualizar_estoque" method="post" id="formEstoque">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel"><i class="fas fa-plus-square"></i> Atualizar Estoque</h5>
        </div>
        <div class="modal-body">
            <div class="control-group">
                <label for="estoqueAtual" class="control-label">Estoque Atual</label>
                <div class="controls">
                    <input id="estoqueAtual" type="text" name="estoqueAtual" value="" readonly />
                </div>
            </div>

            <div class="control-group">
                <label for="estoque" class="control-label">Adicionar Comodato<span class="required">*</span></label>
                <div class="controls">
                    <input type="hidden" id="idComodato" class="idComodato" name="id" value=""/>
                    <input id="estoque" type="text" name="estoque" value=""/>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button class="btn btn-primary">Atualizar</button>
        </div>
    </form>
</div> -->

<!-- Modal Etiquetas
<div id="modal-etiquetas" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="/?php echo base_url() ?>index.php/relatorios/comodatoEtiquetas" method="get">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Gerar etiquetas com Código de Barras</h5>
        </div>
        <div class="modal-body">
            <div class="span12 alert alert-info" style="margin-left: 0"> Escolha o intervalo de comodato para gerar as etiquetas.</div>

            <div class="span12" style="margin-left: 0;">
                <div class="span6" style="margin-left: 0;">
                    <label for="valor">De</label>
                    <input class="span9" style="margin-left: 0" type="text" id="de_id" name="de_id" placeholder="ID do primeiro produto" value=""/>
                </div>


                <div class="span6">
                    <label for="valor">Até</label>
                    <input class="span9" type="text" id="ate_id" name="ate_id" placeholder="ID do último produto" value=""/>
                </div>

                <div class="span4">
                    <label for="valor">Qtd. do Estoque</label>
                    <input class="span12" type="checkbox" name="qtdEtiqueta" value="true"/>
                </div>

                <div class="span6">
                    <label class="span12" for="valor">Formato Etiqueta</label>
                    <select name="etiquetaCode">
                        <option value="EAN13">EAN-13</option>
                        <option value="UPCA">UPCA</option>
                        <option value="C93">CODE 93</option>
                        <option value="C128A">CODE 128</option>
                        <option value="CODABAR">CODABAR</option>
                        <option value="QR">QR-CODE</option>
                    </select>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button class="btn btn-success">Gerar</button>
        </div>
    </form>
</div> -->

<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
 Modal Etiquetas e Estoque
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', 'a', function (event) {
            var comodato = $(this).attr('comodato');
            $('.idComodato').val(comodato);
        });

        $('#formEstoque').validate({
            rules: {
                estoque: {
                    required: true,
                    number: true
                }
            },
            messages: {
                estoque: {
                    required: 'Campo Requerido.',
                    number: 'Informe um número válido.'
                }
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });
    });
</script>

