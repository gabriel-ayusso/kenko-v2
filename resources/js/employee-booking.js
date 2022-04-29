import axios from 'axios';
import moment from 'moment';
import 'icheck/icheck';

const trataDisponibilidades = () => {
    const serviceId = $('#service_id').val();
    const date = $('#date').val();

    $('#service-feedback').empty();
    $('#horarios').empty();
    
    if (!date) return;
    if (!serviceId) return;
    
    const spin = $(`<i class="fas fa-spinner fa-spin fa-lg text-primary"></i>`);
    $('#horarios').append(spin);

    axios.get(`/booking/${serviceId}/available?date=${date}`).then(resp => {
        $('#horarios').empty();

        if (resp.data.result != 'success') {
            $('#service-feedback').append(`<div class="alert alert-danger">Houve uma falha ao obter os horários disponíveis. Por favor, tente novamente mais tarde.</div>`);
            return;
        }

        let found = false;

        resp.data.data.map((value, index) => {
            const div = $(`<div class="form-check"></div>`)
            const el = $(`<input type="radio" value="${value.time}" id="time_${index}" name="time" class="icheck" />`);
            const label = $(`<label for="time" class="form-check-label"> ${moment(value.time).format('HH:mm')}</label>`)
            div.append(el);
            div.append(label);
            $('#horarios').append(div);
            found = true;
        });

        if (!found) {
            $('#service-feedback').append(`<div class="alert alert-danger">Não há horários disponíveis nesse dia para esse serviço.</div>`);
        } else {
            //            $('.icheck').iCheck();
            $('.icheck').each(function () {
                var self = $(this),
                    label = self.next(),
                    label_text = label.text();

                label.remove();
                self.iCheck({
                    checkboxClass: 'icheckbox_line-green',
                    radioClass: 'iradio_line-green',
                    insert: '<div class="icheck_line-icon"></div>' + label_text
                });
            });
        }

    }).catch(e => {
        if (e.response) {
            const data = e.response.data;
            let errors = [];
            for (const key in data.errors) {
                const element = data.errors[key];
                if (element[0])
                    errors.push(element[0]);
            }

            if (errors.length > 0) {
                const div = $(`<div class="alert alert-danger"></div>`);
                errors.map(error => {
                    $(div).append(`<p class="m-0">${error}</p>`);
                });
                $('#service-feedback').append(div);
            } else {
                $('#service-feedback').append(`<div class="alert alert-danger">${data.message}</div>`);
            }
        } else {
            console.error(e);
        }
    });
}

$(document).ready(function () {
    $('#date').on('change', function (e) {
        trataDisponibilidades();
    });
    $('#service_id').on('change', function (e) {
        trataDisponibilidades();
    });
    trataDisponibilidades();
});