import './FormGroup.css'
import type { ReactNode } from 'react';

interface FormGroupProps {
  label: string;
  htmlFor: string;
  children: ReactNode;
}

function FormGroup({ label, htmlFor, children }: FormGroupProps) {
  return (
    <div className="form-group">
      <label htmlFor={htmlFor}>{label}</label>
      {children}
    </div>
  );
}

export default FormGroup;