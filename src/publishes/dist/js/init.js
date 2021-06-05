var sistema = {

    /**
     * Configura as requisições em AJAX.
     */
    configurarAjax: function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') },
            beforeSend: function () {
                $(".loading-spinner").show();
            },
            complete: function () {
                $(".loading-spinner").hide();
            }
        });
    },

    /**
     * Aplica os plugins necessários para o sistema funcionar corretamente.
     */
    aplicarPluginsExternos: function (elemento) {
        if (elemento == null) {
            elemento = $(document);
        }

        $('form.validate', elemento).each(function () {
            $(this).formValidator();
        });

        $('body', elemento).on('click', '.confirma-acao', function (event) {
            event.preventDefault();
            var $el = $(this),
                texto = $el.attr('data-texto');

            Swal.fire({
                title: 'Confirmação',
                text: texto,
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: `Sim`,
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = $("<form/>",
                        {
                            action: $el.attr('href'),
                            method: 'POST',
                            style: 'display:none'
                        }
                    );
                    form.append(
                        $("<input>",
                            {
                                type: 'text',
                                name: '_method',
                                value: 'DELETE'
                            }
                        )
                    );
                    form.append(
                        $("<input>",
                            {
                                type: 'text',
                                name: '_token',
                                value: $('meta[name="_token"]').attr('content')
                            }
                        )
                    );
                    $('#form-delete').append(form);
                    form.submit();
                }
            })

        });

        $('[data="select"]').each(function () {
            var campo = $(this).attr('campo')
            var model = $(this).attr('model')

            $(this).select2({
                language: 'pt-BR',
                theme: 'bootstrap',
                width: '100%',
                placeholder: {
                    id: '-1',
                    text: 'Selecione'
                },
                minimumInputLength: $(this).attr("sel") ? 0 : 0,
                ajax: {
                    url: SITE_PATH + '/fill',
                    dataType: 'json',
                    cache: true,
                    type: 'post',
                    data: function (params) {
                        return {
                            termo: params.term ? params.term : '',
                            size: 10,
                            page: params.page,
                            campo: campo,
                            model: model
                        };
                    },
                    processResults: function (data) {
                        // parse the results into the format expected by Select2.
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data
                        return {
                            results: data.dados,
                            pagination: {
                                more: data.more
                            }
                        }
                    }
                }
            });
        });


    }
};

$(function () {
    sistema.configurarAjax();
    sistema.aplicarPluginsExternos();

    $('[toggle="tooltip"]').tooltip();

});
