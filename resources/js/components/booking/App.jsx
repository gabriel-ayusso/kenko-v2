import React from 'react';
import ReactDOM from 'react-dom';
import Booking from './Booking';

if (document.getElementById('app')) {
    ReactDOM.render(<Booking />, document.getElementById('app'));
}
