import React from 'react';
import { Formik, Form } from 'formik';
import Spinner from './commons/Spinner';
import DefaultTextField from './commons/DefaultTextField';
import * as Yup from 'yup';
import { handleAxiosError, sendToast } from './commons/helpers';
import axios from 'axios';
import moment from 'moment';

export default class BookingForm extends React.Component {
    constructor(props) {
        super(props);
    }

    handleGoBack = () => {
        this.props.onGoBack();
    }

    handleSubmit = (values, actions) => {

        actions.setSubmitting(true);

        let time = moment(this.props.availability.time).format('YYYY-MM-DD HH:mm:ss');

        const data = {
            date: time,
            service_id: this.props.service.id,
            employee_id: this.props.availability.employee.id,
            ...values
        }

        axios.post(`/api/booking`, data).then(resp => {
            if (resp.data.result === 'success') {
                sendToast('Parabéns', 'Seu agendamento foi realizado com sucesso. Você receberá um email de confirmação', 'success');
                this.props.onSuccess();

                try {
                    window.gtag('event', 'conversion', { 'send_to': 'AW-721404582/wkgrCJ-XnNEBEKaF_9cC' });
                } catch (e) {
                    console.log(e);
                };
            } else {
                if (resp.data.message)
                    actions.setStatus('Ops, houve um erro no seu agendamento: ' + resp.data.message);
                else
                    actions.setStatus('Ops, houve um erro no seu agendamento. Tente novamente. Se o problema persistir, entre em contato conosco.');
            }

            actions.setSubmitting(false);
        }).catch(e => {
            actions.setSubmitting(false);
            handleAxiosError(e, actions);
        });

    }

    render() {

        const initialValues = {
            name: '',
            email: '',
            phone: '',
        }

        return (
            <Formik
                initialValues={initialValues}
                validationSchema={this.validationSchema}
                onSubmit={this.handleSubmit}>

                {({ values, errors, status, isSubmitting, handleChange }) =>
                    <React.Fragment>
                        {isSubmitting ? <Spinner /> :
                            <Form ref={this.form}>
                                {status && <div className="alert alert-danger">{status}</div>}

                                <DefaultTextField
                                    label="Nome"
                                    name="name"
                                    value={values.name}
                                    error={errors.name}
                                    onChange={handleChange} />

                                <DefaultTextField
                                    label="Email"
                                    name="email"
                                    value={values.email}
                                    error={errors.email}
                                    onChange={handleChange} />

                                <DefaultTextField
                                    label="Telefone"
                                    name="phone"
                                    type="number"
                                    help="Insira apenas números com DDD. Exemplo: 11999999999"
                                    value={values.phone}
                                    error={errors.phone}
                                    onChange={handleChange} />

                                <div className="form-group row">
                                    <div className="col-lg-10 offset-lg-2">
                                        <button type="button" onClick={this.handleGoBack} className="btn btn-outline-primary mr-2">Voltar</button>
                                        <button type="submit" disabled={isSubmitting} className="btn btn-primary">Agendar</button>
                                    </div>
                                </div>

                            </Form>
                        }
                    </React.Fragment>
                }

            </Formik>
        );
    }

    validationSchema = Yup.object().shape({
        name: Yup.string()
            .max(255, 'Nome não pode ter mais de 255 carácteres.')
            .required('Por favor, preencha o nome.'),
        email: Yup.string()
            .max(255, 'E-mail não pode ter mais de 255 carácteres.')
            .email('Por favor, preencha um e-mail válido'),
        phone: Yup.string()
            .required('Por favor, preencha o telefone.')
            .max(25, 'Telefone não pode ter mais de 25 carácteres.')
    });

}

