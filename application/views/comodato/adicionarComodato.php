<style>
    /* Hiding the checkbox, but allowing it to be focused */
    .badgebox {
        opacity: 0;
    }

    .badgebox+.badge {
        /* Move the check mark away when unchecked */
        text-indent: -999999px;
        /* Makes the badge's width stay the same checked and unchecked */
        width: 27px;
    }

    .badgebox:focus+.badge {
        /* Set something to make the badge looks focused */
        /* This really depends on the application, in my case it was: */

        /* Adding a light border */
        box-shadow: inset 0px 0px 5px;
        /* Taking the difference out of the padding */
    }

    .badgebox:checked+.badge {
        /* Move the check mark back when checked */
        text-indent: 0;
    }
</style>
<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-archive"></i>
                </span>
                <h5>Cadastro de Comodato</h5>
            </div>
            <div class="widget-content nopadding">
                <?php echo $custom_error; ?>
                <form action="<?php echo current_url(); ?>" id="formComodato" method="post" class="form-horizontal">
                    <div class="control-group">
                            <label for="codComodato" class="control-label">Código do Equipamento<span class=""></span></label>
                            <div class="controls">
                                <input id="codComodato"  type="text" name="codComodato" value="<?php echo set_value('codComodato'); ?>" />
                            </div>
                        </div>
                    <div class="control-group">
                        <label for="comodato" class="control-label">Equipamento<span class="required">*</span></label>
                        <div class="controls">
                            <input id="comodato" type="text" name="comodato" value="<?php echo set_value('comodato'); ?>" />
                        </div>
                    </div>
                
                    <div class="control-group">
                        <label for="fabricante" class="control-label">Fabricante<span class="required">*</span></label>
                        <div class="controls">
                            <input id="fabricante" type="text" name="fabricante" value="<?php echo set_value('fabricante'); ?>" />
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3">
                                <button type="submit" class="btn btn-danger"><i class="fas fa-plus"></i> Adicionar</button>
                                <a href="<?php echo base_url() ?>index.php/comodato" id="" class="btn"><i class="fas fa-backward"></i> Voltar</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(".money").maskMoney();
        $('#formComodato').validate({
            rules: {
                Comodato: {
                    required: true
                },
                fabricante: {
                    required: true
                }
            },
            messages: {
                Comodato: {
                    required: 'Campo Requerido.'
                },
                fabricante: {
                    required: 'Campo Requerido.'
                }
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });
    });
</script>
