import React from 'react';

export default props => {
    return (
        <div className="col-lg-3 col-md-4 d-flex align-items-stretch">
            <div className="card bg-light mb-3 animated fadeIn">
                <h5 className="card-header">{props.service.name}</h5>
                {/* <div className="card-header">{props.service.name}</div> */}
                <div className="card-body">
                    <div className="card-text d-flex flex-column justify-content-between h-100">
                        <div className="flex-grow">
                            {props.service.description}
                        </div>

                        <div className="mt-2 py-2" style={{ borderTop: '1px solid #ccc' }}>
                            <div className="row">
                                <div className="col-md-6">
                                    {props.service.employees.map((employee, idx) => (
                                        <span key={idx} className="badge bg-secondary text-white me-1"><i className="fas fa-spa"></i> {employee.firstname} {employee.lastname}</span>
                                    ))}
                                    <span className="mt-2" style={{ fontSize: 14, display: 'block' }}>R$ {parseFloat(props.service.price).toFixed(2)}</span>
                                </div>
                                <div className="col-md-6 text-end">
                                    <div className="d-grid gap-2">
                                        <button className="btn btn-primary" onClick={() => props.onClick(props.service)}><i className="far fa-calendar me-1"></i> Agendar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
