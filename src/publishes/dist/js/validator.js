(function($) {
    $.fn.formValidator = function() {
        var passwordValue;
        var emailValue;
        var is;
        var BORDER_COLOR_SUCCESS = "1px solid #DEDEDE";
        var BORDER_COLOR_ERROR = "1px solid rgba(253, 0, 27, 0.42)";
        var ELEMENT_ERROR_STYLE = "padding: 0px;margin: 0px;font-size: 12px;color: rgb(237, 85, 101);";

        var validator = {
            email: function validateEmail(value, element) {
                var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                emailValue = value;
                return validateByRegex(value, element, regex);
            },
            secondEmail: function confirmEmail(value, element) {
                if (emailValue === value && value.length) {
                    return success(element);
                } else {
                    return error(element);
                }
            },
            cpf: function validateCPF(value, element) {
                if (validatorCPF(value)) {
                    return success(element);
                } else {
                    return error(element);
                }
            },
            cnpj: function validateCNPJ(value, element) {
                if (validatorCNPJ(value)) {
                    return success(element);
                } else {
                    return error(element);
                }
            },
            cpf_cnpj: function validadeCpfCnpj(value, element) {
                validation = false;
                if (value.length <= 14) {
                    value = mascaraCPF(value);
                    element.val(value);
                    validation = validatorCPF(value);
                } else {
                    value = mascaraCNPJ(value);
                    element.val(value);
                    validation = validatorCNPJ(value);
                }
                if (validation) {
                    return success(element);
                } else {
                    return error(element);
                }
            },
            required: function isRequired(value, element) {
                if (value.length) {
                    return success(element);
                } else {
                    return error(element);
                }
            },
            date: function validateDate(value, element) {
                var regex = /^(0([1-9])|(1|2)([0-9])|3([0-1]))\/(0([1-9])|1([0-2]))\/(19|2[0-9])[0-9]{2}/;
                return validateByRegex(value, element, regex);
            },
            datetime: function validateDate(value, element) {
                var regex = /^(0([1-9])|(1|2)([0-9])|3([0-1]))\/(0([1-9])|1([0-2]))\/(19|2[0-9])[0-9]{2}/;
                return validateByRegex(value, element, regex);
            },
            mesAno: function validateDate(value, element) {
                var regex = /^(0([1-9])|1([0-2]))\/[0-9]{2}/;
                return validateByRegex(value, element, regex);
            },
            dateToday: function validateDate(value, element) {
                var regex = /^(0([1-9])|(1|2)([0-9])|3([0-1]))\/(0([1-9])|1([0-2]))\/(19|2[0-9])[0-9]{2}/;
                return validateByRegex(value, element, regex);
            },
            phone: function validatePhone(value, element) {
                var regex = /^(\([0-9]{2}\))\s([0-9]{4,5})-([0-9]{4})$/;
                return validateByRegex(value, element, regex);
            },
            cep: function validateCEP(value, element) {

                $.ajax({
                    url: SITE_PATH+'/cep/' + value,
                    type: 'GET'
                }).done(function (jsonString) {
                    var json = $.parseJSON(jsonString);
                     var form = element.closest('form');
                    if (json.resultado==1) {
                        $form.find('.estado').each(function(){
                            success($(this));
                            $(this).val(json.estado)
                        })
                        $form.find('.cidade').each(function(){
                            success($(this));
                            $(this).val(json.cidade)
                        })
                        $form.find('.bairro').each(function(){
                            success($(this));
                            $(this).val(json.bairro)
                        })
                        $form.find('.endereco').each(function(){
                            success($(this));
                            $(this).val(json.tipo_logradouro + " " + json.logradouro)
                        })

                        return success(element);

                    } else {
                        return error(element);
                    }

                })

                if(empty($('#' + element.attr('id') + '_element_error').html())){
                    return success(element);
                }
            },
            cep_old: function validateCEP(value, element) {
                $.ajax({
                    url: SITE_PATH+'/cep/' + value,
                    type: 'GET'
                }).done(function (jsonString) {

                    var json = $.parseJSON(jsonString);
                    // var form = element.closest('form');
                    if (json.resultado==1) {
                        $form.find('.estado_old').each(function(){
                            success($(this));
                            $(this).val(json.estado)
                        })
                        $form.find('.cidade_old').each(function(){
                            success($(this));
                            $(this).val(json.cidade)
                        })
                        $form.find('.bairro_old').each(function(){
                            success($(this));
                            $(this).val(json.bairro)
                        })
                        $form.find('.endereco_old').each(function(){
                            success($(this));
                            $(this).val(json.tipo_logradouro + " " + json.logradouro)
                        })

                        return success(element);

                    } else {
                        return error(element);
                    }

                })
                if(empty($('#' + element.attr('id') + '_element_error').html())){
                    return success(element);
                }
            },
            cep_un: function validateCEP(value, element) {
                $.ajax({
                    url: '//app.credseguro.com.br/webservice/' + value,
                    type: 'GET',
                }).done(function(jsonString) {
                    var json = $.parseJSON(jsonString);
                    var form = element.closest('form');
                    if (json.success) {
                        var estado = form.find('.estado');
                        var cidade = form.find('.cidade');
                        var bairro = form.find('.bairro');
                        var endereco = form.find('.endereco');
                        estado.val(json.estado);
                        cidade.val(json.cidade);
                        bairro.val(json.bairro);
                        endereco.val(json.logradouro_tipo + " " + json.logradouro);
                        success(estado);
                        success(cidade);
                        success(bairro);
                        success(endereco);
                        return success(element);
                    } else {
                        return error(element);
                    }
                }).fail(function() {
                    return error(element);
                });
            },
            money: function validateMoney(value, element) {
                var regex = /^(\R\$\s)?([0-9]{1,3}\.)*[0-9]{1,3}\,[0-9]{2}/;
                return validateByRegex(value, element, regex);
            },
            number: function validateMoney(value, element) {
                if (parseInt(value) <= 0) {
                    return error(element);
                }
                var regex = /^(0|[1-9][0-9]*)$/;
                return validateByRegex(value, element, regex);
            },
            cartao: function validateMoney(value, element) {
                var regex = /^([0-9]{4}) ([0-9]{4}) ([0-9]{4}) ([0-9]{4})$/;
                return validateByRegex(value, element, regex);
            },
            firstPass: function validatePass(value, element) {
                passwordValue = value;
                if (value.length) {
                    return success(element);
                } else {
                    return error(element);
                }
            },
            secondPass: function confirmPass(value, element) {
                if (passwordValue === value && value.length) {
                    return success(element);
                } else {
                    return error(element);
                }
            },
            time: function validationTime(value, element) {
                var regex = /^(([0-1]\d)|(2[0-3]))\:[0-5]\d/;
                return validateByRegex(value, element, regex);
            }
        };
        var masks = {
            email: function maskEmail(value, element) {
                var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                emailValue = value;
                return validateByRegex(value, element, regex);
            },
            time: function timeMask(e) {
                e.clockpicker({
                    placement: 'bottom',
                    align: 'left',
                    autoclose: true,
                });
            },
            cpf: function cpfMask(e) {
                e.mask("999.999.999-99");
            },
            cnpj: function cnpjMask(e) {
                e.mask("99.999.999/9999-99");
            },
            cpf_cnpj: function cpfCnpjMask(e) {},
            date: function dateMask(e) {
                e.mask("99/99/9999");
                e.datepicker({
                    format: "dd/mm/yyyy",
                    todayBtn: "linked",
                    language: "pt-BR",
                    calendarWeeks: false,
                    autoclose: true
                }).on('hide', function(e) {
                    genericValidationElement($(this));
                });
            },
            datetime: function dateMask(e) {
                e.mask("99/99/9999 99:99");

                e.datetimepicker({
                    locale: 'pt-br',
                    sideBySide: true,
                    extraFormats: ['YYYY-MM-DD HH:mm:ss', 'YYYY-MM-DD'],
                    showTodayButton: true,
                    useStrict: true,
                    showClear: true,
                    allowInputToggle: true,
                    widgetPositioning: {
                        horizontal: 'auto',
                        vertical: 'bottom'
                    }
                }).on('dp.show', function (e) {
                    var widget = $('.bootstrap-datetimepicker-widget:last')
                    widget.addClass('piker-widget')
                });

            },
            mesAno: function dateMask(e) {
                e.mask("99/99");
            },
            dateToday: function dateMask(e) {
                e.mask("99/99/9999");
                e.datepicker({
                    format: "dd/mm/yyyy",
                    todayBtn: "linked",
                    language: "pt-BR",
                    startDate: "today",
                    calendarWeeks: false,
                    autoclose: true
                }).on('hide', function(e) {
                    genericValidationElement($(this));
                });
            },
            phone: function phoneMask(e) {
                var SPMaskBehavior = function(val) {
                        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
                    },
                    spOptions = {
                        onKeyPress: function(val, e, field, options) {
                            field.mask(SPMaskBehavior.apply({}, arguments), options);
                        }
                    };
                e.mask(SPMaskBehavior, spOptions);
            },
            cep: function cepMask(e) {
                e.mask("99999-999");
            },
            money: function moneyMask(e) {
                e.mask("#.##0,00", {
                    reverse: true
                });
            },
            number: function moneyMask(e) {
                e.mask("#0", {
                    reverse: true
                });
            },
            cartao: function moneyMask(e) {
                e.mask("9999 9999 9999 9999");
            }
        };


        function init() {
            $('form').each(function() {
                $(this).find('input').each(function(i) {
                    var attr = $(this).attr('is');
                    if (!(typeof attr !== typeof undefined && attr !== false)) {
                        attr = $(this).attr('mask');
                    }
                    if (typeof attr !== typeof undefined && attr !== false) {
                        var fn = masks[attr];
                        if (typeof fn === "function") {
                            fn($(this));
                        }
                    }
                });
            });
        }
        init();

        function validatorCPF(str) {
            str = str.replace('.', '');
            str = str.replace('.', '');
            str = str.replace('-', '');
            cpf = str;
            var numeros, digitos, soma, i, resultado, digitos_iguais;
            digitos_iguais = 1;
            if (cpf.length != 11) {
                return false;
            }
            for (i = 0; i < cpf.length - 1; i++) {
                if (cpf.charAt(i) != cpf.charAt(i + 1)) {
                    digitos_iguais = 0;
                    break;
                }
            }
            if (!digitos_iguais) {
                numeros = cpf.substring(0, 9);
                digitos = cpf.substring(9);
                soma = 0;
                for (i = 10; i > 1; i--) {
                    soma += numeros.charAt(10 - i) * i;
                }
                resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                if (resultado != digitos.charAt(0)) {
                    return false;
                }
                numeros = cpf.substring(0, 10);
                soma = 0;
                for (i = 11; i > 1; i--) {
                    soma += numeros.charAt(11 - i) * i;
                }
                resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                if (resultado != digitos.charAt(1)) {
                    return false;
                }
                return true;
            } else {
                return false;
            }
        }

        function validatorCNPJ(cnpj) {
            cnpj = cnpj.replace(/[^\d]+/g, '');
            if (cnpj == '') return false;
            if (cnpj.length != 14)
                return false;
            if (cnpj == "00000000000000" || cnpj == "11111111111111" || cnpj == "22222222222222" || cnpj == "33333333333333" || cnpj == "44444444444444" || cnpj == "55555555555555" || cnpj == "66666666666666" || cnpj == "77777777777777" || cnpj == "88888888888888" || cnpj == "99999999999999")
                return false;
            tamanho = cnpj.length - 2
            numeros = cnpj.substring(0, tamanho);
            digitos = cnpj.substring(tamanho);
            soma = 0;
            pos = tamanho - 7;
            for (i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2)
                    pos = 9;
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(0))
                return false;
            tamanho = tamanho + 1;
            numeros = cnpj.substring(0, tamanho);
            soma = 0;
            pos = tamanho - 7;
            for (i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2)
                    pos = 9;
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(1))
                return false;
            return true;
        }

        function validateByRegex(value, element, regex) {
            if (regex.exec(value)) {
                return success(element);
            } else {
                return error(element);
            }
        }

        function success(element) {
            if (element) element.css("border", BORDER_COLOR_SUCCESS);
            return true;
        }

        function error(element) {
            if (element) element.css("border", BORDER_COLOR_ERROR);
            var msg = $(element).attr('msg');
            if (typeof msg !== typeof undefined && msg !== false) {
                $("#error_" + $(element).attr('name')).remove();
                $('<span class="msg_error" id="error_' + $(element).attr('name') + '">' + msg + '</span>').insertAfter('#msg_error_' + $(element).attr('name') + '');
            }
            return false;
        }

        function genericValidation() {
            is = $(this).attr('is');
            var fn = validator[is];
            var v = $(this).val();
            if (typeof fn === "function") {
                return fn(v, $(this));
            }
            return false;
        }

        function genericValidationElement(element) {
            is = element.attr('is');
            var fn = validator[is];
            var v = element.val();
            if (typeof fn === "function") {
                return fn(v, element);
            }
            return false;
        }

        function mascaraCPF(valor) {
            valor = valor.replace(/\D/g, "");
            valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
            valor = valor.replace(/(\d{3})\.(\d{3})(\d)/, "$1.$2.$3");
            valor = valor.replace(/(\d{3})\.(\d{3})\.(\d{3})(\d{1,2})$/, "$1.$2.$3-$4");
            return valor;
        }

        function mascaraCNPJ(valor) {
            valor = valor.replace(/\D/g, "");
            valor = valor.replace(/(\d{2})(\d)/, "$1.$2");
            valor = valor.replace(/(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
            valor = valor.replace(/(\d{2})\.(\d{3})\.(\d{3})(\d)/, "$1.$2.$3/$4");
            valor = valor.replace(/(\d{2})\.(\d{3})\.(\d{3})\/(\d{4})(\d{1,2})/, "$1.$2.$3/$4-$5");
            return valor;
        }
        $(this).on('blur', 'input[is]', function() {
            genericValidationElement($(this))
        });
        $(this).on('keyup', 'input[is]', function() {
            genericValidationElement($(this))
        });
        $(this).on('change', 'select[is]', function() {
            genericValidationElement($(this))
        });
        $(this).submit(function(event) {
            var validationFail = false;
            $(this).find('select').each(function() {
                var attr = $(this).attr('is');
                if (attr == 'required') {
                    if ($(this).val() == "") {
                        var border = $(this).css('border-color');
                        if (border != BORDER_COLOR_SUCCESS) {
                            if (!genericValidationElement($(this))) {
                                validationFail = true;
                            }
                        }
                    }
                }
            });
            $(this).find('textarea').each(function() {
                var attr = $(this).attr('is');
                if (attr == 'required') {
                    if ($(this).text() == "") {
                        var border = $(this).css('border-color');
                        if (border != BORDER_COLOR_SUCCESS) {
                            if (!genericValidationElement($(this))) {
                                validationFail = true;
                            }
                        }
                    }
                }
            });
            $(this).find('input').each(function(i) {
                var attr = $(this).attr('is');
                var mask = $(this).attr('mask');
                if (typeof attr !== typeof undefined && attr !== false) {
                    var border = $(this).css('border');
                    if (border !== BORDER_COLOR_SUCCESS) {
                        if (!genericValidationElement($(this))) {
                            validationFail = true;
                        }
                    }
                }
                if (typeof mask !== typeof undefined && mask !== false) {
                    var border = $(this).css('border');
                    if ($(this).val() != "") {
                        if (border !== BORDER_COLOR_SUCCESS) {
                            if (!genericValidationElementMask($(this))) {
                                validationFail = true;
                            }
                        }
                    } else {
                        $(this).css('border', BORDER_COLOR_SUCCESS)
                    }
                }
            });
            if (validationFail) {
                event.preventDefault();
                return false;
            } else {
                return true;
            }
        });

        function genericValidationElementMask(element) {
            is = element.attr('mask');
            var fn = masks[is];
            var v = element.val();
            if (v == '') {
                return true;
            }
            if (typeof fn === "function") {
                return true;
               // return fn(v, element);
            }
            return false;
        }
    };
})(jQuery);