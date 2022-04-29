import React from 'react';

export default props => {
    return (
        <div className="invalid-feedback">
            {props.children}
        </div>
    )
}