import React from 'react';
import { Field, ErrorMessage } from 'formik';
import InvalidFeedback from './InvalidFeedback';

export default props => {
    const hasError = props.error ? true : false
    const type = props.type ?? 'text';
    let className = "form-control";
    if(hasError)
        className += " is-invalid";

    return  (
        <React.Fragment>
            <Field 
                {...props}
                autoComplete={props.name}
                name={props.name}
                type={type}
                id={props.name}
                className={className}
                />
            { props.help && <small id={`${props.name}Help`} className="form-text text-muted">{props.help}</small> }
            <ErrorMessage name={props.name} component={InvalidFeedback} className="invalid-feedback" />
        </React.Fragment>
    );
}