var sistema = {

  /**
   * Configura as requisições em AJAX.
   */
  configurarAjax: function ()
  {
      $.ajaxSetup({
          headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')},
          beforeSend: function ()
          {
              $(".loading-spinner").show();
          },
          complete: function ()
          {
              $(".loading-spinner").hide();
          }
      });
  },

  /**
   * Aplica os plugins necessários para o sistema funcionar corretamente.
   */
  aplicarPluginsExternos: function (elemento)
  {
      if (elemento == null)
      {
          elemento = $(document);
      }

      $('form.validate', elemento).each(function ()
      {
          $(this).formValidator();
      });

      $('body', elemento).on('click', '.confirma-acao', function (event)
      {
          event.preventDefault();
          var $el = $(this),
              texto = $el.attr('data-texto');

          swal({
              title: 'Confirmação',
              text: texto,
              type: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Sim',
              cancelButtonText: 'Cancelar'
          }, function ()
          {
              var form = $("<form/>",
                  {
                      action: $el.attr('href'),
                      method: 'POST',
                      style:'display:none'
                  }
              );
              form.append(
                  $("<input>",
                      { type:'text',
                          name:'_method',
                          value:'DELETE' }
                  )
              );
              form.append(
                  $("<input>",
                      { type:'text',
                          name:'_token',
                          value: $('meta[name="_token"]').attr('content')}
                  )
              );
              $('#form-delete').append(form);
              form.submit();
          });
      });


  }
};

$(function ()
{
  sistema.configurarAjax();
  sistema.aplicarPluginsExternos();

});