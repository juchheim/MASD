// src/Modal.js
import React from 'react';
import './Modal.css';

const Modal = ({ message, onConfirm, onCancel }) => (
  <div className="modal">
    <div className="modal-content">
      <p>{message}</p>
      <button onClick={onConfirm}>Allow</button>
      <button onClick={onCancel}>Deny</button>
    </div>
  </div>
);

export default Modal;
