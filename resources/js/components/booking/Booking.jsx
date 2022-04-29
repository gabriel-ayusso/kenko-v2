import React from 'react';
import axios from 'axios';
import Spinner from './commons/Spinner';
import ServiceCard from './commons/ServiceCard';
import moment from 'moment';
import BookingForm from './BookingForm';

export default class Booking extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            search: '',
            loading: false,
            loadingAvailables: false,
            availables: null,
            categories: [],
            employees: [],
            current: {}, // current service - that one the user was selected.
            bookingStage: 1,
            bookingTime: null,
            totalServices: 0
        }
    }

    componentDidMount() {
        this.loadServices();
    }

    loadServices = () => {
        this.setState({ loading: true });
        axios.get('/api/booking/categories').then(resp => {
            this.setState({ loading: false, categories: resp.data.categories });

            let totalServices = 0;
            resp.data.categories.forEach(category => totalServices += category.services_count);
            this.setState({ totalServices });

        }).catch(e => {
            this.setState({ loading: false });
            console.error(e);
        });
    }

    handleSelectService = (service) => {
        this.setState({ 
            current: service, 
            bookingStage: 1,
            availables: null,
        });
        $('#availabilityDate').val('');
        $('#modalBooking').modal('show');
    }

    loadAvailables = (e) => {
        this.setState({ loadingAvailables: true });
        axios.get(`/api/booking/${this.state.current.id}/available?date=${e.target.value}`).then(resp => {
            this.setState({ loadingAvailables: false, availables: resp.data.data });

            let employeeIds = [];
            resp.data.data.forEach(element => {
                if (!employeeIds.includes(element.employee.id))
                    employeeIds.push(element.employee.id);
            });

            let employees = [];
            employeeIds.forEach(id => {
                for (let i = 0; i < resp.data.data.length; i++) {
                    if (id === resp.data.data[i].employee.id) {
                        employees.push(resp.data.data[i].employee);
                        break;
                    }
                }
            });

            this.setState({ employees });

        }).catch(e => {
            if (e.response)
                console.error(e.response);
            this.setState({ loadingAvailables: false });
            console.error(e);
        });
    }

    handleStage2 = (availability) => {
        this.setState({ availability, bookingStage: 2 });
    }

    handleBookingSuccess = () => {
        $('#modalBooking').modal('hide');
        this.setState({ bookingStage: 1 });
    }

    handleGoBack = () => {
        this.setState({ bookingStage: 1 });
    }

    render() {
        const stage1 = (
            <div className="animated fadeIn">
                <h5>Selecione o dia:</h5>
                <input id="availabilityDate" type="date" className="form-control" onChange={this.loadAvailables} disabled={this.state.loadingAvailables} />

                {this.state.loadingAvailables ? <Spinner /> :
                    <React.Fragment>
                        {this.state.availables &&
                            <div>
                                <h4 className="mt-2">Horários disponíveis</h4>

                                {this.state.availables.length === 0 && <div className="alert alert-info">Sentimos muito, mas não temos horários disponíveis nessa data. Por favor, selecione outra.</div>}

                                {this.state.employees.map((employee, employeeIdx) => (
                                    <div className="row" key={employeeIdx}>
                                        <div className="col-md-12">
                                            <h5 className="text-primary">{employee.firstname} {employee.lastname}</h5>

                                            {this.state.availables.filter((value) => value.employee.id === employee.id).map((availability, idx) => (
                                                <button className="btn btn-primary mr-2 mb-2" key={idx} onClick={() => this.handleStage2(availability)}>
                                                    {moment(availability.time).format('HH:mm')}
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                ))}


                            </div>
                        }
                    </React.Fragment>
                }
            </div>
        );

        const stage2 = <div className="animated fadeIn"><BookingForm onGoBack={this.handleGoBack} availability={this.state.availability} service={this.state.current} onSuccess={this.handleBookingSuccess} /></div>;

        return (
            <React.Fragment>
                <div className="row mb-4">
                    <div className="col-md-6">
                        <h2>Navegue pelo serviço</h2>
                    </div>
                    <div className="col-md-6">
                        <div className="input-group">
                            <input type="text" className="form-control" placeholder="Buscar serviço..." aria-label="Buscar serviço..." aria-describedby="basic-addon2" value={this.state.search} onChange={(e) => this.setState({ search: e.target.value })} />
                            <div className="input-group-append">
                                <span className="input-group-text" id="basic-addon2"><i className="fas fa-search"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                {this.state.loading ? <Spinner /> :
                    this.state.totalServices === 0 ? <div className="alert alert-info">Sentimos muito, mas não temos nenhum serviço disponível no momento. Volte novamente mais tarde ou entre em contato conosco.</div> :
                    this.state.categories.map((category, index) => (
                        <div key={index}>
                            {category.services.length > 0 &&
                                <div className="row mb-2">
                                    {category.services.filter(service => service.name.toLowerCase().includes(this.state.search.toLowerCase())).length > 0 &&
                                        <div className="col-md-12 animated fadeIn">
                                            <h4>{category.name}</h4>
                                            <div className="row">
                                                {category.services.filter(service => service.name.toLowerCase().includes(this.state.search.toLowerCase())).map((service, idx) => (
                                                    <ServiceCard onClick={this.handleSelectService} service={service} key={idx} />
                                                ))}
                                            </div>
                                        </div>
                                    }
                                </div>
                            }
                        </div>
                    ))
                }

                <div className="modal fade" id="modalBooking" tabIndex="-1" role="dialog" aria-labelledby="modalBookingLabel" aria-hidden="true">
                    <div className="modal-dialog" role="document">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title" id="modalBookingLabel">{this.state.current.name}</h5>
                                <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div className="modal-body">
                                {this.state.bookingStage === 1 ? stage1 : stage2}
                            </div>
                        </div>
                    </div>
                </div>
            </React.Fragment>
        );
    }
}