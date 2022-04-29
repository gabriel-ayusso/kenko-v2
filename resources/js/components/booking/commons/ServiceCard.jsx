import React from 'react';

export default props => {
    return (
        <div className="col-md-3">
            <div className="card text-white bg-secondary mb-3 animated fadeIn">
                {/* <div className="card-header">{props.service.name}</div> */}
                <div className="card-body">
                    <h5 className="card-title">{props.service.name}</h5>
                    <div className="card-text">
                        {props.service.description}

                        <div className="mt-2 py-2" style={{ borderTop: '1px solid #ccc' }}>
                            <div className="row">
                                <div className="col-md-6">
                                    {props.service.employees.map((employee, idx) => (
                                        <span key={idx} className="badge bg-light text-dark me-1"><i className="fas fa-spa"></i> {employee.firstname} {employee.lastname}</span>
                                    ))}
                                    <span style={{fontSize: 14, display: 'block'}}>R$ {parseFloat(props.service.price).toFixed(2)}</span>
                                </div>
                                <div className="col-md-6 text-end">
                                    <button className="btn btn-light btn-block" onClick={() => props.onClick(props.service)}><i className="far fa-calendar mr-1"></i> Agendar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
