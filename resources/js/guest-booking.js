import axios from 'axios';
import moment from 'moment';
import 'icheck/icheck';

const setLoadingHorarios = () => {
    console.log('gag');
    $('#horarios').empty();
    const container = $('<div/>', { "class": "text-center" });
    const el = $('<span/>', { "class": "fas fa-circle-notch fa-spin fa-2x text-muted" });
    container.append(el);
    $('#horarios').append(container);
}

const loadHorarios = (date, serviceId) => {
    let lastName = '';

    axios.get(`/api/booking/${serviceId}/available?date=${date}`).then(resp => {
        const divHorarios = $('#horarios');

        divHorarios.empty();
        $('#service-feedback').empty();
        console.log(resp.data);

        if (resp.data.result != 'success') {
            $('#service-feedback').append(`<div class="alert alert-danger">Houve uma falha ao obter os horários disponíveis. Por favor, tente novamente mais tarde.</div>`);
            return;
        }

        let found = false;

        let itemsDiv = $('<div/>', { "class": "row" });
        divHorarios.append(itemsDiv);

        resp.data.data.map((value, index) => {
            const name = `${value.employee.firstname} ${value.employee.lastname}`;
            if (name !== lastName) {
                const title = $(`<h4 class="text-secondary mt-2 col-md-12">${name}</h4>`);
                itemsDiv.append(title);
                itemsDiv = $('<div/>', { "class": "row" });
                divHorarios.append(itemsDiv);
                lastName = name;
            }

            const div = $(`<div class="col-6 col-md-3"></div>`);
            const el = $(`<input type="radio" value="${value.time}" id="time_${index}" name="time" class="icheck" data-employee-id="${value.employee.id}" />`);
            const label = $(`<label for="time" class="form-check-label">${moment(value.time).format('HH:mm')}</label>`)
            div.append(el);
            div.append(label);
            itemsDiv.append(div);
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
                    radioClass: 'iradio_line-green',
                    uncheckedRadioClass: 'iradio_line-aero',
                    insert: '<div class="icheck_line-icon"></div>' + label_text
                });

                self.on('ifChecked', function(event) {
                    $('#employee_id').val($(event.target).attr('data-employee-id'));
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

$(() => {
    $('#date').on('change', e => {
        setLoadingHorarios();

        const serviceId = $('#serviceId').val();
        const date = $('#date').val();
        loadHorarios(date, serviceId);
    });
});
