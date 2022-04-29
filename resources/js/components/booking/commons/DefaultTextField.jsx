import React from 'react';
import TextField from './TextField';

export default props => {
    return (
        <div className="form-group row">
            <label className="col-lg-2 col-form-label">{props.label}</label>
            <div className="col-lg-10">
                <TextField
                    {...props}
                    placeholder={props.placeholder ?? props.label}
                />
            </div>
        </div>
    );
}