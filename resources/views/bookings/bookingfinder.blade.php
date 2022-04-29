@php
    $id = null;
    $desc = null;

    if(isset($booking)) {
        $id = $booking->id;
        $desc = "[" . $booking->date->format('d/m/Y H:i') . "] " . $booking->service->name . " - " . $booking->name;
    }

@endphp

<div class="form-group row">
    <label for="booking_id" class="col-sm-2 col-form-label">Agendamento</label>
    <div class="col-md-10">
        <div class="input-group mb-3">
            <input type="hidden" name="booking_id" id="booking_id" value="{{$id}}">
            <input type="text" class="form-control" placeholder="" aria-label="Agendamento" aria-describedby="button-bookingid" id="booking_desc" disabled value="{{$desc}}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="button-bookingid" data-toggle="modal" data-target="#bookingModal"><i class="fas fa-search"></i></button>
            </div>
        </div>
        @error('booking_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div id="booking-feedback"></div>
    </div>
</div>
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Pesquisar Agendamentos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th><input type="date" class="form-control" placeholder="Data" id="searchDate"></th>
                            <th><input type="text" class="form-control" placeholder="Cliente" id="searchCustomer"></th>
                            <th><input type="text" class="form-control" placeholder="Serviço" id="searchService"></th>
                            <th><input type="text" class="form-control" placeholder="Profissional" id="searchEmployee"></th>
                            <th><button type="button" class="btn btn-outline-primary" id="btnSearch"><i class="fas fa-search"></i></button></th>
                        </tr>
                    </thead>
                    <tbody id="bookingTableBody">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script>
    selectBooking = (id, desc) => {
        $('#bookingModal').modal('hide');
        $('#booking_id').val(id);
        $('#booking_desc').val(desc);
    }

    window.onload = () => {
        moment.locale('pt-BR');

        $('#btnSearch').bind('click', e => {
            const values = {
                date: $('#searchDate').val(),
                customer: $('#searchCustomer').val(),
                service: $('#searchService').val(),
                employee: $('#searchEmployee').val(),
            }

            axios.get('/api/booking/search', {params: values }).then(resp => {
                console.log(resp.data);
                if(resp.data.bookings) {
                    $('#bookingTableBody').empty();
                    resp.data.bookings.map(booking => {
                        const date = moment.utc(booking.date).format('DD/MM/YYYY HH:mm');
                        const desc = `[${date}] ${booking.service.name} - ${booking.name}`;
                        let tr = $('<tr/>');
                        tr.append($(`<td><a href="#" onclick="selectBooking(${booking.id}, '${desc}');return false;">${date}</a></td>`));
                        tr.append($(`<td>${booking.name}</td>`));
                        tr.append($(`<td>${booking.service.name}</td>`));
                        tr.append($(`<td>${booking.employee.firstname} ${booking.employee.lastname}</td>`));
                        tr.append($(`<td></td>`));
                        $('#bookingTableBody').append(tr);
                    });
                }

            }).catch(e => {
                console.log(e);
                alert('Ops... houve um erro ao carregar os agendamentos. Tente novamente mais tarde.');
            });
        });
    }
</script>